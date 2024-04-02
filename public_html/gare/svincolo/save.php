<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	include_once("../../../config.php");
	$edit = false;
	$lock = true;

	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if ($edit && !$lock) {
		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice_gara";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0) {
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$documentale = array();
			$documentale["codice"] = $_POST["codice"];
			$documentale["codice_gara"] = $_POST["codice_gara"];
			$documentale["codice_lotto"] = $_POST["codice_lotto"];
			$documentale["codice_ente"] = $_SESSION["ente"]["codice"];
			$documentale["tipo"] = "svincolo_fideiussione";
			$documentale["corpo"] = $_POST["corpo"];
			$documentale["bozza"] = $_POST["bozza"] === "N" ? "N" : "S";

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_documentale";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $documentale;
			$codice_elemento = $salva->save();

			if(! empty($_POST["bozza"]) && $_POST["bozza"] == "N") {
				$sql = "UPDATE b_gare SET stato = 8 WHERE codice = :codice_gara";
				$update_stato = $pdo->bindAndExec($sql,$bind);

				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"Invio","Svincolo fideiussione");

				$oggetto = "Svincolo fideiussione: " . $record_gara["oggetto"];
				$html= "<html><body>{$_POST["corpo"]}</body></html>";

				$allegato["online"] = 'N';
				$allegato["codice_gara"] = $_POST["codice_gara"];
				$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
				$allegato["nome_file"] = $allegato["codice_gara"] . " - Svincolo fideiussione.".time().".pdf";
				$allegato["titolo"] = "Svincolo fideiussione";

				$percorso = "{$config["arch_folder"]}/{$allegato["codice_gara"]}";
				if (!is_dir($percorso)) mkdir($percorso,0777,true);

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

				$codice_allegato = "";
				if (file_exists("{$percorso}/{$allegato["nome_file"]}")) {

					$allegato["riferimento"] = getRealName($percorso."/".$allegato["nome_file"]);
					rename("{$percorso}/{$allegato["nome_file"]}", "{$percorso}/{$allegato["riferimento"]}");

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_allegati";
					$salva->operazione = "INSERT";
					$salva->oggetto = $allegato;
					$codice_allegato = $salva->save();

					$bind = array();
					$bind[":codice_allegato"] = $codice_allegato;
					$bind[":codice_elemento"] = $codice_elemento;
					$sql = "UPDATE b_documentale SET codice_allegato = :codice_allegato WHERE codice = :codice_elemento";
					$ris = $pdo->bindAndExec($sql,$bind);
				}
				if (isset($_POST["invia"]) && $_POST["invia"] == "S") {
					$ris_rdo = $pdo->go("SELECT codice FROM b_rdo_ad WHERE codice_gara = :codice",[":codice"=>$record_gara["codice"]]);
					if ($ris_rdo->rowCount() > 0) {
						$strsql = "SELECT r_partecipanti.*, MAX(r_rdo_ad.timestamp_trasmissione) AS time_risposta
								FROM r_partecipanti
								LEFT JOIN r_rdo_ad ON r_partecipanti.codice = r_rdo_ad.codice_partecipante
								WHERE codice_gara = :codice_gara
								AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)
								AND r_rdo_ad.timestamp_trasmissione > 0 AND r_partecipanti.primo <> 'S'
								GROUP BY r_partecipanti.codice ORDER BY primo DESC, ragione_sociale ";
							$ris_partecipanti = $pdo->bindAndExec($strsql,[":codice_gara"=>$record_gara["codice"]]);
					} else {
						$bind = array();
						$bind[":codice"]=$record_gara["codice"];
						$bind[":codice_lotto"]=$_POST["codice_lotto"];
						$sql = "SELECT r_partecipanti.* FROM r_partecipanti WHERE r_partecipanti.codice_gara = :codice AND r_partecipanti.codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND r_partecipanti.primo <> 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";
						$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
					}
					if ($ris_partecipanti->rowCount() > 0) { 
						$tmp = $ris_partecipanti->fetchAll(PDO::FETCH_ASSOC);
						$destinatari = []; 
						foreach($tmp AS $destinatario) $destinatari[] = $destinatario["codice_utente"];
						$mailer = new Communicator();
						$mailer->oggetto = $oggetto;
						$mailer->corpo = "<h2>{$oggetto}</h2>{$documentale["corpo"]}";
						$mailer->codice_pec = $record_gara["codice_pec"];
						$mailer->comunicazione = true;
						$mailer->coda = false;
						$mailer->sezione = "gara";
						$mailer->codice_gara = $record_gara["codice"];
						$mailer->codice_lotto = $_POST["codice_lotto"];
						$mailer->destinatari = $destinatari;
						$esito = $mailer->send();
						if ($esito !== true) {
							echo "alert(\"" . $esito . "\");";
						}
					}
				}
			}
			$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
			?>
				alert('Salvataggio effettuato con successo');
				window.location.href = '<? echo $href ?>';
			<?
		}
	}
?>
