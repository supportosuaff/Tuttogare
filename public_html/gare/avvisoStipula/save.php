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
			$documentale["tipo"] = "avviso_appalto_aggiudicato";
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

				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"Invio","Avviso di appalto aggiudicato");

				$oggetto = "Avviso di appalto aggiudicato: " . $record_gara["oggetto"];
				$html= "<html><body>{$_POST["corpo"]}</body></html>";

				$allegato["online"] = 'S';
				$allegato["codice_gara"] = $_POST["codice_gara"];
				$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
				$allegato["nome_file"] = $allegato["codice_gara"] . " - Avviso di appalto aggiudicato.".time().".pdf";
				$allegato["titolo"] = "Avviso di appalto aggiudicato";

				$percorso = "{$config["pub_doc_folder"]}/allegati/{$allegato["codice_gara"]}";
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

				$avviso = array();
				$avviso["data"] = date("d-m-Y");
				$avviso["titolo"] = "Avviso di appalto aggiudicato - Procedura " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];
				$avviso["testo"] = "Si comunica che &egrave; stato pubblicato l'avviso di appalto aggiudicato per la gara in oggetto";
				$avviso["codice_gara"] = $record_gara["codice"];
				$avviso["codice_ente"] = $_SESSION["ente"]["codice"];
				$avviso["cod_allegati"] = $codice_allegato;

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_avvisi";
				$salva->operazione = "INSERT";
				$salva->oggetto = $avviso;
				$codice_avviso = $salva->save();
				if (isset($_POST["invia"]) && $_POST["invia"] == "S") {
					$mailer = new Communicator();
					$mailer->oggetto = $oggetto;
					$mailer->corpo = "<h2>{$oggetto}</h2>{$documentale["corpo"]}";
					$mailer->codice_pec = $record_gara["codice_pec"];
					$mailer->comunicazione = true;
					$mailer->coda = false;
					$mailer->sezione = "gara";
					$mailer->codice_gara = $record_gara["codice"];
					$mailer->codice_lotto = $_POST["codice_lotto"];
					$esito = $mailer->send();
					if ($esito !== true) {
						echo "alert(\"" . $esito . "\");";
					}
				}
			}
			if (class_exists("syncERP")) {
        $syncERP = new syncERP();
        if (method_exists($syncERP,"sendUpdateRequest")) {
          $syncERP->sendUpdateRequest($_POST["codice_gara"],"esito");
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
