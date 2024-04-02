<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$strsql = "SELECT * FROM b_conf_gestione_concorsi WHERE link = '/concorsi/chiarimenti/index.php'";
			$risultato = $pdo->query($strsql);
			if ($risultato->rowCount()>0) {
				$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
				$esito = check_permessi_concorso($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
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
		if (isset($_POST["operazione"])) {

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_risposte_concorsi";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST;
			$codice = $salva->save();
			if ($codice != false) {
				log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"RISPOSTA","Quesito - Codice: " . $_POST["codice_quesito"]);

				$bind = array();
				$bind[":codice"] = $_POST["codice_quesito"];
				$bind[":attivo"] = "N";
				if ($_POST["pubblica_all"]=="S") $bind[":attivo"] = "S";

				$strsql = "UPDATE b_quesiti_concorsi SET attivo = :attivo WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);

				$bind = array();
				$bind[":codice"] = $_POST["codice_gara"];
				$strsql= "SELECT b_concorsi.* FROM b_concorsi WHERE b_concorsi.codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					if ($record_gara["pubblica"] > 0 ) {

						$bind = array();
						$bind[":codice"] = $_POST["codice_quesito"];
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$bind[":codice_gara"] = $record_gara["codice"];
						$strsql = "SELECT * FROM b_quesiti_concorsi WHERE codice = :codice";
						$strsql .= " AND codice_ente = :codice_ente";
						$strsql .= " AND codice_gara = :codice_gara";

						$risultato = $pdo->bindAndExec($strsql,$bind);

						if ($risultato->rowCount() > 0) {
							$record_quesito = $risultato->fetch(PDO::FETCH_ASSOC);

							$oggetto = "Chiarimento sulla gara: " . $record_gara["oggetto"];

							$corpo = "E' stato pubblicato un chiarimento riguardante la gara:<br>";
							$corpo.= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/concorsi/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $record_gara["oggetto"] . "</strong></a><br><br>";
							$corpo.= "<h2>" . $_POST["quesito"] . "</h2>";
							$corpo.= $_POST["testo"];
							$corpo.= "<br><br>Distinti Saluti<br><br>";

							$mailer = new Communicator();
							$mailer->oggetto = $oggetto;
							$mailer->corpo = $corpo;
							$mailer->codice_pec = $record_gara["codice_pec"];
							$mailer->comunicazione = true;
							$mailer->codice_gara = $record_gara["codice"];
							$mailer->sezione = "concorsi";
							$mailer->coda = false;
							$mailer->destinatari = $record_quesito["utente_modifica"];
							$esito = $mailer->send();

						}
					}
				}
				if ($_POST["operazione"]=="UPDATE") {

					$href = "/concorsi/chiarimenti/index.php?codice=".$_POST["codice_gara"];
					?>
					alert('Modifica effettuata con successo');
					window.location.href = '<? echo $href ?>';
					<?
				} elseif ($_POST["operazione"]=="INSERT") {
					$href = "/concorsi/chiarimenti/index.php?codice=".$_POST["codice_gara"];
					?>
					alert('Inserimento effettuato con successo');
					window.location.href = '<? echo $href ?>';
					<?
				}
			} else {
				?>
				alert('Errore nel salvataggio. Riprovare');
				<?
			}
		}
	}



?>
