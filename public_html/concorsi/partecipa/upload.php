<?
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
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
			?>
	  	<h3 class="ui-state-error">Si è verificato un errore nella procedura di upload.<br><br>
				Si prega di riprovare se il problema persiste contattare l'Help Desk tecnico al numero <strong><?= $_SESSION["numero_assistenza"] ?></strong></h3>
			<? if (!empty($codice_gara)) { ?>
				<a class="submit_big" style="background-color:#444"  href="/concorsi/partecipa/modulo.php?cod=<?= $codice_gara ?>">Ritorna al pannello</a>
			<? }
	  }
	}
	register_shutdown_function('fatal_handler');

	if (isset($_POST["codice_gara"]) && isset($_POST["codice_busta"]) && isset($_POST["filechunk"]) && is_operatore()) {

		$codice_gara = $_POST["codice_gara"];
		$codice_busta = $_POST["codice_busta"];
		$msg = "";
		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_concorsi.* FROM b_concorsi
								WHERE b_concorsi.codice = :codice ";
		$strsql .= "AND b_concorsi.annullata = 'N' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		if ($risultato->rowCount() > 0) {

			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$i = 0;
			$open = false;
			$last = array();
			$fase_attiva = array();

			$sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara  ";
			$ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice_gara"=>$record_gara["codice"]));
			if ($ris_fasi->rowCount() > 0) {
				$open = true;
				while($fase = $ris_fasi->fetch(PDO::FETCH_ASSOC)) {
					if ($fase["attiva"]=="S") {
						if ($i > 0) $open = false;
						$last = $fase_attiva;
						$fase_attiva = $fase;
					}
					$i++;
				}
			}

			if ($open) {
				$accedi = true;
			} else if (!empty($last["codice"])) {
				$sql_check = "SELECT * FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
								WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND r_partecipanti_concorsi.conferma = 1 AND r_partecipanti_concorsi.ammesso = 'S'
								AND r_partecipanti_concorsi.escluso = 'N' AND r_partecipanti_utenti_concorsi.codice_utente = :codice_utente ";
				$ris_check = $pdo->bindAndExec($sql_check,array(":codice_gara"=>$record_gara["codice"],":codice_fase"=>$last["codice"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_check->rowCount() > 0) $accedi = true;
			}

		if ($accedi && !empty($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"])) {
			$partecipante = $_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]];
			$codice_partecipante = $partecipante["codice"];
			$print_form = true;
			?>
			<h1>CARICA DOCUMENTAZIONE - ID <? echo $record_gara["id"] ?></h1>
			<h2><? echo $record_gara["oggetto"] ?> - Fase: <?= $fase_attiva["oggetto"] ?></h2>
			<?

			if (strtotime($fase_attiva["scadenza"]) > time()) {

				$strsql = "SELECT b_fasi_concorsi_buste.* FROM b_fasi_concorsi_buste
									 WHERE codice = :codice_busta LIMIT 0,1";
				$ris_buste = $pdo->bindAndExec($strsql,array(":codice_busta"=>$_POST["codice_busta"]));
				if ($ris_buste->rowCount() > 0) {
					$error = false;
					$sub_error = "";
					$infoBusta = $ris_buste->fetch(PDO::FETCH_ASSOC);
					$bind_buste = array(":codice_partecipante" => $codice_partecipante);
					$sql = "SELECT b_buste_concorsi.* FROM b_buste_concorsi WHERE ";
					if ($infoBusta["tecnica"]=="N") {
						$sql .= " b_buste_concorsi.codice_busta = :codice_busta AND ";
						$bind_buste[":codice_busta"] = $codice_busta;
					}
					$sql .= " b_buste_concorsi.codice_partecipante = :codice_partecipante";

					$ris_old_busta = $pdo->bindAndExec($sql,$bind_buste);
					if ($ris_old_busta->rowCount()>0) {
						while($rec_delete = $ris_old_busta->fetch(PDO::FETCH_ASSOC)) {
							$fileURL = $config["doc_folder"] ."/concorsi/" . $record_gara["codice"]."/". $fase_attiva["codice"] ."/".$rec_delete["nome_file"];
							$confirmURL = $config["doc_folder"] ."/concorsi/" . $record_gara["codice"]."/". $fase_attiva["codice"] ."/".$codice_partecipante."_conferma.pdf";
							if (file_exists($fileURL)) unlink($fileURL);
							if (file_exists($fileURL.".tsr")) unlink($fileURL.".tsr");
							if (file_exists($confirmURL)) unlink($confirmURL);
							if (file_exists($confirmURL.".tsr")) unlink($confirmURL.".tsr");
						}
					}


					$strsql = "DELETE FROM b_buste_concorsi WHERE ";
					if ($infoBusta["tecnica"]=="N") {
						$strsql .= " b_buste_concorsi.codice_busta = :codice_busta AND ";
					}
					$strsql .= " codice_partecipante = :codice_partecipante";

					// $strsql = "DELETE FROM b_buste_concorsi WHERE codice_partecipante = :codice_partecipante AND codice_busta = :codice_busta";
					$delete_buste = $pdo->bindAndExec($strsql,$bind_buste);

					/* INIZIO REVOCA SE SOVRASCRIZIONE */
					if ($partecipante["conferma"] == 1) {
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = -1;
						$salva->nome_tabella = "r_partecipanti_concorsi";
						$salva->operazione = "UPDATE";
						$salva->oggetto = array("codice"=>$codice_partecipante,"conferma"=>0);
						$salva->save();

						$_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["conferma"] = 0;

						$oggetto = "Conferma di revoca al concorso " . $record_gara["oggetto"] . " Fase: " . $fase_attiva["oggetto"];
						$corpo = "Il partecipante " . $partecipante["identificativo"] . ",  ha revocato la partecipazione al concorso:<br>";
						$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";

						$corpo.= "Distinti Saluti<br><br>";

						$pec_conferma = getIndirizzoConferma($record_gara["codice_pec"]);

						$mailer = new Communicator();
						$mailer->oggetto = $oggetto;
						$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
						$mailer->codice_pec = -1;
						$mailer->destinatari = $pec_conferma;
						$mailer->sezione = "concorsi";
						$mailer->codice_gara = $record_gara["codice"];
						$mailer->type = 'comunicazione-concorso';
						$esito = $mailer->send();

						/* FINE REVOCA SE SOVRASCRIZIONE */
					}
					$msg = "";
					if (strpos($_POST["filechunk"], "../")===false) {

						$p7m = new P7Manager($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);

						$md5_file = $p7m->getHash('md5');

					  if ($md5_file == $_POST["md5_file"]) {
							$msg .= "<li>File integro - HASH MD5: " . $md5_file . "</li>";
							if ($infoBusta["tecnica"] == "N") {
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


									if ($infoBusta["tecnica"] == "N") {
										$bind = array();
										$bind[":codice_gara"] = $record_gara["codice"];
										$bind[":codice_partecipante"] = $codice_partecipante;
										$strsql = "SELECT b_buste_concorsi.* FROM b_buste_concorsi JOIN b_fasi_concorsi_buste ON b_buste_concorsi.codice_busta = b_fasi_concorsi_buste.codice
															 WHERE b_buste_concorsi.codice_gara = :codice_gara
															 AND b_buste_concorsi.codice_partecipante = :codice_partecipante
															 AND b_fasi_concorsi_buste.tecnica = 'S' ";
										$ris_checkFile = $pdo->bindAndExec($strsql,$bind);
										if (!isset($ris_checkFile) || (isset($ris_checkFile) && $ris_checkFile->rowCount()==0)) {
											$error = true;
											$sub_error .= "<li>Prima di proseguire è necessario caricare la busta tecnica</li>";
										} else {
											$bustaTecnica = $ris_checkFile->fetch(PDO::FETCH_ASSOC);
											$hashVerify = array();
											if ($bustaTecnica["md5"] != "") $hashVerify[] = $p7m->find($bustaTecnica["md5"],"md5");
											if ($bustaTecnica["sha1"] != "") $hashVerify[] = $p7m->find($bustaTecnica["sha1"],"sha1");
											if ($bustaTecnica["sha256"] != "") $hashVerify[] = $p7m->find($bustaTecnica["sha256"],"sha256");
											$found = false;
											if (count($hashVerify) > 0 && in_array(false,$hashVerify)!==false) {
												$error = true;
												$sub_error .= "<li>Impossibile trovare la copia firmata digitalmente della busta tecnica</strong></li>";
											} else {
												$found = true;
											}
											if ($found) 	$msg .= "<li>Busta tecnica verificata</li>";
										}

									}

									/* if ($infoBusta["tecnica"] == "S") {
										$bind = array();
										$bind[":codice_gara"] = $record_gara["codice"];
										$bind[":codice_partecipante"] = $codice_partecipante;
						        $strsql = "SELECT * FROM b_offerte_concorso WHERE codice_gara = :codice_gara
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
													$sub_error .= "<li>Impossibile trovare la copia firmata digitalmente del file d'offerta tecnica generato dal sistema - <strong>Attenzione: Non aprire l'offerta direttamente dal browser ma seleziona il file originale dalla cartella di download</strong></li>";
												}
											} else {
												$found = true;
											}
											if ($found) 	$msg .= "<li>Offerta tecnica verificata</li>";
										} else {
											$bind = array();
											$bind[":codice_gara"] = $record_gara["codice"];
											$bind[":codice_fase"] = $fase_attiva["codice"];
											$sql_valutazione = "SELECT b_criteri_valutazione_concorsi.* FROM b_criteri_valutazione_concorsi
																					WHERE b_criteri_valutazione_concorsi.codice_gara = :codice_gara AND b_criteri_valutazione_concorsi.codice_fase = :codice_fase AND b_criteri_valutazione_concorsi.valutazione <> ''";
											$ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
											if ($ris_valutazione->rowCount() > 0) {
												$error = true;
												$sub_error .= "<li>Prima di proseguire è necessario generare il file di offerta tecnica</li>";
											}
										}
									} */
									} else {
										$error = true;
										$sub_error .= "<li>Firma del file non valida</li>";
									}
								} else {
									$esito = $p7m->verifySignedContent();
									if ($esito) {
										$error = true;
										$sub_error .= "<li>La busta contiene files firmati digitalmente</li>";
									}
								}
								if (!$error) {

									$busta = array();
									$busta["codice_gara"] = $record_gara["codice"];
									$busta["codice_partecipante"] = $codice_partecipante;
									$busta["codice_busta"] = $codice_busta;
									$busta["codice_fase"] = $fase_attiva["codice"];
									$busta["md5"] = $p7m->getHash("md5");
									$busta["sha1"] = $p7m->getHash("sha1");
									$busta["sha256"] = $p7m->getHash("sha256");
									$busta["nome_file"] = $busta["codice_partecipante"] . "_" . $busta["codice_busta"];
									$destinationPath = $config["doc_folder"] . "/concorsi/" . $record_gara["codice"] . "/" . $fase_attiva["codice"] . "/";
									$esito_salvataggio = $p7m->encryptAndSave($_POST["salt"],$destinationPath,$busta["nome_file"]);
									if ($esito_salvataggio === true) {
										/* $timestamp = $p7m->putTimestamp($destinationPath.$busta["nome_file"]);
										if ($timestamp !== false) {
											$msg .= "<li>Marcatura temporale effettuata con successo: " . $timestamp; */
											$busta["salt"] = $p7m->publicEncrypt($_POST["salt"],$record_gara["public_key"]);
											if ($busta["salt"]!==false) {
												$msg .= "<li>Criptazione effettuata con successo</li>";
												$salva = new salva();
												$salva->debug = false;
												$salva->codop = -1;
												$salva->nome_tabella = "b_buste_concorsi";
												$salva->operazione = "INSERT";
												$salva->oggetto = $busta;
												$codice_busta = $salva->save();
												if ($codice_busta > 0) {
													$msg .= "<li>Salvataggio effettuato con successo</li>";
													?>
													<ul class="success">
														<? echo $msg ?>
													</ul>
													<?
												} else {
													?>
													<h3 class="ui-state-error">Errore nel salvataggio della busta</h3>
													<?
												}
											} else {
												?>
													<h3 class="ui-state-error">Errore nella criptazione del file</h3>
												<?
											}
										/* } else {
											?>
												<h3 class="ui-state-error">Errore nella marcatura temporale</h3>
											<?
										} */
									} else {
										?>
										<h3 class="ui-state-error">Errore durante il salvataggio - <ul><?= $esito_salvataggio ?></ul></h3>
										<?
									}
								} else {
									?>
									<h3 class="ui-state-error">Verifica dell'offerta fallita - <ul><?= $sub_error ?></ul></h3>
									<?
								}
						} else {
							?>
					    <h3 class="ui-state-error">Errore nella procedura di upload</h3>
							<?
						}
						unlink($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);
					} else {
						?>
						<h3 class="ui-state-error">Errore nella procedura di upload</h3>
						<?
					}
					?>
					<a class="submit_big" style="background-color:#444"  href="/concorsi/partecipa/modulo.php?cod=<?= $codice_gara ?>">Ritorna al pannello</a>
					<?
				} else {
					?><h1>Impossibile continuare: Errore nella procedura</h1><?


				}
			} else { ?>
				<h1>Impossibile continuare: Termini scaduti</h1>
					<?
			}
			} else {
				?>
				<h1>Impossibile continuare: Privilegi insufficienti</h1>
				<?
			}
		} else {
			?>
			<h1>Gara inesistente o privilegi insufficienti</h1>
			<?
		}
	} else {
		echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
