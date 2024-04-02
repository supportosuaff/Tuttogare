<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (is_operatore()) {
		$bind = array();
		$bind[":codice"] = $_POST["codice"];
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql = "SELECT b_gare.codice AS codice_gara, b_gare.codice_pec, b_gare.oggetto, b_lotti.oggetto AS lotto, b_operatori_economici.*
						FROM b_operatori_economici JOIN r_partecipanti ON b_operatori_economici.codice = r_partecipanti.codice_operatore
						JOIN b_gare ON r_partecipanti.codice_gara = b_gare.codice
						LEFT JOIN b_lotti ON r_partecipanti.codice_lotto = b_lotti.codice
						WHERE b_gare.data_scadenza >= now() AND r_partecipanti.codice = :codice AND b_operatori_economici.codice_utente = :codice_utente";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) {
			$ope = $ris->fetch(PDO::FETCH_ASSOC);
			$bind = array();
			$bind[":codice"] = $_POST["codice"];
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$strsql = "SELECT codice FROM r_partecipanti WHERE codice = :codice AND codice_utente = :codice_utente";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount()>0) {

				$bind = array();
				$bind[":codice"] = $_POST["codice"];

				$strsql = "DELETE FROM r_partecipanti WHERE codice_capogruppo = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("r_partecipanti","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);


				$bind = array();
				$bind[":codice"] = $_POST["codice"];
				$sql = "SELECT * FROM b_buste WHERE codice_partecipante = :codice";
				$risultato = $pdo->bindAndExec($sql,$bind);
				if ($risultato->rowCount()>0) {
				while($rec = $risultato->fetch(PDO::FETCH_ASSOC)) {
						 $fileURL = $config["doc_folder"] ."/" . $rec["codice_gara"]."/". $rec["codice_lotto"] ."/".$rec["nome_file"];
						 $confirmURL = $config["doc_folder"] ."/" . $rec["codice_gara"]."/". $rec["codice_lotto"] ."/".$rec["codice_partecipante"]."_conferma.pdf";
					   if (file_exists($fileURL)) unlink($fileURL);
						 if (file_exists($fileURL.".tsr")) unlink($fileURL.".tsr");
						 if (file_exists($confirmURL)) unlink($confirmURL);
						 if (file_exists($confirmURL.".tsr")) unlink($confirmURL.".tsr");
					}
				}
				if (file_exists($config["doc_folder"].""))
				$bind = array();
				$bind[":codice"] = $_POST["codice"];
				$strsql = "DELETE FROM b_buste WHERE codice_partecipante = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("b_buste","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);

				$strsql = "DELETE FROM b_dettaglio_offerte WHERE codice_partecipante = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("b_dettaglio_offerte","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);

				$strsql = "DELETE FROM b_offerte_economiche WHERE codice_partecipante = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("b_offerte_economiche","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);


				$bind = array();
				$bind[":codice"] = $_POST["codice"];
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "DELETE FROM r_partecipanti WHERE codice = :codice AND codice_utente = :codice_utente";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("r_partecipanti","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);

				$oggetto = "Conferma di revoca alla gara " . $ope["oggetto"];
				$corpo = "L'operatore economico " . $ope["codice_fiscale_impresa"] . " " . $ope["ragione_sociale"] . ",  ha revocato la partecipazione alla gara telematica:<br>";
				$corpo.= "<br><strong>" . $ope["oggetto"] . "</strong><br><br>";
				if ($ope["lotto"] != "") $corpo.= "Lotto: <strong>" . $ope["lotto"] . "</strong><br><br>";
				$corpo.= "Distinti Saluti<br><br>";

				$comunicazione = array();
				$comunicazione["codice_ente"] = $_SESSION["ente"]["codice"];
				$comunicazione["codice_gara"] = $ope["codice_gara"];
				$comunicazione["oggetto"] = $oggetto;
				$comunicazione["corpo"] = $corpo;

				$strsql = "SELECT pec FROM b_utenti WHERE codice = :codice_utente";
				$risultato = $pdo->bindAndExec($strsql,array(":codice_utente"=>$_SESSION["codice_utente"]));
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
				$mailer->codice_gara = $ope["codice_gara"];
				$mailer->destinatari = $indirizzi;
				$esito = $mailer->send();

				$pec_conferma = getIndirizzoConferma($ope["codice_pec"]);

				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
				$mailer->codice_pec = -1;
				$mailer->destinatari = $pec_conferma;
				$mailer->type = 'comunicazione-gara';
				$mailer->sezione = "gara";
				$mailer->codice_gara = $ope["codice_gara"];
				$esito = $mailer->send();

			?>
      alert('<?= traduci("Revoca effettuata con successo") ?>');
			window.location.reload();
      <?
		} else {
			?>
			jalert('<?= traduci("errore nella revoca") ?>. <?= traduci("partecipante non riconosciuto") ?> - 1');
			<?
		}
	} else {
		?>
		jalert('<?= traduci("errore nella revoca") ?>. <?= traduci("partecipante non riconosciuto") ?> - 2');
		<?
	}
}
?>
