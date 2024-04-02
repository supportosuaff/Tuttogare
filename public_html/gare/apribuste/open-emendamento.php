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
						$originale = $risultato->fetch(PDO::FETCH_ASSOC);
						if ($originale["aperto"]=="S") {
							$bind = array();
							$bind[":codice_gara"] = $_POST["codice_gara"];
							$bind[":codice_lotto"] = $_POST["codice_lotto"];
							$bind[":codice_partecipante"] = $_POST["codice_partecipante"];
							$bind[":codice_busta"] = $originale["codice"];

							$strsql  = "SELECT b_emendamenti.* FROM b_emendamenti
										WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_partecipante = :codice_partecipante
										AND busta_originale = :codice_busta";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount() > 0) {
								$key = openssl_pkey_get_private($_POST["private_key"]);
								$busta = $risultato->fetch(PDO::FETCH_ASSOC);
								$enc_data = file_get_contents($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/emendamenti/" . $busta["nome_file"]);
								if (@openssl_private_decrypt($busta["salt"],$salt,$key)) {
									$data = openssl_decrypt($enc_data,$config["crypt_alg"],$salt,OPENSSL_RAW_DATA,$config["enc_salt"]);
									$log["esito"] = "Positivo";
									
									$oggetto = "Apertura " . $originale["nome"]. " per la gara: " . $record_gara["oggetto"] . $lotto;
									$corpo = "Si comunica l'avvenuta apertura dell'ememendamento per la busta in oggetto<br><br>";
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

									if (!is_dir($config["arch_folder"]."/".$busta["codice_gara"]."/".$busta["codice_lotto"]."/".$originale["nome"]."/emendamenti")) mkdir($config["arch_folder"]."/".$busta["codice_gara"]."/".$busta["codice_lotto"]."/".$originale["nome"]."/emendamenti",0777,true);
									$percorso = $config["arch_folder"]."/".$busta["codice_gara"]."/".$busta["codice_lotto"]."/".$originale["nome"]."/emendamenti";

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
									$allegato["cartella"] = $busta["codice_lotto"]."/".$originale["nome"]."/emendamenti";
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
									$bind[":descrizione"] = simple_decrypt($busta["descrizione"],$salt);
									$bind[":codice"] = $busta["codice"];

									$sql = "UPDATE b_emendamenti SET aperto = 'S', descrizione = :descrizione, codice_allegato = :codice_allegato WHERE codice = :codice";
									$ris = $pdo->bindAndExec($sql,$bind);
									?>
									$("#form_emendamento_<? echo $busta["codice_partecipante"] . "_" . $busta["codice_busta"] ?>").remove();
									$("#button_emendamento_<? echo $busta["codice_partecipante"] . "_" . $busta["codice_busta"] ?>").show();
									showInfoEmendamento(<?= $busta["codice_gara"] ?>,<?= $busta["codice_partecipante"] ?>,<?= $busta["codice"] ?>);
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
	$salva->debug = false;
	$salva->codop = $_SESSION["codice_utente"];
	$salva->nome_tabella = "b_log_aperture";
	$salva->operazione = "INSERT";
	$salva->oggetto = $log;
	$codice_log = $salva->save();
?>
