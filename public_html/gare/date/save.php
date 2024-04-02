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
		if ($edit && !$lock)
	{
		$bind = array();
		$bind[":codice"] = $_POST["codice"];
		$sql_old = "SELECT data_accesso, data_scadenza, data_apertura FROM b_gare WHERE codice = :codice";
		$ris_old = $pdo->bindAndExec($sql_old,$bind);
		$string_date='';
		$old_date = $ris_old->fetch(PDO::FETCH_ASSOC);
		$string_date.="'".$old_date["data_accesso"].";".$old_date["data_scadenza"].";".$old_date["data_apertura"]."'";

		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_gare";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $_POST;
		$codice_gara = $salva->save();

		log_gare($_SESSION["ente"]["codice"],$_POST["codice"],"UPDATE","Scadenze gara");

		$bind = array();
		$bind[":codice"] = $_POST["codice"];
		$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura, b_procedure.invito AS invito FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {

			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			// $inviti = $pdo->go("SELECT * FROM r_inviti_gare WHERE codice_gara = :codice",[":codice"=>$record_gara["codice"]]);
			// $inviti = $inviti->rowCount();

			$avviso = array();
			$avviso["data"] = date("d-m-Y");
			$avviso["titolo"] = "Modifica date procedura " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];
			$avviso["testo"] = "Si comunica l'avvenuta modifica delle date relative la procedura in oggetto<br><br>";
			$avviso["testo"] .= "A seguire la tabella riepilogativa delle date aggiornate<br><br><table>";
			$avviso["testo"] .= "<tr><td class=\"etichetta\"><strong>Termine richieste chiarimenti</strong></td><td>" . $_POST["data_accesso"] . "</td></tr>";
			$avviso["testo"] .= "<tr><td class=\"etichetta\"><strong>Scadenza presentazione offerte</strong></td><td>" . $_POST["data_scadenza"] . "</td></tr>";
			$avviso["testo"] .= "<tr><td class=\"etichetta\"><strong>Apertura delle offerte</strong></td><td>" . $_POST["data_apertura"] . "</td></tr>";
			$avviso["testo"] .= "</table><br><br>";

			$avviso["codice_gara"] = $record_gara["codice"];
			$avviso["codice_ente"] = $_SESSION["ente"]["codice"];
			
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_avvisi";
			$salva->operazione = "INSERT";
			$salva->oggetto = $avviso;
			$codice = $salva->save();


			if ($record_gara["pubblica"] > 0) {

				$oggetto = $avviso["titolo"];

				$corpo = $avviso["testo"];

				$corpo.= "Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\">";
				$corpo.= $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
				$corpo.= "</a><br><br>";
				$corpo.= "Distinti Saluti<br><br>";

				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
				$mailer->codice_pec = $record_gara["codice_pec"];
				$mailer->comunicazione = true;
				$mailer->coda = true;
				$mailer->sezione = "gara";
				$mailer->codice_gara = $record_gara["codice"];
				$esito = $mailer->send();

				$bind = array();
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$bind[":codice_gara"] = $record_gara["codice"];
				$bind[":note"] = $string_date;
				$strsql = "INSERT INTO b_guue (codice_ente, codice_gara, number , dataRichiesta, note) VALUES (:codice_ente,:codice_gara, 14, NOW(),:note)";
				$risultato_guue = $pdo->bindAndExec($strsql,$bind);
			}
		}
		$href = "/gare/pannello.php?codice=" . $_POST["codice"];
		$href = str_replace('"',"",$href);
		$href = str_replace(' ',"-",$href);
		if (class_exists("syncERP")) {
			$syncERP = new syncERP();
			if (method_exists($syncERP,"sendUpdateRequest")) {
				$syncERP->sendUpdateRequest($record_gara["codice"]);
			}
		}
		
		?>
			alert('Modifica effettuato con successo');
		        window.location.href = '<? echo $href ?>';
	    	<?
	}



?>
