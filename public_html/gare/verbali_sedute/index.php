<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
						$edit = $esito["permesso"];
						$lock = $esito["lock"];
					}
					if (!$edit) {
						echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						die();
					}
				} else {
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice"]=$codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql = "SELECT * FROM b_gare WHERE codice = :codice AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$strsql .= " AND data_apertura <= now() ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
				?>
				<h1>Verbale seduta</h1>
				<?
				$bind = array();
				$bind[":codice"] = $codice;
				$sql_lotti = "SELECT b_lotti.* FROM b_lotti WHERE b_lotti.codice_gara = :codice";
				$sql_lotti.= " GROUP BY b_lotti.codice ORDER BY codice";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				$print_form = false;
				if ($ris_lotti->rowCount()>0) {
					if (isset($_GET["lotto"])) {
						$codice_lotto = $_GET["lotto"];
						$bind=array();
						$bind[":codice_lotto"] = $codice_lotto;
						$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice_lotto ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						if ($ris_lotti->rowCount()>0) {
							$print_form = true;
							$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
							echo "<h2>" . $lotto["oggetto"] . "</h2>";
						}
					} else {
						while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
						?>
							<a class="submit_big" href ="index.php?codice=<? echo $_GET["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
								<? echo $lotto["oggetto"]; ?>
							</a>
						<?
						}
					}
				} else {
					$print_form = true;
					$codice_lotto = 0;
				}
				if ($print_form) {
					$bind = array();
					$bind[":codice"] = $codice;
					$bind[":codice_lotto"] = $codice_lotto;

					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					$record_gara["oggetto"] = $record_gara["cig"] . " - " . $record_gara["oggetto"];
					if (isset($lotto)) {
						$record_gara["oggetto"] .= " - Lotto: " . $lotto["cig"] . " - " . $lotto["oggetto"];
					}
					$bind = array();
					$bind[":codice"]=$record_gara["codice"];
					$bind[":codice_lotto"]=$codice_lotto;

					$sql = "SELECT b_date_apertura.*, b_criteri_buste.nome FROM b_date_apertura JOIN b_criteri_buste ON b_date_apertura.codice_busta = b_criteri_buste.codice 
									WHERE b_date_apertura.codice_gara = :codice AND b_date_apertura.codice_lotto = :codice_lotto AND data_apertura <= now() ";
					if (!empty($_GET["seduta"])) {
						$sql .= " AND b_date_apertura.codice = :codice_seduta";
						$bind[":codice_seduta"] = $_GET["seduta"];
					}
					$ris = $pdo->bindAndExec($sql,$bind);
					if (!isset($_GET["seduta"])) {
						?>
						<a class="submit_big" href ="index.php?codice=<? echo $_GET["codice"] ?>&lotto=<? echo $codice_lotto ?>&seduta=0">
							Prima Seduta <?= mysql2completedate($record_gara["data_apertura"]); ?>
						</a>
						<?
						if ($ris->rowCount()>0)  {
							while($select = $ris->fetch(PDO::FETCH_ASSOC)) {
							?>
								<a class="submit_big" href ="index.php?codice=<? echo $_GET["codice"] ?>&lotto=<? echo $codice_lotto ?>&seduta=<?= $select["codice"] ?>">
									Seduta <?= $select["nome"] ?> - <?= mysql2completedate($select["data_apertura"]); ?>
								</a>
							<?
							}
						}
					} else {
						if (empty($_GET["seduta"])) {
							$seduta=[];
							$seduta["codice"] = 0;
							$seduta["data_apertura"] = $record_gara["data_apertura"];
						} else {
							$seduta = $ris->fetch(PDO::FETCH_ASSOC);
						}
					}
					if (!empty($seduta)) {
						$bind = array();
						$bind[":codice"] = $codice;
						$bind[":codice_lotto"] = $codice_lotto;
						$bind[":data_apertura"] = $seduta["data_apertura"];
						$next = $pdo->go("SELECT data_apertura FROM b_date_apertura WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_apertura > :data_apertura ORDER BY data_apertura LIMIT 0,1",$bind);
						if ($next->rowCount() == 1) {
							$next = $next->fetch(PDO::FETCH_ASSOC);
						} else {
							unset($next);
						}

						$bind = array();
						$bind[":codice"] = $codice;
						$bind[":codice_lotto"] = $codice_lotto;
						$sql = "SELECT r_partecipanti.* FROM r_partecipanti ";
						$sql .= "WHERE r_partecipanti.codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
						$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
						$editor_tipo = "seduta_{$seduta["codice"]}";
						$bind = array();
						$bind[":tipo"] = $editor_tipo;
						$bind[":codice"] = $record_gara["codice"];
						$bind[":codice_lotto"] = $codice_lotto;
						$strsql = "SELECT * FROM b_documentale WHERE tipo = :tipo AND attivo = 'S' AND sezione = 'gara' AND  codice_gara = :codice AND codice_lotto = :codice_lotto";
						$risultato = $pdo->bindAndExec($strsql,$bind);
						if ($risultato->rowCount()>0) {
							$elemento = $risultato->fetch(PDO::FETCH_ASSOC);
							$html = $elemento["corpo"];
							$operazione = "UPDATE";
							$codice_elemento = $elemento["codice"];
						} else {
							$operazione = "INSERT";
							$codice_elemento = 0;
							$vocabolario["##data_seduta##"] = mysql2completedate($seduta["data_apertura"]);
							$record_gara["data_accesso"] = mysql2completedate($record_gara["data_accesso"]);
							$record_gara["data_scadenza"] = mysql2completedate($record_gara["data_scadenza"]);
							$record_gara["data_apertura"] = mysql2completedate($record_gara["data_apertura"]);
							$record_gara["data_atto_indizione"] = mysql2date($record_gara["data_atto_indizione"]);
							$record_gara["data_pubblicazione"] = mysql2date($record_gara["data_pubblicazione"]);
							$record_gara["url"] = "https://" . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
							$bind = array();
							$bind[":codice"] = $record_gara["criterio"];

							$sql = "SELECT * FROM b_criteri WHERE codice = :codice";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								$rec = $ris->fetch(PDO::FETCH_ASSOC);
								$record_gara["nome_criterio"] = $rec["criterio"];
								$directory = $rec["directory"];
								/* Completamento in caso di Massimo ribasso */
								if ($record_gara["criterio"] == 6) {
									$oggetto_ribasso = " sull'importo a base di gara";
									$bind = array();
									$bind[":codice"] = $record_gara["codice"];
									$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione = 58";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) $oggetto_ribasso = " sull'elenco prezzi";
									$record_gara["nome_criterio"] .= $oggetto_ribasso;
								}
								/* Fine completamento MR */
								$record_gara["riferimento_criterio"] = $rec["riferimento_normativo"];
							}
							$bind = array();
							$bind[":codice"] = $record_gara["procedura"];
							$sql = "SELECT * FROM b_procedure WHERE codice=:codice";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								$rec = $ris->fetch(PDO::FETCH_ASSOC);
								$record_gara["nome_procedura"] = $rec["nome"];
								$record_gara["riferimento_procedura"] = $rec["riferimento_normativo"];
							}

							$bind = array();
							$bind[":codice"] = $record_gara["codice"];
							$record_gara["tipologie_gara"] = "";
							$sql = "SELECT tipologia FROM b_tipologie JOIN b_importi_gara ON b_tipologie.codice = b_importi_gara.codice_tipologia WHERE b_importi_gara.codice_gara = :codice";
							$ris_tipologie = $pdo->bindAndExec($sql,$bind);
							if ($ris_tipologie->rowCount()>0) {
								while($rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC)) {
									$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
								}
							}
							$bind = array();
							$bind[":codice"] = $record_gara["codice"];
							$sql = "SELECT b_importi_gara.*, b_tipologie.tipologia FROM b_importi_gara JOIN b_tipologie ON b_importi_gara.codice_tipologia = b_tipologie.codice WHERE codice_gara = :codice";
							$ris_importi = $pdo->bindAndExec($sql,$bind);

							$bind = array();
							$bind[":codice_ente"]=$record_gara["codice_ente"];

							$sql = "SELECT * FROM b_enti WHERE codice = :codice_ente";
							$ris_ente = $pdo->bindAndExec($sql,$bind);
							if ($ris_ente->rowCount()>0) $record_appaltatore = $ris_ente->fetch(PDO::FETCH_ASSOC);

							$bind = array();
							$bind[":codice_ente"]=$record_gara["codice_gestore"];
							$sql = "SELECT * FROM b_enti WHERE codice = :codice_ente";
							$ris_gestore = $pdo->bindAndExec($sql,$bind);
							if ($ris_gestore->rowCount()>0) $record_gestore = $ris_gestore->fetch(PDO::FETCH_ASSOC);

								$chiavi = array_keys($record_appaltatore);
								foreach($chiavi as $chiave) {
									$vocabolario["#record_appaltatore-".$chiave."#"] = $record_appaltatore[$chiave];
								}
								$vocabolario["#record_appaltatore-logo-path#"] = ! empty($record_appaltatore["logo"]) ? "{$config["link_sito"]}/documenti/enti/{$record_appaltatore["logo"]}" : "{$config["link_sito"]}/img/no_logo.gif";

								$chiavi = array_keys($record_gestore);
								foreach($chiavi as $chiave) {
									$vocabolario["#record_gestore-".$chiave."#"] = $record_gestore[$chiave];
								}
								$vocabolario["#record_gestore-logo-path#"] = ! empty($record_gestore["logo"]) ? "{$config["link_sito"]}/documenti/enti/{$record_gestore["logo"]}" : "{$config["link_sito"]}/img/no_logo.gif";

								$record_gara["totale_progetto"] = number_format($record_gara["prezzoBase"]+$record_gara["somme_disponibili"],2,",",".");

								$chiavi = array_keys($record_gara);
								foreach($chiavi as $chiave) {
									$vocabolario["#record_gara-".$chiave."#"] = $record_gara[$chiave];
								}


								/* ----------------- INIZIO VARIABILI VOCABOLARIO ----------------- */

								$bind = array();
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_lotto"] = $codice_lotto;
								$bind[":data_apertura"] = $seduta["data_apertura"];

								$sql = "SELECT b_log_aperture.timestamp, r_partecipanti.partita_iva, r_partecipanti.ragione_sociale, b_criteri_buste.nome
												FROM b_log_aperture JOIN b_criteri_buste ON b_log_aperture.codice_busta = b_criteri_buste.codice
												JOIN r_partecipanti ON b_log_aperture.codice_partecipante = r_partecipanti.codice
												WHERE r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
												AND b_log_aperture.esito = 'Positivo' AND b_log_aperture.timestamp >= :data_apertura ";
								if (isset($next)) {
									$bind[":data_next"] = $next["data_apertura"];
									$sql .= " AND b_log_aperture.timestamp <= :data_next";
								}
								$sql.=" ORDER BY b_log_aperture.timestamp ";
								$ris_buste = $pdo->bindAndExec($sql,$bind);
								$vocabolario["#timing_apertura#"] = "";
								if ($ris_buste->rowCount()>0) {
									$vocabolario["#timing_apertura#"] = "<table style='width:100%'>";
									while ($record_busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
										$vocabolario["#timing_apertura#"] .= "<tr>
											<td style='width:15%'>" . mysql2completedate($record_busta["timestamp"]) . "</td>
											<td style='width:25%'>" .strtoupper($record_busta["nome"]) . "</td>
											<td style='width:20%'>". strtoupper($record_busta["partita_iva"])."</td>
											<td style='width:35%'>". strtoupper($record_busta["ragione_sociale"])."</td></tr>";
									}
									$vocabolario["#timing_apertura#"] .= "</table>";
								}

								$bind = array();
								$bind[":codice"] = $record_gara["codice"];

								$sql = "SELECT * FROM b_commissioni WHERE codice_gara = :codice ORDER BY valutatore ";
								$ris_commissione = $pdo->bindAndExec($sql,$bind);
								$vocabolario["#firme_componenti#"] = "";
								$current_valutatore = "";
								if ($ris_commissione->rowCount()>0) {
									while($componente = $ris_commissione->fetch(PDO::FETCH_ASSOC)) {
										if ($current_valutatore != $componente["valutatore"]) {
											$current_valutatore = $componente["valutatore"];
											if ($current_valutatore == "S") {
													$vocabolario["#firme_componenti#"] .= "<br><strong>Commissione valuatrice:</strong><br>";
											} else {
												$vocabolario["#firme_componenti#"] .= "<br><strong>Seggio di gara:</strong><br>";
											}
										}
										$vocabolario["#firme_componenti#"] .= "<strong>" . strtoupper($componente["ruolo"]) . ":</strong> " . $componente["titolo"] . " " . $componente["cognome"] . " " . $componente["nome"] . "<br>";
									}
								}

								$bind_soccorsi = [":codice_lotto"=>$codice_lotto,":codice_gara"=>$record_gara["codice"],":data_apertura"=>$seduta["data_apertura"]];
								$vocabolario["#partecipanti#"] = $ris_partecipanti->rowCount();
								$vocabolario["#esito_controllo_documentazione#"] = "";
								$sql_soccorsi = "SELECT b_integrazioni.richiesta, b_integrazioni.data_scadenza, r_partecipanti.ragione_sociale, r_partecipanti.partita_iva
													FROM b_integrazioni JOIN r_integrazioni ON b_integrazioni.codice = r_integrazioni.codice_integrazione
													JOIN r_partecipanti ON r_partecipanti.codice = r_integrazioni.codice_partecipante 
													WHERE b_integrazioni.soccorso_istruttorio = 'S' AND b_integrazioni.codice_lotto = :codice_lotto AND b_integrazioni.codice_gara = :codice_gara 
													AND b_integrazioni.timestamp >= :data_apertura ";
								if (isset($next)) {
									$bind_soccorsi[":data_next"] = $next["data_apertura"];
									$sql_soccorsi .= " AND b_integrazioni.timestamp <= :data_next ";
								}
								$ris_soccorsi = $pdo->go($sql_soccorsi,$bind_soccorsi);
								if ($ris_soccorsi->rowCount() > 0) {
									$vocabolario["#esito_controllo_documentazione#"] .= "Sono state attivate le seguenti procedure di soccorso istruttorio:<br><br>
																																				<table style='width:100%'>
																																				<tr>
																																					<th>Partita IVA</th>
																																					<th>Ragione Sociale</th>
																																					<th>Richiesta</th>
																																					<th>Termine di presentazione</th>
																																				</tr>";
									while($soccorso = $ris_soccorsi->fetch(PDO::FETCH_ASSOC)) {
										$vocabolario["#esito_controllo_documentazione#"] .= "<tr>
																																					<td>{$soccorso["partita_iva"]}</td>
																																					<td>{$soccorso["ragione_sociale"]}</td>
																																					<td>{$soccorso["richiesta"]}</td>
																																					<td>".mysql2datetime($soccorso["data_scadenza"])."</td>
																																				</tr>";
									}
									$vocabolario["#esito_controllo_documentazione#"] .= "</table>";
									$vocabolario["#esito_controllo_documentazione#"] .= "<br><br>";
								}

								$vocabolario["#esito_controllo_documentazione#"] .= "Dal controllo effettuato dalla Commissione di gara risulta che la documentazione &egrave; conforme e le ditte concorrenti di cui sopra vengono ammesse alla fase successiva di gara.";
								$escluse = false;
								if ($ris_partecipanti->rowCount()>0) {
									$vocabolario["#elenco_ditte#"] = "<table style='width:100%'>";
									$vocabolario["#elenco_ditte#"] .= "<tr>";
									$vocabolario["#elenco_ditte#"] .= "<th style=\"width:6%\">#</th>";
									$vocabolario["#elenco_ditte#"] .= "<th style=\"width:19%\" colspan='2'>Protocollo</th>";
									$vocabolario["#elenco_ditte#"] .= "<th style=\"width:15%\">Codice Fiscale</th>";
									$vocabolario["#elenco_ditte#"] .= "<th style=\"width:60%\">Ragione Sociale</th></tr>";
									$i = 0;
									$sql = "SELECT * FROM r_partecipanti WHERE codice_capogruppo = :codice ";
									$sub = $pdo->prepare($sql);
									while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
										$i++;
										$sub->bindValue(":codice",$partecipante["codice"]);
										$sub->execute();
										$sub_num = $sub->rowCount();
										$rowspan = "1";
										if ($sub_num > 0) $rowspan = $sub_num + 1;
										$vocabolario["#elenco_ditte#"] .= "<tr>";
										$vocabolario["#elenco_ditte#"] .= "<td rowspan=\"{$rowspan}\" style=\"width:6%\">" . $i . "</td>";
										if ($partecipante["numero_protocollo"] == "") {
											$vocabolario["#elenco_ditte#"] .= "<td rowspan=\"{$rowspan}\" style=\"width:10%\">" . $partecipante["codice"] . "</td>";
											$vocabolario["#elenco_ditte#"] .= "<td rowspan=\"{$rowspan}\" style=\"width:9%\">" . mysql2datetime($partecipante["timestamp"]) . "</td>";
										} else {
											$vocabolario["#elenco_ditte#"] .= "<td rowspan=\"{$rowspan}\" style=\"width:10%\">" . $partecipante["numero_protocollo"] . "</td>";
											$vocabolario["#elenco_ditte#"] .= "<td rowspan=\"{$rowspan}\" style=\"width:9%\">" . mysql2datetime($partecipante["data_protocollo"]) . "</td>";
										}
										$vocabolario["#elenco_ditte#"] .= "<td style=\"width:15%\">" . strtoupper($partecipante["partita_iva"]) . "</td>";
										$vocabolario["#elenco_ditte#"] .= "<td style=\"width:60%\">" . strtoupper($partecipante["ragione_sociale"]);
										if ($sub_num > 0) $vocabolario["#elenco_ditte#"] .= " - " . $partecipante["tipo"];
										$vocabolario["#elenco_ditte#"] .= "</td>";
										$vocabolario["#elenco_ditte#"] .= "</tr>";
										if ($sub_num > 0) {
											while ($s = $sub->fetch(PDO::FETCH_ASSOC)) {
												$vocabolario["#elenco_ditte#"] .= "<tr><td>{$s["partita_iva"]}</td><td>{$s["ragione_sociale"]} - {$s["tipo"]}</td></tr>";
											}
										}
										if ($partecipante["ammesso"]=="N") {
											if (!$escluse) {
												$escluse = true;
												$vocabolario["#esito_controllo_documentazione#"] = substr($vocabolario["#esito_controllo_documentazione#"],0,-1) . ", tranne le seguenti: <br>";
												$vocabolario["#esito_controllo_documentazione#"] .=  "<br><br><table style='width:100%'>";
												$vocabolario["#esito_controllo_documentazione#"] .= "<tr>";
												$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:6%\">#</td>";
												$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:19%\" colspan='2'>Protocollo</td>";
												$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:15%\">Codice Fiscale</td>";
												$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:40%\">Ragione Sociale</td>";
												$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:20%\">Motivazione</td></tr>";
											}
											$vocabolario["#esito_controllo_documentazione#"] .= "<tr>";
											$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:6%\">" . $i . "</td>";
											if ($partecipante["numero_protocollo"] == "") {
												$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:10%\">" . $partecipante["codice"] . "</td>";
												$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:9%\">" . mysql2date($partecipante["timestamp"]) . "</td>";
											} else {
												$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:10%\">" . $partecipante["numero_protocollo"] . "</td>";
												$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:9%\">" . mysql2date($partecipante["data_protocollo"]) . "</td>";
											}
											$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:15%\">" . strtoupper($partecipante["partita_iva"]) . "</td>";
											$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:40%\">" . strtoupper($partecipante["ragione_sociale"]) . "</td>";
											$vocabolario["#esito_controllo_documentazione#"] .= "<td style=\"width:20%\">" . $partecipante["motivazione"] . "</td>";
											$vocabolario["#esito_controllo_documentazione#"] .= "</tr>";
										}
									}
									if ($escluse) $vocabolario["#esito_controllo_documentazione#"] .= "</table>";
									$vocabolario["#elenco_ditte#"] .= "</table>";
								}
								$sql_modello = "SELECT * FROM b_modelli_standard WHERE codice = 29"; // Codice 29 - Verbale Seduta
								$ris_modello = $pdo->query($sql_modello);
								if ($ris_modello->rowCount()>0) {
									$modello = $ris_modello->fetch(PDO::FETCH_ASSOC);
									$bind = array();
									$bind[":codice"] = $modello["codice"];
									$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
									$sql = "SELECT * FROM b_modelli_enti WHERE attivo = 'S' AND codice_modello = :codice AND codice_ente = :codice_ente";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) {
										$modello = $ris->fetch(PDO::FETCH_ASSOC);
									}
									$html = strtr($modello["corpo"],$vocabolario);
								} else {
									echo "<h1>Modello mancante</h1>";
								}
							}
						?>
						<form name="box" method="post" action="save.php" rel="validate">
							<input type="hidden" name="operazione" value="<? echo $operazione ?>">
							<input type="hidden" name="codice" value="<? echo $codice_elemento; ?>">
							<input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"]; ?>">
							<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
							<input type="hidden" name="codice_seduta" value="<? echo $seduta["codice"]; ?>">
							<input type="hidden" name="data_seduta" value="<? echo $seduta["data_apertura"]; ?>">
							<input type="hidden" name="allega" id="allega" value="N">
							<?
								$file_title = "Verbale Seduta " . mysql2datetime($seduta["data_apertura"]);
								include($root."/moduli/editor.php");
							if ($codice_elemento>0) { ?>
								<input type="button" style="background-color:#C00" class="submit_big" onClick="elimina('<? echo $codice_elemento ?>','gare/verbali/I_verbale');" src="/img/del.png" value="Rielabora verbale">
							<? } ?>
								<input class="submit_big" type="submit" value="Salva">
								<input class="submit_big" type="submit" onclick="$('#allega').val('S');return true;" value="Salva ed Allega">
						</form>
						<div class="clear"></div>
						<?
					}
			}
			include($root."/gare/ritorna.php");
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
