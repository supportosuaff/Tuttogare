<?
	include_once("../../../config.php");
	include_once($root."/inc/p7m.class.php");
	include_once($root."/layout/top.php");
	include($root."/inc/pdftotext.phpclass");

	$public = true;
	function fatal_handler() {
	  $error = error_get_last();
	  if( $error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_USER_ERROR))
	  {
			global $codice_gara;
			global $codice_lotto;
			$path = $config["path_vocabolario"]."/{$_SESSION["language"]}/errore-upload.php";
			if (file_exists($path)) include($path);
			if (!empty($codice_gara)) { ?>
				<a class="submit_big" style="background-color:#444"  href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=".$codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
			<? }
	  }
	}
	register_shutdown_function('fatal_handler');

	if (isset($_POST["codice_gara"]) && isset($_POST["codice_lotto"]) && isset($_POST["codice_busta"]) && isset($_POST["filechunk"]) && is_operatore()) {

		$codice_gara = $_POST["codice_gara"];
		$codice_lotto = $_POST["codice_lotto"];
		$codice_busta = $_POST["codice_busta"];

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								WHERE b_gare.codice = :codice ";
		$strsql .= "AND b_gare.annullata = 'N' AND b_modalita.online = 'S' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$accedi = false;

		if ($risultato->rowCount() > 0) {
			$bind = array();
			$bind[":codice"] = $codice_gara;
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$derivazione = "";
			$sql = "SELECT * FROM b_procedure WHERE codice = :codice_procedura";
			$ris = $pdo->bindAndExec($sql,array(":codice_procedura"=>$record_gara["procedura"]));
			if ($ris->rowCount()>0) {
				$rec_procedura = $ris->fetch(PDO::FETCH_ASSOC);
				$directory = $rec_procedura["directory"];
				$record["nome_procedura"] = $rec_procedura["nome"];
				$record["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
				if ($rec_procedura["mercato_elettronico"] == "S") $derivazione = "me";
				if ($rec_procedura["directory"] == "sda")  $derivazione = "sda";
				if ($rec_procedura["directory"] == "dialogo")  $dialogo = true;

			}

			$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice";
			$ris_inviti = $pdo->bindAndExec($strsql,$bind);
			if ($ris_inviti->rowCount()>0) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice AND r_inviti_gare.codice_utente = :codice_utente";
				$ris_invitato = $pdo->bindAndExec($strsql,$bind);
				if ($ris_invitato->rowCount()>0) $accedi = true;
			} else {
				if($record_gara["invito"] == "N" || !empty($derivazione)) {
					$accedi = true;
				}
			}
			if ($derivazione != "") {
				$sql_abilitato = "SELECT * FROM r_partecipanti_".$derivazione." WHERE codice_bando = :codice_derivazione AND ammesso = 'S' AND codice_utente = :codice_utente ";
				$ris_abilitato = $pdo->bindAndExec($sql_abilitato,array(":codice_derivazione"=>$record_gara["codice_derivazione"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_abilitato->rowCount() == 0) {
					$accedi = false;
				}
			}
		}
		if ($accedi) {
			$print_form = true;
			$record_gara["tipologie_gara"] = "";
			$bind = array();
			$bind[":tipologia"] = $record_gara["tipologia"];
			$sql = "SELECT tipologia FROM b_tipologie WHERE b_tipologie.codice = :tipologia";
			$ris_tipologie = $pdo->bindAndExec($sql,$bind);
			if ($ris_tipologie->rowCount()>0) {
				$rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC);
				$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
			}
			?>
			<h1><?= traduci("carica documentazione") ?> - ID <? echo $record_gara["id"] ?></h1>
			<h2><strong><? echo traduci(trim($record_gara["tipologie_gara"])) ?></strong> - <? echo $record_gara["oggetto"] ?></h2>
			<?
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
			if ($ris_lotti->rowCount() > 0) {
				$print_form =false;
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto ORDER BY codice";
				$bind[":codice_lotto"] = $codice_lotto;
				$ris_check_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				if ($ris_check_lotti->rowCount() > 0) {
						$lotto = $ris_check_lotti->fetch(PDO::FETCH_ASSOC);
						if ($record_gara["modalita_lotti"]==1) {
							$bind =array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND conferma = TRUE AND codice_utente = :codice_utente";
							$ris_partecipazioni = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipazioni->rowCount() > 0) {
								$bind = array();
								$bind[":lotto"] = $codice_lotto;
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_lotto = :lotto AND conferma = TRUE AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
								$ris_partecipante_lotto = $pdo->bindAndExec($sql,$bind);
								if ($ris_partecipante_lotto->rowCount() > 0) {
									$print_form = true;
								} else {
									?>
									<h2 style="color:#C00"><?= traduci("Impossibile partecipare a più lotti") ?></h2>
									<?
								}
							} else {
								$print_form = true;
							}
					} else {
						$print_form = true;
					}
				}
			} else {
				$codice_lotto = 0;
			}

			if ($print_form) {

				$submit = false;

				if (isset($lotto)) {
					$codice_lotto = $lotto["codice"];
					echo "<div class=\"box\"><h3>" . $lotto["oggetto"] . "</h3>";
					echo $lotto["descrizione"]."</div>";
				}

				if (strtotime($record_gara["data_scadenza"]) > time()) {
						$submit = true;
				} else if ($record_gara["fasi"] == 'S') {
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$bind[":lotto"] = $codice_lotto;
					$sql_fase = "SELECT r_partecipanti.* FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto
											WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_lotto = :lotto AND r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_capogruppo = 0
											AND ammesso = 'S' AND escluso = 'N' AND b_2fase.data_inizio <= now() AND b_2fase.data_fine > now()";
					$ris_fase = $pdo->bindAndExec($sql_fase,$bind);
					if ($ris_fase->rowCount() > 0) {
						$submit = true;
						$seconda_fase = true;
					}
				}

				$filtro_mercato = "";
				if ($record_gara["mercato_elettronico"]=="S") $filtro_mercato = " AND mercato_elettronico = 'S' ";
				$filtro_fase = "";
				if ($record_gara["fasi"]=="S") {
					if (strtotime($record_gara["data_scadenza"]) > time()) {
						$filtro_fase = " AND 2fase = 'N' ";
					}
				}
				if (isset($dialogo) && $dialogo = true) $filtro_fase = " AND 2fase = 'S' ";

				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];
				$bind[":codice_busta"] = $codice_busta;

				$strsql = "SELECT b_criteri_buste.* FROM b_criteri_buste
									 WHERE codice_criterio = :codice_criterio " . $filtro_fase . $filtro_mercato ." AND codice = :codice_busta AND eliminato = 'N' LIMIT 0,1";
				$ris_buste = $pdo->bindAndExec($strsql,$bind);
				if ($ris_buste->rowCount() > 0 && $submit) {
					$error = false;
					$sub_error = "";
					$infoBusta = $ris_buste->fetch(PDO::FETCH_ASSOC);

					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_lotto"] = $codice_lotto;
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND codice_capogruppo = 0 ";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount() > 0) {
						$partecipante = $ris->fetch(PDO::FETCH_ASSOC);
						$codice_partecipante = $partecipante["codice"];

						$sql = "SELECT b_buste.* FROM b_buste WHERE
										b_buste.codice_busta = :codice_busta AND
										b_buste.codice_partecipante = :codice_partecipante";
						if (isset($seconda_fase)) $sql .= " AND b_buste.nome_file LIKE '%seconda_fase'";
						$ris_old_busta = $pdo->bindAndExec($sql,array(":codice_busta" => $codice_busta,":codice_partecipante" => $codice_partecipante));
						if ($ris_old_busta->rowCount()>0) {
							while($rec_delete = $ris_old_busta->fetch(PDO::FETCH_ASSOC)) {
								$fileURL = $config["doc_folder"] ."/" . $record_gara["codice"]."/".$codice_lotto."/".$rec_delete["nome_file"];
								$confirmURL = $config["doc_folder"] ."/" . $record_gara["codice"]."/".$codice_lotto."/".$codice_partecipante."_conferma.pdf";
								if (file_exists($fileURL)) unlink($fileURL);
								if (file_exists($fileURL.".tsr")) unlink($fileURL.".tsr");
								if (file_exists($confirmURL)) unlink($confirmURL);
								if (file_exists($confirmURL.".tsr")) unlink($confirmURL.".tsr");
							}
						}

						$strsql = "DELETE FROM b_buste WHERE codice_partecipante = :codice_partecipante AND codice_busta = :codice_busta";
						$delete_buste = $pdo->bindAndExec($strsql,array(":codice_busta" => $codice_busta,":codice_partecipante" => $codice_partecipante));

						/* INIZIO REVOCA SE SOVRASCRIZIONE */
						if ($partecipante["conferma"] == 1) {

							$sql = "UPDATE r_partecipanti SET conferma = 0 WHERE codice = :codice_partecipante";
							$ris = $pdo->bindAndExec($sql,array(":codice_partecipante"=>$codice_partecipante));

								$oggetto = "Conferma di revoca della gara " . $record_gara["oggetto"];
								$corpo = "L'operatore economico " . $partecipante["partita_iva"] . " " . $partecipante["ragione_sociale"] . ",  ha revocato la partecipazione alla gara telematica:<br>";
								$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
								if (isset($lotto) != "") $corpo.= "Lotto: <strong>" . $lotto["oggetto"] . "</strong><br><br>";
								$corpo.= "Distinti Saluti<br><br>";

								$strsql = "SELECT pec FROM b_utenti WHERE codice = :codice_utente";
								$bind = array();
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$risultato = $pdo->bindAndExec($strsql,$bind);
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
								$mailer->codice_gara = $record_gara["codice"];
								$mailer->destinatari = $indirizzi;
								$esito = $mailer->send();

								$pec_conferma = getIndirizzoConferma($record_gara["codice_pec"]);

								$mailer = new Communicator();
								$mailer->oggetto = $oggetto;
								$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
								$mailer->codice_pec = -1;
								$mailer->destinatari = $pec_conferma;
								$mailer->codice_gara = $record_gara["codice"];
								$mailer->type = 'comunicazione-gara';
								$mailer->sezione = "gara";
								$esito = $mailer->send();

							}
							/* FINE REVOCA SE SOVRASCRIZIONE */
					} else if (!isset($seconda_fase)) {

						$sql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice WHERE b_operatori_economici.codice_utente = :codice_utente ";
						$ris_operatori_economici = $pdo->bindAndExec($sql,array(":codice_utente"=>$_SESSION["codice_utente"]));

						$partecipante = array();
						$operatore_economico = $ris_operatori_economici->fetch(PDO::FETCH_ASSOC);
						$partecipante["codice_gara"] = $record_gara["codice"];
						$partecipante["codice_lotto"] = $codice_lotto;
						$partecipante["codice_operatore"] = $operatore_economico["codice"];
						$partecipante["codice_utente"] = $_SESSION["codice_utente"];
						$partecipante["partita_iva"] = $operatore_economico["codice_fiscale_impresa"];
						$partecipante["ragione_sociale"] = $operatore_economico["ragione_sociale"];
						$partecipante["pec"] = $operatore_economico["pec"];
						$partecipante["identificativoEstero"] = $operatore_economico["identificativoEstero"];
						$partecipante["conferma"] = 0;
						$partecipante["ammesso"] = 'N';

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_partecipanti";
						$salva->operazione = "INSERT";
						$salva->oggetto = $partecipante;
						$codice_partecipante = $salva->save();
					}
					$msg = "";
					if (strpos($_POST["filechunk"], "../")===false) {
						ini_set('max_execution_time', 600);
						ini_set('memory_limit', '-1');
						$p7m = new P7Manager($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);

						$md5_file = $p7m->getHash('md5');

					  if ($md5_file == $_POST["md5_file"]) {
							$msg .= "<li>File integro - HASH MD5: " . $md5_file . "</li>";
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
								if ($infoBusta["economica"] == "S") {
									$check_content = false;
									if ($record_gara["nuovaOfferta"] == "N") {
										$check_content = true;
									} else {
										$bind = array();
										$bind[":codice_gara"] = $record_gara["codice"];
										$sql_valutazione = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica
																				JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
																				WHERE b_valutazione_tecnica.codice_gara = :codice_gara
																				AND b_valutazione_tecnica.tipo = 'N'
																				AND b_valutazione_tecnica.valutazione <> ''
																				AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
										if ($record_gara["nuovaOfferta"] == "S") {
											$bind[":codice_lotto"] = $codice_lotto;
											$sql_valutazione .= " AND (b_valutazione_tecnica.codice_lotto = 0 OR b_valutazione_tecnica.codice_lotto = :codice_lotto) ";
										}
										$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
										if ($ris_valutazione->rowCount() > 0) $check_content = true;
									}
									if ($check_content) {
										$bind = array();
										$bind[":codice_gara"] = $record_gara["codice"];
										$bind[":codice_lotto"] = $codice_lotto;
										$bind[":codice_partecipante"] = $codice_partecipante;
										$bind[":tipo"] = "economica";
						        $strsql = "SELECT * FROM b_offerte_economiche WHERE codice_gara = :codice_gara
															 AND codice_lotto = :codice_lotto
															 AND codice_partecipante = :codice_partecipante
															 AND tipo = :tipo ORDER BY timestamp DESC";
						        $ris_offerta = $pdo->bindAndExec($strsql,$bind);
										if ($ris_offerta->rowCount()>0) {
											$hashVerify = array();
						          $rec_offerta = $ris_offerta->fetch(PDO::FETCH_ASSOC);
											if ($rec_offerta["md5"] != "") $hashVerify[] = $p7m->find($rec_offerta["md5"],"md5");
											if ($rec_offerta["sha1"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha1"],"sha1");
											if ($rec_offerta["sha256"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha256"],"sha256");
											$found = false;
											if (count($hashVerify) > 0 && in_array(false,$hashVerify)!==false) {
												if ($p7m->find($rec_offerta["shaContent"],"sha256",true)) {
													$found = true;
												} else {
													$error = true;
													$sub_error .= "<li>".traduci('partecipazione-offerta-mancante')."</li>";
												}
											} else {
												$found = true;
											}
											if ($found) $msg .= "<li>Offerta economica verificata</li>";
										} else {
											$error = true;
											$sub_error .= "<li>" . traduci("Prima è necessario generare il file di offerta") . "</li>";
										}
									}
								}

								if ($infoBusta["tecnica"] == "S") {
									$bind = array();
									$bind[":codice_gara"] = $record_gara["codice"];
									$bind[":codice_lotto"] = $codice_lotto;
									$bind[":codice_partecipante"] = $codice_partecipante;
									$bind[":tipo"] = "tecnica";
					        $strsql = "SELECT * FROM b_offerte_economiche WHERE codice_gara = :codice_gara
														 AND codice_lotto = :codice_lotto
														 AND codice_partecipante = :codice_partecipante
														 AND tipo = :tipo ORDER BY timestamp DESC";
					        $ris_offerta = $pdo->bindAndExec($strsql,$bind);
					        if ($ris_offerta->rowCount()>0) {
										$hashVerify = array();
					          $rec_offerta = $ris_offerta->fetch(PDO::FETCH_ASSOC);
										if ($rec_offerta["md5"] != "") $hashVerify[] = $p7m->find($rec_offerta["md5"],"md5");
										if ($rec_offerta["sha1"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha1"],"sha1");
										if ($rec_offerta["sha256"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha256"],"sha256");
										$found = false;
										if (count($hashVerify) > 0 && in_array(false,$hashVerify)!==false) {
											if ($p7m->find($rec_offerta["shaContent"],"sha256",true)) {
												$found = true;
											} else {
												$error = true;
												$sub_error .= "<li>".traduci('partecipazione-offerta-mancante')."</li>";
											}
										} else {
											$found = true;
										}
										if ($found) 	$msg .= "<li>Offerta tecnica verificata</li>";
									} else {
										$bind = array();
										$bind[":codice_gara"] = $record_gara["codice"];
										$sql_valutazione = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica
																				JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
																				WHERE b_valutazione_tecnica.codice_gara = :codice_gara
																				AND b_valutazione_tecnica.tipo = 'N'
																				AND b_valutazione_tecnica.valutazione <> ''
																				AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
										if ($record_gara["nuovaOfferta"] == "S") {
											$bind[":codice_lotto"] = $codice_lotto;
											$sql_valutazione .= " AND (b_valutazione_tecnica.codice_lotto = 0 OR b_valutazione_tecnica.codice_lotto = :codice_lotto) ";
										}
										$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
										if ($ris_valutazione->rowCount() > 0) {
											$error = true;
											$sub_error .= "<li>" . traduci("Prima è necessario generare il file di offerta") . "</li>";
										}
									}
								}

								if (!$error) {

									$busta = array();
									$busta["codice_gara"] = $record_gara["codice"];
									$busta["codice_lotto"] = $codice_lotto;
									$busta["codice_partecipante"] = $codice_partecipante;
									$busta["codice_busta"] = $codice_busta;
									$busta["md5"] = $p7m->getHash("md5");
									$busta["sha1"] = $p7m->getHash("sha1");
									$busta["sha256"] = $p7m->getHash("sha256");
									$busta["nome_file"] = $busta["codice_partecipante"] . "_" . $busta["codice_busta"];
									if (isset($seconda_fase)) $busta["nome_file"] .= "_seconda_fase";
									$destinationPath = $config["doc_folder"] . "/" . $record_gara["codice"] . "/" . $codice_lotto . "/";
									$esito_salvataggio = $p7m->encryptAndSave($_POST["salt"],$destinationPath,$busta["nome_file"]);
									if ($esito_salvataggio === true) {
										// ELIMINATA MARCATURA SU SINGOLE BUSTE - INSERITA IN PROCEDURA DI CONFERMA
										// $timestamp = $p7m->putTimestamp($destinationPath.$busta["nome_file"]);
										// if ($timestamp !== false) {
											// $msg .= "<li>Marcatura temporale effettuata con successo: " . $timestamp;
											$busta["salt"] = $p7m->publicEncrypt($_POST["salt"],$record_gara["public_key"]);
											if ($busta["salt"]!==false) {
												$msg .= "<li>Criptazione effettuata con successo</li>";
												$salva = new salva();
												$salva->debug = false;
												$salva->codop = $_SESSION["codice_utente"];
												$salva->nome_tabella = "b_buste";
												$salva->operazione = "INSERT";
												$salva->oggetto = $busta;
												$codice_busta = $salva->save();
												if ($codice_busta > 0) {
													$msg .= "<li>" . traduci("salvataggio riuscito con successo")  . "</li>";
													?>
													<ul class="success">
														<? echo $msg ?>
													</ul>
													<?
												} else {
													?>
													<h3 class="ui-state-error"><?= traduci("Errore nel salvataggio dell'offerta") ?></h3>
													<?
												}
											} else {
												?>
												<h3 class="ui-state-error"><?= traduci("Errore nella criptazione dell'offerta") ?></h3>
												<?
											}
										/* } else {
											?>
												<h3 class="ui-state-error">Errore nella marcatura temporale</h3>
											<?
										} */
									} else {
										?>
										<h3 class="ui-state-error"><?= traduci('errore-salvataggio') ?></h3>
										<?
									}
								} else {
									?>
									<h3 class="ui-state-error"><ul><?= $sub_error ?></ul></h3>
									<?
								}
							} else {
								?>
								<h3 class="ui-state-error"><?= traduci("Firma del file non valida") ?></h3>
								<?
							}
						} else {
							?>
					    <h3 class="ui-state-error"><?= traduci("Errore nella procedura di upload") ?></h3>
							<?
						}
						if (file_exists($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"])) unlink($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);
					} else {
						?>
						<h3 class="ui-state-error"><?= traduci("Errore nella procedura di upload") ?></h3>
						<?
					}
					?>
					<a class="submit_big" style="background-color:#444"  href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=".$codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
					<?
				} else {
					if (!$submit) {
						echo "<h1>". traduci('impossibile accedere') . " - " . traduci('termini scaduti') . "</h1>";
					} else {
						echo "<h1>". traduci('impossibile accedere') . " - ERROR 3</h1>";
					}
				}
			} else {
				echo "<h1>". traduci('impossibile accedere') . " - ERROR 2</h1>";
			}
		} else {
			echo "<h1>". traduci('impossibile accedere') . " - ERROR 1</h1>";
		}
	} else {
		echo "<h1>". traduci('impossibile accedere') . " - ERROR 0</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
