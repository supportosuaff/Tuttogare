<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && !empty($_POST["codice_gara"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/integrazioni/index.php'";
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
		if ($edit && !$lock)
	 {
		if (isset($_POST["operazione"])) {
			$errore = false;
			$_POST["codice_ente"] = $_SESSION["ente"]["codice"];
			if ($_POST["operazione"]=="UPDATE") {
				$bind = array(":codice"=>$_POST["codice"]);
				$sql = "SELECT * FROM b_integrazioni WHERE codice = :codice AND data_apertura > 0";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount()>0 && $_POST["data_apertura"] == "") $errore = true;
			}
			if (!$errore) {
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_integrazioni";
						$salva->operazione = $_POST["operazione"];
						$salva->oggetto = $_POST;
						$codice_integrazione = $salva->save();
						if ($codice_integrazione != false) {
							log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],$_POST["operazione"],"Richiesta integrazioni");
							$bind = array();
							$bind[":codice_gara"] = $_POST["codice_gara"];
							$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura, b_procedure.invito AS invito FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice_gara";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount()>0) {
								$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
								$invio = false;
								if (isset($_POST["codici_partecipante"]) && $_POST["codici_partecipante"] != "") {
									$invio = true;
									$codici_partecipante = explode(",",$_POST["codici_partecipante"]);
								}
								// invio comunicazione;

								if ($invio) {
									$oggetto = "Richiesta integrazioni: " . $record_gara["oggetto"];

									$corpo = "E' stata richiesta un'integrazione riguardante la gara:<br>";
									$corpo.= $_POST["richiesta"];
									$corpo.= "<br><br><strong>Termini presentazione:</strong> " . $_POST["data_scadenza"] . "<br><br>";
									$corpo.= "<br><br><strong>Si specifica che la risposta alla presente dovr&agrave; pervenire esclusivamente a mezzo piattaforma.</strong>";
									$corpo.= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $record_gara["oggetto"] . "</strong></a><br><br>";
									$corpo.= "<br><br>Distinti Saluti<br><br>";

									$corpo_allegati = "";
									$cod_allegati = "";
									if (isset($_POST["cod_allegati"]) && $_POST["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$_POST["cod_allegati"])) {
										$cod_allegati = $_POST["cod_allegati"];
										$allegati = explode(";",$_POST["cod_allegati"]);
										$str_allegati = ltrim(implode(",",$allegati),",");
										$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ") AND online = 'S'";
										$ris_allegati = $pdo->query($sql);
										$corpo_allegati = "<strong>Allegati</strong><br><table width=\"100%\">";
					          if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
											$i = 0;
					          	while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
												$class= "even";
												$i++;
												if ($i%2!=0) $class = "odd";
											 	$corpo_allegati  .= "<tr class=\"". $class . "\">";
											 	$corpo_allegati  .= "<td width=\"10\"><img src=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/img/" . substr($allegato["nome_file"],-3) . ".png\" alt=\"File " . substr($allegato["nome_file"],0,-3) . "\" style=\"vertical-align:middle\"></td>";
											 	$corpo_allegati  .= "<td><strong><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/documenti/allegati/".$allegato["codice_gara"]. "/" . $allegato["nome_file"] . "\" target=\"_blank\">" . $allegato["titolo"] . "</a></strong></td>";
											 	$corpo_allegati  .= "</tr>";
											}
										}
					          $corpo_allegati .= "</table>";
									}
									$indirizzi = array();
									foreach($codici_partecipante as $codice) {
										if ($codice != "") {
											$bind = array();
											$bind[":codice"] = $codice;
											$strsql = "SELECT r_partecipanti.*, b_utenti.pec AS pec_registrazione FROM r_partecipanti
																LEFT JOIN b_utenti ON r_partecipanti.codice_utente = b_utenti.codice
																WHERE r_partecipanti.codice = :codice";
											$risultato = $pdo->bindAndExec($strsql,$bind);
											if ($risultato->rowCount()>0) {
												$record = $risultato->fetch(PDO::FETCH_ASSOC);
												$indirizzi[] = ! empty($record["codice_utente"]) ? $record["codice_utente"] : $record["pec"];

												$r_integrazione = array();
												$r_integrazione["codice_integrazione"] = $codice_integrazione;
												$r_integrazione["codice_ente"] = $_SESSION["ente"]["codice"];
												$r_integrazione["codice_partecipante"] = $record["codice"];
												$r_integrazione["codice_utente"] = $record["codice_utente"];

												$salva = new salva();
												$salva->debug = false;
												$salva->codop = $_SESSION["codice_utente"];
												$salva->nome_tabella = "r_integrazioni";
												$salva->operazione = "INSERT";
												$salva->oggetto = $r_integrazione;
												$r_comunicazioni = $salva->save();
											}
										}
									}
									$mailer = new Communicator();
									$mailer->oggetto = $oggetto;
									$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo.$corpo_allegati;
									$mailer->codice_pec = $record_gara["codice_pec"];
									$mailer->comunicazione = true;
									$mailer->coda = true;
									$mailer->sezione = "gara";
									$mailer->codice_gara = $record_gara["codice"];
									$mailer->cod_allegati = $cod_allegati;
									$mailer->destinatari = $indirizzi;
									$esito = $mailer->send();
								}
							}

							if ($_POST["operazione"]=="UPDATE") {

								$href = "/gare/integrazioni/index.php?codice=".$_POST["codice_gara"];
								?>
								alert('Modifica effettuata con successo');
								window.location.href = '<? echo $href ?>';
								<?
							} elseif ($_POST["operazione"]=="INSERT") {
								$href = "/gare/integrazioni/index.php?codice=".$_POST["codice_gara"];
								?>
								alert('Inserimento effettuato con successo');
								window.location.href = '<? echo $href ?>';
								<?
							}
			} else {
				?>
				alert('Errore nel salvataggio. Si prega di riprovare');
				<?
			}
		} else {
			?>
			alert('Errore nel salvataggio. La data di apertura non può essere vuota');
			<?
		}
		}
	}



?>
