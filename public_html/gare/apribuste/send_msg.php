<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase("","/gare/apribuste/edit.php");
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_GET["codice_gara"],$_SESSION["codice_utente"]);
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
		$sql = "SELECT * FROM b_gare WHERE codice = :codice_gara";
		$ris = $pdo->bindAndExec($sql,array(":codice_gara"=>$_GET["codice_gara"]));
		if ($ris->rowCount() > 0) {
			$rec = $ris->fetch(PDO::FETCH_ASSOC);
			$mailer = new Communicator();
			$mailer->oggetto = "Apertura seduta pubblica - " . $rec["oggetto"];
			$mailer->corpo = "<h2>" . $mailer->oggetto . "</h2> Si comunica l'avvenuta apertura della seduta pubblica per la gara in oggetto.<br><br>Maggiori informazioni al link: ";
			$mailer->corpo .= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $rec["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $rec["oggetto"] . "</strong></a><br><br>";
			$mailer->codice_pec = $rec["codice_pec"];
			$mailer->comunicazione = true;
			$mailer->coda = true;
			$mailer->sezione = "gara";
			$mailer->codice_gara = $rec["codice"];
			$mailer->codice_lotto = $_GET["codice_lotto"];
			$esito = $mailer->send();
			if ($esito == true) { 
				?>
				alert("Invio effettuato con successo");
				<?
			} else {
				?>
				alert("Errore nell'invio");
				<?
			}
		}
	}

?>
