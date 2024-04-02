<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
			if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
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
				$bind[":codice_bando"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql = "SELECT * FROM b_bandi_albo WHERE codice = :codice_bando ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				?><h1>Partecipanti</h1><?
				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					?>
					<h3><?= $record["oggetto"] ?></h3><br>
					<?
					$schede = array();
					$schede["nuove_istanze"] = "r_partecipanti_albo.valutato = 'N' AND (r_partecipanti_albo.timestamp_abilitazione IS NULL)";
					$schede["aggiornamenti"] = "r_partecipanti_albo.valutato = 'N' AND (r_partecipanti_albo.timestamp_abilitazione IS NOT NULL)";
					$schede["ammessi"] = " r_partecipanti_albo.ammesso = 'S' AND r_partecipanti_albo.valutato = 'S' ";
					$schede["respinti"] = " r_partecipanti_albo.ammesso = 'N' AND r_partecipanti_albo.valutato = 'S' ";
					$ris_array = array();
					$bind = array();
					$bind[":codice"] = $record["codice"];
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					foreach($schede AS $key => $where_statement) {
						$sql = "SELECT b_operatori_economici.*, r_partecipanti_albo.visto, r_enti_operatori.feedback,
										r_partecipanti_albo.timestamp_richiesta AS ricezione,
										r_partecipanti_albo.timestamp_aggiornamento AS aggiornamento,
										r_partecipanti_albo.timestamp_abilitazione AS abilitazione,
										r_partecipanti_albo.timestamp AS ultima_modifica
										FROM b_operatori_economici
										JOIN r_partecipanti_albo ON b_operatori_economici.codice = r_partecipanti_albo.codice_operatore
										LEFT JOIN r_enti_operatori ON b_operatori_economici.codice_utente = r_enti_operatori.cod_utente AND r_enti_operatori.cod_ente = :codice_ente
										WHERE {$where_statement}
										AND r_partecipanti_albo.codice_bando = :codice
										AND r_partecipanti_albo.conferma = 'S'
										GROUP BY b_operatori_economici.codice
										ORDER BY r_partecipanti_albo.timestamp_richiesta ASC, r_partecipanti_albo.visto";
						$ris_array[$key] = $pdo->bindAndExec($sql,$bind);
					}
					$risultati = false;
					foreach($ris_array AS $key => $ris_partecipanti) {
						if ($ris_partecipanti->rowCount()>0) $risultati = true;
					}
					if ($risultati) {
					?>
						<div id="tabs">
							<ul>
							<?
								foreach($ris_array AS $key => $ris_partecipanti) {
									if ($ris_partecipanti->rowCount()>0) echo "<li><a href='#" . $key ."'>". ucfirst(str_replace('_', ' ', $key)) . "</a></li>";
								}
							?>
						</ul>
						<?
						foreach($ris_array AS $key => $ris_partecipanti) {
							if ($ris_partecipanti->rowCount()>0) {
							?>
							<div id="<? echo $key ?>">
								<div style="text-align:right">
									<a href="pdf.php?codice_bando=<?= $codice ?>&key=<?= $key ?>" target="_blank" title="Esporta PDF">Esporta PDF</a> |
									<a href="csv.php?codice_bando=<?= $codice ?>&key=<?= $key ?>" target="_blank" title="Esporta CSV">Esporta CSV</a>
								</div>
							<?
							$export = [];
							?>
							<table width="100%" class="elenco">
								<thead>
        							<tr>
										<td width="10"></td>
        								<td width="10"></td>
        								<td width="150">Codice Fiscale Impresa</td>
        								<td>Ragione sociale</td>
												<td width="150"><?= ($key != "respinti") ? "Data richiesta" : "Data respingimento" ?></td>
												<? if ($key != "nuove_istanze") { ?>
													<? if ($key != "respinti") { ?>
														<td width="150">Data Aggiornamento</td>
														<td width="150">Data abilitazione</td>
													<? } 
														 if (!empty($record["periodo_revisione"])) { ?>
														<td>Scadenza</td>
													<? } ?>
												<? } else { ?>
													<td width="10">GG da richiesta</td>
												<? } ?>
        								<td width="10">Dettaglio</td>
        							</tr>
        						</thead>
        						<tbody>
										<?
										$i = 0;
										while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
											$tmp = [];
											$tmp["codice"] = $record_partecipante["codice"];
											$tmp["feedback"] = number_format($record_partecipante["feedback"],1);
											$tmp["visto"] = $record_partecipante["visto"];
											$tmp["partita_iva"] = $record_partecipante["partita_iva"];
											$tmp["codice_fiscale_impresa"] = $record_partecipante["codice_fiscale_impresa"];
											$tmp["ragione_sociale"] = $record_partecipante["ragione_sociale"];
											$tmp["ricezione"] = $record_partecipante["ricezione"];
											$tmp["aggiornamento"] = $record_partecipante["aggiornamento"];
											$tmp["abilitazione"] = $record_partecipante["abilitazione"];
											$background = "";
											$data_scadenza = "";
											$gg_scadenza = "";
											if (!empty($record["periodo_revisione"]) && $key != "nuove_istanze" && !empty($record_partecipante["abilitazione"])) {
												$time_scadenza = strtotime("+{$record["periodo_revisione"]} months",strtotime($record_partecipante["abilitazione"]));
												$data_scadenza = date('d/m/Y', $time_scadenza);
												$gg_scadenza = floor(($time_scadenza - time()) / 60 / 60 / 24);
												if ($key != "respinti") {
													if ($gg_scadenza <= 1) {
														$background = "background-color:#F30 !important;";
													} else if ($gg_scadenza <= 10) $background = "background-color:#FC0";
												}
											} else if ($key == "nuove_istanze") {
												$gg_scadenza = floor((time() - strtotime($record_partecipante["ricezione"])) / 60 / 60 / 24);
												if ($gg_scadenza > 30) {
													$background = "background-color:#F30 !important;";
													if ($gg_scadenza <= 90) $background = "background-color:#FC0";
												} 
											}
											$tmp["data_scadenza"] = $data_scadenza;
											$tmp["gg_scadenza"] = $gg_scadenza;
											$i++;
										?>
									<tr style="<? if ($record_partecipante["visto"]=="N") echo "font-weight:bold;"; ?> <?= $background ?>">
											<td><?= $i ?></td>
											<td style="text-align:center">
												<? if (!empty($record_partecipante["feedback"])) { ?>
													<img src="/img/<?= number_format($record_partecipante["feedback"],0) ?>.png" alt="Ranking" height="12" valign="middle" style="margin-top:-3px"><br><b><?= number_format($record_partecipante["feedback"], 1) ?></b>
												<? } ?>
											</td>
											<td><?= (!empty($record_partecipante["partita_iva"])) ? $record_partecipante["partita_iva"] : $record_partecipante["codice_fiscale_impresa"] ?></td>
											<td><? echo strtoupper($record_partecipante["ragione_sociale"]) ?></td>
											<td>
												<? if ($key != "respinti") { ?>
													<span style="display:none"><? echo $record_partecipante["ricezione"] ?></span><? echo mysql2datetime($record_partecipante["ricezione"]) ?>
												<? } else { 
												?>
												<span style="display:none"><? echo $record_partecipante["ultima_modifica"] ?></span><? echo mysql2datetime($record_partecipante["ultima_modifica"]) ?>
												<?
												}
												?>
											</td>
											<? if ($key != "nuove_istanze") { ?>
												<? if ($key != "respinti") { ?>
													<td><span style="display:none"><? echo $record_partecipante["aggiornamento"] ?></span><? echo mysql2datetime($record_partecipante["aggiornamento"]) ?></td>
													<td><span style="display:none"><? echo $record_partecipante["abilitazione"] ?></span><? echo mysql2datetime($record_partecipante["abilitazione"]) ?></td>
													<? }
													if (!empty($record["periodo_revisione"])) { ?>
													<td style="text-align:center"><?= $data_scadenza ?>
														<? if ($key != "respinti") echo "<br>" . $gg_scadenza . "gg"; ?></td>
												<? } ?>
											<? } else { ?>
												<td style="text-align:center">
													<? if ($key != "respinti") echo $gg_scadenza . "<br>gg"; ?></td>
												</td>
											<? } ?>

											<td style="text-align: center"><a href="dettaglio.php?codice=<?= $record_partecipante["codice"] ?>&codice_bando=<? echo $record["codice"] ?>"><span class="btn-round btn-warning" style="display:block"><span style="margin-top:8px" class="fa fa-search"></span></span></a></td>
    								</tr>
									<?
										$export[] = $tmp;
										}
									?>
									</tbody>
								</table>
								<?
									$_SESSION["exportElencoPartecipanti"]["albo"][$codice][$key] = $export;
								?>
							</div>
  						<?
  							}
  						}
  						?>
  						</div>
  						<script>
  							$("#tabs").tabs();
  						</script>
  						<?
						} else {
							echo "<h1>Nessun partecipante presente</h1>";
						}
				 			include($root."/albo_fornitori/ritorna.php");
				} else {
					echo "<h1>Bando non trovato</h1>";
				}

include_once($root."/layout/bottom.php");
	?>
