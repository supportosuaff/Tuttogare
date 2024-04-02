<?
	session_start();
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	include_once($root."/inc/p7m.class.php");
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
	if (isset($_POST["codice_fase"])) $log["codice_fase"] = $_POST["codice_fase"];

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_concorso($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
	}
	if ($edit && !$lock) {
		if (isset($_POST["private_key"])) {

			$codice = $_POST["codice_gara"] ;

			$bind = array();
			$bind[":codice"] = $codice;
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
			$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			if ($_SESSION["gerarchia"] > 0) {
				$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
				$strsql .= " AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {
				$permission = false;
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":codice_gara"] = $record_gara["codice"];
				// $bind[":codice_fase"] = $_POST["codice_fase"];
				$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara AND attiva = 'S' AND apertura <= now() ORDER BY codice DESC LIMIT 0,1";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount() > 0) {
					$fase = $ris->fetch(PDO::FETCH_ASSOC);
					$permission = true;

					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$bind[":codice_fase"] = $_POST["codice_fase"];
					$bind[":codice_busta"] = $_POST["codice_busta"];


					$strsql  = "SELECT * FROM b_date_apertura_concorsi ";
					$strsql .= "WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND codice_busta = :codice_busta ";
					$strsql .= "ORDER BY codice DESC LIMIT 0,1";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount()>0) {
						$record_data = $risultato->fetch(PDO::FETCH_ASSOC);
						$time = strtotime($record_data["data_apertura"]);
						if ($time > time()) {
							$permission = false;
						}
					}
				}
				if ($permission) {

					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$bind[":codice_fase"] = $_POST["codice_fase"];
					$bind[":codice_partecipante"] = $_POST["codice_partecipante"];
					$bind[":codice_busta"] = $_POST["codice_busta"];

					$strsql  = "SELECT b_buste_concorsi.*, b_fasi_concorsi_buste.nome, r_partecipanti_concorsi.identificativo, b_fasi_concorsi_buste.tecnica FROM b_buste_concorsi
											JOIN b_fasi_concorsi_buste ON b_buste_concorsi.codice_busta = b_fasi_concorsi_buste.codice
											JOIN r_partecipanti_concorsi ON b_buste_concorsi.codice_partecipante = r_partecipanti_concorsi.codice
											WHERE b_buste_concorsi.codice_gara = :codice_gara AND b_buste_concorsi.codice_fase = :codice_fase AND b_buste_concorsi.codice_partecipante = :codice_partecipante
											AND b_buste_concorsi.codice_busta = :codice_busta";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$busta = $risultato->fetch(PDO::FETCH_ASSOC);
						if ($busta["aperto"]=="N") {

							$bind = array();
							$bind[":codice_gara"] = $busta["codice_gara"];
							$bind[":codice_fase"] = $busta["codice_fase"];
							$bind[":codice_partecipante"] = $busta["codice_partecipante"];
							$bind[":codice_busta"] = $busta["codice_busta"];

							$key = openssl_pkey_get_private($_POST["private_key"]);
							$strsql  = "SELECT b_buste_concorsi.*
													FROM b_buste_concorsi
													JOIN b_fasi_concorsi_buste ON b_buste_concorsi.codice_busta = b_fasi_concorsi_buste.codice
													JOIN r_partecipanti_concorsi ON b_buste_concorsi.codice_partecipante = r_partecipanti_concorsi.codice
													WHERE b_buste_concorsi.codice_gara = :codice_gara AND b_buste_concorsi.codice_fase = :codice_fase
													AND b_buste_concorsi.aperto = 'N' AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL) AND ((r_partecipanti_concorsi.codice < :codice_partecipante
													AND b_fasi_concorsi_buste.codice = :codice_busta) OR (b_fasi_concorsi_buste.codice < :codice_busta))" ;
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount() === 0) {
								$enc_data = file_get_contents($config["doc_folder"] . "/concorsi/" . $busta["codice_gara"] . "/" . $busta["codice_fase"] . "/" . $busta["nome_file"]);
								if (@openssl_private_decrypt($busta["salt"],$salt,$key)) {
									$data = openssl_decrypt($enc_data,$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
									$log["esito"] = "Positivo";
									/* if ($busta["tecnica"] == "S") {
										$bind = array();
										$bind[":codice_gara"] = $busta["codice_gara"];
										$bind[":codice_partecipante"] = $busta["codice_partecipante"];
										$strsql  = "SELECT * FROM b_offerte_concorso ";
										$strsql .= "WHERE codice_gara = :codice_gara AND codice_partecipante = :codice_partecipante AND tipo = 'tecnica' ";
										$ris_offerta = $pdo->bindAndExec($strsql,$bind);
										if ($ris_offerta->rowCount()>0) {
											$offerta = $ris_offerta->fetch(PDO::FETCH_ASSOC);
											if (@openssl_private_decrypt($offerta["salt"],$salt_offerta,$key)) {
												$bind = array();
												$bind[":codice_offerta"] = $offerta["codice"];
												$sql_dettaglio = "SELECT * FROM b_dettaglio_offerte_concorso WHERE codice_offerta = :codice_offerta";
												$ris_dettaglio = $pdo->bindAndExec($sql_dettaglio,$bind);
												if ($ris_dettaglio->rowCount()>0) {
													while ($dettaglio = $ris_dettaglio->fetch(PDO::FETCH_ASSOC)) {
														$decrypt = array();
														$decrypt["codice_partecipante"] = $dettaglio["codice_partecipante"];
														$decrypt["tipo"] = $dettaglio["tipo"];
														$decrypt["codice_dettaglio"] = $dettaglio["codice_dettaglio"];
														$decrypt["offerta"] = openssl_decrypt($dettaglio["offerta"],$config["crypt_alg"],$salt_offerta,OPENSSL_RAW_DATA,$config["enc_salt"]);

														$salva = new salva();
														$salva->debug = false;
														$salva->codop = $_SESSION["codice_utente"];
														$salva->nome_tabella = "b_offerte_decriptate_concorso";
														$salva->operazione = "INSERT";
														$salva->oggetto = $decrypt;
														$codice_offerta_de = $salva->save();

													}
												}

											}
										}
									} */

									if (!is_dir($config["arch_folder"]."/concorsi/".$busta["codice_gara"]."/".$busta["codice_fase"]."/".$busta["nome"])) mkdir($config["arch_folder"]."/concorsi/".$busta["codice_gara"]."/".$busta["codice_fase"]."/".$busta["nome"],0777,true);
									$percorso = $config["arch_folder"]."/concorsi/".$busta["codice_gara"]."/".$busta["codice_fase"]."/".$busta["nome"];

									$file_info = new finfo(FILEINFO_MIME_TYPE);
							    $mime_type = $file_info->buffer($data);

									$estensione = explode("/",$mime_type);
									$estensione = $estensione[1];
									$riferimento = $busta["identificativo"]."-".getRealNameFromData($data);
									file_put_contents($percorso."/".$riferimento,$data);
									$md5_file = md5($data);

									$allegato = array();
									$allegato["codice_gara"] = $busta["codice_gara"];
									$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
									$allegato["cartella"] = $busta["codice_fase"]."/".$busta["nome"];
									$allegato["nome_file"] = $busta["identificativo"].".".$estensione;
									$allegato["riferimento"] = $riferimento;
									$allegato["titolo"] = $busta["identificativo"];
									$allegato["sezione"] = "concorsi";
									$allegato["online"] = "N";

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "b_allegati";
									$salva->operazione = "INSERT";
									$salva->oggetto = $allegato;
									$codice_allegato = $salva->save();

									$bind = array();
									$bind[":codice_allegato"] = $codice_allegato;
									$bind[":codice"] = $busta["codice"];

									$sql = "UPDATE b_buste_concorsi SET aperto = 'S', codice_allegato = :codice_allegato WHERE codice = :codice";
									$ris = $pdo->bindAndExec($sql,$bind);
									$note = "";
									$class= "";
									$note.= "<div style=\"text-align:center\">Hash MD5: " . $md5_file . "</div><br>";

									$cella="<a href=\"/allegati/download_allegato.php?codice=" . $codice_allegato . "\" title=\"Download Allegato\">";
									$cella.="<img src=\"/img/download.png\" alt=\"Download Allegato\" width=\"25\"></a>&nbsp;";
									if ($estensione=="p7m") {
										$cella.="<a href=\"/allegati/open_p7m.php?codice=". $codice_allegato ."\" title=\"Estrai Contenuto\">";
										$cella.="<img src=\"/img/p7m.png\" alt=\"Download Allegato\" width=\"25\"></a>";
									}
									$cella.="<br>Busta aperta";

									if ($busta["tecnica"] == "N") {

										$bind = array();
										$bind[":codice_gara"] = $busta["codice_gara"];
										$bind[":codice_fase"] = $busta["codice_fase"];
										$bind[":codice_partecipante"] = $busta["codice_partecipante"];

										$sql = "SELECT r_partecipanti_concorsi.*, r_partecipanti_utenti_concorsi.codice AS codice_criptato, r_partecipanti_utenti_concorsi.codice_operatore, r_partecipanti_utenti_concorsi.codice_utente,
														r_partecipanti_utenti_concorsi.partita_iva, r_partecipanti_utenti_concorsi.ragione_sociale, r_partecipanti_utenti_concorsi.identificativoEstero,
														r_partecipanti_utenti_concorsi.pec
														FROM r_partecipanti_concorsi
														JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
														WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND (conferma = TRUE OR conferma IS NULL) AND escluso = 'N' AND r_partecipanti_concorsi.codice = :codice_partecipante ";
										$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
										if ($ris_partecipanti->rowCount()==1) {
											// $key = openssl_pkey_get_private($_POST["private_key"]);
											$partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC);
											if (!is_numeric($partecipante["codice_utente"])) {
												if (@openssl_private_decrypt(base64_decode($partecipante["salt"]),$salt,$key)) {

														$decrypt = array();
														$decrypt["codice"] = $partecipante["codice_criptato"];
														$decrypt["codice_utente"] = openssl_decrypt($partecipante["codice_utente"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
														$decrypt["codice_operatore"] = openssl_decrypt($partecipante["codice_operatore"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
														$decrypt["partita_iva"] = openssl_decrypt($partecipante["partita_iva"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
														$decrypt["ragione_sociale"] = openssl_decrypt($partecipante["ragione_sociale"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
														$decrypt["identificativoEstero"] = openssl_decrypt($partecipante["identificativoEstero"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
														$decrypt["pec"] = openssl_decrypt($partecipante["pec"],$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);


														$salva = new salva();
														$salva->debug = false;
														$salva->codop = $_SESSION["codice_utente"];
														$salva->nome_tabella = "r_partecipanti_utenti_concorsi";
														$salva->operazione = "UPDATE";
														$salva->oggetto = $decrypt;
														$salva->save();

												}
											}
										}

										$p7m = new P7Manager($percorso."/".$riferimento);
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
											if (isset($data["subject"]["commonName"])) $note .=  "<h1>" . $data["subject"]["commonName"] . "</h1>";
											if (isset($data["subject"]["organizationName"]) && $data["subject"]["organizationName"] != "NON PRESENTE") $note .= "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
											if (isset($data["subject"]["title"])) $note .=  $data["subject"]["title"] . "<br>";
											if (isset($data["issuer"]["organizationName"])) $note .=  "<br>Emesso da:<strong> " . $data["issuer"]["organizationName"] . "</strong>";
											$note .=  "<br><br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
											$note .=  "</div>";
										}
										if ($note != "") $note = "<ul class='firme'>" . $note . "</ul>";

									}
										?>
									$("#<? echo $busta["codice_partecipante"] . "_" . $busta["codice_busta"] ?>").html('<? echo $cella ?>');
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
				        									title: 'Info'
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
	$salva = new salva();
	$salva->debug = false;
	$salva->codop = $_SESSION["codice_utente"];
	$salva->nome_tabella = "b_log_aperture_concorsi";
	$salva->operazione = "INSERT";
	$salva->oggetto = $log;
	$codice_log = $salva->save();
?>
