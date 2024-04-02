<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
	$tipi_editor = array("Bando","Disciplinare","Invito");
		if ((isset($_GET["codice"]) || isset($_GET["cod"])) && in_array($_GET["type"],$tipi_editor)) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$strsql = "SELECT * FROM b_gestione_gare WHERE link LIKE '/gare/elaborazione/modello.php%'";
					$risultato = $pdo->query($strsql);
					if ($risultato->rowCount()>0) {
						$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
						$esito = check_permessi_gara($gestione["codice"],$_GET["codice"],$_SESSION["codice_utente"]);
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
				$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					$editor_tipo = $_GET["type"];
					$bind = array();
					$bind[":tipo"] = $editor_tipo;
					$bind[":codice_gara"] = $record_gara["codice"];
					$strsql = "SELECT * FROM b_documentale WHERE tipo = :tipo AND attivo = 'S' AND sezione = 'gara' AND codice_gara = :codice_gara";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount()>0) {
						$elemento	 = $risultato->fetch(PDO::FETCH_ASSOC);
						$html = $elemento["corpo"];
						$operazione = "UPDATE";
						$codice_elemento = $elemento["codice"];
					} else {
						$bind = array();
						$bind[":codice_gara"] = $record_gara["codice"];
						$sql = "SELECT b_importi_gara.*, b_tipologie_importi.titolo AS tipologia FROM b_importi_gara JOIN
										b_tipologie_importi ON b_importi_gara.codice_tipologia = b_tipologie_importi.codice
										WHERE b_importi_gara.codice_gara = :codice_gara";
						$ris_importi = $pdo->bindAndExec($sql,$bind);
						if ($ris_importi->rowCount()) {
							$ris_importi = $ris_importi->fetchAll(PDO::FETCH_ASSOC);
							$totale_gara = 0;
							foreach($ris_importi AS $rec_importo) {
								$totale_gara = $totale_gara + $rec_importo["importo_base"] + $rec_importo["importo_oneri_no_ribasso"]; // + $rec_importo["importo_oneri_ribasso"] + $rec_importo["importo_personale"];
							}
							$bind = array();
							$bind[":tipologia"] = $record_gara["tipologia"];
							$bind[":criterio"] = $record_gara["criterio"];
							$bind[":procedura"] = $record_gara["procedura"];
							$bind[":tipo"] = $editor_tipo;
							$bind[":totale_gara"] = $totale_gara;
							$strsql  = "SELECT b_modelli_new.* FROM b_modelli_new WHERE attivo ='S' AND (tipologia = :tipologia OR tipologia = 0)";
							$strsql .= " AND (criterio = :criterio OR criterio = 0)";
							$strsql .= " AND procedura = :procedura";
							$strsql .= " AND tipo = :tipo ";
							$strsql .= " AND (importo_massimo >= :totale_gara OR importo_massimo = 0)";
							$strsql .= " AND (importo_minimo <= :totale_gara OR importo_minimo = 0)";
							$risultato_modelli = $pdo->bindAndExec($strsql,$bind);
							if ($risultato_modelli->rowCount()>0) {
								$modello = $risultato_modelli->fetch(PDO::FETCH_ASSOC);
								$bind = array();
								$bind[":codice_modello"] = $modello["codice"];
								$bind[":modalita"] = $record_gara["modalita"];
								$bind[":totale_gara"] = $totale_gara;
								$strsql = "SELECT * FROM b_paragrafi_new WHERE codice_modello = :codice_modello AND attivo = 'S' AND eliminato = 'N' ";
								$strsql .= " AND (modalita = :modalita OR modalita = 0)";
								$strsql .= " AND (criteri REGEXP '[[:<:]]{$record_gara['criterio']}[[:>:]]' OR criteri = '' OR criteri IS NULL)";
								$strsql .= " AND (tipologie REGEXP '[[:<:]]{$record_gara['tipologia']}[[:>:]]' OR tipologie = '' OR tipologie IS NULL)";
								$strsql .= " AND (importo_massimo >= :totale_gara OR importo_massimo = 0)";
								$strsql .= " AND (importo_minimo <= :totale_gara OR importo_minimo = 0)";
								$strsql .= " ORDER BY ordinamento ";
								$risultato_paragrafi = $pdo->bindAndExec($strsql,$bind);
								if ($risultato_paragrafi->rowCount()>0) {
									$operazione = "INSERT";
									$codice_elemento = 0;
									$record_gara["totale_appalto"] = number_format($totale_gara,"2",",",".");
									$record_gara["data_accesso"] = mysql2completedate($record_gara["data_accesso"]);
									$record_gara["data_scadenza"] = mysql2completedate($record_gara["data_scadenza"]);
									$record_gara["data_apertura"] = mysql2completedate($record_gara["data_apertura"]);
									$record_gara["data_pubblicazione"] = mysql2date($record_gara["data_pubblicazione"]);
									$record_gara["data_atto_insizione"] = mysql2date($record_gara["data_atto_indizione"]);
									$record_gara["url"] = "https://" . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
									$_SESSION["gara"] = $record_gara;

									/* VALUTAZIONE SOA: INIZIO */

									$array_soa = array();

									$bind = array();
									$bind[":codice_gara"] = $record_gara["codice"];

										$strsql = "SELECT b_categorie_soa.*, b_qualificazione_lavori.tipo, SUM(b_qualificazione_lavori.importo_base) AS importo_base
										FROM b_qualificazione_lavori JOIN b_categorie_soa ON b_qualificazione_lavori.codice_categoria = b_categorie_soa.codice
										WHERE codice_gara = :codice_gara GROUP BY codice_gara, tipo, id ORDER BY b_qualificazione_lavori.tipo ";
										$ris_qualificazione_gara = $pdo->bindAndExec($strsql,$bind);
										$sios = array();
										$qo = array();
										$no_qo = array();
										$sios_30 = array();
										$sios_no_30 = array();
										$tutelate = array();
										$scorporabili = array();
										$og11 = array();
										if ($ris_qualificazione_gara->rowCount() > 1) {
												$totale_sios = 0;
												$array_soa[] = 11;
												while($categoria = $ris_qualificazione_gara->fetch(PDO::FETCH_ASSOC)) {
														if ($categoria["tipo"] != "P") {
																$flag_sios = false;
																$scorporabili[] = $categoria["id"];
																if ($categoria["sios"] == "S") {
																	if ((($categoria["importo_base"]*100)/$record_gara["prezzoBase"]) > 15) {
																		$flag_sios = true;
																		$array_soa[] = 1;
																		$sios[] = $categoria["id"];
																		$totale_sios += $categoria["importo_base"];
																		$categoria["classifica"] = "";$bind = array();
													          $bind[":importo_base"] = $categoria["importo_base"];
															      $sql_classifica = "SELECT * FROM b_classifiche_soa WHERE attivo = 'S' AND minimo <= :importo_base AND (massimo >= :importo_base OR massimo = 0)";
																		$ris_classifica = $pdo->bindAndExec($sql_classifica,$bind);
																		if ($ris_classifica->rowCount() > 0) {
																			$classifica = $ris_classifica->fetch(PDO::FETCH_ASSOC);
																			$categoria["classifica"] = "";$bind = array();
														          $bind[":importo_base"] = $categoria["importo_base"] * 0.7;
																      $sql_classifica = "SELECT * FROM b_classifiche_soa WHERE attivo = 'S' AND minimo <= :importo_base AND (massimo >= :importo_base OR massimo = 0)";
																			$ris_classifica = $pdo->bindAndExec($sql_classifica,$bind);
																			if ($ris_classifica->rowCount() > 0) {
																				$classifica_ridotta = $ris_classifica->fetch(PDO::FETCH_ASSOC);
																				if ($classifica_ridotta["id"] != $classifica["id"]) {
																					$sios_30[] = $categoria["id"];
																					$array_soa[] = 2;
																				} else {
																					$sios_no_30[] = $categoria["id"];
																					$array_soa[] = 3;
																				}
																			}
																		}

																	}
																}
																if ($categoria["obbligo_qualificazione"] == "S" && !$flag_sios) {
																	if (((($categoria["importo_base"]*100)/$record_gara["prezzoBase"]) > 10) || $categoria["importo_base"] > 150000) {
																		$array_soa[] = 6;
																		$qo[] = $categoria["id"];
																	}
																}
																if ($categoria["obbligo_qualificazione"] == "N") {
																	$array_soa[] = 7;
																	$no_qo[] = $categoria["id"];
																}
																if ($categoria["tutelate"] == "N") {
																	$array_soa[] = 8;
																	$tutelate[] = $categoria["id"];
																}
																if ($categoria["id"] == "OG 11") $array_soa[] = 9;
																if (($categoria["id"] == "OS 3") || ($categoria["id"] == "OS 28") || ($categoria["id"] == "OS 30")) {
																	$og11[$categoria["id"]] = $categoria["importo_base"];
																}
															}
														}
												if (count($tutelate) == 0) $array_soa[]=13;
												if (count($og11)==3) {
													$flag = 0;
													foreach($og11 AS $key=>$importo) {
														switch($key) {
															case "OS 3":
																if ((($importo*100)/$record_gara["prezzoBase"]) >= 10) $flag++;
															break;
															case "OS 28":
																if ((($importo*100)/$record_gara["prezzoBase"]) >= 25) $flag++;
															break;
															case "OS 30":
																if ((($importo*100)/$record_gara["prezzoBase"]) >= 25) $flag++;
															break;
														}
													}
													if ($flag == 3) $array_soa[]=9;
												}
												if ($totale_sios>0) {
													if (($record_gara["prezzoBase"] - ($totale_sios*0.7))>20658000) {
														$array_soa[] = 4;
													} else {
														$array_soa[] = 5;
													}
												} else {
													$array_soa[] = 12;
												}
										} else {
											$array_soa[] = 10;
										}

										$array_soa = array_unique($array_soa);
										$sios = implode(",",$sios);
										$qo =  implode(",",$qo);
										$no_qo =  implode(",",$no_qo);
										$sios_30 =  implode(",",$sios_30);
										$sios_no_30 =  implode(",",$sios_no_30);
										$tutelate =  implode(",",$tutelate);
										$scorporabili =  implode(",",$scorporabili);
									/* VALUTAZIONE SOA: FINE */

									$bind = array();
									$bind[":criterio"] = $record_gara["criterio"];
									$sql = "SELECT * FROM b_criteri WHERE codice= :criterio";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) {
										$rec = $ris->fetch(PDO::FETCH_ASSOC);
										$record_gara["nome_criterio"] = $rec["criterio"];
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
									$bind[":procedura"] = $record_gara["procedura"];
									$sql = "SELECT * FROM b_procedure WHERE codice= :procedura";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) {
										$rec = $ris->fetch(PDO::FETCH_ASSOC);
										$record_gara["nome_procedura"] = $rec["nome"];
										$record_gara["riferimento_procedura"] = $rec["riferimento_normativo"];
									}
									$bind = array();
									$bind[":tipologia"] = $record_gara["tipologia"];
									$record_gara["tipologie_gara"] = "";
									$sql = "SELECT tipologia FROM b_tipologie WHERE b_tipologie.codice = :tipologia";
									$ris_tipologie = $pdo->bindAndExec($sql,$bind);
									if ($ris_tipologie->rowCount()>0) {
										$rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC);
										$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
									}

									$bind = array();
									$bind[":codice_ente"] = $record_gara["codice_ente"];

									$sql = "SELECT b_enti.*, b_tipologie_ente.esender AS tipo_amministrazione, b_tipo_attivita.value AS settori_attivita
									 FROM b_enti JOIN b_tipologie_ente ON b_enti.tipologia_ente = b_tipologie_ente.codice
									JOIN b_tipo_attivita ON b_enti.tipo_attivita = b_tipo_attivita.tag
									WHERE b_enti.codice = :codice_ente";

									$ris_ente = $pdo->bindAndExec($sql,$bind);
									if ($ris_ente->rowCount()>0) {
										$record_appaltatore = $ris_ente->fetch(PDO::FETCH_ASSOC);
										switch ($record_appaltatore["tipo_amministrazione"]) {
											case "REGIONAL_AUTHORITY": $record_appaltatore["tipo_amministrazione"] = "Autorit&agrave; Regionali"; break;
											case "MINISTRY": $record_appaltatore["tipo_amministrazione"] = "Ministero o altri uffici nazionale, locale o regionale"; break;
											case "BODY_PUBLIC": $record_appaltatore["tipo_amministrazione"] = "Organismo di diritto pubblico"; break;
											case "EU_INSTITUTION": $record_appaltatore["tipo_amministrazione"] = "Istituzione europea o internazionale"; break;
											case "NATIONAL_AGENCY": $record_appaltatore["tipo_amministrazione"] = "Agenzia nazionale"; break;
											case "REGIONAL_AGENCY": $record_appaltatore["tipo_amministrazione"] = "Agenzia regionale"; break;
										}
									}

									$bind = array();
									$bind[":codice_ente"] = $record_gara["codice_gestore"];

									$sql = "SELECT b_enti.*, b_tipologie_ente.esender AS tipo_amministrazione, b_tipo_attivita.value AS settori_attivita
									FROM b_enti JOIN b_tipologie_ente ON b_enti.tipologia_ente = b_tipologie_ente.codice
									JOIN b_tipo_attivita ON b_enti.tipo_attivita = b_tipo_attivita.tag
									WHERE b_enti.codice = :codice_ente";
									$ris_gestore = $pdo->bindAndExec($sql,$bind);
									if ($ris_gestore->rowCount()>0) {
										$record_gestore = $ris_gestore->fetch(PDO::FETCH_ASSOC);
										switch ($record_gestore["tipo_amministrazione"]) {
											case "REGIONAL_AUTHORITY": $record_gestore["tipo_amministrazione"] = "Autorit&agrave; Regionali"; break;
											case "MINISTRY": $record_gestore["tipo_amministrazione"] = "Ministero o altri uffici nazionale, locale o regionale"; break;
											case "BODY_PUBLIC": $record_gestore["tipo_amministrazione"] = "Organismo di diritto pubblico"; break;
											case "EU_INSTITUTION": $record_gestore["tipo_amministrazione"] = "Istituzione europea o internazionale"; break;
											case "NATIONAL_AGENCY": $record_gestore["tipo_amministrazione"] = "Agenzia nazionale"; break;
											case "REGIONAL_AGENCY": $record_gestore["tipo_amministrazione"] = "Agenzia regionale"; break;
										}
									}

									$vocabolario = array();
									$vocabolario["#no_qo#"] = $no_qo;
									$vocabolario["#sios#"] = $sios;
									$vocabolario["#qo#"] = $qo;
									$vocabolario["#no_qo#"] = $no_qo;
									$vocabolario["#sios_trenta#"] = $sios_30;
									$vocabolario["#sios_no_trenta#"] = $sios_no_30;
									$vocabolario["#tutelate#"] = $tutelate;
									$vocabolario["#scorporabili#"] = $scorporabili;
									$chiavi = array_keys($record_appaltatore);
									foreach($chiavi as $chiave) {
										$vocabolario["#record_appaltatore-".$chiave."#"] = $record_appaltatore[$chiave];
									}
									$chiavi = array_keys($record_gestore);
									foreach($chiavi as $chiave) {
										$vocabolario["#record_gestore-".$chiave."#"] = $record_gestore[$chiave];
									}
									$chiavi = array_keys($record_gara);
									foreach($chiavi as $chiave) {
										$vocabolario["#record_gara-".$chiave."#"] = $record_gara[$chiave];
									}
									$vocabolario["#record_gara-importo_due_pc#"] = number_format($record_gara["prezzoBase"] * 0.02,2,",",".");
									$perc5 = $record_gara["prezzoBase"] * 0.05;
									if ($perc5 < 500000) $perc5 = 500000;
									if ($perc5 > 5186000) $perc5 = 5186000;
									$vocabolario["#record_gara-importo_cinguq_pc#"] = number_format($perc5,2,",",".");

//									echo "<pre>"; print_r($vocabolario); echo "</pre>";

									$html = "<html><head><style>";
									$html.= "body {	font-family: Tahoma, Geneva, sans-serif; text-align:justify; }";
									$html.= "div { margin:1px; padding:10px;border:1px solid #000; } ;";
									$html.= "div div { margin:0px; padding:0px; margin-left:20px; border:none }";
									$html.= "table td { padding:2px; border:1px solid #000 } ";
									$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
									$html.= "ol li ol {list-style-type:lower-alpha;}";
									$html.= "h2 { border-bottom:1px solid #000 }";
									$html.= "</style></head><body>";
									ob_start();
									?>
									<table align="center" border="0" cellpadding="1" cellspacing="1" style="width:100%">
										<tbody>
											<tr>
												<td style="text-align:center; width:20%;">
													<?
													if (file_exists("{$config["pub_doc_folder"]}/enti/{$record_gestore["riferimento"]}")) {
														?><img alt="" src="<?= "{$config["link_sito"]}/documenti/enti/{$record_gestore["logo"]}" ?>" style="width:100px" /><?
													}
													?>
												</td>
												<td>
													<h1 style="text-align:center"><?= $record_gestore["denominazione"] ?></h1>
													<?
													if ($record_gestore["codice"] != $record_appaltatore["codice"]) {
														?>
														<h1 style="text-align:center">Stazione unica appaltante</h1>
														<h2 style="text-align:center"><?= $record_appaltatore["denominazione"] ?></h2>
														<?
													}
													?>
												</td>
												<td style="text-align:center; width:20%;">
													<?
													if (file_exists("{$config["pub_doc_folder"]}/enti/{$record_appaltatore["riferimento"]}")) {
														?><img alt="" src="<?=  "{$config["link_sito"]}/documenti/enti/{$record_appaltatore["logo"]}" ?>" style="width:100px" /><?
													}
													?>
												</td>
											</tr>
											<tr>
												<td colspan="3">
													<h2 style="text-align:center">Bando di gara mediante Procedura <?= $record_gara["nome_procedura"] ?></h2>
													<div style="text-align:center">
														Procedura: <?= $record_gara["nome_procedura"] ?> ai sensi dell&#39;<?= $record_gara["riferimento_procedura"] ?><br />
														Criterio: <?= $record_gara["nome_criterio"] ?> ai sensi dell&#39;<?= $record_gara["riferimento_criterio"] ?>
													</div>
												</td>
											</tr>
											<tr>
												<td colspan="3"><strong>Oggetto</strong>: <?= $record_gara["oggetto"] ?></td>
											</tr>
										</tbody>
									</table>
									<?
									$html .= ob_get_clean();
									$current_esender = "";
									while($paragrafo = $risultato_paragrafi->fetch(PDO::FETCH_ASSOC)) {
										$inserisci = true;
										if ($paragrafo["codice_opzione"] != "") {
											$opzioni = explode(",",$paragrafo["codice_opzione"]);
											foreach ($opzioni AS $opzione) {
												if ($opzione == '') $opzione = 0;
													$bind = array();
													$bind[":codice_gara"] = $record_gara["codice"];
													$bind[":opzione"] = $opzione;
													$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = :opzione";
													$ris = $pdo->bindAndExec($sql,$bind);
													if ($ris->rowCount()==0) $inserisci = false;
											}
										}
										if ($inserisci && $paragrafo["vincoli_soa"]>0) {
											if (!in_array($paragrafo["vincoli_soa"],$array_soa)) $inserisci = false;
										}
										if ($inserisci) {
										switch ($paragrafo["tipo"]) {
											case "avanzato":
												$includeFile = "bando.php";
												if ($editor_tipo == "Disciplinare") $includeFile = "disciplinare.php";
											 if (file_exists($root."/gare/elaborazione/moduli_avanzati/".$paragrafo["directory"]."/".$includeFile))	include($root."/gare/elaborazione/moduli_avanzati/".$paragrafo["directory"]."/".$includeFile);
											break;
											case "paragrafo":
														if (strpos($paragrafo["contenuto"], "<h") !== false && !empty($current_esender)) {
																$html.= "<!-- FINE " . $current_esender . " -->";
																$current_esender = "";
														}
														if (strpos($paragrafo["contenuto"], "<h") === false && !empty($paragrafo["tag_esender"])) {
																$html.= "<!-- INIZIO " . $paragrafo["tag_esender"] . " -->";
																$current_esender = $paragrafo["tag_esender"];
														}
														$html.= strtr($paragrafo["contenuto"],$vocabolario);
														if (strpos($paragrafo["contenuto"], "<h") === false && !empty($paragrafo["tag_esender"])) {
																$html.= "<!-- FINE " . $current_esender . " -->";
																$current_esender = "";
														} else if (strpos($paragrafo["contenuto"], "<h") !== false && !empty($paragrafo["tag_esender"])) {
															$html.= "<!-- INIZIO " . $paragrafo["tag_esender"] . " -->";
															$current_esender = $paragrafo["tag_esender"];
														}
											break;
											case "ricorsivo":
											$sql = "SELECT * FROM b_paragrafi_ricorsivi WHERE codice = :codice_ricorsivo ";
											$ris_rico = $pdo->bindAndExec($sql,array(":codice_ricorsivo"=>$paragrafo["codice_ricorsivo"]));
											if ($ris_rico->rowCount() == 1) {
												$html.= strtr($ris_rico->fetch(PDO::FETCH_ASSOC)["contenuto"],$vocabolario);
											}
											break;
										}
									}
								}
							$html.= "</body></html>";
						} else {
							echo "<h2>Nessun modello disponibile</h2>";
						}
					} else {
						echo "<h2>Nessun modello disponibile</h2>";
					}
				} else {
					echo "<h2>Nessun modello disponibile</h2>";
				}
			}
			if (isset($html)) {
			?>
				<h1>Modello <?= $editor_tipo ?></h1>
                <? if (!$lock) { ?>
                 <form name="box" method="post" action="save_modello.php" rel="validate">
                 <input type="hidden" name="operazione" value="<? echo $operazione ?>">
							 <input type="hidden" name="codice" value="<? echo $codice_elemento; ?>">
							 <input type="hidden" name="tipo" value="<? echo $_GET["type"] ?>">
                 <input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"]; ?>">
                <input type="hidden" name="allega" id="allega" value="N">
						<div class="comandi">
							<?
							$bind = array();
							$bind[":codice_gestore"] = $_SESSION["ente"]["codice"];
							$bind[":tipologia_a"] = $record_gara["tipologia"];
							$bind[":tipologia_b"] = $record_gara["tipologia"] . ";%";
							$bind[":tipologia_c"] = "%;" . $record_gara["tipologia"] . ";%";
							$bind[":tipologia_d"] = "%;" . $record_gara["tipologia"];
							$sql_minimi = "SELECT * FROM b_impostazioni_dati_minimi WHERE (tipologie = :tipologia_a OR tipologie LIKE :tipologia_b OR tipologie LIKE :tipologia_c OR tipologie LIKE :tipologia_d) AND
														 codice_gestore = :codice_gestore AND attivo = 'S' AND eliminato = 'N' ";
							$ris_minimi = $pdo->bindAndExec($sql_minimi,$bind);
							if ($ris_minimi->rowCount() > 0) {
								?>
								<div id="datiMinimi" style="display:none">
									<? include("dati_minimi.php"); ?>
								</div>
								<button class='btn-round btn-primary' title="Vedi dati minimi" onClick="$('#datiMinimi').dialog({
									modal:true,
									width: '800px',
									position: 'top',
								}); return false;"><span class="fa fa-search"></span></button>
								<?
							} ?>
							<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
						</div>
        <?	}
				$file_title = "Bando_di_gara";
				include($root."/moduli/editor.php");
				?>
                <? if (!$lock) { ?>
                <? if ($codice_elemento>0) { ?>
				<input type="button" style="background-color:#C00" class="submit_big" onClick="elimina('<? echo $codice_elemento ?>','gare/elaborazione/modello');" src="/img/del.png" value="Rielabora Bando">
				<? } ?>
                <input class="submit_big" type="submit" value="Salva">
				<input class="submit_big" type="submit" onclick="$('#allega').val('S');return true;" value="Salva ed Allega">
                </form>
                <?
				} else {
					?>
                    <script>
                        $(":input").not('.espandi').prop("disabled", true);
					</script>
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
