<?
	ini_set('memory_limit', '-1');
	include_once("../../config.php");
	include_once($root."/inc/p7m.class.php");
	include_once($root."/layout/top.php");
	$public = true;
		if (is_operatore()) {
			$msg ="";
			if (isset($_POST["codice_bando"])) {
				$codice = $_POST["codice_bando"];
				$bind = array();
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
				$ris = $pdo->bindAndExec($strsql,$bind);
				if ($ris->rowCount() > 0) {
					$operatore = $ris->fetch(PDO::FETCH_ASSOC);
					$bind = array();
					$bind[":codice"] = $codice;
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql  = "SELECT * FROM b_bandi_sda WHERE codice = :codice ";
					$strsql .= "AND annullata = 'N' AND data_scadenza > now() ";
					$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
					$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
					$risultato = $pdo->bindAndExec($strsql,$bind);

					if ($risultato->rowCount() > 0) {

							$errore_cpv = false;

							$record_bando = $risultato->fetch(PDO::FETCH_ASSOC);

							$bind = array();
							$bind[":codice_bando"] = $record_bando["codice"];
							$bind[":codice_operatore"] = $operatore["codice"];

							$strsql = "DELETE FROM r_cpv_operatori_sda WHERE codice_bando = :codice_bando AND codice_operatore = :codice_operatore";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							$array_cpv = explode(",",$_POST["cpv"]);
							foreach ($array_cpv as $cpv) {
								if ($cpv != "") {
									$categoria = array();
									$categoria["codice"] = $cpv;
									$categoria["codice_bando"] = $record_bando["codice"];
									$categoria["codice_operatore"] = $operatore["codice"];
									$categoria["codice_utente"] = $_SESSION["codice_utente"];

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "r_cpv_operatori_sda";
									$salva->operazione = "INSERT";
									$salva->oggetto = $categoria;
									$codice_relazione = $salva->save();
								}
							}

							$bind = array();
							$bind[":codice_bando"] = $record_bando["codice"];
							$bind[":codice_operatore"] = $operatore["codice"];

							$strsql= "SELECT * FROM r_cpv_operatori_sda WHERE codice_bando = :codice_bando AND codice_operatore = :codice_operatore";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount()==0)	$errore_cpv = true;

							$bind = array();
							$bind[":codice_bando"] = $record_bando["codice"];

							$sql = "SELECT * FROM b_modulistica_sda WHERE codice_bando = :codice_bando AND attivo = 'S' ORDER BY codice";
							$risultato = $pdo->bindAndExec($sql,$bind);
							if ($risultato->rowCount()>0) {
								$errore_upload = false;
								while ($record_modulo = $risultato->fetch(PDO::FETCH_ASSOC)) {
									$errore_file = false;
									$msg = "";
									if (isset($_POST["filechunk_".$record_modulo["codice"]]) && ($_POST["filechunk_".$record_modulo["codice"]] != "")) {

										$p7m = new P7Manager($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk_".$record_modulo["codice"]]);
										$md5_file = $p7m->getHash('md5');
										if ($md5_file == $_POST["md5_file_".$record_modulo["codice"]]) {
											$msg .= "<li>File integro</li>";
											$verifica =	false;
											$esito = $p7m->checkSignatures();
											if ($esito == "Verification successful") {
												$msg .= "<li>Firma formalmente valida";
												$certificati = $p7m->extractSignatures();
												$msg .= "<ul class=\"firme\">";
									      foreach ($certificati AS $certificato) {
									        $data = openssl_x509_parse($certificato,false);
									        $validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
									        $validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
									        $msg .=  "<li>";
									        if (isset($data["subject"]["commonName"])) $msg .= "<h1>" . $data["subject"]["commonName"] . "</h1>";
									        if (isset($data["subject"]["title"])) $msg .=  $data["subject"]["title"] . "<br>";
									        if (isset($data["issuer"]["organizationName"])) $msg .=  "<br>Emesso da:<strong>" . $data["issuer"]["organizationName"] . "</strong>";
									        $msg .=  "<br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
									        $msg .=  "</li>";
									      }
												$msg .= "</ul>";
												$msg .= "</li>";

												$allegato = array();
												if (!is_dir($config["arch_folder"] . "/allegati_sda/" . $operatore["codice"] . "/")) mkdir($config["arch_folder"] . "/allegati_sda/" . $operatore["codice"],0770,true);
												$path_file = $config["arch_folder"] ."/allegati_sda/" . $operatore["codice"];
												$copy = copiafile_chunck($_POST["filechunk_".$record_modulo["codice"]],$path_file."/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);
												if ($copy != false) {
													$allegato["nome_file"] = $copy["nome_file"];
													$allegato["riferimento"] = $copy["nome_fisico"];
													$allegato["codice_modulo"] = $record_modulo["codice"];
													$allegato["codice_operatore"] = $operatore["codice"];

													$codice_allegato = 0;
													$operazione = "INSERT";

													$bind = array(":codice_modulo"=>$allegato["codice_modulo"],":codice_operatore"=>$allegato["codice_operatore"]);

													$sql = "SELECT * FROM b_allegati_sda WHERE ";
													$sql.= " codice_modulo = :codice_modulo";
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
													$salva->nome_tabella = "b_allegati_sda";
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
							$bind[":codice_bando"] = $record_bando["codice"];
							$sql = "SELECT * FROM b_modulistica_sda WHERE obbligatorio = 'S' AND attivo = 'S' AND codice_bando = :codice_bando ORDER BY codice";
							$risultato = $pdo->bindAndExec($sql,$bind);
						if ($risultato->rowCount()>0) {
								while ($record_modulo = $risultato->fetch(PDO::FETCH_ASSOC)) {

									$bind = array(":codice_modulo"=>$record_modulo["codice"],":codice_operatore"=>$operatore["codice"]);
									$sql = "SELECT * FROM b_allegati_sda WHERE ";
									$sql.= " codice_modulo = :codice_modulo";
									$sql.= " AND codice_operatore = :codice_operatore";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()==0) $errore_allegati = true;
								}
							}

							if (isset($_POST["invia"]) && $_POST["invia"]=="S") {
								if (!$errore_cpv && !$errore_allegati) {
									$partecipante = array();
									$partecipante["codice_bando"] = $record_bando["codice"];
									$partecipante["codice_operatore"] = $operatore["codice"];
									$partecipante["codice_utente"] = $_SESSION["codice_utente"];
									$partecipante["timestamp_aggiornamento"] = date('Y-m-d H:i:s');
									$partecipante["ammesso"] = "N";
									$partecipante["conferma"] = "S";

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "r_partecipanti_sda";
									$salva->operazione = "INSERT";
									$salva->oggetto = $partecipante;
									$codice_partecipante = $salva->save();

									if ($codice_partecipante!=false) {
										$oggetto = "Richiesta di abilitazione: " . $record_bando["oggetto"];
										$corpo = "L'operatore economico " . $operatore["codice_fiscale_impresa"] . " " . $operatore["ragione_sociale"] . ",  ha richiesto l'abilitazione al sistema dinamico d'acquisizione:<br>";
										$corpo.= "<br><strong>" . $record_bando["oggetto"] . "</strong><br><br>";
										$corpo.= "Distinti Saluti<br><br>";

										$bind=array();
										$bind[":codice_utente"] =  $_SESSION["codice_utente"];
										$strsql = "SELECT pec FROM b_utenti WHERE codice = :codice_utente";
										$risultato = $pdo->bindAndExec($strsql,$bind);
										$indirizzi = array();
										if ($risultato->rowCount()>0) {
											$record = $risultato->fetch(PDO::FETCH_ASSOC);
											$indirizzi[] = $record["pec"];
										}

										$mailer = new Communicator();
										$mailer->oggetto = $oggetto;
										$mailer->corpo = "<h2>" . $oggetto . "</h2>" .$corpo;
										$mailer->codice_pec = $record_bando["codice_pec"];
										$mailer->comunicazione = true;
										$mailer->coda = false;
										$mailer->sezione = "sda";
										$mailer->codice_gara = $record_bando["codice"];
										$mailer->destinatari = $_SESSION["codice_utente"];
										$esito = $mailer->send();

										$pec_conferma = getIndirizzoConferma($record_bando["codice_pec"]);

										$mailer = new Communicator();
										$mailer->oggetto = $oggetto;
										$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
										$mailer->codice_pec = -1;
										$mailer->destinatari = $pec_conferma;
										$mailer->sezione = "sda";
										$mailer->codice_gara = $record_bando["codice"];
										$mailer->type = 'comunicazione-sda';
										$esito = $mailer->send();

										echo "<h1>".traduci("salvataggio riuscito con successo")."!</h1>";
										echo "<h2>" . traduci("La richiesta di abilitazione è stata inviata correttamente") . "</h2>";

									} else {
										$msg = "<h2 class=\"ui-state-error\">" . traduci('errore-salvataggio'). "</h2>";
									}
								} else {
									echo "<h2 class=\"ui-state-error\">" . traduci("Impossibile effettuare l'invio") . "</h2>";
									if ($errore_allegati) echo "<h3 class=\"ui-state-error\">File obbligatori mancanti</h3>";
									if ($errore_cpv) echo "<h3 class=\"ui-state-error\">Nessuna categoria selezionata</h3>";
								}
							} else {
								$bind = array();
								$bind[":codice_bando"] = $record_bando["codice"];
								$bind[":codice_operatore"] = $operatore["codice"];
								$strsql = "SELECT * FROM r_partecipanti_sda WHERE codice_bando = :codice_bando AND codice_operatore = :codice_operatore";
								$risultato = $pdo->bindAndExec($strsql,$bind);
								if ($risultato->rowCount()>0) {

									$record_partecipante = $risultato->fetch(PDO::FETCH_ASSOC);

									$partecipante = array();
									$partecipante["codice_bando"] = $record_bando["codice"];
									$partecipante["codice_operatore"] = $operatore["codice"];
									$partecipante["codice_utente"] = $_SESSION["codice_utente"];
									if ($record_partecipante["ammesso"]=="N") $partecipante["timestamp_richiesta"] = date('Y-m-d H:i:s');
									$partecipante["timestamp_aggiornamento"] = date('Y-m-d H:i:s');
									$partecipante["codice"] = $record_partecipante["codice"];
									$partecipante["valutato"] = "N";
									$partecipante["visto"] = "N";

									if ($record_partecipante["ammesso"]=="S" && $record_partecipante["valutato"]=="S") {

										$oggetto = "Aggiornamento dati: " . $record_bando["oggetto"];

										$corpo = "L'operatore economico " . $operatore["codice_fiscale_impresa"] . " " . $operatore["ragione_sociale"] . ",  ha aggiornato i dati della sua istanza al Sistema dinamico di acquisizione:<br>";
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
										$mailer->sezione = "sda";
										$mailer->codice_gara = $record_bando["codice"];
										$mailer->type = 'comunicazione-sda';
										$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
										$mailer->codice_pec = -1;
										$mailer->destinatari = $pec_conferma;
										$esito = $mailer->send();

									}

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "r_partecipanti_sda";
									$salva->operazione = "UPDATE";
									$salva->oggetto = $partecipante;
									$codice_partecipante = $salva->save();

								}
								if ($msg == "") echo "<h1>".traduci("salvataggio riuscito con successo")."!</h1>";
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
			} else {
					echo "<h1>" . traduci('impossibile accedere') . " - 4</h1>";
			}
	include_once($root."/layout/bottom.php");

	?>
