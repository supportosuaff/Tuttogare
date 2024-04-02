<?

	use Dompdf\Dompdf;
	use Dompdf\Options;

	include_once("../../../config.php");
	include_once($root."/inc/p7m.class.php");
	include_once($root."/layout/top.php");

	$public = true;
	if (isset($_POST["codice_gara"]) && isset($_POST["codice_lotto"]) && is_operatore()) {

		$codice_gara = $_POST["codice_gara"];
		$codice_lotto = $_POST["codice_lotto"];

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
			<h1><?= traduci("invia la partecipazione") ?> - ID <? echo $record_gara["id"] ?></h1>
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
											WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_lotto = :lotto AND r_partecipanti.codice_gara = :codice_gara
											AND ammesso = 'S' AND escluso = 'N' AND b_2fase.data_inizio <= now() AND b_2fase.data_fine > now()";
					$ris_fase = $pdo->bindAndExec($sql_fase,$bind);
					if ($ris_fase->rowCount() > 0) $submit = true;
				}
				if ($submit) {
					$sql_partecipante = "SELECT r_partecipanti.* FROM r_partecipanti WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_lotto = :lotto AND r_partecipanti.codice_gara = :codice_gara AND codice_capogruppo = 0";
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$bind[":lotto"] = $codice_lotto;
					$ris_partecipante = $pdo->bindAndExec($sql_partecipante,$bind);
					if($ris_partecipante->rowCount()==1) {
						$partecipante = $ris_partecipante->fetch(PDO::FETCH_ASSOC);
						if ($partecipante["conferma"]==0) {
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

							$strsql = "SELECT b_criteri_buste.* FROM b_criteri_buste
												 WHERE codice_criterio = :codice_criterio " . $filtro_fase . $filtro_mercato ." AND eliminato = 'N' ORDER BY ordinamento";
							$ris_buste = $pdo->bindAndExec($strsql,$bind);

							if ($ris_buste->rowCount() > 0 && $submit) {
								$buste = array();
								$msg = "";
								$error = false;
								while($busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
									$buste[$busta["codice"]] = false;
									$sql_in = "SELECT b_buste.* FROM b_buste WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_busta = :codice_busta AND codice_partecipante = :codice_partecipante ";
									$ris_in = $pdo->bindAndExec($sql_in,array(":codice_busta"=>$busta["codice"],":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$partecipante["codice"]));
									if ($ris_in->rowCount()>0) {
										 $buste[$busta["codice"]] = true;
										 $presented = $ris_in->fetch(PDO::FETCH_ASSOC);
										 $msg .= "<li><h2>" . $busta["nome"] . "</h2><ul>";
										 if (!empty($presented["md5"])) $msg.= "<li>MD5: <strong>" . $presented["md5"] . "</strong></li>";
										 if (!empty($presented["sha1"])) $msg.= "<li>SHA1: <strong>" . $presented["sha1"] . "</strong></li>";
										 if (!empty($presented["sha256"])) $msg.= "<li>SHA256: <strong>" . $presented["sha256"] . "</strong></li>";
										 $msg .= "</ul></li>";
									} else {
										$error = true;
										$msg.= "<li><h2 class=\"ui-state-error\">" . $busta["nome"] . " non trovata</h2></li>";
									}
								}
								if (!$error) {
									$update = array();
									$update["codice"] = $partecipante["codice"];
									$update["conferma"] = 1;
									$update["ammesso"] = 'S';
									if (!empty($lotto)) $record_gara["oggetto"] .= " - Lotto " . $lotto["oggetto"];
									$oggetto = "Conferma di partecipazione alla gara " . $record_gara["oggetto"];

									$corpo = "L'operatore economico " . $partecipante["partita_iva"] . " " . $partecipante["ragione_sociale"] . ",  ha partecipato alla gara telematica:<br>";
									$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
									$corpo.= "Hash delle buste inviate: <ul>";
									$corpo.= $msg;
									$corpo.= "</ul><br><br>";

									$tmp_path = $config["doc_folder"] . "/" . $record_gara["codice"] . "/" . $codice_lotto . "/" . $partecipante["codice"]."_conferma.pdf";

									$options = new Options();
									$options->set('defaultFont', 'Helvetica');
									$options->setIsRemoteEnabled(true);
									$dompdf = new Dompdf($options);
									$dompdf->loadHtml("<h1>". $oggetto."</h1><br>".$corpo);
									$dompdf->setPaper('A4', 'portrait');
									$dompdf->set_option('defaultFont', 'Helvetica');
									$dompdf->render();
									$content = $dompdf->output();
									file_put_contents($tmp_path,$content);
									if (file_exists($tmp_path)) {
										// $timestamp = P7Manager::putTimestamp($tmp_path);
										// if ($timestamp !== false) {
											$salva = new salva();
											$salva->debug = false;
											$salva->codop = $_SESSION["codice_utente"];
											$salva->nome_tabella = "r_partecipanti";
											$salva->operazione = "UPDATE";
											$salva->oggetto = $update;
											$codice_partecipante = $salva->save();
											if ($codice_partecipante != false) {
												?>
												<ul class="success">
													<?= $msg ?>
												</ul>
												<?

												$corpo.= "Distinti Saluti<br><br>";
												$indirizzi = array();
												$indirizzi[]  = $_SESSION["codice_utente"];
												
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

												if ($esito === true) {
													$pec_conferma = getIndirizzoConferma($record_gara["codice_pec"]);

													$mailer = new Communicator();
													$mailer->oggetto = $oggetto;
													$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
													$mailer->codice_pec = -1;
													$mailer->destinatari = $pec_conferma;
													$mailer->sezione = "gara";
													$mailer->codice_gara = $record_gara["codice"];
													$mailer->type = 'comunicazione-gara';
													$esito = $mailer->send();
													?>
													<ul class="success">
						                <li><?= traduci('la partecipazione e stata ricevuta con successo') ?>
					                    	<br>
																<?= traduci('partecipazione-conferma-pec') ?>
					                    </li>
					                </ul>
													<?
												} else {
													?>
													<ul class="success">
														<li><?= traduci('la partecipazione e stata ricevuta con successo') ?><br>
																<strong class="ui-state-error"><?= traduci('partecipazione-conferma-no-pec') ?></strong>
															</li>
													</ul>
													<?
												}
											} else {
												$error = true;
												?>
												<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?> - ERROR 10</h3>
												<?
											}
										/* } else {
											$error = true;
											?>
											<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?> - ERROR 9</h3>
											<?
										} */
									} else {
										$error = true;
										?>
										<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?> - ERROR 8</h3>
										<?
									}
								} else {
										$error = true;
									?>
									<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?> - ERROR 7</h3>
									<ul>
										<?= $msg ?>
									</ul>
									<?
								}
							} else {
								$error = true;
								?>
									<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?> - ERROR 0</h3>
								<?
							}
						} else {
							$error = true;
							?>
								<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?>: <?= ("Partecipazione già inviata") ?></h3>
							<?
						}
					} else {
						$error = true;
						?>
							<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?>: <?= traduci("Documentazione non caricata") ?></h3>
						<?
					}
				} else {
					$error = true;
					?>
						<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?>: <?= traduci("Termini scaduti") ?></h3>
					<?
				}
			} else {
				$error = true;
				?>
				<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?> - ERROR 1</h3>
				<?
			}
		} else {
			$error = true;
			?>
			<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?> - ERROR 2</h3>
			<?
		}
		?>
		<a class="submit_big" style="background-color:#444"  href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=".$codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
		<?
	} else {
		?>
		<h3 class="ui-state-error"><?= traduci("Impossibile accedere") ?> - ERROR 3</h3>
		<?
	}
	include_once($root."/layout/bottom.php");
	?>
