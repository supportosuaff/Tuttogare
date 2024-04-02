<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	include_once("../../../config.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$strsql = "SELECT * FROM b_gestione_gare WHERE link LIKE '/gare/commissione/edit.php%'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if ($edit && !$lock)
	{
		if ($_POST["codice_albo"]==-1) {
			if (file_exists($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["import_filechunk"])) {

				$tmp_commissione = array();
				$tmp_commissione["attivo"] = "S";
				$tmp_commissione["codice_ente"] = $_SESSION["ente"]["codice"];
				$tmp_commissione["codice_gestore"] = $tmp_commissione["codice_ente"];
				$tmp_commissione["codice_gara"] = $_POST["codice_gara"];
				$tmp_commissione["oggetto"] = "Importazione manuale da gara #" . $_POST["codice_gara"];
				$tmp_commissione["utente_creazione"] = $_SESSION["codice_utente"];
				$tmp_commissione["timestamp_creazione"] = date('Y-m-d H:i:s');


				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_albi_commissione";
				$salva->operazione = "INSERT";
				$salva->oggetto = $tmp_commissione;
				$codice_albo = $salva->save();
				if ($codice_albo > 0) {
					include($root."/albi_commissione/iscritti/utility.php");
					$msg = importoCSV2Albo($_POST["import_filechunk"],$codice_albo);
				} else {
					$msg = "Errore nella creazione dell'elenco";
				}
			} else {
				$msg = "E' necessario selezionare il file";
			}
			if (empty($msg)) {
				$_POST["codice_albo"] = $codice_albo;
			} else {
				?>
				alert('<?= $msg ?>');
				<?
				die();
			}
		}
		$sql = "SELECT * FROM b_albi_commissione WHERE codice = :codice_albo AND codice_gestore = :codice_gestore ";
		$ris_albo = $pdo->bindAndExec($sql,array(":codice_albo"=>$_POST["codice_albo"],":codice_gestore"=>$_SESSION["ente"]["codice"]));
		if ($ris_albo->rowCount() > 0) {
			$albo = $ris_albo->fetch(PDO::FETCH_ASSOC);
			$sql = "SELECT * FROM b_commissari_albo WHERE codice_albo = :codice_albo AND attivo = 'S'";
			$ris_commissari = $pdo->bindAndExec($sql,array(":codice_albo"=>$albo["codice"]));

			$sql = "SELECT * FROM b_commissari_albo WHERE codice_albo = :codice_albo AND interno = 'N' AND attivo = 'S'";
			$ris_commissari_esterni = $pdo->bindAndExec($sql,array(":codice_albo"=>$albo["codice"]));
			$count_esterni = $ris_commissari_esterni->rowCount();
			if (($ris_commissari->rowCount() >= $_POST["componenti"]) && ($count_esterni >= ($_POST["componenti"] - $_POST["interni"]))) {

				$estrazione = array();
				$estrazione["codice_gara"] = $_POST["codice_gara"];
				$estrazione["codice_albo"] = $albo["codice"];
				$estrazione["componenti"] = $_POST["componenti"];
				$estrazione["interni"] = $_POST["interni"];

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_estrazioni_commissioni";
				$salva->operazione = "INSERT";
				$salva->oggetto = $estrazione;
				$codice_estrazione = $salva->save();

				if ($codice_estrazione !== false) {
					$commissari = $ris_commissari->fetchAll(PDO::FETCH_ASSOC);
					$totale_interni = 0;
					$totale_esterni = 0;
					$count_array_interni = array();
					$count_array_esterni = array();

					foreach($commissari AS $commissario) {
						$sql = "SELECT r_estrazioni_commissioni.* FROM r_estrazioni_commissioni JOIN b_estrazioni_commissioni ON r_estrazioni_commissioni.codice_estrazione = b_estrazioni_commissioni.codice
										WHERE b_estrazioni_commissioni.codice_albo = :codice_albo AND r_estrazioni_commissioni.codice_commissario = :codice_commissario AND selezionato = 'S'";
						$ris_count = $pdo->bindAndExec($sql,array(":codice_albo"=>$albo["codice"],":codice_commissario"=>$commissario["codice"]));
						$selezioni = $ris_count->rowCount();
						if ($commissario["interno"]=="S") {
							$totale_interni++;
							if (empty($count_array_interni[$selezioni])) $count_array_interni[$selezioni] = array();
							$count_array_interni[$selezioni][] = $commissario;
						} else {
							$totale_esterni++;
							if (empty($count_array_esterni[$selezioni])) $count_array_esterni[$selezioni] = array();
							$count_array_esterni[$selezioni][] = $commissario;
						}
					}
					if ($estrazione["interni"] > 0 || ($totale_esterni < $estrazione["componenti"])) {
						if (empty($estrazione["interni"])) {
							$estrazione["interni"] = $estrazione["componenti"] - $totale_esterni;
							$integrazione_richiesta = true;
						}
						$selected_interni = array();
						$pick_interni = array();

						ksort($count_array_interni,SORT_NUMERIC);
						foreach($count_array_interni AS $count => $commissari) {
							if (count($commissari) <= ($estrazione["interni"]-(count($selected_interni)+count($pick_interni)))) {
								foreach($commissari as $commissario) {

									$selected_interni[] = $commissario;

									$relazione = array();
									$relazione["codice_estrazione"] = $codice_estrazione;
									$relazione["codice_commissario"] = $commissario["codice"];
									$relazione["escluso"] = "N";
									$relazione["tipo"] = "I";
									$relazione["identificativo"] = "-1";
									$relazione["selezionato"] = "S";

									$salva->nome_tabella = "r_estrazioni_commissioni";
									$salva->operazione = "INSERT";
									$salva->oggetto = $relazione;
									$salva->save();

								}
							} else {
								if ((count($selected_interni)+count($pick_interni)) < $estrazione["interni"]) {
									foreach($commissari as $commissario) {
										$pick_interni[] = $commissario;
									}
								} else {
									foreach($commissari as $commissario) {
										$relazione = array();
										$relazione["codice_estrazione"] = $codice_estrazione;
										$relazione["codice_commissario"] = $commissario["codice"];
										$relazione["escluso"] = "S";
										$relazione["tipo"] = "I";
										$relazione["selezionato"] = "N";

										$salva->nome_tabella = "r_estrazioni_commissioni";
										$salva->operazione = "INSERT";
										$salva->oggetto = $relazione;
										$salva->save();
									}
								}
							}
						}
						if (count($pick_interni) > 0) {
							shuffle($pick_interni);
							$to_pick = $estrazione["interni"] - count($selected_interni);
							$sequenza_interna = array_rand($pick_interni,$to_pick);
							if (!is_array($sequenza_interna)) $sequenza_interna = array($sequenza_interna);
							$riserve_i = array_diff(array_keys($pick_interni),$sequenza_interna);
							$sequenza_sost_interna = array_rand($riserve_i,$to_pick);
							if (!is_array($sequenza_sost_interna)) $sequenza_sost_interna = array($sequenza_sost_interna);
							foreach($pick_interni AS $key => $commissario) {
								$selezionato = (in_array($key,$sequenza_interna)!==false) ? "S" : "N";
								if ($selezionato == "N") {
									$selezionato = (in_array($key,$sequenza_sost_interna)!==false) ? "C" : "N";
								}

								$relazione = array();
								$relazione["codice_estrazione"] = $codice_estrazione;
								$relazione["codice_commissario"] = $commissario["codice"];
								$relazione["identificativo"] = $key;
								$relazione["tipo"] = "I";
								$relazione["escluso"] = "N";
								$relazione["selezionato"] = $selezionato;

								$salva->nome_tabella = "r_estrazioni_commissioni";
								$salva->operazione = "INSERT";
								$salva->oggetto = $relazione;
								$salva->save();
							}

							$update_estrazione = array();
							$update_estrazione["codice"] = $codice_estrazione;
							$update_estrazione["sequenza_i"] = implode(",", $sequenza_interna). " | " . implode(",", $sequenza_sost_interna);

							$salva->nome_tabella = "b_estrazioni_commissioni";
							$salva->operazione = "UPDATE";
							$salva->oggetto = $update_estrazione;
							$codice_estrazione = $salva->save();
						}
					}

					$vacanti = $estrazione["componenti"] - $estrazione["interni"];


					$selected_esterni = array();
					$pick_esterni = array();

					ksort($count_array_esterni,SORT_NUMERIC);

					foreach($count_array_esterni AS $count => $commissari) {
						if (count($commissari) <= ($vacanti-(count($selected_esterni)+count($pick_esterni)))) {
							foreach($commissari as $commissario) {

								$selected_esterni[] = $commissario;

								$relazione = array();
								$relazione["codice_estrazione"] = $codice_estrazione;
								$relazione["codice_commissario"] = $commissario["codice"];
								$relazione["escluso"] = "N";
								$relazione["identificativo"] = -1;
								$relazione["tipo"] = "E";
								$relazione["selezionato"] = "S";

								$salva->nome_tabella = "r_estrazioni_commissioni";
								$salva->operazione = "INSERT";
								$salva->oggetto = $relazione;
								$salva->save();

							}
						} else {
							if ((count($selected_esterni)+count($pick_esterni)) < $vacanti) {
								foreach($commissari as $commissario) $pick_esterni[] = $commissario;
							} else {
								foreach($commissari as $commissario) {
									$relazione = array();
									$relazione["codice_estrazione"] = $codice_estrazione;
									$relazione["codice_commissario"] = $commissario["codice"];
									$relazione["escluso"] = "S";
									$relazione["tipo"] = "E";
									$relazione["selezionato"] = "N";

									$salva->nome_tabella = "r_estrazioni_commissioni";
									$salva->operazione = "INSERT";
									$salva->oggetto = $relazione;
									$salva->save();
								}
							}
						}
					}
					if (count($pick_esterni) > 0) {
						shuffle($pick_esterni);
						$to_pick = $vacanti - count($selected_esterni);
						$sequenza_esterna = array_rand($pick_esterni,$to_pick);
						if (!is_array($sequenza_esterna)) $sequenza_esterna = array($sequenza_esterna);
						$riserve_e = array_diff(array_keys($pick_esterni),$sequenza_esterna);
						$sequenza_sost_esterna = array_rand($riserve_e,$to_pick);
						if (!is_array($sequenza_sost_esterna)) $sequenza_sost_esterna = array($sequenza_sost_esterna);
						foreach($pick_esterni AS $key => $commissario) {
							$selezionato = (in_array($key,$sequenza_esterna)!==false) ? "S" : "N";
							if ($selezionato == "N") {
								$selezionato = (in_array($key,$sequenza_sost_esterna)!==false) ? "C" : "N";
							}
							$relazione = array();
							$relazione["codice_estrazione"] = $codice_estrazione;
							$relazione["codice_commissario"] = $commissario["codice"];
							$relazione["identificativo"] = $key;
							$relazione["tipo"] = "E";
							$relazione["escluso"] = "N";
							$relazione["selezionato"] = $selezionato;
							$relazione["presidente"] = "N";

							$salva->nome_tabella = "r_estrazioni_commissioni";
							$salva->operazione = "INSERT";
							$salva->oggetto = $relazione;
							$salva->save();
						}

						$update_estrazione = array();
						$update_estrazione["codice"] = $codice_estrazione;
						if (isset($integrazione_richiesta)) $update_estrazione["integrazione"] = "S";
						$update_estrazione["sequenza_e"] = implode(",", $sequenza_esterna) . " | " . implode(",", $sequenza_sost_esterna);

						$salva->nome_tabella = "b_estrazioni_commissioni";
						$salva->operazione = "UPDATE";
						$salva->oggetto = $update_estrazione;
						$codice_estrazione = $salva->save();
					}

					$sql = "SELECT * FROM b_estrazioni_commissioni WHERE codice_gara = :codice_gara ";
					$ris = $pdo->bindAndExec($sql,array(":codice_gara"=>$_POST["codice_gara"]));
					if ($ris->rowCount() == 0) {
						$sql = "SELECT * FROM r_estrazioni_commissioni WHERE codice_estrazione = :codice_estrazione AND tipo = 'E' AND selezionato = 'S' ";
						$esterni = $pdo->bindAndExec($sql,array(":codice_estrazione"=>$codice_estrazione));
						if ($esterni->rowCount() > 1) {
							$esterni = $esterni->fetchAll(PDO::FETCH_ASSOC);
							$presidente = rand(0,count($esterni)-1);
							$presidente = $esterni[$presidente];
						} else {
							$presidente = $esterni->fetch(PDO::FETCH_ASSOC);
						}

						$presidente["presidente"] = "S";
	
						$salva->nome_tabella = "r_estrazioni_commissioni";
						$salva->operazione = "UPDATE";
						$salva->oggetto = $presidente;
						$salva->save();
					}

					$codice_gara = $_POST["codice_gara"];
					log_gare($_SESSION["ente"]["codice"],$codice_gara,"INSERT","Estrazione commissione di gara");

					$html ="<html>";
					$html.= "<style>";
					$html.= "body { font-size:10px } table { width:100%; } ";
					$html.= "table td { padding:2px; border:1px solid #CCC } ";
					$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
					$html.= "</style>";
					$html.= "<body>";
					ob_start();
					include("report.php");
					$report = ob_get_clean();
					$html.=$report;
					$html.= "</body></html>";

					$percorso = $config["arch_folder"];

					$allegato["online"] = 'N';
					$allegato["codice_gara"] = $codice_gara;
					$allegato["codice_ente"] = $_SESSION["ente"]["codice"];

					$percorso .= "/".$allegato["codice_gara"];

					if (!is_dir($percorso)) mkdir($percorso,0777,true);
					$allegato["nome_file"] = $allegato["codice_gara"] . " - Verbale estrazione commissione.".time().".pdf";
					$allegato["titolo"] = "Verbale Estrazione commissione - " . date("Y-m-d H:i:s");

					$options = new Options();
					$options->set('defaultFont', 'Helvetica');
					$options->setIsRemoteEnabled(true);
					$dompdf = new Dompdf($options);
					$dompdf->loadHtml($html);
					$dompdf->setPaper('A4', 'portrait');
					$dompdf->set_option('defaultFont', 'Helvetica');
					$dompdf->render();
					$content = $dompdf->output();
					file_put_contents($percorso."/".$allegato["nome_file"],$content);

					if (file_exists($percorso."/".$allegato["nome_file"])) {
						$allegato["riferimento"] = getRealName($percorso."/".$allegato["nome_file"]);
						rename($percorso."/".$allegato["nome_file"],$percorso."/".$allegato["riferimento"]);
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_allegati";
						$salva->operazione = "INSERT";
						$salva->oggetto = $allegato;
						$codice_allegato = $salva->save();

					}

					?>
					alert('Sorteggio effettuato con successo.');
					window.location.href = window.location.href;
					<?
				} else {
					?>
					alert("Errori nella creazione dell'estrazione");
					<?
				}
			} else {
			?>
				alert("Non sono disponibili soggetti sufficienti nell' albo selezionato");
			<?
			}
		} else {
		?>
			alert('Nessun albo trovato');
		<?
		}
	}
?>
