<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
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
			$bind[":codice_lotto"] = $_POST["codice_lotto"];
			$strsql = "DELETE FROM r_punteggi_gare WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$strsql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND ammesso = 'N'";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$array_esclusi = array();
			if ($risultato->rowCount()>0) {
				while($record=$risultato->fetch(PDO::FETCH_ASSOC)) {
					$array_esclusi[] = $record["codice"];
				}
			}

			$array_id = array();
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$strsql = "SELECT * FROM b_gare WHERE codice = :codice_gara";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			$aggiudicazione_multipla = false;
			$bind = array();
			$bind[":codice"] = $record_gara["procedura"];
			$sql_procedura = "SELECT * FROM b_procedure WHERE codice = :codice AND aggiudicazione_multipla = 'S'";
			$ris_procedura = $pdo->bindAndExec($sql_procedura,$bind);
			if ($ris_procedura->rowCount()>0) $aggiudicazione_multipla = true;
			$cig = $record_gara["cig"];
			if ($_POST["codice_lotto"] != 0) {
				$bind = array();
				$bind[":codice"] = $_POST["codice_lotto"];
				$strsql = "SELECT * FROM b_lotti WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				$record_lotto = $risultato->fetch(PDO::FETCH_ASSOC);
				$cig = $record_lotto["cig"];
			}
			$errore_save = false;
			foreach($_POST["partecipante"] as $partecipante) {

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "r_partecipanti";
				$salva->operazione = "UPDATE";
				$salva->oggetto = $partecipante;
				$codice = $salva->save();
				if ($codice != false) {
					$bind = array();
					$bind[":codice"] = $codice;
					$strsql = "SELECT * FROM r_partecipanti WHERE codice = :codice";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					$stato_partecipante = $risultato->fetch(PDO::FETCH_ASSOC);
					$oggetto = $record_gara["oggetto"] . " CIG: " . $cig;
					if (isset($record_lotto)) $oggetto .= " - Lotto: " . $record_lotto["oggetto"];
					$corpo = "";
					if ($stato_partecipante["ammesso"] == "N" && (!in_array($stato_partecipante["codice"],$array_esclusi))) {
						$oggetto .= "Esclusione dalla gara " . $record_gara["oggetto"] . " CIG: " . $cig;
						if (isset($record_lotto)) $oggetto .= " Lotto: " . $record_lotto["oggetto"];
						$corpo .= "Si informa la S.V. che &egrave; stata esclusa dalla partecipazione alla gara in oggetto.<br><br> Segue motivazione dell'esclusione:<br><br><strong>";
						$corpo .= $stato_partecipante["motivazione"];
						$corpo .= "</strong><br><br>";
					} else if ($stato_partecipante["ammesso"] == "S" && (in_array($stato_partecipante["codice"],$array_esclusi))) {
						$oggetto .= "Riammissione alla gara " . $record_gara["oggetto"] . " CIG: " . $cig;
						if (isset($record_lotto)) $oggetto .= " Lotto: " . $record_lotto["oggetto"];
						$corpo .= "Si informa la S.V. che &egrave; stata riammessa alla partecipazione alla gara in oggetto.<br><br>";
					}
					if ($corpo != "" && !empty($stato_partecipante["codice_utente"])) {
						if (isset($_POST["invia_esclusione"]) && $_POST["invia_esclusione"] == "S") {
							$mailer = new Communicator();
							$mailer->oggetto = $oggetto;
							$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
							$mailer->codice_pec = $record_gara["codice_pec"];
							$mailer->comunicazione = true;
							$mailer->coda = true;
							$mailer->sezione = "gara";
							$mailer->codice_gara = $record_gara["codice"];
							if (isset($record_lotto)) $mailer->codice_lotto = $record_lotto["codice"];
							$mailer->destinatari = $stato_partecipante["codice_utente"];
							$esito = $mailer->send();
						}
					}
					if ($stato_partecipante["ammesso"] == "S") {
						if (isset($_POST["invia_ammissione"]) && $_POST["invia_ammissione"] == "S") {
							if (empty($corpo)) {
								$oggetto = "Ammissione alla gara - " . $oggetto;
								$corpo .= "Si informa la S.V. che &egrave; stata ammessa alla partecipazione alla gara in oggetto.<br><br>";
								$mailer = new Communicator();
								$mailer->oggetto = $oggetto;
								$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
								$mailer->codice_pec = $record_gara["codice_pec"];
								$mailer->comunicazione = true;
								$mailer->coda = true;
								$mailer->sezione = "gara";
								$mailer->codice_gara = $record_gara["codice"];
								if (isset($record_lotto)) $mailer->codice_lotto = $record_lotto["codice"];
								$mailer->destinatari = $stato_partecipante["codice_utente"];
								$esito = $mailer->send();
							}
						}
					}
					if (isset($_POST["punteggio"][$partecipante["codice"]])) {
						foreach ($_POST["punteggio"][$partecipante["codice"]] as $punteggio) {

							$punteggio["codice_partecipante"] = $partecipante["codice"];
							$punteggio["codice_gara"] = $_POST["codice_gara"];
							$punteggio["codice_lotto"] = $_POST["codice_lotto"];

							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "r_punteggi_gare";
							$salva->operazione = "INSERT";
							$salva->oggetto = $punteggio;
							$codice_punteggio  = $salva->save();

						}
					}
				} else {
					$errore = true;
				}
			}
			if (!isset($errore) || (isset($errore) && !$errore)) {
				$bind = array();
				$bind[":codice_gara"] = $_POST["codice_gara"];

				$strsql = "SELECT directory FROM b_criteri JOIN b_gare ON b_criteri.codice = b_gare.criterio WHERE b_gare.codice = :codice_gara";
				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount()>0) {
					$criterio = $risultato->fetch(PDO::FETCH_ASSOC);
					$errore = false;
					$msg = "";
					if ($_POST["calcola_graduatoria"] == 'S') {
						include($criterio["directory"]."/calcolo.php");
						if (!$errore) {
							$bind = array();
							$bind[":codice_gara"] = $_POST["codice_gara"];
							$sql = "UPDATE b_gare SET stato = 4 WHERE codice = :codice_gara";
							$update_stato = $pdo->bindAndExec($sql,$bind);
							if (class_exists("syncERP")) {
								$syncERP = new syncERP();
								if (method_exists($syncERP,"sendUpdateRequest")) {
									$syncERP->sendUpdateRequest($_POST["codice_gara"],"provvisoria");
								}
							}
						} else {
							?>
								alert('<? echo str_replace("'","\'",trim($msg)); ?>');
							<?
						}
					}
					$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
					if ($msg!="") { ?>
						alert('<? echo str_replace("'","\'",trim($msg)); ?>');
					<? } else {	?>
						alert('Elaborazione effettuata con successo');
					<? }  ?>
						window.location.href = window.location.href;
					<?
						log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Modifica graduatoria provvisoria");
					} else {
					?>
						alert('Si è verificato un errore');
					<?
				}
			} else {
				?>
					alert('Si è verificato un errore durante il salvataggio. Riprovare');
				<?
			}
	}

?>
