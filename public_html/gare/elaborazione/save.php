<?
	include_once("../../../config.php");
	$edit = false;
	$lock = true;
	$in_elaborazione = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}
		if ($edit && !$lock) {

			$bind = array();
			$bind[":codice"]=$_POST["codice_gara"];
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
			$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			if ($_SESSION["gerarchia"] > 0) {
				$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
				$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":modalita"] = $record_gara["modalita"];
				$sql = "SELECT * FROM b_modalita WHERE codice = :modalita";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount()>0) {
						$rec = $ris->fetch(PDO::FETCH_ASSOC);
						$record_gara["online"] = $rec["online"];
					}
				$riepilogo = $record_gara;
				$record_gara = $record_gara;

			$bind = array();
			$bind[":codice_gestore"] = $_SESSION["ente"]["codice"];
			$bind[":tipologia_a"] = $record_gara["tipologia"];
			$bind[":tipologia_b"] = $record_gara["tipologia"] . ";%";
			$bind[":tipologia_c"] = "%;" . $record_gara["tipologia"] . ";%";
			$bind[":tipologia_d"] = "%;" . $record_gara["tipologia"];
			$sql_minimi = "SELECT * FROM b_impostazioni_dati_minimi WHERE (tipologie = :tipologia_a OR tipologie LIKE :tipologia_b OR tipologie LIKE :tipologia_c OR tipologie LIKE :tipologia_d) AND
										 codice_gestore = :codice_gestore AND attivo = 'S' AND eliminato = 'N' ";
			$ris_minimi = $pdo->bindAndExec($sql_minimi,$bind);

			$bind = array();
			$bind[":codice"] = $_POST["codice_gara"];

			$sql = "DELETE FROM b_opzioni_selezionate WHERE codice_gara = :codice";
			$remove_opzioni = $pdo->bindAndExec($sql,$bind);

			$sql = "SELECT b_importi_gara.* FROM b_importi_gara ";
			$sql .= "WHERE codice_gara = :codice";

			$ris_importi = $pdo->bindAndExec($sql,$bind);
			$ris_importi = $ris_importi->fetchAll(PDO::FETCH_ASSOC);

			$totale_gara = 0;

			$array_opzioni = array();
			$gruppi_opzioni = array();
			$array_script = array();

			$bind = array();
			$bind[":criterio"] = $record_gara["criterio"];
			$strsql = "SELECT * FROM b_criteri WHERE codice = :criterio";
			$risultato_criterio = $pdo->bindAndExec($strsql,$bind);
			if ($risultato_criterio->rowCount()>0) {
				$specifiche_criterio = $risultato_criterio->fetch(PDO::FETCH_ASSOC);
				if ($specifiche_criterio["opzioni"]!="") $gruppi_opzioni = explode(",",$specifiche_criterio["opzioni"]);
				if ($specifiche_criterio["script"]!="") $array_script = explode(",",$specifiche_criterio["script"]);
			}

			foreach($ris_importi AS $rec_importo) {
				$totale_gara = $totale_gara + $rec_importo["importo_base"] + $rec_importo["importo_oneri_no_ribasso"]; // + $rec_importo["importo_oneri_ribasso"] + $rec_importo["importo_personale"];
			}

			$bind = array();
			$bind[":tipologia"] = $record_gara["tipologia"];
			$bind[":criterio"] = $record_gara["criterio"];
			$bind[":procedura"] = $record_gara["procedura"];
			$bind[":totale_gara"] = $totale_gara;

			$strsql  = "SELECT b_modelli_new.* FROM b_modelli_new WHERE attivo = 'S' AND (tipologia = :tipologia OR tipologia = 0)";
			$strsql .= " AND (criterio = :criterio OR criterio = 0)";
			$strsql .= " AND procedura = :procedura";
			$strsql .= " AND (importo_massimo >= :totale_gara OR importo_massimo = 0)";
			$strsql .= " AND (importo_minimo <= :totale_gara OR importo_minimo = 0)";
			$risultato_modelli = $pdo->bindAndExec($strsql,$bind);
			if ($risultato_modelli->rowCount()>0) {
				while($modello = $risultato_modelli->fetch(PDO::FETCH_ASSOC)) {

					$bind = array();
					$bind[":codice_modello"] = $modello["codice"];
					$bind[":modalita"] = $record_gara["modalita"];
					$bind[":totale_gara"] = $totale_gara;

					$strsql = "SELECT * FROM b_paragrafi_new WHERE codice_modello = :codice_modello AND codice_opzione <> '' ";
					$strsql .= " AND (modalita = :modalita OR modalita = 0)";
					$strsql .= " AND (importo_massimo >= :totale_gara OR importo_massimo = 0)";
					$strsql .= " AND (importo_minimo <= :totale_gara OR importo_minimo = 0)";
					$strsql .= " AND (criteri REGEXP '[[:<:]]{$record_gara['criterio']}[[:>:]]' OR criteri = '' OR criteri IS NULL)";
					$strsql .= " AND (tipologie REGEXP '[[:<:]]{$record_gara['tipologia']}[[:>:]]' OR tipologie = '' OR tipologie IS NULL)";
					$risultato_opzioni = $pdo->bindAndExec($strsql,$bind);
					if ($risultato_opzioni->rowCount()>0) {
						while($paragrafo = $risultato_opzioni->fetch(PDO::FETCH_ASSOC)) {
							$opzioni = explode(",",$paragrafo["codice_opzione"]);
							$array_opzioni = array_merge($array_opzioni,$opzioni);
						}
					}
				$strsql = "SELECT * FROM b_paragrafi_new WHERE codice_modello = :codice_modello AND tipo = 'avanzato' ";
				$strsql .= " AND (modalita = :modalita OR modalita = 0)";
				$strsql .= " AND (importo_massimo >= :totale_gara OR importo_massimo = 0)";
				$strsql .= " AND (importo_minimo <= :totale_gara OR importo_minimo = 0)";
				$strsql .= " AND (criteri REGEXP '[[:<:]]{$record_gara['criterio']}[[:>:]]' OR criteri = '' OR criteri IS NULL)";
				$strsql .= " AND (tipologie REGEXP '[[:<:]]{$record_gara['tipologia']}[[:>:]]' OR tipologie = '' OR tipologie IS NULL)";
				$risultato_script = $pdo->bindAndExec($strsql,$bind);
				if ($risultato_script->rowCount()>0) {
					while($paragrafo = $risultato_script->fetch(PDO::FETCH_ASSOC)) {
						$array_script[] = $paragrafo["directory"];
						}
					}
				}
			}
			$array_opzioni = array_unique($array_opzioni);
			$array_opzioni = array_filter($array_opzioni);
			sort($array_opzioni);
			if ((count($gruppi_opzioni) > 0) || (count($array_opzioni)>0)) {
				$strsql = "SELECT * FROM b_gruppi_opzioni WHERE attivo = 'S' AND eliminato = 'N' AND (";
				if (count($array_opzioni)>0) {
					$under_flag = true;
					$strsql .= " (codice IN (SELECT codice_gruppo FROM b_opzioni WHERE attivo = 'S' AND eliminato = 'N' AND codice IN (" . implode(",",$array_opzioni) . ")))";
				}
				if (count($gruppi_opzioni)>0) {
					if (isset($under_flag)) $strsql .= " OR ";
					$strsql .= " (codice IN (" . implode(",",$gruppi_opzioni) . "))";
				}
				$strsql .= ")";
				$risultato_opzioni = $pdo->query($strsql);
			}

			$array_script = array_unique($array_script);
			$array_script = array_filter($array_script);
			sort($array_script);
			if (count($array_script)>0) {
				foreach($array_script as $script) {
					$continua = true;
					if ($script == "criterio_offerta_tecnica" && $record_gara["nuovaOfferta"] == "S") $continua = false;
					if ($continua) {
						if (file_exists($root."/gare/elaborazione/moduli_avanzati/".$script."/save.php"))
							include($root."/gare/elaborazione/moduli_avanzati/".$script."/save.php");
					}
				}
			}
			if (isset($risultato_opzioni) && $risultato_opzioni->rowCount()>0) {
						while($gruppo = $risultato_opzioni->fetch(PDO::FETCH_ASSOC)) {
							$opzione["codice_gara"] = $_POST["codice_gara"];
							if (isset($_POST["gruppo"][$gruppo["codice"]])) {
								if (!is_array($_POST["gruppo"][$gruppo["codice"]])) {
									$opzione["opzione"] = $_POST["gruppo"][$gruppo["codice"]];

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "b_opzioni_selezionate";
									$salva->operazione = "INSERT";
									$salva->oggetto = $opzione;
									$salva->save();

								} else {
									foreach ($_POST["gruppo"][$gruppo["codice"]] as $valore) {
										$opzione["opzione"] = $valore;

										$salva = new salva();
										$salva->debug = false;
										$salva->codop = $_SESSION["codice_utente"];
										$salva->nome_tabella = "b_opzioni_selezionate";
										$salva->operazione = "INSERT";
										$salva->oggetto = $opzione;
										$salva->save();

									}
								}
							}
						}
					}

				$bind = array();
				$bind[":procedura"] = $record_gara["procedura"];

				$sql_guue = "SELECT guue FROM b_procedure WHERE codice = :procedura";
				$ris_guue = $pdo->bindAndExec($sql_guue,$bind);

				if($ris_guue->rowCount()>0) {
					$guue = $ris_guue->fetch(PDO::FETCH_ASSOC);
					$guue = $guue["guue"];

					$bind = array();
					$bind[":guue"] = $guue;
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql = "SELECT * FROM b_guue WHERE number = :guue AND codice_gara = :codice_gara AND codice_ente = :codice_ente";
					$risultato_guue = $pdo->bindAndExec($strsql,$bind);
					if($risultato_guue->rowCount()===0) {
						$strsql = "INSERT INTO b_guue (codice_ente, codice_gara, number , dataRichiesta) VALUES (:codice_ente,:codice_gara,:guue, NOW()) ";
						$risultato_guue = $pdo->bindAndExec($strsql,$bind);
					}
				}

				$errori_dati_minimi = array();
				if ($ris_minimi->rowCount() > 0) {
					while ($campo = $ris_minimi->fetch(PDO::FETCH_ASSOC)) {
						$sql_dati = "SELECT * FROM b_dati_minimi WHERE codice_gara = :codice_gara AND codice_campo = :codice_campo ";
						$ris_dati = $pdo->bindAndExec($sql_dati,array(":codice_gara"=>$_POST["codice_gara"],":codice_campo"=>$campo["codice"]));
						$valore = "";
						$codice_valore = "";
						if($ris_dati->rowCount() > 0) {
							$valore = $ris_dati->fetch(PDO::FETCH_ASSOC);
							$codice_valore = $valore["codice"];
							$valore = $valore["valore"];
							if ($campo["tipo"] == "attach") {
								$valore_tmp = json_decode($valore,true);
								$sql = "SELECT * FROM b_allegati WHERE codice = :codice";
								$ris_check = $pdo->bindAndExec($sql,array(":codice"=>$valore_tmp["codice_allegato"]));
								if ($ris_check->rowCount() == 0) $valore = "";
							}
						}
						if (isset($_POST["datiMinimi"][$campo["codice"]]["valore"])) {
							if ($campo["tipo"]!="attach"||($campo["tipo"]=="attach") && !empty($_POST["datiMinimi"][$campo["codice"]]["valore"])) {
								if ($valore != $_POST["datiMinimi"][$campo["codice"]]["valore"]) {
									$tmp = array();
									$tmp["codice_campo"] = $campo["codice"];
									$tmp["codice_gara"] = $_POST["codice_gara"];
									if ($codice_valore != "") {
										$tmp["codice"] = $codice_valore;
									}
									if ($campo["tipo"] != "attach") {
										$tmp["valore"] = $_POST["datiMinimi"][$campo["codice"]]["valore"];
									} else {
										$allegato = array();
										if (!empty($valore)) {
											$valore = json_decode($valore,true);
											$allegato["codice"] = $valore["codice_allegato"];
										}
										$allegato["online"] = 'N';
										$allegato["codice_gara"] = $_POST["codice_gara"];
										$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
										$allegato["titolo"] = $campo["titolo"];
										$percorso = $config["arch_folder"] ."/".$allegato["codice_gara"];
										if (!is_dir($percorso)) mkdir($percorso,0777,true);
										$copy = @copiafile_chunck($_POST["datiMinimi"][$campo["codice"]]["valore"],$percorso."/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);
										$allegato["nome_file"] = $copy["nome_file"];
										$allegato["riferimento"] = $copy["nome_fisico"];
										if (file_exists($percorso."/".$allegato["riferimento"])) {
											$salva = new salva();
											$salva->debug = false;
											$salva->codop = $_SESSION["codice_utente"];
											$salva->nome_tabella = "b_allegati";
											$salva->operazione = (!empty($allegato["codice"])) ? "UPDATE" : "INSERT";
											$salva->oggetto = $allegato;
											$codice_allegato = $salva->save();
											if ($codice_allegato !== "false") {
												$json = array();
												$json["codice_allegato"] = $codice_allegato;
												$json["nome_file"] = $allegato["nome_file"];
												$json["riferimento"] = $allegato["riferimento"];
												$tmp["valore"] = json_encode($json);
											} else {
												$errori_dati_minimi[] = "<li><strong>" . $campo["titolo"] . "</strong> si &egrave; verificato un errore durante il salvataggio dell'allegato, si prega di riprovare";
											}
										} else {
											$errori_dati_minimi[] = "<li><strong>" . $campo["titolo"] . "</strong> si &egrave; verificato un errore durante l'upload del file, si prega di riprovare";
										}
									}
									$salva = new salva();
									$salva->debug = false;
									$salva->codop = $_SESSION["codice_utente"];
									$salva->nome_tabella = "b_dati_minimi";
									$salva->operazione = (!empty($tmp["codice"])) ? "UPDATE" : "INSERT";
									$salva->oggetto = $tmp;
									if ($salva->save()===false) $errori_dati_minimi[] = "<li><strong>" . $campo["titolo"] . "</strong> si &egrave; verificato un errore durante il salvataggio, si prega di riprovare";
								} else {
									$tmp["valore"] = $valore;
								}
							}
						}
						if ($campo["obbligatorio"] == "S" && empty($tmp["valore"])) {
							if ($campo["tipo"] != "attach" || ($campo["tipo"] == "attach" && empty($valore))) {
								$errori_dati_minimi[] = "<li><strong>" . $campo["titolo"] . "</strong> &egrave; obbligatorio";
							}
						}
					}
				}
				$errori_validazione = false;
				if ($_SESSION["record_utente"]["codice_ente"] != $_SESSION["ente"]["codice"] && !empty($_SESSION["record_utente"]["codice_ente"])) {

					if (count($errori_dati_minimi) > 0) {
						ob_start();
						$errori_validazione = true;
						echo '<h3 class="ui-state-error">La gara non può essere elaborata, mancano i seguenti dati:</h3>';
						echo '<ul class="ui-state_error">';
						foreach($errori_dati_minimi AS $error_text) echo $error_text;
						echo '</ul>';
						$errori = ob_get_clean();
						?>
						jalert('<?= str_replace("'","\'",$errori) ?>');
						<?
					} else {
						$bind = array();
						$bind[":codice"] = $_POST["codice_gara"];

						$sql = "UPDATE b_gare SET stato = 2 WHERE codice = :codice";
						$update_stato = $pdo->bindAndExec($sql,$bind);

						$corpo = "L'utente " . $_SESSION["record_utente"]["cognome"] . " " . $_SESSION["record_utente"]["nome"] . " ha elaborato i dati per la seguente gara: <strong>" . $record_gara["oggetto"]."</strong>";

						$head = "<html>
											<head>
												<style>
													body { font-family: Tahoma, Geneva, sans-serif; margin:0px; padding:0px }
													.padding { padding:20px; }
													tr.odd { background-color:#F6F6F6;}
													tr.even { background-color:#ECECEC; }
														#bottom { padding:20px; background-color: #DDD; text-align:right }
												</style>
											</head>
											<body>";
						$head.= "<div class=\"padding\"><table>";
						$head.= "<tr><td><img src=\"https://gare.comune.roma.it/img/tuttogarepa-logo-software-sx-small.png\" alt=\"Tutto Gare\"></td>";
						$head.= "<td>";
						$head.= "<h1>Tutto Gare</h1>";
						$head.= "</td></tr>";
						$head.= "</table></div>";
						$head.= "<hr><div class=\"padding\">";

						$bottom = "</div>";
						$bottom .= "<div id=\"bottom\">";
						$bottom .= "<img src=\"https://gare.comune.roma.it/img/tuttogarepa-logo-software-sx-small.png\" alt=\"Tutto Gare\">";
						$bottom .= "</div>";
						$bottom .= "</body></html>";

						$mail = new PHPMailer();
						$mail->IsSMTP();
						$mail->Host = $config["smtp_server"];
						$mail->Port = $config["smtp_port"];
						if ($config["smtp_ssl"] == true) $mail->SMTPSecure = 'ssl';
						$mail->SMTPAuth = true;
						$mail->Username = $config["mittente_mail"];
						$mail->Password = $config["smtp_password"];
						$mail->SetFrom($config["mittente_mail"],$config["nome_sito"]);
						$mail->AddAddress($_SESSION["ente"]["pec"]);
						$mail->Subject = $config["nome_sito"] . " - Elaborazione gara - " . $record_gara["oggetto"];
						$mail->MsgHTML($corpo);
						$mail->Send();

						$mail = new PHPMailer();
						$mail->IsSMTP();
						$mail->Host = $config["smtp_server"];
						$mail->Port = $config["smtp_port"];
						if ($config["smtp_ssl"] == true) $mail->SMTPSecure = 'ssl';
						$mail->SMTPAuth = true;
						$mail->Username = $config["mittente_mail"];
						$mail->Password = $config["smtp_password"];
						$mail->SetFrom($config["mittente_mail"],$config["nome_sito"]);
						$mail->AddAddress($_SESSION["record_utente"]["email"]);
						$mail->Subject = $config["nome_sito"] . " - Elaborazione gara - " . $record_gara["oggetto"];
						$mail->MsgHTML($corpo);
						$mail->Send();
					}
				} else {
					$bind = array();
					$bind[":codice"] = $_POST["codice_gara"];

					$sql = "UPDATE b_gare SET stato = 2 WHERE codice = :codice";
					$update_stato = $pdo->bindAndExec($sql,$bind);
				}

				$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio = :codice_criterio";
				$ris_check = $pdo->bindAndExec($sql,array(":codice_criterio"=>$record_gara["criterio"]));
				$sql_val = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara";
				$ris_val = $pdo->bindAndExec($sql_val,array(":codice_gara"=>$record_gara["codice"]));
				if ($ris_val->rowCount() == 0 && $ris_check->rowCount() === 1 && $record_gara["nuovaOfferta"] == "S") {
					$punteggio_rif = $ris_check->fetch(PDO::FETCH_ASSOC);
					if ($punteggio_rif["economica"] == "S") {
						$preset = array();
						$preset["codice_gara"] = $record_gara["codice"];
						$preset["tipo"] = "N";
						$preset["punteggio_riferimento"] = $punteggio_rif["codice"];
						$preset["punteggio"] = 100;
					  $bind = array();
					  $bind[":codice_gara"] = $record_gara["codice"];
					  $sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 58";
					  $ris = $pdo->bindAndExec($sql,$bind);
					  if ($ris->rowCount() > 0) {
							$preset["valutazione"] = "E";
							$preset["descrizione"] = "Elenco prezzi";
						} else {
							$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 57";
						  $ris = $pdo->bindAndExec($sql,$bind);
						  if ($ris->rowCount() > 0) {
								$preset["valutazione"] = "P";
								$preset["descrizione"] = "Ribasso percentuale";
							} else {
								$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 270";
							  $ris = $pdo->bindAndExec($sql,$bind);
							  if ($ris->rowCount() > 0) {
									$preset["valutazione"] = "P";
									$preset["descrizione"] = "Rialzo percentuale";
								}
							}
						}
						if (isset($preset["valutazione"])) {
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_valutazione_tecnica";
							$salva->operazione = "INSERT";
							$salva->oggetto = $preset;
							$salva->save();
						}
					}
				}

				$href = "/gare/pannello.php?codice=" . $_POST["codice_gara"];
				$href = str_replace('"',"",$href);
				$href = str_replace(' ',"-",$href);

				log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Elaborazione gara");
				if (!$errori_validazione) {
					?>
					alert('Inserimento effettuato con successo');
	    	  window.location.href = '<? echo $href ?>';
	      	<?
				}
			}
	}
?>
