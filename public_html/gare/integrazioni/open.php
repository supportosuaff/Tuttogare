<?
	session_start();

	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	include_once($root."/inc/p7m.class.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && isset($_POST["codice_gara"])) {
		$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/integrazioni/index.php'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
	}
	if ($edit && !$lock) {
		if (isset($_POST["private_key"])) {
			$codice = $_POST["codice"] ;

			$bind = array();
			$bind[":codice"] = $codice;
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$strsql  = "SELECT b_integrazioni.*, r_partecipanti.pec, r_partecipanti.partita_iva, r_partecipanti.ragione_sociale, r_integrazioni.aperto,
									r_integrazioni.codice_partecipante,r_integrazioni.salt,r_integrazioni.nome_file, b_gare.codice_pec, b_gare.oggetto, b_gare.public_key, b_lotti.oggetto AS lotto FROM
									r_integrazioni JOIN b_integrazioni ON r_integrazioni.codice_integrazione = b_integrazioni.codice
									JOIN b_gare ON b_integrazioni.codice_gara = b_gare.codice
									JOIN r_partecipanti ON r_integrazioni.codice_partecipante = r_partecipanti.codice
									LEFT JOIN b_lotti ON b_integrazioni.codice_lotto = b_lotti.codice
									WHERE r_integrazioni.codice = :codice
									AND b_gare.annullata = 'N'
									AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
									AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') AND b_gare.codice = :codice_gara
									AND b_integrazioni.data_apertura <= now() ";
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {
				$integrazione = $risultato->fetch(PDO::FETCH_ASSOC);
				if ($integrazione["aperto"]=="N") {
							$key = openssl_pkey_get_private($_POST["private_key"]);
							$enc_data = file_get_contents($config["doc_folder"] . "/" . $integrazione["codice_gara"] . "/" . $integrazione["codice_lotto"] . "/integrazioni/".$integrazione["codice"]."/" . $integrazione["nome_file"]);
								if (@openssl_private_decrypt($integrazione["salt"],$salt,$key)) {
									$data = openssl_decrypt($enc_data,$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
									$oggetto = "Apertura integrazione per la gara: " . $integrazione["oggetto"] . " - " . $integrazione["lotto"];
									$corpo = "Si comunica l'avvenuta apertura dell'integrazione trasmessa riferita alla richiesta:<br><br>";
									$corpo = "<strong>".$integrazione["richiesta"]."</strong><br><br>";
									$corpo.= "Data e ora di apertura: <strong>" . date("d/m/Y H:i:s") . "</strong><br><br>";
									$corpo.= "Distinti Saluti<br><br>";

									$bind = array();
									$bind[":codice_gara"] = $_POST["codice_gara"];
									$bind[":codice_partecipante"] = $integrazione["codice_partecipante"];

									$sql = "SELECT b_utenti.*,b_operatori_economici.codice_fiscale_impresa FROM b_utenti JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
									$sql.= "JOIN r_partecipanti ON b_utenti.codice = r_partecipanti.codice_utente ";
									$sql.= "WHERE r_partecipanti.codice = :codice_partecipante AND r_partecipanti.codice_gara = :codice_gara";

									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) {
										$utente = $ris->fetch(PDO::FETCH_ASSOC);

										$mailer = new Communicator();
										$mailer->oggetto = $oggetto;
										$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
										$mailer->codice_pec = $integrazione["codice_pec"];
										$mailer->comunicazione = true;
										$mailer->coda = false;
										$mailer->sezione = "gara";
										$mailer->codice_gara = $integrazione["codice_gara"];
										$mailer->destinatari = $utente["codice"];
										$esito = $mailer->send();

									}


									if (!is_dir($config["arch_folder"]."/" . $integrazione["codice_gara"] . "/" . $integrazione["codice_lotto"] . "/integrazioni/".$integrazione["codice"]."/")) mkdir($config["arch_folder"]."/" . $integrazione["codice_gara"] . "/" . $integrazione["codice_lotto"] . "/integrazioni/".$integrazione["codice"]."/",0777,true);
									$percorso = $config["arch_folder"]."/" . $integrazione["codice_gara"] . "/" . $integrazione["codice_lotto"] . "/integrazioni/".$integrazione["codice"]."/";


									$file_info = new finfo(FILEINFO_MIME_TYPE);
									$mime_type = $file_info->buffer($data);
									$estensione = "p7m";
									if (strpos($mime_type, "pdf")!==false) $estensione = "pdf";

									$riferimento = $integrazione["codice"]."-".getRealNameFromData($data);
									file_put_contents($percorso."/".$riferimento,$data);

									$md5_file = md5($data);

									$allegato = array();
									$allegato["codice_gara"] = $_POST["codice_gara"];
									$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
									$allegato["cartella"] = $integrazione["codice_lotto"]."/integrazioni/".$integrazione["codice"];
									$allegato["nome_file"] = $utente["codice_fiscale_impresa"].".".$estensione;
									$allegato["riferimento"] = $riferimento;
									$allegato["titolo"] = "Integrazione " . $integrazione["partita_iva"];
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
									$bind[":codice"] = $codice;

									$sql = "UPDATE r_integrazioni SET aperto = 'S', codice_allegato = :codice_allegato WHERE codice = :codice";
									$ris = $pdo->bindAndExec($sql,$bind);
									$note = "";
									$class= "";
									log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"APERTURA","Aperta integrazione Codice: " . $codice);

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
										$note.= "<div style=\"text-align:center\">Hash MD5: " . $md5_file . "</div><br>";
										if (isset($data["subject"]["commonName"])) $note .=  "<h1>" . $data["subject"]["commonName"] . "</h1>";
										if (isset($data["subject"]["organizationName"]) && $data["subject"]["organizationName"] != "NON PRESENTE") $note .= "<strong>" . $data["subject"]["organizationName"] . "</strong><br>";
										if (isset($data["subject"]["title"])) $note .=  $data["subject"]["title"] . "<br>";
										if (isset($data["issuer"]["organizationName"])) $note .=  "<br>Emesso da:<strong> " . $data["issuer"]["organizationName"] . "</strong>";
										$note .=  "<br><br>Valido da:<strong> " . $validFrom . "</strong><br>A <strong>" . $validTo . "</strong>";
										$note .=  "</div>";
									}
										if ($note != "") $note = "<ul class='firme'>" . $note . "</ul>";
										$cella="<a href=\"/allegati/download_allegato.php?codice=" . $codice_allegato . "\" title=\"Download Allegato\">";
										$cella.="<img src=\"/img/download.png\" alt=\"Download Allegato\" width=\"25\"></a>&nbsp;";
										if ($estensione=="p7m") {
											$cella.="<a href=\"/allegati/open_p7m.php?codice=". $codice_allegato ."\" title=\"Estrai Contenuto\">";
											$cella.="<img src=\"/img/p7m.png\" alt=\"Download Allegato\" width=\"25\"></a>";
										}

									?>
									$("#cella_<? echo $integrazione["codice_partecipante"] ?>").html('<? echo $cella ?>');
									$("<div><? echo addslashes($note) ?></div>").dialog({
												dialogClass: '<? echo $class ?>',
			            						buttons: {
			            									"Scarica P7M": function(){ window.location.href = '/allegati/download_allegato.php?codice=<? echo $codice_allegato ?>';},
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
				$("<div><h2>Documento gi&agrave; aperto</h2></div>").dialog({
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
				$("<div><h2>Documento inesistente</h2></div>").dialog({
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

?>
