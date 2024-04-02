<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/sopralluogo/index.php'";
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
		if ($edit && !$lock) {
		if (!empty($_POST["codice"])) {

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_sopralluoghi";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $_POST;
			$codice = $salva->save();
			if ($codice != false) {
				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"RISPOSTA","Richiesta sopralluogo - Codice: " . $_POST["codice"]);

				$bind = array();
				$bind[":codice"] = $_POST["codice_gara"];
				$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura, b_procedure.invito AS invito FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

					$bind = array();
					$bind[":codice"] = $_POST["codice"];
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$bind[":codice_gara"] = $record_gara["codice"];
					$strsql = "SELECT * FROM b_sopralluoghi WHERE codice = :codice
										 AND codice_ente = :codice_ente
										 AND codice_gara = :codice_gara";

					$risultato = $pdo->bindAndExec($strsql,$bind);

					if ($risultato->rowCount() > 0) {
						$record = $risultato->fetch(PDO::FETCH_ASSOC);

						$oggetto = "Risposta a richiesta sopralluogo sulla gara: " . $record_gara["oggetto"];
						$corpo = "<br><br><strong>Appuntamento: " . mysql2completedate($record["appuntamento"]) . "</strong><br><br>";
						$corpo .= $_POST["note_risposta"];
						$corpo.= "<br><br>Maggiori informazioni relative la procedura: <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $record_gara["oggetto"] . "</strong></a><br><br>";
						$corpo.= "<br><br>Distinti Saluti<br><br>";

						$mailer = new Communicator();
						$mailer->oggetto = $oggetto;
						$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
						$mailer->codice_pec = $record_gara["codice_pec"];
						$mailer->comunicazione = true;
						$mailer->coda = true;
						$mailer->sezione = "gara";
						$mailer->comunicazione_tecnica = false;
						$mailer->codice_gara = $record_gara["codice"];
						$mailer->destinatari = $record["codice_utente"];

						$esito = $mailer->send();
					}
				}
				$href = "/gare/sopralluogo/index.php?codice=".$_POST["codice_gara"];
				?>
				alert('Modifica effettuata con successo');
				window.location.href = '<? echo $href ?>';
				<?
			} else {
				?>
				alert('Errore nel salvataggio. Riprovare');
				<?
			}
		}
	}



?>
