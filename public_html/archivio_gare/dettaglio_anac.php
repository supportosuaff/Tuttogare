<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (isset($_SESSION["ente"]) && !empty($_GET["codice"])) {
		$bind = array(':codice_ente' => $_SESSION["ente"]["codice"],":codice"=>$_GET["codice"]);
		
		$strsql  = "SELECT b_simog.*, b_enti.denominazione FROM b_simog 
								JOIN b_enti ON b_simog.codice_ente = b_enti.codice 
								JOIN b_enti AS b_ente_gestore ON b_simog.codice_gestore = b_ente_gestore.codice 
								JOIN b_lotti_simog ON b_simog.codice = b_lotti_simog.codice_simog
								WHERE b_lotti_simog.codice_gara IS NULL AND b_lotti_simog.190importoAggiudicato > 0 
								AND (b_simog.codice_gestore = :codice_ente OR b_simog.codice_ente = :codice_ente) AND b_simog.codice = :codice AND b_lotti_simog.eliminato = 'N'";
		if (isset($_GET["codice_ente"])) {
			$bind[":codice_ente_filtro"]=$_GET["codice_ente"];
			$strsql .= " AND b_simog.codice_ente = :codice_ente_filtro ";
		}
		$strsql .= " GROUP BY b_simog.codice ";
		
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
		$listeSimog = getListeSIMOG();
		
		if ($risultato->rowCount() == 1) {
			$record = $risultato->fetch(PDO::FETCH_ASSOC);
			?>
			<h1><?= traduci("DETTAGLIO") ?></h1>
			<div class="box">
				<table width="100%">
					<tr>
						<td class="etichetta"><?= traduci("Stazione appaltante") ?></td>
						<td colspan="3"><strong><?= $record["denominazione"] ?></strong></td>
					</tr>
					<tr><td class="etichetta"><?= traduci("Oggetto") ?></td><td colspan="3"><? echo $record["oggetto"] ?></td></tr>
					<? if ($record["importo_gara"] > 0) { ?>
						<tr><td class="etichetta"><?= traduci("Totale appalto") ?></td><td colspan="3"><strong>&euro; <? echo number_format($record["importo_gara"],2,",","."); ?></strong></td></tr>
					<? } ?>
				</table>
			</div>
			<?
				$sql_lotti = "SELECT * FROM b_lotti_simog WHERE codice_simog = :codice_simog AND b_lotti_simog.eliminato = 'N' ORDER BY codice ";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,array(":codice_simog"=>$record["codice"]));
				if ($ris_lotti->rowCount() > 0) {
					if ($ris_lotti->rowCount() > 1) { ?><h2>Lotti</h2><? }
						while ($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
							?>
							<div class="box">
							<table width="100%">
								<tr>
									<td class="etichetta">CIG</td>
									<td width="40%"><?= $lotto["cig"] ?></td>
									<td class="etichetta">CUP</td>
									<td><?= $lotto["cup"] ?></td>
								</tr>
								<? if ($ris_lotti->rowCount() > 1) { ?>
									<tr>
										<td class="etichetta"><?= traduci("Oggetto") ?></td>
										<td colspan="3"><strong><? echo $lotto["oggetto"] ?></strong></td>
									</tr>
								<? } if ($lotto["importo_lotto"] > 0) { ?>
									<tr>
										<td class="etichetta"><?= traduci("Totale lotto") ?></td>
										<td colspan="3"><strong>&euro; <? echo number_format($lotto["importo_lotto"],2,",","."); ?></strong></td>
									</tr>
								<? } ?>
									<tr>
										<td class="etichetta"><?= traduci("Procedura") ?></td>
										<td><?= (!empty($listeSimog["SceltaContraenteType"][$lotto["id_scelta_contraente"]])) ? $listeSimog["SceltaContraenteType"][$lotto["id_scelta_contraente"]] : "" ?></td>
										<td class="etichetta"><?= traduci("Tipologia") ?></td>
										<td><?= (!empty($listeSimog["CategoriaType"][$lotto["id_categoria_prevalente"]])) ? $listeSimog["CategoriaType"][$lotto["id_categoria_prevalente"]] : "" ?></td>
									</tr>
									<tr>
										<td class="etichetta"><?= traduci("Categorie merceologiche") ?></td><td colspan="3">
											<?
												if ($_SESSION["language"] == "IT") {
													$strsql = "SELECT b_cpv.* FROM b_cpv WHERE b_cpv.codice_completo = :cpv";
												} else {
													$strsql = "SELECT b_cpv_dict.*, b_cpv_dict.{$_SESSION["language"]} AS descrizione
																		FROM b_cpv_dict WHERE b_cpv_dict.codice_completo = :cpv";
												}

												$risultato_cpv = $pdo->bindAndExec($strsql,array(":cpv"=>$lotto["cpv"]));
												if ($risultato_cpv->rowCount()>0) {
													while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
														echo "<strong>" . $rec_cpv["codice"] . "</strong> - " . $rec_cpv["descrizione"] . "<br>";
													}
												}
											?></td>
									</tr>
									<tr>
										<td class="etichetta"><?= traduci("Inizio contratto") ?></td>
										<td><?= mysql2date($lotto["190data_inizio"]) ?></td>
										<td class="etichetta"><?= traduci("Fine contratto") ?></td>
										<td><?= mysql2date($lotto["190data_fine"]) ?></td>
									</tr>
									<tr>
										<td class="etichetta"><?= traduci("Importo aggiudicato") ?></td>
										<td><?= number_format($lotto["190importoAggiudicato"],2,",",".") ?></td>
										<td class="etichetta"><?= traduci("Importo liquidato") ?></td>
										<td><?= number_format($lotto["190liquidazioni"],2,",",".") ?></td>
									</tr>
								</table>
							<?
								$sql_partecipanti = "SELECT * FROM r_partecipanti_simog WHERE codice_lotto_simog = :codice_lotto AND codice_capogruppo = 0 ORDER BY primo DESC";
								$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,[":codice_lotto"=>$lotto["codice"]]);
								if ($ris_partecipanti->rowCount() > 0) {
									?>
									<br><h3><?= traduci("Partecipanti") ?></h3>
									<table width="100%">
										<?
										while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
											$sql_partecipanti = "SELECT * FROM r_partecipanti_simog WHERE codice_lotto_simog = :codice_lotto AND codice_capogruppo = :codice_capogruppo";
											$ris_partecipanti_gruppo = $pdo->bindAndExec($sql_partecipanti,array(":codice_lotto"=>$lotto["codice"],":codice_capogruppo"=>$partecipante["codice"]));
											if ($ris_partecipanti_gruppo->rowCount()>0) {
												?><tr><th colspan='2'><?= traduci("RAGGRUPPAMENTO") ?></th></tr><?
											}
											?>
											<tr>
												<td width="1"><h3><?= $partecipante["partita_iva"] ?></h3></td>
												<td>
													<h3><strong><?= $partecipante["ragione_sociale"] ?></strong> - <?= traduci($partecipante["tipo"]) ?>
														<? if ($partecipante["primo"] =="S") { ?>
															<strong style="padding:5px; background-color:green; color:white;"><?= traduci("Aggiudicatario") ?></strong>
														<? } ?>
													</h3>
												</td>
											</tr>
											<?
											if ($ris_partecipanti_gruppo->rowCount()>0) {
												while ($partecipante = $ris_partecipanti_gruppo->fetch(PDO::FETCH_ASSOC)) {
													?>
													<tr>
														<td width="1"><h3><?= $partecipante["partita_iva"] ?></h3></td>
														<td><h3><strong><?= $partecipante["ragione_sociale"] ?></strong> - <?= traduci($partecipante["tipo"]) ?></h3></td>
													</tr>
													<?
												}
												echo "<tr><th colspan='2'></th></tr>";
											}
										}
										?>
									</table>
								<?
								}
							?>
							</div>
							<?
						}
				}
			?>
			<div class="clear"></div>
		<?php
		} else { ?>
			<h1 style="text-align:center">Nessuna gara disponibile</h1>
		<? }
	}
	include_once($root."/layout/bottom.php");
	?>
