<?
	session_start();
	ini_set('max_execution_time', 1200);
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	include_once($root."/inc/p7m.class.php");
	$salva = new salva();

	$edit = false;

	$log = array();
	$log["esito"] = "Negativo";
	$log["ip"] = get_client_ip();
	$log["codice_gara"] = 0;
	$log["codice_partecipante"] = 0;
	$log["codice_busta"] = 0;

	if (isset($_POST["codice_gara"])) $log["codice_gara"] = $_POST["codice_gara"];
	if (isset($_POST["codice_partecipante"])) $log["codice_partecipante"] = $_POST["codice_partecipante"];
	if (isset($_POST["codice_busta"])) $log["codice_busta"] = $_POST["codice_busta"];

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if ($edit) {
			if ($_SESSION["gerarchia"] > 0) {
				$edit = false;
				$sql = "SELECT codice_utente FROM r_permessi_apertura_buste WHERE codice_gara = :codice_gara AND codice_busta = :codice_busta ";
				$ris_perm = $pdo->bindAndExec($sql,[":codice_gara"=>$_POST["codice_gara"],":codice_busta"=>$_POST["codice_busta"]]);
				if ($ris_perm->rowCount() > 0) {
					while($perm = $ris_perm->fetch(PDO::FETCH_ASSOC)) {
						if ($perm["codice_utente"] == $_SESSION["codice_utente"]) {
							$edit = true;
							continue;
						}
					}
				} else {
					$edit = true;
				}
			}
		}
	}
	if ($edit && !$lock) {
		if (isset($_POST["private_key"])) {

			$codice = $_POST["codice_gara"] ;

			$bind = array();
			$bind[":codice"] = $codice;
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
			$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			if ($_SESSION["gerarchia"] > 0) {
				$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
				$strsql .= " AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
			}
			$strsql .= " AND data_apertura <= now() ";
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				if ($_POST["codice_lotto"] != 0) {
					$bind = array();
					$bind[":codice"] = $_POST["codice_lotto"];
					$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice ORDER BY codice";
					$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
					if ($ris_lotti->rowCount()>0) {
						$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
						$lotto = " Lotto: " . $lotto["oggetto"];
					}
				} else {
					$lotto = "";
				}
				$permission = true;

				$bind = array();
				$bind[":codice_gara"] = $_POST["codice_gara"];
				$bind[":codice_lotto"] = $_POST["codice_lotto"];
				$bind[":codice_busta"] = $_POST["codice_busta"];


				$strsql  = "SELECT * FROM b_date_apertura ";
				$strsql .= "WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_busta = :codice_busta ";
				$strsql .= "ORDER BY codice DESC LIMIT 0,1";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$record_data = $risultato->fetch(PDO::FETCH_ASSOC);
					$time = strtotime($record_data["data_apertura"]);
					if ($time > time()) {
						$permission = false;
					}
				}
				if ($permission) {
					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$bind[":codice_lotto"] = $_POST["codice_lotto"];
					$bind[":codice_partecipante"] = $_POST["codice_partecipante"];
					$bind[":codice_busta"] = $_POST["codice_busta"];

					$strsql  = "SELECT b_buste.*, b_criteri_buste.nome,b_criteri_buste.ordinamento,b_criteri_buste.economica, b_criteri_buste.tecnica FROM b_buste JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice ";
					$strsql .= " WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_partecipante = :codice_partecipante	";
					$strsql .= " AND codice_busta = :codice_busta";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$busta = $risultato->fetch(PDO::FETCH_ASSOC);
						if ($busta["aperto"]=="N") {

							$bind = array();
							$bind[":codice_gara"] = $busta["codice_gara"];
							$bind[":codice_lotto"] = $busta["codice_lotto"];
							$bind[":codice_partecipante"] = $busta["codice_partecipante"];
							$bind[":ordinamento"] = $busta["ordinamento"];

							$key = openssl_pkey_get_private($_POST["private_key"]);
							$strsql  = "SELECT b_buste.*, b_criteri_buste.nome, b_criteri_buste.ordinamento, b_criteri_buste.economica, b_criteri_buste.tecnica FROM
													b_buste JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice JOIN r_partecipanti ON b_buste.codice_partecipante = r_partecipanti.codice ";
							$strsql .= " WHERE b_buste.codice_gara = :codice_gara AND b_buste.codice_lotto = :codice_lotto ";
							$strsql .= " AND b_buste.aperto = 'N' AND r_partecipanti.ammesso = 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND ((r_partecipanti.codice < :codice_partecipante
													 AND b_criteri_buste.ordinamento = :ordinamento) OR (b_criteri_buste.ordinamento < :ordinamento))" ;
							$risultato = $pdo->bindAndExec($strsql,$bind);

							$sql = "SELECT codice FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 288 ";
							$inversione = $pdo->bindAndExec($sql,[":codice_gara"=>$busta["codice_gara"]]);

							if ($risultato->rowCount() === 0 || $inversione->rowCount() > 0) {
								$enc_data = file_get_contents($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"]);
								if (@openssl_private_decrypt($busta["salt"],$salt,$key)) {
									$data = openssl_decrypt($enc_data,$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
									$log["esito"] = "Positivo";
									if ($busta["tecnica"] == "S") {
										$bind = array();
										$bind[":codice_gara"] = $busta["codice_gara"];
										$bind[":codice_lotto"] = $busta["codice_lotto"];
										$bind[":codice_partecipante"] = $busta["codice_partecipante"];
										$strsql  = "SELECT * FROM b_offerte_economiche ";
										$strsql .= "WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_partecipante = :codice_partecipante AND tipo = 'tecnica' ";
										$ris_offerta = $pdo->bindAndExec($strsql,$bind);
										if ($ris_offerta->rowCount()>0) {
											$offerta = $ris_offerta->fetch(PDO::FETCH_ASSOC);
											if (@openssl_private_decrypt($offerta["salt"],$salt_offerta,$key)) {
												$bind = array();
												$bind[":codice_offerta"] = $offerta["codice"];
												$sql_dettaglio = "SELECT * FROM b_dettaglio_offerte WHERE codice_offerta = :codice_offerta";
												$ris_dettaglio = $pdo->bindAndExec($sql_dettaglio,$bind);
												if ($ris_dettaglio->rowCount()>0) {
													while ($dettaglio = $ris_dettaglio->fetch(PDO::FETCH_ASSOC)) {
														$decrypt = array();
														$decrypt["codice_partecipante"] = $dettaglio["codice_partecipante"];
														$decrypt["tipo"] = $dettaglio["tipo"];
														$decrypt["codice_dettaglio"] = $dettaglio["codice_dettaglio"];
														$decrypt["offerta"] = openssl_decrypt($dettaglio["offerta"],$config["crypt_alg"],$salt_offerta,OPENSSL_RAW_DATA,$config["enc_salt"]);

														$salva->debug = false;
														$salva->codop = $_SESSION["codice_utente"];
														$salva->nome_tabella = "b_offerte_decriptate";
														$salva->operazione = "INSERT";
														$salva->oggetto = $decrypt;
														$codice_offerta_de = $salva->save();

													}
												}

											}
										}
									}
									if ($busta["economica"] == "S") {
										$bind = array();
										$bind[":codice_gara"] = $busta["codice_gara"];
										$bind[":codice_lotto"] = $busta["codice_lotto"];
										$bind[":codice_partecipante"] = $busta["codice_partecipante"];
										$strsql  = "SELECT * FROM b_offerte_economiche ";
										$strsql .= "WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_partecipante = :codice_partecipante";
										$strsql .= " AND tipo <> 'tecnica' ";
										$ris_offerta = $pdo->bindAndExec($strsql,$bind);
										if ($ris_offerta->rowCount()>0) {
											$offerta = $ris_offerta->fetch(PDO::FETCH_ASSOC);
											if (@openssl_private_decrypt($offerta["salt"],$salt_offerta,$key)) {
												$bind = array();
												$bind[":codice_offerta"] = $offerta["codice"];
												$sql_dettaglio = "SELECT * FROM b_dettaglio_offerte WHERE codice_offerta = :codice_offerta";
												$ris_dettaglio = $pdo->bindAndExec($sql_dettaglio,$bind);
												if ($ris_dettaglio->rowCount()>0) {
													while ($dettaglio = $ris_dettaglio->fetch(PDO::FETCH_ASSOC)) {
														$decrypt = array();
														$decrypt["codice_partecipante"] = $dettaglio["codice_partecipante"];
														$decrypt["tipo"] = $dettaglio["tipo"];
														$decrypt["codice_dettaglio"] = $dettaglio["codice_dettaglio"];
														$decrypt["offerta"] = openssl_decrypt($dettaglio["offerta"],$config["crypt_alg"],$salt_offerta,OPENSSL_RAW_DATA,$config["enc_salt"]);

														$salva->debug = false;
														$salva->codop = $_SESSION["codice_utente"];
														$salva->nome_tabella = "b_offerte_decriptate";
														$salva->operazione = "INSERT";
														$salva->oggetto = $decrypt;
														$codice_offerta_de = $salva->save();

													}
												}

											}
										}
									}
									$oggetto = "Apertura " . $busta["nome"]. " per la gara: " . $record_gara["oggetto"] . $lotto;
									$corpo = "Si comunica l'avvenuta apertura della busta in oggetto<br><br>";
									$corpo.= "Data e ora di apertura: <strong>" . date("d/m/Y H:i:s") . "</strong><br><br>";
									$corpo.= "Distinti Saluti<br><br>";

									$bind = array();
									$bind[":codice_gara"] = $busta["codice_gara"];
									$bind[":codice_partecipante"] = $busta["codice_partecipante"];

									$sql = "SELECT b_utenti.*,b_operatori_economici.codice_fiscale_impresa FROM b_utenti JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
									$sql.= "JOIN r_partecipanti ON b_utenti.codice = r_partecipanti.codice_utente ";
									$sql.= "WHERE r_partecipanti.codice = :codice_partecipante AND r_partecipanti.codice_gara = :codice_gara";

									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) {
										$utente = $ris->fetch(PDO::FETCH_ASSOC);
										$mailer = new Communicator();
										$mailer->oggetto = $oggetto;
										$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
										$mailer->codice_pec = $record_gara["codice_pec"];
										$mailer->comunicazione = true;
										$mailer->coda = true;
										$mailer->sezione = "gara";
										$mailer->codice_gara = $record_gara["codice"];
										$mailer->destinatari = $utente["pec"];
										$esito = $mailer->send();
										// if ($esito !== true) echo "alert(\"Errore nell'invio della comunicazione di apertura all'OE. ".$esito."\")";
									}

									if (!is_dir($config["arch_folder"]."/".$busta["codice_gara"]."/".$busta["codice_lotto"]."/".$busta["nome"])) mkdir($config["arch_folder"]."/".$busta["codice_gara"]."/".$busta["codice_lotto"]."/".$busta["nome"],0777,true);
									$percorso = $config["arch_folder"]."/".$busta["codice_gara"]."/".$busta["codice_lotto"]."/".$busta["nome"];

									$tmp = "{$config["chunk_folder"]}/ab/{$_SESSION["ente"]["codice"]}/{$busta["codice_gara"]}/{$busta["codice_lotto"]}";

									$tmpname = uniqid('', true);

									if(! is_dir($tmp)) { mkdir($tmp, 0777, 1); }
									file_put_contents("{$tmp}/{$tmpname}", $data);
									$mime_type = mime_content_type("{$tmp}/{$tmpname}");
									@unlink("{$tmp}/{$tmpname}");

									$estensione = "p7m";
									if (stripos($mime_type, "pdf")!==false) $estensione = "pdf";

									$riferimento = $utente["codice_fiscale_impresa"]."-".getRealNameFromData($data);
									file_put_contents($percorso."/".$riferimento,$data);
									$p7m = new P7Manager($percorso."/".$riferimento);
									$extracted = $p7m->extractContent();

									$tmpname = uniqid('', true);

									if(! is_dir($tmp)) { mkdir($tmp, 0777, 1); }
									file_put_contents("{$tmp}/{$tmpname}", $extracted);
									$cont_type = mime_content_type("{$tmp}/{$tmpname}");
									@unlink("{$tmp}/{$tmpname}");

									$rar = false;
									if (stripos($cont_type, "rar") !== false) $rar = true;

									$md5_file = md5($data);

									$allegato = array();
									$allegato["codice_gara"] = $busta["codice_gara"];
									$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
									$allegato["cartella"] = $busta["codice_lotto"]."/".$busta["nome"];
									$allegato["nome_file"] = $utente["codice_fiscale_impresa"].".".$estensione;
									$allegato["riferimento"] = $riferimento;
									$allegato["titolo"] = $utente["codice_fiscale_impresa"];
									$allegato["online"] = "N";

									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "b_allegati";
									$salva->operazione = "INSERT";
									$salva->oggetto = $allegato;
									$codice_allegato = $salva->save();

									$bind = array();
									$bind[":codice_allegato"] = $codice_allegato;
									$bind[":codice"] = $busta["codice"];

									$sql = "UPDATE b_buste SET aperto = 'S', codice_allegato = :codice_allegato WHERE codice = :codice";
									$ris = $pdo->bindAndExec($sql,$bind);
									$note = "";
									$class= "";

									$esito = $p7m->checkSignatures();
									$certificati = $p7m->extractSignatures();
									if (trim($esito) != "Verification successful") $class="ui-state-error";
									foreach ($certificati AS $certificato) {
										$data = openssl_x509_parse($certificato,false);
										$validFrom = date('d-m-Y H:i:s', $data['validFrom_time_t']);
										$validTo = date('d-m-Y H:i:s', $data['validTo_time_t']);
										$note .= "<li>";
										if (time()<=$data["validFrom_time_t"] || time()>=$data['validTo_time_t']) {
											$class="ui-state-error";
											$note .=  "<div class='ui-state-error'>";
										} else {
											$note .=  "<div class='success padding'>";
										}

										$note.= "<div style=\"text-align:center\">Hash MD5: " . $md5_file . "</div><br>";
										if ($rar) {
											$note.= "<div style=\"border: 1px solid #ffb800; background-color: #fff298; padding: 10px; text-align:center\">";
											$note.= "<span class=\"fa fa-exclamation-circle fa-3x\"></span><br>";
											$note.= "La documentazione &egrave; in formato RAR, per accedere ai files potrebbe essere necessario aggiornare il software di decompressione ";
											$note.= " reperibile al link <a href=\"https://www.winrar.it/prelievo.php\" target=\"_blank\" title=\"sito esterno\">https://www.winrar.it/prelievo.php</a></div><br>";
										}
										if (isset($data["subject"]["commonName"])) $note .=  "<h1>" . $data["subject"]["commonName"] . "</h1>";
										if (isset($data["subject"]["organizationName"]) && $data["subject"]["organizationName"] != "NON PRESENTE") $note .= "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
										if (isset($data["subject"]["title"])) $note .=  $data["subject"]["title"] . "<br>";
										if (isset($data["issuer"]["organizationName"])) $note .=  "<br>Emesso da:<strong> " . $data["issuer"]["organizationName"] . "</strong>";
										$note .=  "<br><br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";

										if (!empty($_POST["codice_lotto"])) {
											$sql_checko = "SELECT b_lotti.cig, b_lotti.oggetto, b_buste.*
																		 FROM b_buste JOIN b_lotti ON b_buste.codice_lotto = b_lotti.codice
																		 WHERE b_buste.codice_busta = :codice_busta
																		 AND b_buste.codice_gara = :codice_gara
																		 AND b_buste.codice_lotto <> :codice_lotto
																		 AND b_buste.utente_modifica = :codice_utente
																		 AND b_buste.sha256 = :sha256
																		 AND b_buste.aperto = 'S' ";
											$bindo = array();
											$bindo[":codice_gara"] = $_POST["codice_gara"];
											$bindo[":codice_lotto"] = $_POST["codice_lotto"];
											$bindo[":codice_utente"] = $utente["codice"];
											$bindo[":codice_busta"] = $_POST["codice_busta"];
											$bindo[":sha256"] = $busta["sha256"];
											$ris_checko = $pdo->bindAndExec($sql_checko,$bindo);
											if ($ris_checko->rowCount() > 0) {
												$note.= "<br><br><div class=\"selezionato\" style=\"font-weight:normal\">";
													$note.= "<div style=\"padding:5px\"><div style=\"text-align:center;\">Busta gi&agrave; aperta nei lotti</div>";
													while($openl = $ris_checko->fetch(PDO::FETCH_ASSOC)) {
														$note .= "<br><strong>CIG:" . $openl["cig"] . "</strong> - <span style=\"font-size:0.8em\">".$openl["oggetto"]."</span><hr>";
													}
													$note.= "</div>";
												$note.= "</div>";
											}
										}

										$note .=  "</div>";
									}
									if ($note != "") $note = "<ul class='firme'>" . $note . "</ul>";
									$cella="<a href=\"/allegati/download_allegato.php?codice=" . $codice_allegato . "\" title=\"Download Allegato\">";
                  $cella.="<img src=\"/img/download.png\" alt=\"Download Allegato\" width=\"25\"></a>&nbsp;";
                  if ($estensione=="p7m") {
										$cella.="<a href=\"/allegati/open_p7m.php?codice=". $codice_allegato ."\" title=\"Estrai Contenuto\">";
                  	$cella.="<img src=\"/img/p7m.png\" alt=\"Download Allegato\" width=\"25\"></a>";
									}
                  $cella.="<br>Busta aperta";
									?>
									$("#<? echo $busta["codice_partecipante"] . "_" . $busta["codice_busta"] ?>").html('<? echo $cella ?>');
									$("#emendamento_<? echo $busta["codice_partecipante"] . "_" . $busta["codice_busta"] ?>").show();
									$("<div><? echo addslashes($note) ?></div>").dialog({
												dialogClass: '<? echo $class ?>',
			            						buttons: {
			            									"Download Allegato": function(){ window.location.href = '/allegati/download_allegato.php?codice=<? echo $codice_allegato ?>';},
			            									<? if ($estensione=="p7m") { ?>"Estrai contenuto": function(){ window.location.href = '/allegati/open_p7m.php?codice=<? echo $codice_allegato ?>';},<? } ?>
			            									Chiudi: function(){$(this).dialog('close');}
			            								},
			            									close: function(){$(this).remove();},
			            									draggable: true,
			            									modal: true,
			            									resizable: false,
			            									width: '640px',
				        									position: ['center', 100],
				        									title: 'Firmatari'
			        							});
									<?
								} else {
									?>
									$("<div><h2>Chiave privata errata</h2></div>").dialog({
																				dialogClass: 'ui-state-error',
											            						buttons: {
											            									OK: function(){$(this).dialog('close');}},
											            									close: function(){$(this).remove();},
											            									draggable: true,
											            									modal: true,
											            									resizable: false,
											            									width: 'auto',
												        									position: ['center', 100],
											        							});
									<?
								}
							} else {
							?>
							$("<div><h2>Terminare l'apertura delle precedenti buste</h2></div>").dialog({
																							dialogClass: 'ui-state-error',
														            						buttons: {
														            									OK: function(){$(this).dialog('close');}},
														            									close: function(){$(this).remove();},
														            									draggable: true,
														            									modal: true,
														            									resizable: false,
														            									width: 'auto',
															        									position: ['center', 100],
														        							});
							<?
							}
							if (class_exists("syncERP")) {
								$sync = new syncERP();
								$sync->sendUpdateRequest($_POST["codice_gara"],"apertura");
							}
						} else {
				?>
				$("<div><h2>Busta gi&agrave; aperta</h2></div>").dialog({
														dialogClass: 'ui-state-error',
					            						buttons: {
					            									OK: function(){$(this).dialog('close');}},
					            									close: function(){$(this).remove();},
					            									draggable: true,
					            									modal: true,
					            									resizable: false,
					            									width: 'auto',
						        									position: ['center', 100],
					        							});
                <?
				}
					} else {
				?>
				$("<div><h2>Busta inesistente</h2></div>").dialog({
														dialogClass: 'ui-state-error',
					            						buttons: {
					            									OK: function(){$(this).dialog('close');}},
					            									close: function(){$(this).remove();},
					            									draggable: true,
					            									modal: true,
					            									resizable: false,
					            									width: 'auto',
						        									position: ['center', 100],
					        							});
                <?
				}
				} else {
						?>
						$("<div><h2>Attendere la data di apertura della busta</h2></div>").dialog({
							dialogClass: 'ui-state-error',
    						buttons: {
    									OK: function(){$(this).dialog('close');}},
    									close: function(){$(this).remove();},
    									draggable: true,
    									modal: true,
    									resizable: false,
    									width: 'auto',
    									position: ['center', 100],
							});
						<?
						}
			} else {
				?>
				$("<div><h2>Non &egrave; possibile accedere ai documenti di gara</h2></div>").dialog({
					dialogClass: 'ui-state-error',
					buttons: {
								OK: function(){$(this).dialog('close');}},
								close: function(){$(this).remove();},
								draggable: true,
								modal: true,
								resizable: false,
								width: 'auto',
								position: ['center', 100],
					});
                <?
			}
		} else {
			?>
			$("<div><h2>Chiave privata non presente</h2></div>").dialog({
				dialogClass: 'ui-state-error',
				buttons: {
							OK: function(){$(this).dialog('close');}},
							close: function(){$(this).remove();},
							draggable: true,
							modal: true,
							resizable: false,
							width: 'auto',
							position: ['center', 100],
				});
<?
		}
	} else {
			?>
			$("<div><h2>Non si dispone dei permessi necessari</h2></div>").dialog({
				dialogClass: 'ui-state-error',
				buttons: {
							OK: function(){$(this).dialog('close');}},
							close: function(){$(this).remove();},
							draggable: true,
							modal: true,
							resizable: false,
							width: 'auto',
							position: ['center', 100],
				});
            <?
	}
	$salva->debug = false;
	$salva->codop = $_SESSION["codice_utente"];
	$salva->nome_tabella = "b_log_aperture";
	$salva->operazione = "INSERT";
	$salva->oggetto = $log;
	$codice_log = $salva->save();
?>
