<?
	ini_set('memory_limit', '-1');
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	include_once($root."/inc/p7m.class.php");
	include($root."/inc/pdftotext.phpclass");

	$public = true;
		if (is_operatore()) {
			if (isset($_POST["codice_bando"])) {
				$codice = $_POST["codice_bando"];
				$bind=array();
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
				$ris = $pdo->bindAndExec($strsql,$bind);
				$operatore = $ris->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":codice"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql  = "SELECT * FROM b_bandi_albo WHERE codice = :codice ";
				$strsql .= "AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = 0) ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
						$record_bando = $risultato->fetch(PDO::FETCH_ASSOC);
						if (!empty($record_bando["jsonQuestionario"])) {
							$moduloQuestionario = [];
							$moduloQuestionario["codice"] = $record_bando["codice"] * -1;
							$moduloQuestionario["codice_bando"] = $record_bando["codice"];
							$moduloQuestionario["titolo"] = "Domanda di iscrizione generata dal sistema";
							$moduloQuestionario["obbligatorio"] = "S";
						}
						$bind = array();
						$bind[":codice"] = $record_bando["codice"];
						$sql = "SELECT * FROM b_modulistica_albo WHERE codice_bando = :codice AND attivo = 'S' ORDER BY codice";
						$risultato = $pdo->bindAndExec($sql,$bind);
						$moduli = [];
						if ($risultato->rowCount()>0) {
							$moduli = $risultato->fetchAll(PDO::FETCH_ASSOC);
						}	
						if (isset($moduloQuestionario)) {
							array_unshift($moduli,$moduloQuestionario);
						}
						if (!empty($moduli)) {
							$bind_operatore = $bind;
							$bind_operatore[":codice_operatore"] = $operatore["codice"];
							$strsql = "SELECT * FROM r_partecipanti_albo WHERE codice_bando = :codice AND codice_operatore = :codice_operatore";
							$risultatoOE = $pdo->bindAndExec($strsql,$bind_operatore);
							if ($risultatoOE->rowCount()>0) {
								$record_partecipante = $risultatoOE->fetch(PDO::FETCH_ASSOC);
							}
							$errore_upload = false;
							foreach ($moduli AS $record_modulo) {
								$errore_file = false;
								$msg = "";
								if (isset($_POST["filechunk_".$record_modulo["codice"]]) && ($_POST["filechunk_".$record_modulo["codice"]] != "") && (strpos($_POST["filechunk_".$record_modulo["codice"]], "../")===false)) {
									$p7m = new P7Manager($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk_".$record_modulo["codice"]]);
									$md5_file = $p7m->getHash('md5');
									if ($md5_file == $_POST["md5_file_".$record_modulo["codice"]]) {
										$msg .= "<li>File integro</li>";
										$verifica =	false;
										$esito = $p7m->checkSignatures();
										if ($esito == "Verification successful") {
											$continua = true;
											if ($record_modulo["codice"] < 0) {
												$continua = false;
												if (!empty($record_partecipante["hashQuestionario"])) {
													$hashes = json_decode($record_partecipante["hashQuestionario"]);
													if (!empty($hashes)) {
														foreach($hashes AS $alg => $hash) {
															$checkContent = false;
															if ($alg === "shaContent") {
																$checkContent = true;
																$alg = "sha256";
															}
															if ($p7m->find($hash,$alg,$checkContent)) {
																$continua = true;
																break;
															} 
														}
													}
												}
											}
											if ($continua) {
												$msg .= "<li>Firma formalmente valida";
												$certificati = $p7m->extractSignatures();
												$msg .= "<ul class=\"firme\">";
												foreach ($certificati AS $esito) {
													$data = openssl_x509_parse($esito,false);
													$validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
													$validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
													$msg .=  "<li>";
													if (isset($data["subject"]["commonName"])) $msg .= "<h1>" . $data["subject"]["commonName"] . "</h1>";
													if (isset($data["subject"]["organizationName"])) $msg .= "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
													if (isset($data["subject"]["title"])) $msg .=  $data["subject"]["title"] . "<br>";
													if (isset($data["issuer"]["organizationName"])) $msg .=  "<br>Emesso da:<strong>" . $data["issuer"]["organizationName"] . "</strong>";
													$msg .=  "<br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
													$msg .=  "</li>";
												}
												$msg .= "</ul>";
												$msg .= "</li>";
												if (!is_dir($config["arch_folder"] . "/allegati_albo/" . $operatore["codice"] . "/")) mkdir($config["arch_folder"] . "/allegati_albo/" . $operatore["codice"],0770,true);
												$allegato = array();
												$allegato["codice_modulo"] = $record_modulo["codice"];
												$allegato["codice_operatore"] = $operatore["codice"];
												$path_file = $config["arch_folder"] ."/allegati_albo/" . $operatore["codice"];
												$copy = copiafile_chunck($_POST["filechunk_".$record_modulo["codice"]],$path_file."/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);
												if ($copy != false) {
												
													$allegato["nome_file"] = $copy["nome_file"];
													$allegato["riferimento"] = $copy["nome_fisico"];
													$codice_allegato = 0;
													$operazione = "INSERT";
													$bind = array();
													$bind[":codice_modulo"] = $allegato["codice_modulo"];
													$bind[":codice_operatore"] = $allegato["codice_operatore"];
													$sql = "SELECT * FROM b_allegati_albo WHERE ";
													$sql.= " codice_modulo = :codice_modulo ";
													$sql.= " AND codice_operatore = :codice_operatore";

													$ris = $pdo->bindAndExec($sql,$bind);
													if ($ris->rowCount()>0) {
														$codice_allegato = $ris->fetch(PDO::FETCH_ASSOC);
														$allegato["codice"] = $codice_allegato["codice"];
														$operazione = "UPDATE";
													}

													$salva = new salva();
													$salva->debug = false;
													$salva->codop = $_SESSION["codice_utente"];
													$salva->nome_tabella = "b_allegati_albo";
													$salva->operazione = $operazione;
													$salva->oggetto = $allegato;
													$codice_allegato = $salva->save();

													if ($codice_allegato != false) {
														$msg .= "<li>" . traduci("salvataggio riuscito con successo") . "</li>";
													}
												} else {
													$errore_file = true;
													$errore_upload = true;
													$msg = "<h3 class=\"ui-state-error\">" . traduci('errore-salvataggio'). "</h3>";
												}
											} else {
												$errore_file = true;
												$errore_upload = true;
												$msg = "<h3 class=\"ui-state-error\">".traduci("Impossibile trovare copia firmata digitalmente della domanda di iscrizione generata dal sistema") ."</h3>";
											}
										} else {
											$errore_file = true;
											$errore_upload = true;
											$msg = "<h3 class=\"ui-state-error\">".traduci("firma del file non valida") ."</h3>";
										}
									} else {
										$errore_file = true;
										$errore_upload = true;
										$msg = "<h3 class=\"ui-state-error\">" . traduci('errore-salvataggio'). " - ERROR 1</h3>";
									}
									echo "<div class=\"box\">";
									echo "<h2>" . $record_modulo["titolo"] . "</h2>";
									if (!$errore_file) echo "<ul class=\"success\">";
									echo $msg;
									if (!$errore_file) echo "</ul>";
									echo "</div>";
								}
							}
						}

						$errore_allegati = false;
						$bind = array();
						$bind[":codice"] = $record_bando["codice"];
						$sql = "SELECT * FROM b_modulistica_albo WHERE codice_bando = :codice AND obbligatorio = 'S' AND attivo = 'S' ORDER BY codice";
						$risultato = $pdo->bindAndExec($sql,$bind);
						if ($risultato->rowCount()>0) {
							while ($record_modulo = $risultato->fetch(PDO::FETCH_ASSOC)) {
								$bind = array();
								$bind[":codice_modulo"] = $record_modulo["codice"];
								$bind[":codice_operatore"] = $operatore["codice"];
								$sql = "SELECT * FROM b_allegati_albo WHERE ";
								$sql.= " codice_modulo = :codice_modulo ";
								$sql.= " AND codice_operatore = :codice_operatore";
								$ris = $pdo->bindAndExec($sql,$bind);
								if ($ris->rowCount()==0) $errore_allegati = true;
							}
						}

						if (!$errore_allegati) {
							if (isset($_POST["invia"]) && $_POST["invia"]=="S") {
								if (empty($record_partecipante)) {
									$partecipante = array();
									$partecipante["codice_bando"] = $record_bando["codice"];
									$partecipante["codice_operatore"] = $operatore["codice"];
									$partecipante["codice_utente"] = $_SESSION["codice_utente"];
									$operazione = "INSERT";
								} else {
									$operazione = "UPDATE";
									$partecipante = $record_partecipante;
								}
								$partecipante["timestamp_richiesta"] = date('Y-m-d H:i:s');
								$partecipante["timestamp_aggiornamento"] = date('Y-m-d H:i:s');
								$partecipante["ammesso"] = "N";
								$partecipante["conferma"] = "S";

								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $_SESSION["codice_utente"];
								$salva->nome_tabella = "r_partecipanti_albo";
								$salva->operazione = $operazione;
								$salva->oggetto = $partecipante;
								$codice_partecipante = $salva->save();

								if ($codice_partecipante!=false) {

									$oggetto = "Richiesta di ammissione : " . $record_bando["oggetto"];

									$corpo = "L'operatore economico " . $operatore["codice_fiscale_impresa"] . " " . $operatore["ragione_sociale"] . ", ". $operatore["indirizzo_legale"] . " " . $operatore["citta_legale"] . " (" . $operatore["provincia_legale"] . "),  ha richiesto l'ammissione ";
									if ($record_bando["manifestazione_interesse"] == "N") {
										$corpo .= "all'Elenco dei Fornitori:<br>";
									} else if ($record_bando["manifestazione_interesse"] == "S") {
										$corpo .= "all'Indagine di Mercato:<br>";
									}

									$corpo.= "<br><strong>" . $record_bando["oggetto"] . "</strong><br><br>";
									$corpo.= "Distinti Saluti<br><br>";

									$mailer = new Communicator();
									$mailer->oggetto = $oggetto;
									$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
									$mailer->codice_pec = $record_bando["codice_pec"];
									$mailer->comunicazione = true;
									$mailer->coda = false;
									$mailer->sezione = "albo";
									$mailer->codice_gara = $record_bando["codice"];
									$mailer->destinatari = $_SESSION["codice_utente"];
									$esito = $mailer->send();
									
									$pec_conferma = getIndirizzoConferma($record_bando["codice_pec"]);

									$mailer = new Communicator();
									$mailer->oggetto = $oggetto;
									$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
									$mailer->codice_pec = -1;
									$mailer->destinatari = $pec_conferma;
									$mailer->sezione = "albo";
									$mailer->codice_gara = $record_bando["codice"];
									$mailer->type = 'comunicazione-albo';
									$esito = $mailer->send();

									echo "<h1>".traduci("salvataggio riuscito con successo")."!</h1>";
									echo "<h2>" . traduci("La richiesta di abilitazione è stata inviata correttamente") . "</h2>";

								} else {
									$msg = "<h2 class=\"ui-state-error\">" . traduci('errore-salvataggio'). "</h2>";
								}
							} else {
								$bind = array();
								$bind[":codice_bando"] = $record_bando["codice"];
								$bind[":codice_operatore"] = $operatore["codice"];

								if (isset($record_partecipante)) {

									$partecipante = array();
									$partecipante["codice_bando"] = $record_bando["codice"];
									$partecipante["codice_operatore"] = $operatore["codice"];
									$partecipante["codice_utente"] = $_SESSION["codice_utente"];
									if ($record_partecipante["ammesso"]=="N") $partecipante["timestamp_richiesta"] = date('Y-m-d H:i:s');
									$partecipante["timestamp_aggiornamento"] = date('Y-m-d H:i:s');
									$partecipante["codice"] = $record_partecipante["codice"];
									$partecipante["valutato"] = "N";
									$partecipante["visto"] = "N";

									if ($record_partecipante["valutato"]=="S") {
										$oggetto = "Aggiornamento dati: " . $record_bando["oggetto"];

										$corpo = "L'operatore economico " . $operatore["codice_fiscale_impresa"] . " " . $operatore["ragione_sociale"] . ", ". $operatore["indirizzo_legale"] . " " . $operatore["citta_legale"] . " (" . $operatore["provincia_legale"] . "),  ha aggiornato i dati della sua istanza ";
										if ($record_bando["manifestazione_interesse"] == "N") {
											$corpo .= "all'Elenco dei Fornitori:<br>";
										} else if ($record_bando["manifestazione_interesse"] == "S") {
											$corpo .= "all'Indagine di Mercato:<br>";
										}
										$corpo.= "<br><strong>" . $record_bando["oggetto"] . "</strong><br><br>";
										$corpo.= "Distinti Saluti<br><br>";

										$mailer = new Communicator();
										$mailer->oggetto = $oggetto;
										$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
										$mailer->codice_pec = $record_bando["codice_pec"];
										$mailer->comunicazione = true;
										$mailer->coda = false;
										$mailer->sezione = "albo";
										$mailer->codice_gara = $record_bando["codice"];
										$mailer->destinatari = $_SESSION["codice_utente"];
										$esito = $mailer->send();

										$pec_conferma = getIndirizzoConferma($record_bando["codice_pec"]);

										$mailer = new Communicator();
										$mailer->oggetto = $oggetto;
										$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
										$mailer->codice_pec = -1;
										$mailer->destinatari = $pec_conferma;
										$mailer->codice_gara = $record_bando["codice"];
										$mailer->type = 'comunicazione-albo';
										$mailer->sezione = "albo";
										$esito = $mailer->send();

									}

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "r_partecipanti_albo";
									$salva->operazione = "UPDATE";
									$salva->oggetto = $partecipante;
									$codice_partecipante = $salva->save();

								}
								if ($msg == "") echo "<h1>".traduci("salvataggio riuscito con successo")."!</h1>";
							}
						} else {
							echo "<h2 class=\"ui-state-error\">" . traduci("Impossibile effettuare l'invio") . "</h2>";
							if ($errore_allegati) echo "<h3 class=\"ui-state-error\">File obbligatori mancanti</h3>";
						}
						?>
							<a class="submit_big" href="modulo.php?cod=<? echo $record_bando["codice"] ?>"><?= traduci("ritorna al pannello") ?></a>
						<?
					} else {
					echo "<h1>" . traduci('impossibile accedere') . " - 1</h1>";
					}
				} else {
					echo "<h1>" . traduci('impossibile accedere') . " - 2</h1>";
				}
			} else {
				echo "<h1>" . traduci('impossibile accedere') . " - 3</h1>";
			}
	include_once($root."/layout/bottom.php");

	?>
