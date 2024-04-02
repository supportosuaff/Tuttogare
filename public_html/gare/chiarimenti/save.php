<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/chiarimenti/index.php'";
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
		if (isset($_POST["operazione"])) {

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_risposte";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST;
			$codice = $salva->save();
			if ($codice != false) {
				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"RISPOSTA","Quesito - Codice: " . $_POST["codice_quesito"]);

				$bind = array();
				$bind[":codice"] = $_POST["codice_quesito"];
				$bind[":attivo"] = "N";
				if ($_POST["pubblica_all"]=="S") $bind[":attivo"] = "S";

				$strsql = "UPDATE b_quesiti SET attivo = :attivo WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);

				$bind = array();
				$bind[":codice"] = $_POST["codice_gara"];
				$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura, b_procedure.invito AS invito FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					if ($record_gara["pubblica"] > 0 ) {

						$bind = array();
						$bind[":codice"] = $_POST["codice_quesito"];
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$bind[":codice_gara"] = $record_gara["codice"];
						$strsql = "SELECT * FROM b_quesiti WHERE codice = :codice";
						$strsql .= " AND codice_ente = :codice_ente";
						$strsql .= " AND codice_gara = :codice_gara";

						$risultato = $pdo->bindAndExec($strsql,$bind);

						if ($risultato->rowCount() > 0) {
							$record_quesito = $risultato->fetch(PDO::FETCH_ASSOC);

							$oggetto = "Chiarimento sulla gara: " . $record_gara["oggetto"];

							$corpo = "E' stato pubblicato un chiarimento riguardante la gara:<br>";
							$corpo.= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $record_gara["oggetto"] . "</strong></a><br><br>";
							$corpo.= "<h2>" . $_POST["quesito"] . "</h2>";
							$corpo.= $_POST["testo"];
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
							$mailer->destinatari = $record_quesito["utente_modifica"];

							if ($_POST["pubblica_all"]=="S") {
								$sql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice";
								$ris = $pdo->bindAndExec($sql,array(":codice"=>$record_gara["codice"]));
								if ($ris->rowCount() > 0) {
									$mailer->destinatari = 0;
								}
							}

							$esito = $mailer->send();
						}
					}
				}
				if ($_POST["operazione"]=="UPDATE") {

					$href = "/gare/chiarimenti/index.php?codice=".$_POST["codice_gara"];
					?>
					alert('Modifica effettuata con successo');
					window.location.href = '<? echo $href ?>';
					<?
				} elseif ($_POST["operazione"]=="INSERT") {
					$href = "/gare/chiarimenti/index.php?codice=".$_POST["codice_gara"];
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
