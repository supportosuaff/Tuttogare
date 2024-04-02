<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/dialogo/index.php'";
			$risultato = $pdo->query($strsql);
			if ($risultato->rowCount()>0) {
				$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
				$esito = check_permessi_gara($gestione["codice"],$_POST["codice"],$_SESSION["codice_utente"]);
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
		$bind[":codice_gara"] = $_POST["codice"];
		$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura, b_procedure.invito AS invito FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice_gara";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0) {
			$errore = false;
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_gare";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $_POST;
			$codice_gara = $salva->save();
			if ($codice_gara != false) {
				log_gare($_SESSION["ente"]["codice"],$_POST["codice"],"UPDATE","Chiusura dialogo");
				$oggetto = "Chiusura dialogo competitivo: " . $record_gara["oggetto"];

				$corpo = "Si comunica l'avvenuta chiusura del dialogo competitivo: " . $record_gara["oggetto"] . "<br><br>";
				$corpo .= "La S.V. è invitata a presentare un'offerta entro i seguenti termini <br><br><table>";
				$corpo .= "<tr><td class=\"etichetta\"><strong>Termine accesso agli atti</strong></td><td>" . $_POST["data_accesso"] . "</td></tr>";
				$corpo .= "<tr><td class=\"etichetta\"><strong>Scadenza presentazione offerte</strong></td><td>" . $_POST["data_scadenza"] . "</td></tr>";
				$corpo .= "<tr><td class=\"etichetta\"><strong>Apertura delle offerte</strong></td><td>" . $_POST["data_apertura"] . "</td></tr>";
				$corpo .= "</table><br><br>";

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

				?>
				alert('Modifica effettuata con successo');
				window.location.href = '/gare/pannello.php?codice=<?= $record_gara["codice"] ?>';
				<?
			} else {
			?>
			alert('Errore nel salvataggio. Si prega di riprovare');
			<?
			}
		} else {
			?>
			alert('Errore nel salvataggio. Si prega di riprovare');
			<?
		}
	}



?>
