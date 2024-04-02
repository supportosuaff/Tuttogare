<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice"],$_SESSION["codice_utente"]);
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

		$_POST["stato"] = 99;
		$_POST["annullata"] = "S";

		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_gare";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $_POST;
		$codice_gara = $salva->save();
		if ($codice_gara != false) {
			if (class_exists("syncERP")) {
        $syncERP = new syncERP();
        if (method_exists($syncERP,"sendUpdateRequest")) {
          $syncERP->sendUpdateRequest($_POST["codice"]);
        }
      }
			
			log_gare($_SESSION["ente"]["codice"],$_POST["codice"],"UPDATE","Annullamento gara");

			$bind = array();
			$bind[":codice"] = $_POST["codice"];

			$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura, b_procedure.invito AS invito FROM
								b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								WHERE b_gare.codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount()>0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

				$avviso = array();
				$avviso["data"] = date("d-m-Y");
				$avviso["titolo"] = "Annullamento procedura " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];
				$avviso["testo"] = "Si comunica che la procedura in oggetto &egrave; stata annullata";
				$avviso["testo"] = $avviso["testo"];
				$avviso["codice_gara"] = $record_gara["codice"];
				$avviso["codice_ente"] = $_SESSION["ente"]["codice"];

				$avviso["titolo"] = $avviso["titolo"];

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_avvisi";
				$salva->operazione = "INSERT";
				$salva->oggetto = $avviso;
				$codice = $salva->save();

				// $invio = false;
				// if ($record_gara["pubblica"] > 0 ) {

				// 	$oggetto = $avviso["titolo"];

				// 	$corpo = "Si informa la S.V. che &egrave; stata annullata la gara:<br>";
				// 	$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
				// 	$corpo.= "Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\">";
				// 	$corpo.= $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
				// 	$corpo.= "</a><br><br>";
				// 	$corpo.= "Distinti Saluti<br><br>";

				// 	$corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
				// 	$mailer = new Communicator();
				// 	$mailer->oggetto = $oggetto;
				// 	$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
				// 	$mailer->codice_pec = $record_gara["codice_pec"];
				// 	$mailer->comunicazione = true;
				// 	$mailer->coda = true;
				// 	$mailer->sezione = "gara";
				// 	$mailer->codice_gara = $record_gara["codice"];
				// 	$esito = $mailer->send();
				// }
			}
			$href = "/gare/pannello.php?codice=" . $_POST["codice"];
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			?>
				alert('Annullamento effettuato con successo');
				window.location.href = '<? echo $href ?>';
		<?
	} else {
		?>
		alert('Errore nel salvataggio. Riprovare');
		<?
		}
	} ?>
