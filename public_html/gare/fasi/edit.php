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
	<h1>SCADENZA II FASE</h1>
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
									$bind[":codice_lotto"] = $lotto["codice"];
									$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
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
						if ($print_form) { ?>

								<? if (!$lock) { ?>
									<form name="box" method="post" action="save.php" rel="validate">
										<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
										<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
										<div class="comandi">
											<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
										</div>
														<? } ?>
							<table width="100%">
								<?
									$bind = array();
									$bind[":codice"] = $record["codice"];
									$bind[":codice_lotto"] = 0;
									if (isset($lotto)) $bind[":codice_lotto"] = $lotto["codice"];
									$strsql = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto";
									$ris_fase = $pdo->bindAndExec($strsql,$bind);
									if ($ris_fase->rowCount()>0) {
										$fase = $ris_fase->fetch(PDO::FETCH_ASSOC);
									} else {
										$fase = get_campi("b_2fase");
										$record["data_accesso"] = "";
									}
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
													<td class="etichetta">Termine ultimo richiesta chiarimenti</td>
													<td>
														<input title="richiesta chiarimenti" type="text" name="data_chiarimenti" id="data_chiarimenti" class="datetimepick" rel="S;16;16;DT;data_inizio;>" value="<? echo mysql2datetime($record["data_accesso"]) ?>">
													</td>
													<td class="etichetta">Fine</td>
													<td>
														<input title="Fine" type="text" name="data_fine" id="data_fine" class="datetimepick" rel="S;16;16;DT;data_chiarimenti;>" value="<? echo mysql2datetime($fase["data_fine"]) ?>">
													</td>

												</tr>
							</table>
							<?
								$bind = array();
								$bind[":codice"] = $record["codice"];
								$bind[":codice_lotto"] = $codice_lotto;
								$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND ammesso = 'S' AND escluso = 'N' AND codice_capogruppo = 0 ";
								$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
								if ($ris_partecipanti->rowCount()>0) {
									?>
									<table width="100%">
										<tr><td class="etichetta" colspan="3"><h2>Destinatari invito</h2></td></tr>
										<?
									while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
										?>
											<tr <? if ($partecipante["pec"] == "") echo "style='background-color:#EC0'"; ?>>
												<td width="10"><?= $partecipante["partita_iva"] ?></td>
												<td><strong><? if ($partecipante["tipo"] != '') echo "RAGGRUPPAMENTO - " ?><?= $partecipante["ragione_sociale"] ?></strong></td>
												<td><?= $partecipante["pec"] ?></td>
											</tr>
										<?
									}
									?>
									</table>
									<div id="allegati">
										<? $cod_allegati = $fase["cod_allegati"]; ?>
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
					<?	} ?>

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
