<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$error = true;
	if (is_operatore()) {
			$bind = array();
			$bind[":codice"] = $_POST["codice"];
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$strsql = "SELECT r_partecipanti_dialogo.*, b_bandi_dialogo.oggetto, b_bandi_dialogo.codice_pec
								 FROM r_partecipanti_dialogo JOIN b_bandi_dialogo ON r_partecipanti_dialogo.codice_bando = b_bandi_dialogo.codice
								 WHERE r_partecipanti_dialogo.codice = :codice AND r_partecipanti_dialogo.codice_utente = :codice_utente";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount()>0) {
				$partecipazione = $risultato->fetch(PDO::FETCH_ASSOC);
				$codice_bando = $partecipazione["codice_bando"];
				$strsql = "DELETE FROM r_partecipanti_dialogo WHERE codice = :codice AND codice_utente = :codice_utente";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("r_partecipanti_dialogo","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
				$bind = array();
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
				$ris = $pdo->bindAndExec($strsql,$bind);
				$operatore = $ris->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$bind[":codice_operatore"] = $operatore["codice"];
				$bind[":codice_bando"] = $codice_bando;
				$strsql = "DELETE FROM b_allegati_dialogo WHERE
									codice_modulo IN (SELECT codice FROM b_modulistica_dialogo WHERE codice_bando = :codice_bando)
									AND codice_operatore = :codice_operatore AND utente_modifica = :codice_utente ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("b_allegati_dialogo","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);

				$oggetto = "Revoca istanza: " . $partecipazione["oggetto"];

				$corpo = "L'operatore economico " . $operatore["codice_fiscale_impresa"] . " " . $operatore["ragione_sociale"] . ",  ha revocato l'istanza al Dialogo Competitivo";

				$corpo.= "<br><strong>" . $partecipazione["oggetto"] . "</strong><br><br>";
				$corpo.= "Distinti Saluti<br><br>";

				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
				$mailer->codice_pec = $partecipazione["codice_pec"];
				$mailer->comunicazione = true;
				$mailer->coda = false;
				$mailer->sezione = "dialogo";
				$mailer->codice_gara = $partecipazione["codice_bando"];
				$mailer->destinatari = $_SESSION["codice_utente"];
				$esito = $mailer->send();

				$pec_conferma = getIndirizzoConferma($partecipazione["codice_pec"]);

				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
				$mailer->codice_pec = -1;
				$mailer->destinatari = $pec_conferma;
				$mailer->sezione = "dialogo";
				$mailer->codice_gara = $partecipazione["codice_bando"];
				$mailer->type = 'comunicazione-dialogo';
				$esito = $mailer->send();

				$error = false;
			?>
      alert('<?= traduci("Revoca effettuata con successo") ?>');
			window.location.reload();
      <?
		}
	}
	if ($error)  header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);

?>
