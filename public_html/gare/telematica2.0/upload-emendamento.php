<?
ini_set('max_execution_time', 600);
ini_set('memory_limit', '-1');
include_once("../../../config.php");
include_once($root . "/inc/p7m.class.php");
include_once($root . "/layout/top.php");
include($root . "/inc/pdftotext.phpclass");

$public = true;
function fatal_handler()
{
	$error = error_get_last();
	if ($error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_USER_ERROR)) {
		global $codice_gara;
		global $codice_lotto;
		global $config;
		$path = $config["path_vocabolario"] . "/{$_SESSION["language"]}/errore-upload.php";
		if (file_exists($path)) {
			include($path);
		}
		if (!empty($codice_gara)) { ?>
			<a class="submit_big" style="background-color:#444" href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=" . $codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
		<? }
	}
}
register_shutdown_function('fatal_handler');

if (isset($_POST["codice_gara"]) && isset($_POST["codice_lotto"]) && isset($_POST["codice_busta"])  && isset($_POST["busta_originale"]) && isset($_POST["filechunk"]) && is_operatore()) {

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
	$risultato = $pdo->bindAndExec($strsql, $bind);

	$accedi = false;

	if ($risultato->rowCount() > 0) {
		$bind = array();
		$bind[":codice"] = $codice_gara;
		$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
		?>
		<h1><?= traduci("CARICA DOCUMENTAZIONE") ?> - ID <? echo $record_gara["id"] ?></h1>
		<h2><? echo $record_gara["oggetto"] ?></h2>
		<?
		$bind = array();
		$bind[":codice_gara"] = $record_gara["codice"];
		$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
		$ris_lotti = $pdo->bindAndExec($sql_lotti, $bind);
		if ($ris_lotti->rowCount() > 0) {
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto ORDER BY codice";
			$bind[":codice_lotto"] = $codice_lotto;
			$ris_check_lotti = $pdo->bindAndExec($sql_lotti, $bind);
			if ($ris_check_lotti->rowCount() > 0) {
				$lotto = $ris_check_lotti->fetch(PDO::FETCH_ASSOC);
			}
		} else {
			$codice_lotto = 0;
		}
		$submit = false;

		if (isset($lotto)) {
			$codice_lotto = $lotto["codice"];
			echo "<div class=\"box\"><h3>" . $lotto["oggetto"] . "</h3>";
			echo $lotto["descrizione"] . "</div>";
		}
		$msg = "";
		$bind = array();
		$bind[":codice_gara"] = $record_gara["codice"];
		$bind[":codice_lotto"] = $codice_lotto;
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND codice_capogruppo = 0 ";
		$ris = $pdo->bindAndExec($sql, $bind);
		if ($ris->rowCount() > 0) {
			$partecipante = $ris->fetch(PDO::FETCH_ASSOC);
			$sql_in = "SELECT b_buste.*, b_criteri_buste.nome AS nome_busta FROM b_buste 
						   JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice 
						   WHERE b_buste.codice = :codice_originale AND b_buste.codice_gara = :codice_gara AND b_buste.codice_lotto = :codice_lotto AND b_buste.codice_busta = :codice_busta AND b_buste.codice_partecipante = :codice_partecipante ";
			$ris_in = $pdo->bindAndExec($sql_in, array(":codice_originale" => $_POST["busta_originale"], ":codice_busta" => $_POST["codice_busta"], ":codice_gara" => $record_gara["codice"], ":codice_lotto" => $codice_lotto, ":codice_partecipante" => $partecipante["codice"]));
			if ($ris_in->rowCount() > 0) {
				$rec_busta = $ris_in->fetch(PDO::FETCH_ASSOC);
				$emendabile = checkBustaEmendabile($rec_busta);
				if ($emendabile) {
					$bind = array();
					$bind[":codice_busta"] = $_POST["codice_busta"];
					$strsql = "SELECT b_criteri_buste.* FROM b_criteri_buste WHERE codice = :codice_busta AND eliminato = 'N' LIMIT 0,1";
					$ris_buste = $pdo->bindAndExec($strsql, $bind);
					if ($ris_buste->rowCount() > 0) {
						$error = false;
						$sub_error = "";
						$infoBusta = $ris_buste->fetch(PDO::FETCH_ASSOC);

						$sql = "SELECT b_emendamenti.* FROM b_emendamenti WHERE
										b_emendamenti.codice_busta = :codice_busta AND
										b_emendamenti.codice_partecipante = :codice_partecipante AND 
										b_emendamenti.busta_originale = :busta_originale";
						$ris_old_busta = $pdo->bindAndExec($sql, array(":busta_originale" => $_POST["busta_originale"], ":codice_busta" => $codice_busta, ":codice_partecipante" => $partecipante["codice"]));
						if ($ris_old_busta->rowCount() > 0) {
							while ($rec_delete = $ris_old_busta->fetch(PDO::FETCH_ASSOC)) {
								$fileURL = $config["doc_folder"] . "/" . $record_gara["codice"] . "/" . $codice_lotto . "/emendamenti/" . $rec_delete["nome_file"];
								if (file_exists($fileURL)) unlink($fileURL);
								if (file_exists($fileURL . ".tsr")) unlink($fileURL . ".tsr");
							}
						}

						$strsql = "DELETE FROM b_emendamenti WHERE busta_originale = :busta_originale AND codice_partecipante = :codice_partecipante AND codice_busta = :codice_busta";
						$delete_buste = $pdo->bindAndExec($strsql, array(":busta_originale" => $_POST["busta_originale"], ":codice_busta" => $codice_busta, ":codice_partecipante" => $partecipante["codice"]));
						if (strpos($_POST["filechunk"], "../") === false) {
							$p7m = new P7Manager($config["chunk_folder"] . "/" . $_SESSION["codice_utente"] . "/" . $_POST["filechunk"]);
							$md5_file = $p7m->getHash('md5');
							if ($md5_file == $_POST["md5_file"]) {
								$msg .= "<li>File integro - HASH MD5: " . $md5_file . "</li>";
								$esito = $p7m->checkSignatures();
								if ($esito == "Verification successful") {
									$msg .= "<li>Firma formalmente valida";
									$certificati = $p7m->extractSignatures();
									$msg .= "<ul class=\"firme\">";
									foreach ($certificati as $certificato) {
										$data = openssl_x509_parse($certificato, false);
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
											$ris_valutazione = $pdo->bindAndExec($sql_valutazione, $bind);
											if ($ris_valutazione->rowCount() > 0) $check_content = true;
										}
										if ($check_content) {
											$bind = array();
											$bind[":codice_gara"] = $record_gara["codice"];
											$bind[":codice_lotto"] = $codice_lotto;
											$bind[":codice_partecipante"] = $partecipante["codice"];
											$bind[":tipo"] = "economica";
											$strsql = "SELECT * FROM b_offerte_economiche WHERE codice_gara = :codice_gara
															 AND codice_lotto = :codice_lotto
															 AND codice_partecipante = :codice_partecipante
															 AND tipo = :tipo ORDER BY timestamp DESC";
											$ris_offerta = $pdo->bindAndExec($strsql, $bind);
											if ($ris_offerta->rowCount() > 0) {
												$hashVerify = array();
												$rec_offerta = $ris_offerta->fetch(PDO::FETCH_ASSOC);
												if ($rec_offerta["md5"] != "") $hashVerify[] = $p7m->find($rec_offerta["md5"], "md5");
												if ($rec_offerta["sha1"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha1"], "sha1");
												if ($rec_offerta["sha256"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha256"], "sha256");
												$found = false;
												if (count($hashVerify) > 0 && in_array(false, $hashVerify) !== false) {
													if ($p7m->find($rec_offerta["shaContent"], "sha256", true)) {
														$found = true;
													} else {
														$error = true;
														$sub_error .= "<li>" . traduci('partecipazione-offerta-mancante') . "</li>";
													}
												} else {
													$found = true;
												}
												if ($found) $msg .= "<li>Offerta economica verificata</li>";
											} else {
												$error = true;
												$sub_error .= "<li>" . traduci("File di offerta mancante") . "</li>";
											}
										}
									}

									if ($infoBusta["tecnica"] == "S") {
										$bind = array();
										$bind[":codice_gara"] = $record_gara["codice"];
										$bind[":codice_lotto"] = $codice_lotto;
										$bind[":codice_partecipante"] = $partecipante["codice"];
										$bind[":tipo"] = "tecnica";
										$strsql = "SELECT * FROM b_offerte_economiche WHERE codice_gara = :codice_gara
														 AND codice_lotto = :codice_lotto
														 AND codice_partecipante = :codice_partecipante
														 AND tipo = :tipo ORDER BY timestamp DESC";
										$ris_offerta = $pdo->bindAndExec($strsql, $bind);
										if ($ris_offerta->rowCount() > 0) {
											$hashVerify = array();
											$rec_offerta = $ris_offerta->fetch(PDO::FETCH_ASSOC);
											if ($rec_offerta["md5"] != "") $hashVerify[] = $p7m->find($rec_offerta["md5"], "md5");
											if ($rec_offerta["sha1"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha1"], "sha1");
											if ($rec_offerta["sha256"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha256"], "sha256");
											$found = false;
											if (count($hashVerify) > 0 && in_array(false, $hashVerify) !== false) {
												if ($p7m->find($rec_offerta["shaContent"], "sha256", true)) {
													$found = true;
												} else {
													$error = true;
													$sub_error .= "<li>" . traduci('partecipazione-offerta-mancante') . "</li>";
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
											$ris_valutazione = $pdo->bindAndExec($sql_valutazione, $bind);
											if ($ris_valutazione->rowCount() > 0) {
												$error = true;
												$sub_error .= "<li>" . traduci("File di offerta mancante") . "</li>";
											}
										}
									}

									if (!$error) {

										$busta = array();
										$busta["busta_originale"] = $rec_busta["codice"];
										$busta["codice_gara"] = $record_gara["codice"];
										$busta["codice_lotto"] = $codice_lotto;
										$busta["codice_partecipante"] = $partecipante["codice"];
										$busta["codice_busta"] = $codice_busta;
										$busta["md5"] = $p7m->getHash("md5");
										$busta["sha1"] = $p7m->getHash("sha1");
										$busta["sha256"] = $p7m->getHash("sha256");
										$busta["nome_file"] = $busta["codice_partecipante"] . "_" . $busta["codice_busta"];
										$destinationPath = $config["doc_folder"] . "/" . $record_gara["codice"] . "/" . $codice_lotto . "/emendamenti/";
										$esito_salvataggio = $p7m->encryptAndSave($_POST["salt"], $destinationPath, $busta["nome_file"]);
										if ($esito_salvataggio === true) {
											$busta["salt"] = $p7m->publicEncrypt($_POST["salt"], $record_gara["public_key"]);
											$busta["descrizione"] = simple_encrypt($_POST["descrizione"], $_POST["salt"]);
											if ($busta["salt"] !== false) {
												$msg .= "<li>Criptazione effettuata con successo</li>";
												$salva = new salva();
												$salva->debug = false;
												$salva->codop = $_SESSION["codice_utente"];
												$salva->nome_tabella = "b_emendamenti";
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
										} else {
											?>
											<h3 class="ui-state-error"><?= traduci('errore-salvataggio') ?></h3>
										<?
										}
									} else {
										?>
										<h3 class="ui-state-error">
											<ul><?= $sub_error ?></ul>
										</h3>
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
							if (file_exists($config["chunk_folder"] . "/" . $_SESSION["codice_utente"] . "/" . $_POST["filechunk"])) {
								unlink($config["chunk_folder"] . "/" . $_SESSION["codice_utente"] . "/" . $_POST["filechunk"]);
							}
						} else {
							?>
							<h3 class="ui-state-error"><?= traduci("Errore nella procedura di upload") ?></h3>
						<?
						}
						?>
						<a class="submit_big" style="background-color:#444" href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=" . $codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
<?
					} else {
						echo "<h1>" . traduci('impossibile accedere') . " - ERRORE RICHIESTA</h1>";	
					}
				} else {
					echo "<h1>" . traduci('impossibile accedere') . " - ERROR 3</h1>";
				}
			} else {
				echo "<h1>" . traduci('impossibile accedere') . " - DOCUMENTO NON EMENDABILE</h1>";
			}
		} else {
			echo "<h1>" . traduci('impossibile accedere') . " - ERROR 2</h1>";
		}
	} else {
		echo "<h1>" . traduci('impossibile accedere') . " - ERROR 1</h1>";
	}
} else {
	echo "<h1>" . traduci('impossibile accedere') . " - ERROR 0</h1>";
}
include_once($root . "/layout/bottom.php");
?>