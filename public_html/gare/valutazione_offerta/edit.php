<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
			if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
			if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]))
			{
				$strsql = "SELECT * FROM b_gestione_gare WHERE link LIKE '/gare/valutazione_offerta/edit.php%'";
				$risultato = $pdo->query($strsql);
				if ($risultato->rowCount()>0) {
					$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
					$esito = check_permessi_gara($gestione["codice"],$_GET["codice"],$_SESSION["codice_utente"]);
					$edit = $esito["permesso"];
					$lock = $esito["lock"];
				}
				if (!$edit)
				{
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
			}
			else
			{
				echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
				die();
			}
			$economica = true;
			if ($_GET["tecnica"] == "true") $economica = false;

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
			$strsql .= " AND data_apertura <= now() ";
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {
				$record = $risultato->fetch(PDO::FETCH_ASSOC);
				$codice_gara = $record["codice"];
				$bind=array();
				$bind[":codice"] = $record["criterio"];
				$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio = :codice ORDER BY ordinamento ";
				$ris_punteggi = $pdo->bindAndExec($sql,$bind);
				$operazione = "UPDATE";
				?><h1>VALUTAZIONE OFFERTA</h1><?
				$bind=array();
				$bind[":codice_gara"] = $codice_gara;
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				$print_form = false;

				if ($economica) {
					$sql_check = "SELECT b_valutazione_tecnica.*
										FROM b_valutazione_tecnica
										JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
										WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND (codice_lotto = 0 OR codice_lotto = :codice_lotto)
										AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
					$ris_check_criteri = $pdo->prepare($sql_check);
					$ris_check_criteri->bindValue(':codice_gara',$record["codice"]);
				}

				if ($ris_lotti->rowCount()>0)
				{
					if (isset($_GET["lotto"]))
					{
						$codice_lotto = $_GET["lotto"];
						$bind=array();
						$bind[":codice_lotto"] = $codice_lotto;

						$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice_lotto ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						if ($ris_lotti->rowCount()>0)
						{
							$print_form = true;
							$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
							$codice_lotto = $lotto["codice"];
							echo "<h2>" . $lotto["oggetto"] . "</h2>";
						}
					}
					else
					{
						while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC))
						{
							$show = true;
							if (isset($ris_check_criteri)) {
								$ris_check_criteri->bindValue(":codice_lotto",$lotto["codice"]);
								$ris_check_criteri->execute();
								if ($ris_check_criteri->rowCount() === 1) {
									$criterio = $ris_check_criteri->fetch(PDO::FETCH_ASSOC);
									if ($criterio["punteggio"] == 100 && ($criterio["valutazione"] == "P" || $criterio["valutazione"] == 'E')) $show = false;
								}
							}
							if ($show) {
								$bind=array();
								$bind[":codice_gara"] = $codice_gara;
								$bind[":codice_lotto"] = $lotto["codice"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND primo = 'S'";
								$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
								$style = "";
								$primo = "";
								if ($ris_partecipanti->rowCount()>0)
								{
									$primo = $ris_partecipanti->fetch(PDO::FETCH_ASSOC);
									$primo = "<br>" . $primo["partita_iva"] . " - " . $primo["ragione_sociale"];
									$style = "style=\"background-color:#0C0\"";
								} else if ($lotto["deserta"] == "S") {
									$primo = "<br>Deserto";
									$style = "style=\"background-color:#999\"";
								} else if ($lotto["deserta"] == "Y") {
									$primo = "<br>Non aggiudicato";
									$style = "style=\"background-color:#666\"";
								}
								?>
									<a class="submit_big" <?= $style ?> href ="edit.php?codice=<?= $record["codice"] ?>&lotto=<?= $lotto["codice"] ?>&tecnica=<?= $_GET["tecnica"] ?>">
										<?= $lotto["oggetto"] . $primo ?>
									</a>
								<?
							}
						}
					}
				}
				else
				{
					$print_form = true;
					$codice_lotto = 0;
				}
				if ($print_form)
				{
					$continua = true;
					$bind = array();
					$bind[":codice"]=$record["codice"];
					$bind[":codice_lotto"] = $codice_lotto;

					$sql_fasi = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now() ";
					$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
					if ($ris_fasi->rowCount()>0) {
						$print_form = false;
					} else {
						$sql_fasi = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_fine > now() ";
						$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
						if ($ris_fasi->rowCount()>0) $lock = true;
					}

					$sql_fasi = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now() ";
					$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
					if ($ris_fasi->rowCount()>0) {
						$print_form = false;
					} else {
						$sql_fasi = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_fine < now() AND data_fine > 0 ";
						$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
						if ($ris_fasi->rowCount()>0) $lock = true;
					}
					if ($print_form)
					{
						$sql_criteri = "SELECT b_valutazione_tecnica.*,
														 b_criteri_punteggi.economica,
														 b_criteri_punteggi.temporale,
														 b_criteri_punteggi.migliorativa
											FROM b_valutazione_tecnica
											JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
											WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND
														(b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)
											AND b_valutazione_tecnica.codice NOT IN
											(SELECT codice_padre FROM b_valutazione_tecnica WHERE codice_padre <> 0 AND codice_gara = :codice_gara)";
						$economica = true;
						if ($_GET["tecnica"] == "true") $economica = false;
						if ($economica) {
							$sql_criteri .= "AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
						} else {
							$sql_criteri .= "AND (b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N') ";
						}
						$bind = array();
						$bind[":codice_gara"] = $codice_gara;
						$bind[":codice_lotto"] = $codice_lotto;
						$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);
						$qualitativi = 0;
						if ($ris_criteri->rowCount() > 0) {
							$criteri = $ris_criteri->fetchAll();
							$codici_criteri = [];
							foreach($criteri AS $criterio) {
								$codici_criteri[] = $criterio["codice"];
								if ($criterio["tipo"]=="Q") $qualitativi++;
							}

							$bind = array();
							$bind[":codice_gara"] = $codice_gara;
							$bind[":codice_lotto"] = $codice_lotto;
							$codici_criteri = implode(",",$codici_criteri);
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0
											AND (
														(ammesso = 'S' AND escluso = 'N') OR
														(r_partecipanti.codice IN (SELECT codice_partecipante FROM b_punteggi_criteri WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_criterio IN ({$codici_criteri})))
													)
											AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
							$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);
							$n_partecipanti = $ris_r_partecipanti->rowCount();
							if ($n_partecipanti > 0)
							{
								$coppie = false;
								$bind = array();
								$bind[":codice_gara"] = $codice_gara;
								$n_partecipanti = $ris_r_partecipanti->rowCount();
								$sql_confronto = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 124";
								$ris_confronto = $pdo->bindAndExec($sql_confronto,$bind);
								if ($ris_confronto->rowCount()>0) $coppie = true;
								/* if ($qualitativi > 0) {
									$continua = false;
									?>
									<div class="padding">
										<h3 style="float:left">AVANZAMENTO VALUTAZIONE</h3>
										<div style="float:right">
											<input type="checkbox" id="checkRiparamentraMedie" onchange="updateRiparametraMedie()" checked>
											Riparametra la media dei coefficienti attribuiti
										</div>
										<script>
											function updateRiparametraMedie() {
												if ($("#checkRiparamentraMedie:checked").length > 0) {
													$(".riparametraMedie").val("S");
												} else {
													$(".riparametraMedie").val("N");
												}
											}
										</script>

										<div class="clear"></div>
										<div id="avanzamento">
											<? include_once("avanzamento.php"); ?>
										</div>
									</div>
									<script type="text/javascript">
										$(document).ready(function() {
											setInterval(function(){
												$('#avanzamento').load(
													'avanzamento.php',
													{codice: "<?= $codice_gara ?>", partecipanti: "<?= $n_partecipanti ?>", lotto: "<?= $codice_lotto ?>", tecnica: "<?= ($economica) ? 'false':'true' ?>"} ,
													function(){
														f_ready();
													}
												);
											}, 30000);
										});
									</script>
									<?
								} */
								$check = getPunteggiCriterio($record["codice"],$codice_lotto,($economica) ? "economica" : "tecnica");
								if (!$lock) {
									if (($check !== false)) { // || $qualitativi > 0) && $continua) {
									?>
										<form name="box" method="post" action="importa_offerte.php">
											<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
											<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
											<input type="hidden" name="economica" value="<?= ($economica) ? "S" : "N"; ?>">
											<input type="hidden" name="riparametraMedie" class="riparametraMedie" value="S">
											<input type="submit" class="submit_big" onClick="" style="background-color: #FC0" value="Importa Punteggi">
										</form>
									<? } ?>
									<form action="save.php" method="POST" role="form" rel="validate">
										<input type="hidden" name="codice_gara" value="<?= $codice_gara ?>">
										<input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
								<? }
									$i = 0;
									$sql = "SELECT punteggio FROM b_punteggi_criteri
													WHERE codice_criterio = :criterio AND codice_partecipante = :partecipante";
						      $ris_punteggio = $pdo->prepare($sql);
									$export = [];
									$export["gara"] = $record;
									$export["lotto"] = (isset($lotto)) ? $lotto : "";
									$export["partecipanti"] = [];
									$export["criteri"] = $criteri;
									while ($partecipante = $ris_r_partecipanti->fetch(PDO::FETCH_ASSOC)) {
										$ris_punteggio->bindValue(":partecipante",$partecipante["codice"]);
										$i++;
										?>
										<div class="box">
											<h3 style="text-align:center"><?= $i ?> - <?= $partecipante["partita_iva"] ?> - <?= $partecipante["ragione_sociale"] ?></h3><br>
											<table width="100%">
												<thead>
													<tr>
														<td width="1">#</td>
														<td>Criterio</td>
														<td width="1">Max</td>
														<td width="15%">Punteggio</td>
													</tr>
												</thead>
												<tbody>
													<?
														$j = 0;
														$tmp = [];
														foreach($criteri AS $criterio) {
															$ris_punteggio->bindValue(":criterio",$criterio["codice"]);
															$j++;
															$punteggio = "";
															$ris_punteggio->execute();
															if ($ris_punteggio->rowCount() > 0) $punteggio = $ris_punteggio->fetch(PDO::FETCH_ASSOC)["punteggio"];
															?>
															<tr>
																<td><?= $j ?></td>
																<td><?= $criterio["descrizione"] ?></td>
																<td style="text-align:center"><?= $criterio["punteggio"] ?></td>
																<td>
																	<input class="titolo_edit" title="Punteggio <?= $j ?> - <?= $partecipante["ragione_sociale"] ?>"
																		rel="S;0;0;N;<?= $criterio["punteggio"] ?>;<=" type="text"
																		id="inputValutazione_<?= $criterio["codice"] ?>_<?= $partecipante["codice"] ?>"
																		name="valutazione[<?= $partecipante["codice"] ?>][<?= $criterio["codice"] ?>]" value="<?= $punteggio ?>">
																</td>
															</tr>
															<?
															$tmp[$criterio["codice"]] = $punteggio;
														}
														$partecipante["punteggi"] = $tmp;

													?>
												</tbody>
											</table>
										</div>
									<?
										$export["partecipanti"][] = $partecipante;
									}
									if (!$lock)
									{
									?>
										<button type="submit"  class="submit_big" style="cursor:pointer">SALVA VALUTAZIONE</button>
									</form>
									<?
									} else { ?>
										<script>
												 $("*:input").not('.espandi').prop("disabled", true);
										</script> <?
									}
									$name_session = "exportValutazioneOfferta";
									$name_session .= ($economica) ? "Economica" : "Tecnica";
									$name_session .= $record["codice"];
									$name_session .= "_".$codice_lotto;
									$_SESSION[$name_session] = $export;
									?>
									<a target="_blank" class="submit_big" style="background-color:#C00" href="export.php?<?= $_SERVER["QUERY_STRING"] ?>">
										<span class="fa fa-file"></span> Esporta risultati
									</a>
									<?
							} else {
								echo "<h3>Valutazione non necessaria</h3>";
							}
						} else {
							echo "<h1>Errore</h1>";
							echo "<h3>E' necessario inserire i partecipanti alla gara</h3>";
						}
					} else {
						echo "<h1>IMPOSSIBILE ACCEDERE</h1>";
						echo "<h3>Procedure di negoziazione aperte</h3>";
					}
				}
				include($root."/gare/ritorna.php");
			}
			else
			{
				echo "<h1>Gara non trovata</h1>";
			}
		}
		else
		{
			echo "<h1>Gara non trovata</h1>";
		}
	include_once($root."/layout/bottom.php");
?>
