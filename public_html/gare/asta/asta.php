<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	$disable_alert_sessione = true;
	include_once($root."/layout/top.php");
	include_once($root."/inc/p7m.class.php");
	include($root."/inc/pdftotext.phpclass");
	$public = true;
		if ((isset($_GET["cod"]) || isset($_POST["cod"]))&& is_operatore()) {
			if (isset($_POST["cod"])) $_GET["cod"] = $_POST["cod"];
			$codice = $_GET["cod"];
			$bind = array(":codice"=>$codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
			$strsql  = "SELECT b_gare.* FROM b_gare WHERE b_gare.codice = :codice
									AND b_gare.annullata = 'N' AND b_gare.data_scadenza < now()
									AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
									AND (pubblica = '2' OR pubblica = '1')";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$accedi = false;
			if ($risultato->rowCount() > 0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$bind = array(":codice"=>$record_gara["codice"],":codice_utente"=>$_SESSION["codice_utente"]);
				$strsql = "SELECT r_partecipanti.* FROM r_partecipanti
									 JOIN b_aste ON b_aste.codice_gara = r_partecipanti.codice_gara
									 AND b_aste.codice_lotto = r_partecipanti.codice_lotto
									 WHERE b_aste.codice_gara = :codice AND (r_partecipanti.conferma = TRUE or r_partecipanti.conferma IS NULL)
									 AND ammesso = 'S' AND escluso = 'N' AND data_fine > now() AND data_inizio <= now()
									 AND codice_utente = :codice_utente ";
				$ris = $pdo->bindAndExec($strsql,$bind);
				if ($ris->rowCount()>0) {
					$accedi = true;
					$partecipante = $ris->fetch(PDO::FETCH_ASSOC);
				}
			}
			if ($accedi) {
			$record_gara["tipologie_gara"] = "";
			$versionFolder = "old";
			if ($record_gara["nuovaOfferta"] == "S") $versionFolder = "new";
			$bind = array(":tipologia"=>$record_gara["tipologia"]);
			$sql = "SELECT tipologia FROM b_tipologie WHERE b_tipologie.codice = :tipologia";
			$ris_tipologie = $pdo->bindAndExec($sql,$bind);
			if ($ris_tipologie->rowCount()>0) {
					$rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC);
					$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
			}

	    $bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql_tipo = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 40)";
			$ris_tipo = $pdo->bindAndExec($sql_tipo,$bind);
			$rialzo = false;
			if ($ris_tipo->rowCount() > 0) {
				$opzione = $ris_tipo->fetch(PDO::FETCH_ASSOC);
				if ($opzione["opzione"] == "270") $rialzo = true;
			}
		?>
		<h1>ASTA GARA - ID <? echo $record_gara["id"] ?></h1>
		<h2><strong><? echo $record_gara["tipologie_gara"] ?></strong> - <? echo $record_gara["oggetto"] ?></h2>
		<?
		$errore_validazione = false;
		if (isset($pdo) && (isset($_POST["offerta"]) || isset($_POST["filechunk"]))) {
		  $codice_lotto = $_POST["codice_lotto"];
		  $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);
		  $sql_asta = "SELECT * FROM b_aste WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now()";
		  $ris_asta = $pdo->bindAndExec($sql_asta,$bind);
		  if ($ris_asta->rowCount()===1) {

				if ($codice_lotto==0) {
		      $bind = array(":codice_gara"=>$record_gara["codice"]);
		      $sql = "SELECT sum(b_importi_gara.importo_base) AS importo_base, ";
		      $sql.= " sum(b_importi_gara.importo_oneri_ribasso) AS importo_oneri_ribasso, ";
		      $sql.= " sum(b_importi_gara.importo_oneri_no_ribasso) AS importo_oneri_no_ribasso, ";
		      $sql.= " sum(b_importi_gara.importo_personale) AS importo_personale ";
		      $sql.= " FROM b_importi_gara WHERE codice_gara = :codice_gara";
		      $ris_importi = $pdo->bindAndExec($sql,$bind);
		      if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
		    } else {
		      $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);
		      $sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto";
		      $ris_importi = $pdo->bindAndExec($sql,$bind);
		      if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
		    }

				$asta = $ris_asta->fetch(PDO::FETCH_ASSOC);
		    $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);
		    $sql_base = "SELECT MAX(timestamp) AS last_valida
		                 FROM b_offerte_economiche_asta
		                 WHERE codice_gara = :codice_gara
		                 AND codice_lotto = :codice_lotto
		                 AND stato = 1
		                 GROUP BY codice_gara, codice_lotto ";
		    $ris_base = $pdo->bindAndExec($sql_base,$bind);
		    $scaduta = false;
		    if ($ris_base->rowCount()>0) {
		        $tempo_base = $ris_base->fetch(PDO::FETCH_ASSOC);
		        $last_valida = strtotime($tempo_base["last_valida"]);
		        $last_valida = abs(time() - $last_valida);
		        if ($last_valida > ($asta["tempo_base"]*60)) $scaduta = true;
		      }
		      /*
		      CONTROLLO 5 MINUTI SCADENZA ELIMINATO A SEGUITO DI ABROGAZIONE DEL DPR.207/2010
		      $bind[":codice_partecipante"] = $partecipante["codice"];
		      $sql_last = "SELECT MAX(timestamp) AS last FROM b_offerte_economiche_asta WHERE codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND stato = 1";
		      $ris_last = $pdo->bindAndExec($sql_last,$bind);
		      if ($ris_last->rowCount()>0) {
		        $tempo_last = $ris_last->fetch(PDO::FETCH_ASSOC);
		        $last_offerta = strtotime($tempo_last["last"]);
		        $last_offerta = abs(strtotime($asta["data_fine"]) - $last_offerta) / 60;
		        if ($last_offerta < 5) $scaduta = true;
		      }
		      */
		      if (!$scaduta) {
		        $totale_offerta = 0;
		        $offerta_temporale = 0;
		        $elenco_prezzi = false;
		        if (!isset($_POST["filechunk"])) {
							$create_pdf = false;
							include("{$versionFolder}/save.php");
							if ($create_pdf) {
								if (isset($vocabolario["#tabella#"])) {
									if ($codice_lotto > 0) {
	                  $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);
	                  $sql_lotto = "SELECT * FROM b_lotti WHERE codice = :codice_lotto AND codice_gara = :codice_gara";
	                  $ris_lotto = $pdo->bindAndExec($sql_lotto,$bind);
	                  if ($ris_lotto->rowCount()>0) {
	                    $lotto_descrittivo = $ris_lotto->fetch(PDO::FETCH_ASSOC);
	                    $record_gara["oggetto"] .= " - Lotto: " . $lotto_descrittivo["oggetto"];
	                    $record_gara["cig"] = $lotto_descrittivo["cig"];
	                    $record_gara["prezzoBase"] = $lotto_descrittivo["importo_base"] + $lotto_descrittivo["importo_oneri_no_ribasso"]; // + $lotto_descrittivo["importo_oneri_ribasso"] +  + $lotto_descrittivo["importo_personale"];
	                  }
	                }
	                $record_gara["prezzoBase"] = "&euro; " . number_format($record_gara["prezzoBase"],2,",",".");
	                $vocabolario["#ragione-sociale#"] = "";
	                $bind = array(":codice_utente"=>$_SESSION["codice_utente"]);
	                $sql_ope = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
	                $ris_ope = $pdo->bindAndExec($sql_ope,$bind);
	                if ($ris_ope->rowCount()>0) {
	                  $ope = $ris_ope->fetch(PDO::FETCH_ASSOC);
	                  if (!empty($ope["ragione_sociale"])) {
	                    $vocabolario["#ragione-sociale#"] = $ope["ragione_sociale"];
	                  } else {
	                    $vocabolario["#ragione-sociale#"] = $_SESSION["record_utente"]["cognome"] . " " . $_SESSION["record_utente"]["nome"];
	                  }
	                }
	                $chiavi = array_keys($record_gara);
	                foreach($chiavi as $chiave) {
	                  $vocabolario["#record_gara-".$chiave."#"] = $record_gara[$chiave];
	                }
	                $vocabolario["#elenco_operatori#"] = "";
	                $bind = array(":codice_utente"=>$_SESSION["codice_utente"],":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);
	                $sql_capo = "SELECT * FROM r_partecipanti WHERE codice_utente = :codice_utente
															 AND codice_lotto = :codice_lotto AND codice_gara = :codice_gara";
	                $ris_capo = $pdo->bindAndExec($sql_capo,$bind);
	                if ($ris_capo->rowCount()) {
	                  $rec_capo = $ris_capo->fetch(PDO::FETCH_ASSOC);
	                  $sql_g = "SELECT * FROM r_partecipanti WHERE codice_capogruppo = :codice_capogruppo";
	                  $ris_g = $pdo->bindAndExec($sql_g,array(":codice_capogruppo"=>$rec_capo["codice"]));
	                  if ($ris_g->rowCount()) {
	                    $vocabolario["#elenco_operatori#"] .= "<strong>Membri del raggruppamento</strong><br><br>
	                    <table><tr style=\"font-weight:bold\"><td>" . $rec_capo["partita_iva"] . "</td><td>" . $rec_capo["ragione_sociale"] . "</td><td>" . $rec_capo["tipo"] . "</td></tr>";
	                    while($record_m=$ris_g->fetch(PDO::FETCH_ASSOC)) {
	                      $vocabolario["#elenco_operatori#"] .= "<tr><td>" . $record_m["partita_iva"] . "</td><td>" . $record_m["ragione_sociale"] . "</td><td>" . $record_m["tipo"] . "</td></tr>";
	                    }
	                    $vocabolario["#elenco_operatori#"] .= "</table>";
	                  }
	                }
	                $modello["corpo"] = "#tabella#";
	                $sql_modello = "SELECT * FROM b_modelli_standard WHERE codice = 5"; // Codice 5 - Offerta economica
	                $ris_modello = $pdo->query($sql_modello);
	                if ($ris_modello->rowCount()>0) {
	                  $modello = $ris_modello->fetch(PDO::FETCH_ASSOC);
	                  $bind = array(":codice_modello"=>$modello["codice"],$_SESSION["ente"]["codice"]);
	                  $sql = "SELECT * FROM b_modelli_enti WHERE attivo = 'S' AND codice_modello = :codice_modello AND codice_ente = :codice_ente";
	                  $ris = $pdo->bindAndExec($sql,$bind);
	                  if ($ris->rowCount()>0) {
	                    $modello = $ris->fetch(PDO::FETCH_ASSOC);
	                  }
	                }
	                $html = strtr($modello["corpo"],$vocabolario);
									$html = "<style>
	                          table, th, td {
	                            border: 1px solid grey;
	                          }
	                          </style>" . $html;
	                $options = new Options();
	                $options->set('defaultFont', 'Helvetica');
	                $options->setIsRemoteEnabled(true);
	                $dompdf = new Dompdf($options);
	                $dompdf->loadHtml($html);
	                $dompdf->setPaper('A4', 'portrait');
	                $dompdf->set_option('defaultFont', 'Helvetica');
	                $dompdf->render();
	                $content = $dompdf->output();
	                file_put_contents($config["chunk_folder"] . "/" . $codice_offerta . "_rilancio_offerta.pdf",$content);
	                $offerFile = file_get_contents($config["chunk_folder"] . "/" . $codice_offerta . "_rilancio_offerta.pdf");
	                $contentForHash = new PdfToText($config["chunk_folder"] . "/" . $codice_offerta . "_rilancio_offerta.pdf");
	                $contentForHash =  $contentForHash->Text;
	                $contentForHash = preg_replace("/[^a-zA-Z0-9]/", '', $contentForHash);
	                $cryptedContent = openssl_encrypt($offerFile,$config["crypt_alg"],md5($rec_capo["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
	                $_SESSION["offerFile"][$rec_capo["codice"]] = $offerFile;
	                unlink($config["chunk_folder"] . "/" . $codice_offerta . "_rilancio_offerta.pdf");
	                $md5["md5"] = hash("md5",$offerFile);
	                $md5["sha1"] = hash("sha1",$offerFile);
	                $md5["sha256"] = hash("sha256",$offerFile);
	                $md5["shaContent"] = hash("sha256",$contentForHash);
	                $md5["cryptedContent"] = $cryptedContent;
	                $_SESSION["data_offerta_economica"] = $vocabolario["#tabella#"];
	                $md5["codice"] = $codice_offerta;
	                $salva = new salva();
	                $salva->debug = false;
	                $salva->codop = $_SESSION["codice_utente"];
	                $salva->nome_tabella = "b_offerte_economiche_asta";
	                $salva->operazione = "UPDATE";
	                $salva->oggetto = $md5;
	                $codice_offerta = $salva->save();
	                $msg = "<li>Offerta validata</li>";
	                $hide_upload = true;
								}
							} else {
								  $msg = "<li>Errore nella procedura di verifica</li>";
							}
						} else {
							$procedi = false;
							include("{$versionFolder}/verify.php");
							if ($procedi) {
								$msg = "";
								$p7m = new P7Manager($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);
								$md5_file = $p7m->getHash('md5');
								if ($md5_file == $_POST["md5_file"]) {
									$msg .= "<li>File integro</li>";
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
										$bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_utente"=>$_SESSION["codice_utente"]);
										$strsql = "SELECT * FROM b_offerte_economiche_asta
															 WHERE stato = 0 AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto
															 AND utente_modifica = :codice_utente";
										$ris_offerta = $pdo->bindAndExec($strsql,$bind);
										if ($ris_offerta->rowCount()>0) {
											$rec_offerta = $ris_offerta->fetch(PDO::FETCH_ASSOC);
											$hashVerify = array();
											if ($rec_offerta["md5"] != "") $hashVerify[] = $p7m->find($rec_offerta["md5"],"md5");
											if ($rec_offerta["sha1"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha1"],"sha1");
											if ($rec_offerta["sha256"] != "") $hashVerify[] = $p7m->find($rec_offerta["sha256"],"sha256");
											$found = false;
											if (count($hashVerify) > 0 && in_array(false,$hashVerify)!==false) {
												if ($p7m->find($rec_offerta["shaContent"],"sha256",true)) {
													$found = true;
												}
											} else {
												$found = true;
											}
											if ($found) {
												$msg .= "<li>Offerta verificata</li>";
												$destinationPath = $config["doc_folder"] ."/" . $record_gara["codice"] . "/" . $codice_lotto . "/asta/";
												if (!is_dir($destinationPath)) mkdir($destinationPath,0770,true);
												$esito_salvataggio = $p7m->encryptAndSave($partecipante["codice"],$destinationPath,"rilancio_" . $rec_offerta["codice"],true);
												if ($esito_salvataggio === true) {
													$msg .= "<li>Criptazione effettuata con successo</li>";
													$codice_gara = $record_gara["codice"];
													$_POST["codice_gara"] = $codice_gara;
													$codice_offerta = $rec_offerta["codice"];
													$sql_criterio = "SELECT * FROM b_criteri WHERE codice = :codice_criterio";
													$ris_criterio = $pdo->bindAndExec($sql_criterio,array(":codice_criterio"=>$record_gara["criterio"]));
													if ($ris_criterio->rowCount()===1) {
														$directory=$ris_criterio->fetch(PDO::FETCH_ASSOC);
														if (file_exists($versionFolder."/".$directory["directory"]."/punteggi.php")) {
															$bind = array(":codice_gara"=>$codice_gara,":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$partecipante["codice"]);
															$sql_stato = "UPDATE b_offerte_economiche_asta SET stato = 98 WHERE stato = 1 AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_partecipante = :codice_partecipante";
															$ris_delete = $pdo->bindAndExec($sql_stato,$bind);
															$sql_stato = "UPDATE b_offerte_economiche_asta SET stato = 1 WHERE codice = :codice";
															$ris_stato = $pdo->bindAndExec($sql_stato,array(":codice"=>$rec_offerta["codice"]));
															include($versionFolder."/".$directory["directory"]."/punteggi.php");
															$asta_telematica = true;
															include($root."/gare/agg_provvisoria/".$directory["directory"]."/calcolo.php");
															?>
															<ul id="messaggio_conferma" class="success">
																<div style="text-align:right">
																	<a style="color:#000; margin-right:5px;" onClick="$('#messaggio_conferma').remove();"><img src="/img/del.png" alt="Chiudi" style="width:25px; vertical-align:middle"> Chiudi</a>
																</div>
																<? echo $msg ?>
															</ul>
															<?
														} else {
															?>
															<h3 class="ui-state-error">Errore nell'aggiornamento dei punteggi - 2</h3>
															<?
														}
													} else {
														?>
														<h3 class="ui-state-error">Errore nell'aggiornamento dei punteggi - 1</h3>
														<?
													}
												} else {
													?>
														<h3 class="ui-state-error">Errore nella criptazione del file</h3>
													<?
												}
											} else {
												?>
												<h3 class="ui-state-error">Impossibile trovare la copia firmata digitalmente del file d'offerta generato dal sistema - <strong>Attenzione: Non aprire l'offerta direttamente dal browser ma seleziona il file originale dalla cartella di download</strong></h3>
												<?
											}
										} else {
											?>
											<h3 class="ui-state-error">Offerta non generata</h3>
											<?
										}
									} else {
										?>
										<h3 class="ui-state-error">Firma non valida</h3>
										<?
									}
								} else {
									?>
									<h3 class="ui-state-error">Errore nella procedura di upload</h3>
									<?
								}
							} else {
								?>
								<h3 class="ui-state-error">L'offerta è stata superata</h3>
								<?
							}
						}
					}
				}
			}

		$bind = array(":codice_gara"=>$record_gara["codice"],":codice_utente"=>$_SESSION["codice_utente"]);
		$sql_lotti = "SELECT b_lotti.* FROM b_lotti JOIN b_aste ON b_lotti.codice = b_aste.codice_lotto AND b_lotti.codice_gara = b_aste.codice_gara
									JOIN r_partecipanti ON b_lotti.codice_gara = r_partecipanti.codice_gara AND b_lotti.codice = r_partecipanti.codice_lotto
									WHERE b_aste.data_inizio <= now() AND b_aste.data_fine > now() AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N' AND r_partecipanti.codice_gara = :codice_gara
									AND r_partecipanti.codice_utente = :codice_utente";
		$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
		$print_form = false;
		if ($ris_lotti->rowCount() > 0) {
			if (isset($_GET["lotto"]) || isset($codice_lotto)) {
				if (isset($_GET["lotto"]) && !isset($codice_lotto)) $codice_lotto = $_GET["lotto"];
				$bind[":codice_lotto"] = $codice_lotto;
				$sql_lotto = "SELECT b_lotti.* FROM b_lotti JOIN b_aste ON b_lotti.codice = b_aste.codice_lotto AND b_lotti.codice_gara = b_aste.codice_gara
											JOIN r_partecipanti ON b_lotti.codice_gara = r_partecipanti.codice_gara AND b_lotti.codice = r_partecipanti.codice_lotto
											WHERE b_aste.data_inizio <= now() AND b_aste.data_fine > now() AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N'
											AND r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_capogruppo = 0 AND r_partecipanti.codice_utente = :codice_utente
											AND r_partecipanti.codice_lotto = :codice_lotto";
				$ris_lotto = $pdo->bindAndExec($sql_lotto,$bind);
				if ($ris_lotto->rowCount() > 0) {
					$lotto = $ris_lotto->fetch(PDO::FETCH_ASSOC);
					$print_form = true;
					$codice_lotto = $lotto["codice"];
					echo "<div class=\"box\"><h3>" . $lotto["oggetto"] . "</h3>";
					echo $lotto["descrizione"]."</div>";
				}
			} else {
				?>
				<div class="box">
					<h3>Lotti di gara attivi</h3>
					<table width="100%">
					<?
					$choice_select = true;
					while ($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {	?>
					<tr>
						<td><strong><? echo $lotto["oggetto"] ?></strong><br>
							<? echo $lotto["descrizione"] ?>
						</td>
						<td width="20%">
							<a href="/gare/asta/asta.php?cod=<? echo $record_gara["codice"] ?>&lotto=<? echo $lotto["codice"] ?>" class="submit_big" title="Partecipa">Partecipa</a>
						</td>
					</tr>
					<? } ?>
					</table>
				</div><?
			}
		} else {
			$print_form = true;
			$codice_lotto = 0;
		}
		if ($print_form) {
			$bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);
			$sql_asta = "SELECT * FROM b_aste WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now()";
			$ris_asta = $pdo->bindAndExec($sql_asta,$bind);
			if ($ris_asta->rowCount()===1) {
				$asta = $ris_asta->fetch(PDO::FETCH_ASSOC);
				$sql_base = "SELECT MAX(timestamp) AS last_valida FROM b_offerte_economiche_asta WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND stato = 1
				GROUP BY codice_gara, codice_lotto ";
				$ris_base = $pdo->bindAndExec($sql_base,$bind);
				$scaduta = false;
				if ($ris_base->rowCount()>0) {
					$tempo_base = $ris_base->fetch(PDO::FETCH_ASSOC);
					$last_valida = strtotime($tempo_base["last_valida"]);
					$last_valida = abs(time() - $last_valida);
					if ($last_valida > ($asta["tempo_base"]*60)) {
						$scaduta = true;
						$limit_base = true;
					}
				}
				/*
				CONTROLLO 5 MINUTI SCADENZA ELIMINATO A SEGUITO DI ABROGAZIONE DEL DPR.207/2010
				$bind[":codice_partecipante"] = $partecipante["codice"];
				$sql_last = "SELECT MAX(timestamp) AS last FROM b_offerte_economiche_asta WHERE codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND stato = 1";
				$ris_last = $pdo->bindAndExec($sql_last,$bind);
				if ($ris_last->rowCount()>0) {
					$tempo_last = $ris_last->fetch(PDO::FETCH_ASSOC);
					$last_offerta = strtotime($tempo_last["last"]);
					$last_offerta = abs(strtotime($asta["data_fine"]) - $last_offerta) / 60;
					if ($last_offerta < 5) {
						 $scaduta = true;
						 $last = true;
					}
				}
				*/
				if (!$scaduta) {
					$avviso_scadenza = false;
					$minuti_residui = abs(strtotime($asta["data_fine"]) - time()) / 60;
					// if ($minuti_residui < 5) $avviso_scadenza = true; CONTROLLO 5 MINUTI SCADENZA ELIMINATO A SEGUITO DI ABROGAZIONE DEL DPR.207/2010
				?>
					<div style="width:70%; float:left">
						<!--
					  <div id="avviso_scadenza" <? if (!$avviso_scadenza) echo "style='display:none'" ?>>
					    <h2 style="color:#f00">Ai sensi del DPR n. 207 art. 292 c. 3 del 05/10/2010 &egrave; possibile effettuare solo un ultimo rilancio</h2>
					  </div> -->
					  <form id="form_rilancio" action="asta.php" method="post" target="_self" rel="validate">
					    <input type="hidden" name="cod" value="<? echo $record_gara["codice"] ?>">
					    <input type="hidden" name="codice_lotto" value="<? echo $asta["codice_lotto"] ?>">
					    <?
					      $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_utente"=>$_SESSION["codice_utente"]);

					      $sql_offerta = "SELECT b_offerte_economiche_asta.* FROM b_offerte_economiche_asta JOIN r_partecipanti ON b_offerte_economiche_asta.codice_partecipante = r_partecipanti.codice
																WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_lotto = :codice_lotto AND r_partecipanti.codice_gara = :codice_gara
																AND b_offerte_economiche_asta.stato = 0 ";
					      $ris_offerta = $pdo->bindAndExec($sql_offerta,$bind);
					      if ($ris_offerta->rowCount()>0 && (isset($_POST["form_economica"])) && isset($codice_offerta)) {
					        $form_upload = true;
					      } else {
					        $form_upload = false;
					      }
					    if ($form_upload) {
					      ?>
					      <script type="text/javascript" src="/js/spark-md5.min.js"></script>
					      <script type="text/javascript" src="/js/resumable.js"></script>
					      <script type="text/javascript" src="resumable-uploader.js"></script>
					      <ul class="success">
					        <? echo $msg ?>
					      </ul>
					      <button class="submit_big" style="background-color:#0C0" onclick="$('.scegli_file').slideDown();$('#istruzioni').slideDown();window.location = 'download_offerta.php?codice_offerta=<? echo $codice_offerta  ?>';return false;">
					        Scarica l'offerta economica
					      </button>

					      <h2 id="istruzioni" style="text-align:center; display:none"><strong><img src="/img/ok.png" style="vertical-align: middle" alt="Avviso"> Firma e carica il file scaricato</strong></h2>
					      <input type="hidden" name="md5_file" id="md5_file" title="File" rel="S;0;0;A">
					        <input type="hidden" id="filechunk" name="filechunk">
					        <div style='display:none' class="scegli_file"><img src="/img/folder.png" style="vertical-align:middle"><br>Carica il file firmato</div>
					        <script>
					          var uploader = (function($){
					          return (new ResumableUploader($('.scegli_file')));
					          })(jQuery);
					        </script>
					        <div id="progress_bar" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
					        <input type="submit" <? if ($form_upload) { ?> style="display: none;"<? } ?> class="submit_big" value="Invia" id="invio" onClick="if (confirm('Questa operazione sostituir&agrave; eventuali istanze precedenti. Vuoi continuare?')) { $('#wait').hide(); uploader.resumable.upload(); } return false;">
					        <a class="submit_big" style="background-color:#C00" onclick="if (confirm('Sei sicuro di voler tornare all\'elaborazione dell\'offerta?')) { window.location.href='asta.php?cod=<?= $record_gara["codice"] ?>&lotto=<?= $codice_lotto ?>' }" title="Rielabora offerta">Rielabora Offerta</a>
					      <?
					    } else {
								include("{$versionFolder}/form.php");
								?>
								<input type="hidden" name="form_economica" value="True">
								<input type="submit" class="submit_big" value="Valida offerta">
								<?
							}
							?>
			  		</form>
					</div>
					<div style="width:29%; float:right;">
						<script type="text/javascript" src="/js/jquery.plugin.min.js"></script>
						<script type="text/javascript" src="/js/jquery.countdown.min.js"></script>
						<script type="text/javascript" src="/js/jquery.countdown-it.js"></script>
						<div class="box">
							<h2>Ora di sistema</h2>
							<h3 id="clock" style="text-align:center">
							<?
							$now = new DateTime();
							$legal = date("I");
						  echo $now->format("d/m/Y H:i:s");
							?></h3>
						</div>
						<div class="box">
						<h2>Scadenza ufficiale</h2>
						<div style="text-align:center"><?= mysql2completedate($asta["data_fine"]) ?></div>
						<div id="timing" class="countdown"></div>
						<div class="clear"></div>
						</div>
						<?
							$timing_asta = explode(" ",$asta["data_fine"]);
							$timing_asta[0] = explode("-",$timing_asta[0]);
							$timing_asta[1] = explode(":",$timing_asta[1]);
						?>
						<script>
						function serverTime() {
							var time = null;
							$.ajax({url: '/serverTime.php',
								async: false, dataType: 'text',
								success: function(text) {
									time = new Date(text);
								}, error: function(http, message, exc) {
									time = new Date();
								}});
							return time;
						}
						function serverTimeBase() {
							var time = null;
							$.ajax({url: '/serverTime.php?tempo_base=1',
								async: false, dataType: 'text',
								success: function(text) {
									time = new Date(text);
								}, error: function(http, message, exc) {
									time = new Date();
								}});
							return time;
						}
							$("#timing").countdown({
								until: $.countdown.UTCDate(<? if ($legal == 0) { echo "+1,"; } else { echo "+2,"; } ?><?= $timing_asta[0][0] ?>,<?= $timing_asta[0][1] ?> - 1, <?= $timing_asta[0][2] ?>,<?= $timing_asta[1][0] ?>,<?= $timing_asta[1][1] ?>,0),
								serverSync: serverTime,
								padZeroes: true
							});
						</script>
						<div class="box">
						<h2>Limite tempo base</h2>
						<div id="timing_base" class="countdown"></div>
						<?
						$bind = array();
						$bind[":codice_gara"] = $record_gara["codice"];
						$bind[":codice_lotto"] = $codice_lotto;
						$sql_base = "SELECT MAX(timestamp) AS last_valida FROM b_offerte_economiche_asta
													WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND stato = 1
													GROUP BY codice_gara, codice_lotto ";
						$ris_base = $pdo->bindAndExec($sql_base,$bind);
						if ($ris_base->rowCount()>0) {
							$tempo_base = $ris_base->fetch(PDO::FETCH_ASSOC);
							$last_valida = strtotime($tempo_base["last_valida"]);
							$_SESSION["last_valida"] = $last_valida;
							if (($asta["tempo_base"]*60 - (time() - $last_valida))<($minuti_residui*60)) {
								$seconds_left =  $asta["tempo_base"]*60 - abs(time() - $last_valida);
								$scadenza_tempo_base = date('Y-m-d H:i:s', time() + $seconds_left);
								$scadenza_tempo_base = explode(" ",$scadenza_tempo_base);
								$scadenza_tempo_base[0] = explode("-",$scadenza_tempo_base[0]);
								$scadenza_tempo_base[1] = explode(":",$scadenza_tempo_base[1]);
								$legal = date("I");
						?>
						<script>
							$("#timing_base").countdown({
								until: $.countdown.UTCDate(<? if ($legal == 0) { echo "+1,"; } else { echo "+2,"; } ?><?= $scadenza_tempo_base[0][0] ?>,<?= $scadenza_tempo_base[0][1] ?> - 1, <?= $scadenza_tempo_base[0][2] ?>,<?= $scadenza_tempo_base[1][0] ?>,<?= $scadenza_tempo_base[1][1] ?>,<?= $scadenza_tempo_base[1][2] ?>),
								serverSync: serverTimeBase,
								padZeroes: true
							});
						</script>
						<? }
						} ?>
						<div class="clear"></div>
						</div>
						<div class="box">
						<h3>Riepilogo</h3>
						<table width="100%">
							<tr><td class="etichetta">Tempo base</td><td><strong><?= $asta["tempo_base"] ?> min.</strong></td></tr>
							<tr><td class="etichetta">Rilancio % minimo</td><td><strong><?= $asta["rilancio_minimo"] ?> %</strong></td></tr>
						</table>
						</div>
						<div id="log_asta" class="box">
							<?
								$codice_gara = $asta["codice_gara"];
								$codice_lotto = $asta["codice_lotto"];
								include("log.php");
							?>
						</div>
						<script>
						$.ajaxSetup ({
				    // Disable caching of AJAX responses */
				    cache: false
						});
						var ajaxDelay = 3000;
									setInterval(function(){
										$.ajax({
											url: 'log.php',
											dataType: 'html',
											method: 'get',
											async: "true",
											data: "codice_gara=<?= $asta["codice_gara"] ?>&codice_lotto=<?= $asta["codice_lotto"] ?>",
											success: function(script) {
												$("#log_asta").html(script);
											}
										});
								}, ajaxDelay);
								var clockDelay = 1000;
											setInterval(function(){
												$.ajax({
													url: '/serverTime.php',
													dataType: 'html',
													method: 'get',
													data: "format=1",
													async: "true",
													success: function(script) {
														$("#clock").html(script);
													}
												});
												$.countdown.resync();
										}, clockDelay);
						</script>
					</div>
					<div class="clear"></div>
				<?
			} else {
				// if (isset($limit_base)) {
					echo "<h2 style=\"color:#F00\">Gara scaduta per raggiungimento limite tempo base</h2>";
				/* } else {
					CONTROLLO 5 MINUTI SCADENZA ELIMINATO A SEGUITO DI ABROGAZIONE DEL DPR.207/2010
					echo "<h2 style=\"color:#F00\">Ultimo rilancio valido effettuato entro cinque minuti dal termine</h2>
								<h3>Impossibile effettuare nuove offerte ai sensi del DPR n. 207 art. 292 c. 3 del 05/10/2010.</h3>";
				} */
			}
			} else {
					echo "<h2>Gara inesistente o privilegi insufficienti</h2>";
				}
			} else if (!isset($choice_select)) {
				echo "<h2>Gara inesistente o privilegi insufficienti</h2>";
			}
    } else {
		echo "<h1>Asta scaduta, Gara inesistente o privilegi insufficienti</h1>";
		}
	} else {

		echo "<h1>Gara inesistente</h1>";

	}
	include_once($root."/layout/bottom.php");
	?>
