<?

	use Dompdf\Dompdf;
	use Dompdf\Options;

	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	include($root."/inc/pdftotext.phpclass");
	$public = true;
	if (isset($_POST["codice_gara"]) && isset($_POST["codice_lotto"]) && isset($_POST["tipo"]) && isset($_POST["offerta"]) && isset($_POST["salt"]) && is_operatore()) {

		$codice_gara = $_POST["codice_gara"];
		$codice_lotto = $_POST["codice_lotto"];

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								WHERE b_gare.codice = :codice ";
		$strsql .= "AND b_gare.annullata = 'N' AND b_modalita.online = 'S' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$accedi = false;

		if ($risultato->rowCount() > 0) {
			$bind = array();
			$bind[":codice"] = $codice_gara;
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$derivazione = "";
			$sql = "SELECT * FROM b_procedure WHERE codice = :codice_procedura";
			$ris = $pdo->bindAndExec($sql,array(":codice_procedura"=>$record_gara["procedura"]));
			if ($ris->rowCount()>0) {
				$rec_procedura = $ris->fetch(PDO::FETCH_ASSOC);
				$directory = $rec_procedura["directory"];
				$record["nome_procedura"] = $rec_procedura["nome"];
				$record["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
				if ($rec_procedura["mercato_elettronico"] == "S") $derivazione = "me";
				if ($rec_procedura["directory"] == "sda")  $derivazione = "sda";
				if ($rec_procedura["directory"] == "dialogo")  $dialogo = true;

			}

			$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice";
			$ris_inviti = $pdo->bindAndExec($strsql,$bind);
			if ($ris_inviti->rowCount()>0) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice AND r_inviti_gare.codice_utente = :codice_utente";
				$ris_invitato = $pdo->bindAndExec($strsql,$bind);
				if ($ris_invitato->rowCount()>0) $accedi = true;
			} else {
				if($record_gara["invito"] == "N" || !empty($derivazione)) {
					$accedi = true;
				}
			}
			if ($derivazione != "") {
				$sql_abilitato = "SELECT * FROM r_partecipanti_".$derivazione." WHERE codice_bando = :codice_derivazione AND ammesso = 'S' AND codice_utente = :codice_utente ";
				$ris_abilitato = $pdo->bindAndExec($sql_abilitato,array(":codice_derivazione"=>$record_gara["codice_derivazione"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_abilitato->rowCount() == 0) {
					$accedi = false;
				}
			}
		}
		if ($accedi) {
			$print_form = true;
			$record_gara["tipologie_gara"] = "";
			$bind = array();
			$bind[":tipologia"] = $record_gara["tipologia"];
			$sql = "SELECT tipologia FROM b_tipologie WHERE b_tipologie.codice = :tipologia";
			$ris_tipologie = $pdo->bindAndExec($sql,$bind);
			if ($ris_tipologie->rowCount()>0) {
				$rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC);
				$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
			}
			?>
			<h1><?= traduci('genera') ?> <?= traduci("offerta") ?> - ID <? echo $record_gara["id"] ?></h1>
			<h2><strong><? echo traduci(trim($record_gara["tipologie_gara"])) ?></strong> - <? echo $record_gara["oggetto"] ?></h2>
			<?
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
			if ($ris_lotti->rowCount() > 0) {
				$print_form =false;
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto ORDER BY codice";
				$bind[":codice_lotto"] = $codice_lotto;
				$ris_check_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				if ($ris_check_lotti->rowCount() > 0) {
						$lotto = $ris_check_lotti->fetch(PDO::FETCH_ASSOC);
						if ($record_gara["modalita_lotti"]==1) {
							$bind =array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND conferma = TRUE AND codice_utente = :codice_utente";
							$ris_partecipazioni = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipazioni->rowCount() > 0) {
								$bind = array();
								$bind[":lotto"] = $codice_lotto;
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_lotto = :lotto AND conferma = TRUE AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
								$ris_partecipante_lotto = $pdo->bindAndExec($sql,$bind);
								if ($ris_partecipante_lotto->rowCount() > 0) {
									$print_form = true;
								} else {
									?>
									<h2 style="color:#C00"><?= traduci("Impossibile partecipare a più lotti") ?></h2>
									<?
								}
							} else {
								$print_form = true;
							}
					} else {
						$print_form = true;
					}
				}
			} else {
				$codice_lotto = 0;
			}

			if ($print_form) {

				$submit = false;

				if (isset($lotto)) {
					$codice_lotto = $lotto["codice"];
					echo "<div class=\"box\"><h3>" . $lotto["oggetto"] . "</h3>";
					echo $lotto["descrizione"]."</div>";
				}

				if (strtotime($record_gara["data_scadenza"]) > time()) {
						$submit = true;
				} else if ($record_gara["fasi"] == 'S') {
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$bind[":lotto"] = $codice_lotto;
					$sql_fase = "SELECT r_partecipanti.* FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto
											WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_lotto = :lotto AND r_partecipanti.codice_gara = :codice_gara
											AND ammesso = 'S' AND escluso = 'N' AND b_2fase.data_inizio <= now() AND b_2fase.data_fine > now()";
					$ris_fase = $pdo->bindAndExec($sql_fase,$bind);
					if ($ris_fase->rowCount() > 0) {
						$submit = true;
						$seconda_fase = true;
					}
				}

				$filtro_mercato = "";
				if ($record_gara["mercato_elettronico"]=="S") $filtro_mercato = " AND mercato_elettronico = 'S' ";
				$filtro_fase = "";
				if ($record_gara["fasi"]=="S") {
					if (strtotime($record_gara["data_scadenza"]) > time()) {
						$filtro_fase = " AND 2fase = 'N' ";
					}
				}
				if (isset($dialogo) && $dialogo = true) $filtro_fase = " AND 2fase = 'S' ";

				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];

				$strsql = "SELECT b_criteri_buste.* FROM b_criteri_buste
									 WHERE codice_criterio = :codice_criterio " . $filtro_fase . $filtro_mercato ." AND ";
				if ($_POST["tipo"]=="economica") {
					$strsql .= " economica = ";
					$tipo = "economica";
				} else if ($_POST["tipo"]=="tecnica") {
					$strsql .= " tecnica = ";
					$tipo = "tecnica";
				}
				$strsql.= " 'S' AND eliminato = 'N' LIMIT 0,1";
				$ris_buste = $pdo->bindAndExec($strsql,$bind);
				if ($ris_buste->rowCount() > 0 && $submit && isset($tipo)) {
					$busta = $ris_buste->fetch(PDO::FETCH_ASSOC);
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_lotto"] = $codice_lotto;
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND codice_capogruppo = 0 ";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount() > 0) {
						$partecipante = $ris->fetch(PDO::FETCH_ASSOC);
						$codice_partecipante = $partecipante["codice"];

						$sql = "SELECT b_offerte_economiche.* FROM b_offerte_economiche WHERE
										codice_partecipante = :codice_partecipante AND tipo = :tipo ";
						$ris_old_offerta = $pdo->bindAndExec($sql,array(":tipo" => $tipo,":codice_partecipante" => $codice_partecipante));
						if ($ris_old_offerta->rowCount()>0) {
							while($rec_delete = $ris_old_offerta->fetch(PDO::FETCH_ASSOC)) {
								$sql_delete = "DELETE FROM b_dettaglio_offerte WHERE codice_offerta = :codice_offerta AND codice_partecipante = :codice_partecipante";
								$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_offerta" => $rec_delete["codice"],":codice_partecipante" => $codice_partecipante));
								$sql_delete = "DELETE FROM b_offerte_economiche WHERE codice = :codice_offerta AND codice_partecipante = :codice_partecipante";
								$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_offerta" => $rec_delete["codice"],":codice_partecipante" => $codice_partecipante));
							}
						}

						$sql = "SELECT b_buste.* FROM b_buste JOIN r_partecipanti ON b_buste.codice_partecipante = r_partecipanti.codice
										JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice WHERE ";
						if ($tipo=="economica") {
							$sql .= " b_criteri_buste.economica = 'S' ";
						} elseif ($tipo=="tecnica") {
							$sql .= " b_criteri_buste.tecnica = 'S' ";
						}
						$sql.= " AND r_partecipanti.codice = :codice_partecipante";
						if (isset($seconda_fase)) $sql .= " AND b_buste.nome_file LIKE '%seconda_fase'";
						$ris_old_busta = $pdo->bindAndExec($sql,array(":codice_partecipante" => $codice_partecipante));
						if ($ris_old_busta->rowCount()>0) {
							while($rec_delete = $ris_old_busta->fetch(PDO::FETCH_ASSOC)) {

								$fileURL = $config["doc_folder"] ."/" . $record_gara["codice"]."/".$codice_lotto."/".$rec_delete["nome_file"];
								$confirmURL = $config["doc_folder"] ."/" . $record_gara["codice"]."/".$codice_lotto."/".$codice_partecipante."_conferma.pdf";
								if (file_exists($fileURL)) unlink($fileURL);
								if (file_exists($fileURL.".tsr")) unlink($fileURL.".tsr");
								if (file_exists($confirmURL)) unlink($confirmURL);
								if (file_exists($confirmURL.".tsr")) unlink($confirmURL.".tsr");

								$strsql = "DELETE FROM b_buste WHERE codice_partecipante = :codice_partecipante AND codice = :codice_busta";
								$delete_buste = $pdo->bindAndExec($strsql,array(":codice_busta" => $rec_delete["codice"],":codice_partecipante" => $codice_partecipante));
							}
						}

						/* INIZIO REVOCA SE SOVRASCRIZIONE */
						if ($partecipante["conferma"] == 1) {
							$sql = "UPDATE r_partecipanti SET conferma = 0 WHERE codice = :codice_partecipante";
							$ris = $pdo->bindAndExec($sql,array(":codice_partecipante"=>$codice_partecipante));

							$oggetto = "Conferma di revoca della gara " . $record_gara["oggetto"];
							$corpo = "L'operatore economico " . $partecipante["partita_iva"] . " " . $partecipante["ragione_sociale"] . ",  ha revocato la partecipazione alla gara telematica:<br>";
							$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
							if (isset($lotto) != "") $corpo.= "Lotto: <strong>" . $lotto["oggetto"] . "</strong><br><br>";
							$corpo.= "Distinti Saluti<br><br>";

							$strsql = "SELECT pec FROM b_utenti WHERE codice = :codice_utente";
							$bind = array();
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount()>0) {
								$record = $risultato->fetch(PDO::FETCH_ASSOC);
								$indirizzi[] = $record["pec"];
							}

							$mailer = new Communicator();
							$mailer->oggetto = $oggetto;
							$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
							$mailer->codice_pec = -1;
							$mailer->comunicazione = true;
							$mailer->coda = false;
							$mailer->sezione = "gara";
							$mailer->codice_gara = $record_gara["codice"];
							$mailer->destinatari = $indirizzi;
							$esito = $mailer->send();

							$pec_conferma = getIndirizzoConferma($record_gara["codice_pec"]);

							$mailer = new Communicator();
							$mailer->oggetto = $oggetto;
							$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
							$mailer->codice_pec = -1;
							$mailer->destinatari = $pec_conferma;
							$mailer->type = 'comunicazione-gara';
							$mailer->sezione = "gara";
							$mailer->codice_gara = $record_gara["codice"];
							$esito = $mailer->send();

						}
							/* FINE REVOCA SE SOVRASCRIZIONE */
					} else if (!isset($seconda_fase)) {

						$sql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice WHERE b_operatori_economici.codice_utente = :codice_utente ";
						$ris_operatori_economici = $pdo->bindAndExec($sql,array(":codice_utente"=>$_SESSION["codice_utente"]));

						$partecipante = array();
						$operatore_economico = $ris_operatori_economici->fetch(PDO::FETCH_ASSOC);
						$partecipante["codice_gara"] = $record_gara["codice"];
						$partecipante["codice_lotto"] = $codice_lotto;
						$partecipante["codice_operatore"] = $operatore_economico["codice"];
						$partecipante["codice_utente"] = $_SESSION["codice_utente"];
						$partecipante["partita_iva"] = $operatore_economico["codice_fiscale_impresa"];
						$partecipante["ragione_sociale"] = $operatore_economico["ragione_sociale"];
						$partecipante["pec"] = $operatore_economico["pec"];
						$partecipante["identificativoEstero"] = $operatore_economico["identificativoEstero"];
						$partecipante["conferma"] = 0;
						$partecipante["ammesso"] = 'N';

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_partecipanti";
						$salva->operazione = "INSERT";
						$salva->oggetto = $partecipante;
						$codice_partecipante = $salva->save();
					}

					$msg = "";
					$offerta = array();
					$public = openssl_pkey_get_public(trim($record_gara["public_key"]));
					if (openssl_public_encrypt($_POST["salt"],$offerta["salt"],$public)) {
						$msg .= "<li>Criptazione effettuata con successo</li>";
						$offerta["codice_gara"] = $record_gara["codice"];
						$offerta["codice_lotto"] = $codice_lotto;
						$offerta["codice_partecipante"] = $codice_partecipante;
						$offerta["tipo"] = $tipo;
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_offerte_economiche";
						$salva->operazione = "INSERT";
						$salva->oggetto = $offerta;
						$codice_offerta = $salva->save();
						if ($codice_offerta!== false) {
							$errore_offerte = true;
					    $vocabolario["#tabella#"] = "";
							if ($record_gara["nuovaOfferta"] == "S") {
								include("save-offerta/new.php");
							} else {
								include("save-offerta/old.php");
							}
							if (!$errore_offerte) {
								$modello["corpo"] = "#tabella#";
								if ($tipo=="economica") {
									$sql_modello = "SELECT * FROM b_modelli_standard WHERE codice = 5";
								} else if ($tipo=="tecnica") {
									$sql_modello = "SELECT * FROM b_modelli_standard WHERE codice = 7";
								}
								$ris_modello = $pdo->query($sql_modello);
								if ($ris_modello->rowCount()>0) {
									$modello = $ris_modello->fetch(PDO::FETCH_ASSOC);
									$bind=array(":codice_modello"=>$modello["codice"],":codice_ente"=>$_SESSION["ente"]["codice"]);
									$sql = "SELECT * FROM b_modelli_enti WHERE attivo = 'S' AND codice_modello = :codice_modello AND codice_ente = :codice_ente";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) {
										$modello = $ris->fetch(PDO::FETCH_ASSOC);
									}
									if ($codice_lotto > 0) {
										$bind = array();
										$bind[":codice_lotto"] = $codice_lotto;
										$sql_lotto = "SELECT * FROM b_lotti WHERE codice = :codice_lotto";
										$ris_lotto = $pdo->bindAndExec($sql_lotto,$bind);
										if ($ris_lotto->rowCount()>0) {
											$lotto_descrittivo = $ris_lotto->fetch(PDO::FETCH_ASSOC);
											$record_gara["oggetto"] .= " - Lotto: " . $lotto_descrittivo["oggetto"];
											$record_gara["cig"] = $lotto_descrittivo["cig"];
											if ($record_gara["norma"] == "2023-36") {
												$record_gara["prezzoBase"] = $lotto_descrittivo["importo_base"] + $lotto_descrittivo["importo_oneri_no_ribasso"] + $lotto_descrittivo["importo_personale"]; // + $lotto_descrittivo["importo_oneri_ribasso"];
											} else {
												$record_gara["prezzoBase"] = $lotto_descrittivo["importo_base"] + $lotto_descrittivo["importo_oneri_no_ribasso"]; // + $lotto_descrittivo["importo_oneri_ribasso"] + $lotto_descrittivo["importo_personale"];
											}
										}
									}

									$record_gara["prezzoBase"] = "&euro; " . number_format($record_gara["prezzoBase"],2,",",".");

									$vocabolario["#ragione-sociale#"] = $partecipante["ragione_sociale"];

									$chiavi = array_keys($record_gara);
									foreach($chiavi as $chiave) {
										$vocabolario["#record_gara-".$chiave."#"] = $record_gara[$chiave];
									}

									$vocabolario["#elenco_operatori#"] = "";
									$bind =array(":codice_partecipante"=>$codice_partecipante);
									$sql_g = "SELECT * FROM r_partecipanti WHERE codice_capogruppo = :codice_partecipante";
									$ris_g = $pdo->bindAndExec($sql_g,$bind);
									if ($ris_g->rowCount()>0) {
										$vocabolario["#elenco_operatori#"] .= "<strong>Membri del raggruppamento</strong><br><br>
										<table><tr style=\"font-weight:bold\"><td>" . $partecipante["partita_iva"] . "</td><td>" . $partecipante["ragione_sociale"] . "</td><td>" . $partecipante["tipo"] . "</td></tr>";
										while($record_m=$ris_g->fetch(PDO::FETCH_ASSOC)) {
											$vocabolario["#elenco_operatori#"] .= "<tr><td>" . $record_m["partita_iva"] . "</td><td>" . $record_m["ragione_sociale"] . "</td><td>" . $record_m["tipo"] . "</td></tr>";
										}
										$vocabolario["#elenco_operatori#"] .= "</table>";
									}
								}
								$html = strtr($modello["corpo"],$vocabolario);
								$html = "<html>
		                      <head>
		                        <style>
		                          table, th, td {
		                            border: 1px solid grey;
		                          }
		                          th {
		                            text-align:center;
		                            background-color:#CCC;
		                          }
		                        </style>
		                      </head>
		                      <body>" . $html ."</body></html>";
							  	ini_set('memory_limit', '2048M');
            					ini_set('max_execution_time', 600);
								$tmp_path = $config["chunk_folder"]."/".$codice_offerta."_offerta.pdf";
								$options = new Options();
								$options->set('defaultFont', 'Helvetica');
								$options->setIsRemoteEnabled(true);
								$dompdf = new Dompdf($options);
								$dompdf->loadHtml($html);
								$dompdf->setPaper('A4', 'portrait');
								$dompdf->set_option('defaultFont', 'Helvetica');
								$dompdf->render();
								$content = $dompdf->output();
								file_put_contents($tmp_path,$content);
								$offerFile = file_get_contents($tmp_path);
								$contentForHash = new PdfToText($tmp_path);
					      $contentForHash =  $contentForHash->Text;
					      $contentForHash = preg_replace("/[^a-zA-Z0-9]/", '', $contentForHash);
								unlink($tmp_path);
								$cryptedContent = openssl_encrypt($offerFile,$config["crypt_alg"],$_POST["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
								if ($cryptedContent !== false) {
									$_SESSION["offerFile"][$codice_partecipante][$tipo] = $offerFile;
									$update["md5"] = hash("md5",$offerFile);
									$update["sha1"] = hash("sha1",$offerFile);
									$update["sha256"] = hash("sha256",$offerFile);
									$update["shaContent"] = hash("sha256",$contentForHash);
									$update["cryptedContent"] = $cryptedContent;
									$update["codice"] = $codice_offerta;
									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "b_offerte_economiche";
									$salva->operazione = "UPDATE";
									$salva->oggetto = $update;
									$codice_offerta = $salva->save();
									if ($codice_offerta !== false) {
										$msg .= "<li>Salvataggio offerta riuscito correttamente</li>";
										?>
										<ul class="success">
											<? echo $msg ?>
										</ul>
										<a href="download_offerta.php?codice_partecipante=<?=$codice_partecipante ?>&tipo=<?=$tipo?>" target="_blank" class="submit_big" style="background-color:#0C0">
											<?= traduci("Scarica il file di offerta") ?>
										</a>
										<div class="box" style="text-align:center">
											<strong><?= traduci('msg-download-sign-upload') ?></strong>
										</div>
										<a href="/gare/telematica2.0/submit.php?codice_busta=<?= $busta["codice"]?>&codice_gara=<?= $record_gara["codice"] ?>&codice_lotto=<?= $codice_lotto ?>" class="submit_big">
											<?= traduci("Carica documentazione") ?>
										</a>

										<?
									} else {

									}
								} else {
									$bind = array(":codice_partecipante"=>$offerta["codice_partecipante"]);
									$sql = "DELETE FROM b_dettaglio_offerte WHERE codice_partecipante = :codice_partecipante";
									$ris_delete = $pdo->bindAndExec($sql,$bind);
									$sql = "DELETE FROM b_offerte_economiche WHERE codice_partecipante = :codice_partecipante";
									$ris_delete = $pdo->bindAndExec($sql,$bind);
									?>
										<h3 class="ui-state-error"><?= traduci("Errore nell'esportazione PDF dell'offerta") ?></h3>
									<?
								}
							} else {
								$bind = array(":codice_partecipante"=>$offerta["codice_partecipante"]);
								$sql = "DELETE FROM b_dettaglio_offerte WHERE codice_partecipante = :codice_partecipante";
								$ris_delete = $pdo->bindAndExec($sql,$bind);
								$sql = "DELETE FROM b_offerte_economiche WHERE codice_partecipante = :codice_partecipante";
								$ris_delete = $pdo->bindAndExec($sql,$bind);
								?>
									<h3 class="ui-state-error"><?= traduci("Errore nel salvataggio del dettaglio offerta") ?></h3>
								<?
							}
						} else {
							?>
								<h3 class="ui-state-error"><?= traduci("Errore nel salvataggio dell'offerta") ?></h3>
							<?
						}
					} else {
						?>
							<h3 class="ui-state-error"><?= traduci("Errore nella criptazione dell'offerta") ?></h3>
						<?
					}
					?>
					<a class="submit_big" style="background-color:#444" href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=".$codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
					<?
				} else {
					if (!$submit) {
						echo "<h1>". traduci('impossibile accedere') . " - " . traduci('termini scaduti') . "</h1>";
					} else {
						echo "<h1>". traduci('impossibile accedere') . " - ERROR 3</h1>";
					}
				}
			} else {
				echo "<h1>". traduci('impossibile accedere') . " - ERROR 2</h1>";
			}
		} else {
			echo "<h1>". traduci('impossibile accedere') . " - ERROR 1</h1>";
		}
	} else {
		echo "<h1>". traduci('impossibile accedere') . " - ERROR 0</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
