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

	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		$_SESSION["gara"] = $record;
	?>
	<h1>PERFEZIONAMENTO ASTA</h1>
						<?
						$bind = array();
						$bind[":codice"] = $record["codice"];
						$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						$print_form = false;
						if ($ris_lotti->rowCount()>0) {
							if (isset($_GET["lotto"])) {
								$codice_lotto = $_GET["lotto"];
								$bind = array();
								$bind[":codice"] = $codice_lotto;
								$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice ORDER BY codice";
								$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
								if ($ris_lotti->rowCount()>0) {
									$print_form = true;
									$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
									echo "<h2>" . $lotto["oggetto"] . "</h2>";
								}
							} else {
								?>
								<table width="100%">
									<tr><th>Lotto</th></tr>
								<?
								while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
									$bind = array();
									$bind[":codice"] = $record["codice"];
									$bind[":lotto"] = $lotto["codice"];
									$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
									$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
									?>
									<tr>
									<td>
										<a class="submit_big" href ="edit.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
											<? echo $lotto["oggetto"] ?>
										</a>
									</td>
								</tr>
									<?
								}
								?>
								</table>
								<?
							}
						} else {
							$print_form = true;
							$codice_lotto = 0;
						}
						if ($print_form) {
							$bind = array();
							$bind[":codice"] = $record["codice"];
							$bind[":lotto"] = $codice_lotto;
							$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND codice_gara = :codice AND codice_lotto = :lotto AND ammesso = 'S' AND escluso = 'N'";
							$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);

							$bind = array();
							$bind[":codice"] = $record["codice"];

							$sql_criteri = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice AND valutazione <> ''";
							$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);
							$check_criteri = true;

							if ($ris_criteri->rowCount()>0) {
								$check_criteri = false;
								$sql_confronto = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione = 124";
								$ris_confronto = $pdo->bindAndExec($sql_confronto,$bind);
								$confronto_coppie = false;
								if ($ris_confronto->rowCount()>0) $confronto_coppie = true;
								$sql_criteri  = "SELECT `codice`, `descrizione`, `punteggio`
																 FROM `b_valutazione_tecnica`
																 WHERE `punteggio_riferimento` = 2 AND valutazione = '' AND `codice_gara` = :codice";

								$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);

								$bind = array();
								$bind[":codice"] = $record["codice"];
								$bind[":lotto"] = $codice_lotto;

								$sql_punteggi = "SELECT b_punteggi_criteri.* FROM b_punteggi_criteri JOIN b_valutazione_tecnica ON b_punteggi_criteri.codice_criterio = b_valutazione_tecnica.codice
								WHERE b_punteggi_criteri.codice_gara = :codice AND b_punteggi_criteri.codice_lotto = :lotto AND b_valutazione_tecnica.valutazione = '' ";
								$ris_punteggi = $pdo->bindAndExec($sql_punteggi,$bind);
								if (($ris_criteri->rowCount() * $ris_partecipanti->rowCount()) == $ris_punteggi->rowCount()) $check_criteri = true;
							}
							if ($check_criteri) {
								$bind = array();
								$bind[":codice"] = $record["codice"];
								$bind[":lotto"] = $codice_lotto;

								$strsql = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :lotto";
								$ris_fase = $pdo->bindAndExec($strsql,$bind);
								if ($ris_fase->rowCount()>0) {
									$fase = $ris_fase->fetch(PDO::FETCH_ASSOC);
								} else {
									$fase = get_campi("b_aste");
								}
								?>
								<div id="tabs">
									<ul>
										<li><a href="#invito">Invito</a></li>
										<? if (!empty($fase["data_fine"]) && strtotime($fase["data_fine"]) < time()) { ?>
											<li><a href="#offerte">Offerte</a></li>
										<? } ?>
									</ul>
									<div id="invito">
												<?
											 if (!$lock) { ?>
													<form name="box" method="post" action="save.php" rel="validate">
														<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
														<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
														<div class="comandi">
															<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
														</div>
																		<? } ?>
											<table width="100%">
												<?
													if ($fase["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$fase["cod_allegati"])) {
																$allegati = explode(";",$fase["cod_allegati"]);
																$str_allegati = ltrim(implode(",",$allegati),",");
																$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ")";
																$ris_allegati = $pdo->query($sql);
													}
															?>
																<tr>
																	<td class="etichetta">Inizio</td>
																	<td>
																		<input title="Inizio" type="text" name="data_inizio" id="data_inizio" class="datetimepick" rel="S;16;16;DT" value="<? echo mysql2datetime($fase["data_inizio"]) ?>">
																	</td>
																	<td class="etichetta">Fine</td>
																	<td>
																		<input title="Fine" type="text" name="data_fine" id="data_fine" class="datetimepick" rel="S;16;16;DT;data_inizio;>" value="<? echo mysql2datetime($fase["data_fine"]) ?>">
																	</td>
																</tr>
											</table>
											<?
												if ($ris_partecipanti->rowCount()>0) {
													?>
													<table width="100%">
														<tr><td class="etichetta" colspan="3"><h2>Destinatari invito</h2></td></tr>
														<?
													while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
														?>
															<tr <? if ($partecipante["pec"] == "") echo "style='background-color:#EC0'"; ?>>
																<td width="10"><?= $partecipante["partita_iva"] ?></td>
																<td><strong><?= $partecipante["ragione_sociale"] ?></strong></td>
																<td><?= $partecipante["pec"] ?></td>
															</tr>
														<?
													}
													?>
													</table>
													<div id="allegati">
														<?	$cod_allegati = $fase["cod_allegati"]; ?>
														<input type="hidden" value="<? echo $cod_allegati ?>" name="cod_allegati" title="Allegati" id="cod_allegati" rel="N;0;0;A">
														<button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
															<img src="/allegati/icon.png" alt="Allega" width="15" style="vertical-align:middle"> Allega file
														</button>
														<table width="100%" id="tab_allegati">
															<? if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
																	while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
																	include($root."/allegati/tr_allegati.php");
																}
															} ?>
														</table>
													</div>

													<?
												}
											?>
									<? if (!$lock) { ?>
										<input type="submit" class="submit_big" value="Salva">
									</form>
									<?
									$form_upload["codice_gara"] = $record["codice"];
									$form_upload["online"] = 'S';
									include($root."/allegati/form_allegati.php");
									} ?>
									<?	} else {
										?>
										<h2>Errore attribuzione dei punteggi relativi all'offerta tecnica</h2>
										<strong>Si prega di controllare che a tutti i partecipanti siano assegnati correttamente i punteggi</strong>
										<?
									}
								}

									 ?>
							</div>
							<? if (!empty($fase["data_fine"]) && strtotime($fase["data_fine"]) < time()) { ?>
								<div id="offerte">
									<?
										$sql_asta = "SELECT b_offerte_economiche_asta.codice, b_offerte_economiche_asta.codice_partecipante, b_offerte_economiche_asta.codice_allegato, b_offerte_economiche_asta.codice_gara, b_offerte_economiche_asta.codice_lotto, b_offerte_economiche_asta.stato,b_offerte_economiche_asta.timestamp, r_partecipanti.ragione_sociale, r_partecipanti.partita_iva, b_lotti.oggetto FROM
																 b_offerte_economiche_asta JOIN r_partecipanti ON b_offerte_economiche_asta.codice_partecipante = r_partecipanti.codice
																LEFT JOIN b_lotti ON b_offerte_economiche_asta.codice_lotto = b_lotti.codice WHERE b_offerte_economiche_asta.codice_gara = :codice
																ORDER BY b_offerte_economiche_asta.codice_lotto, b_offerte_economiche_asta.codice DESC";
										$risultato_asta = $pdo->bindAndExec($sql_asta,array(":codice"=>$record["codice"]));
										if ($risultato_asta->rowCount() > 0) {  ?>
										<div id="asta">
										<table style="text-align:center; width:100%; font-size:0.8em">
											<thead>
												<tr><th>Codice Offerta</th><th>Operatore Economico</th><th>Esito</th><th width="200">Data</th></tr>
											</thead>
											<tbody>
										<?
												$lotto_attuale = "";
												while ($offerta = $risultato_asta->fetch(PDO::FETCH_ASSOC)) {
													if ($offerta["oggetto"] != $lotto_attuale) {
														?>
														<tr><td class="etichetta" colspan="4"><?= $offerta["oggetto"] ?></td></tr>
														<?
													}
													$operatore		= $offerta["partita_iva"] . " - <strong>" . $offerta["ragione_sociale"] . "</strong>";
													$data			= mysql2completedate($offerta["timestamp"]);
													$colore = "#C00";
													$esito = "Non confermata";
													if ($offerta["stato"] == 1) { $colore = "#0C0"; $esito = "Ultima offerta valida"; }
													if ($offerta["stato"] == 98) { $colore = "#FC0"; $esito = "Offerta superata"; }
													if (empty($offerta["codice_allegato"])) {
														$sourcePath = $config["doc_folder"] ."/" . $offerta["codice_gara"] . "/" . $offerta["codice_lotto"] . "/asta/rilancio_" . $offerta["codice"];
														if (file_exists($sourcePath)) {
															$dataOfferta = file_get_contents($sourcePath);
															$dataOfferta = openssl_decrypt($dataOfferta,$config["crypt_alg"],$offerta["codice_partecipante"],OPENSSL_RAW_DATA,$config["enc_salt"]);
															if ($dataOfferta != false) {
																$percorso = $config["arch_folder"]."/".$offerta["codice_gara"]."/".$offerta["codice_lotto"]."/asta";
																if (!is_dir($percorso)) mkdir($percorso,0777,true);

																$file_info = new finfo(FILEINFO_MIME_TYPE);
																$mime_type = $file_info->buffer($dataOfferta);
																$estensione = "p7m";
																if (strpos($mime_type, "pdf")!==false) $estensione = "pdf";

																$riferimento = getRealNameFromData($dataOfferta);
																file_put_contents($percorso."/".$riferimento,$dataOfferta);

																$allegato = array();
																$allegato["codice_gara"] = $offerta["codice_gara"];
																$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
																$allegato["cartella"] = $offerta["codice_lotto"]."/asta";
																$allegato["nome_file"] = "rilancio_".$offerta["codice"] . "_ " . $offerta["partita_iva"].".".$estensione;
																$allegato["riferimento"] = $riferimento;
																$allegato["titolo"] = "Rilancio #" . $offerta["codice"] . " - " . $offerta["partita_iva"];
																$allegato["online"] = "N";

																$salva = new salva();
																$salva->debug = false;
																$salva->codop = $_SESSION["codice_utente"];
																$salva->nome_tabella = "b_allegati";
																$salva->operazione = "INSERT";
																$salva->oggetto = $allegato;
																$offerta["codice_allegato"] = $salva->save();
																if ($offerta["codice_allegato"]!=false) {
																	$bind = array();
																	$bind[":codice_allegato"] = $offerta["codice_allegato"];
																	$bind[":codice"] = $offerta["codice"];

																	$sql = "UPDATE b_offerte_economiche_asta SET codice_allegato = :codice_allegato WHERE codice = :codice";
																	$ris = $pdo->bindAndExec($sql,$bind);
																}
															}
														}
													}

													?>
													<tr>
														<td><? echo "#".$offerta["codice"] ?></td>
														<td style="text-align:left"><? echo $operatore ?></td>
														<td>
															<strong style="color:<? echo $colore ?>"><? echo $offerta["stato"] . " - " . $esito ?></strong>
															<? if (!empty($offerta["codice_allegato"])) { ?>
																<a href="/allegati/download_allegato.php?codice=<? echo $offerta["codice_allegato"] ?>" title="Scarica Allegato">
																	<img src="/img/download.png" alt="Scarica Allegato" width="15"></a>
																<a href="/allegati/open_p7m.php?codice=<? echo $offerta["codice_allegato"] ?>" title="Estrai Contenuto">
																	<img src="/img/p7m.png" alt="Download Allegato" width="15">
																</a>
															<? } ?>
														</td>
														<td><? echo $data ?></td>
													</tr>
														<?

													}
												?>
									</tbody>
										</table>

										</div>
																<?
								}
									?>
								</div>
							<? } ?>
							</div>
							<script>
									 $("#tabs").tabs();
									 </script>
					<? include($root."/gare/ritorna.php"); ?>
					<? if ($lock) { ?>
						<script>
							$(":input").not('.espandi').not('.ritorna_button').prop("disabled", true);
						</script>
					<? } ?>
						<?
			} else {
				echo "<h1>Gara non trovata</h1>";
			}

					?>


					<?
					include_once($root."/layout/bottom.php");
					?>
