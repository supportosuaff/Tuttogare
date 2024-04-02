<? 
	if (isset($record_partecipante)) { ?>
		<tr
			id="<? echo $record_partecipante["codice"] ?>"
			<?= ($record_partecipante["controllo_possesso_requisiti"] == "S") ? "style='background-color:#FC0'" : "" ?>>
		<td width="10"><?= $i_cont ?></td>
		<td width="10">
		<strong><? echo $record_partecipante["partita_iva"] ?></strong></td>
		<td><? if ($record_partecipante["tipo"] != "") echo "<strong>RAGGRUPPAMENTO</strong> - " ?><? echo $record_partecipante["ragione_sociale"] ?>    </td>
		<?
			if (count($ris_buste)>0) {
				if (!empty($codice_lotto) && !isset($Ifase)) {
					$sql_checko = "SELECT * FROM b_buste WHERE codice_busta = :codice_busta
												AND codice_gara = :codice_gara
												AND codice_lotto <> :codice_lotto
												AND utente_modifica = :codice_utente
												AND sha256 = :sha256
												AND aperto = 'S' ";
					$ris_checko = $pdo->prepare($sql_checko);
					$ris_checko->bindValue(":codice_gara",$record["codice"]);
					$ris_checko->bindValue(":codice_lotto",$codice_lotto);
					$ris_checko->bindValue(":codice_utente",$record_partecipante["codice_utente"]);

				}
				$ris_emendamento = $pdo->prepare("SELECT * FROM b_emendamenti WHERE busta_originale = :codice ");
				foreach($ris_buste AS $busta) {
					$emendamento = null;
					$bind = array();
					$bind[":codice_partecipante"] = $record_partecipante["codice"];
					$bind[":codice_busta"] = $busta["codice"];
					$bind[":codice_gara"] = $record["codice"];
					if (isset($Ifase)) {
						$sql = "SELECT * FROM b_buste_Ifase ";
					} else {
						$sql  = "SELECT * FROM b_buste ";
					}
					$sql .= "WHERE codice_partecipante = :codice_partecipante AND codice_busta  = :codice_busta";
					$sql .= " AND codice_gara = :codice_gara";
					$ris_exist = $pdo->bindAndExec($sql,$bind);
					?>
					<td width="150" style="text-align:center">
						<div id="<? echo $record_partecipante["codice"] . "_" . $busta["codice"] ?>">
							<?
							if ($ris_exist->rowCount()>0) {
								$busta_partecipante = $ris_exist->fetch(PDO::FETCH_ASSOC);
								$ris_emendamento->execute([":codice"=>$busta_partecipante["codice"]]);
								if ($ris_emendamento->rowCount() > 0) {
									$emendamento = $ris_emendamento->fetch(PDO::FETCH_ASSOC);
								}
								if ($busta_partecipante["aperto"] == "N") {
									if ($record_partecipante["ammesso"] == "S") {
										if (!$lock && !isset($Ifase)) {
											$label_busta = "Apri busta";
											if (isset($ris_checko)) {
												$ris_checko->bindValue(":codice_busta",$busta["codice"]);
												$ris_checko->bindValue(":sha256",$busta_partecipante["sha256"]);
												$ris_checko->execute();
												if ($ris_checko->rowCount() > 0) {
													$label_busta = "Continua";
													?>
													<div class="selezionato" style="font-size:0.8em"><strong>Aperta in altro lotto</strong></div>
													<?
												}
											}
											?>
											<form action="open.php" rel="validate" method="post">
												<input type="hidden" name="codice_gara" value="<? echo $record["codice"] ?>">
												<input type="hidden" name="codice_partecipante" value="<? echo $record_partecipante["codice"] ?>">
												<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto ?>">
												<input type="hidden" name="codice_busta" value="<? echo $busta["codice"] ?>">
												<input type="hidden" name="private_key" class="private" rel="S;0;0;A" title="Chiave privata">
												<input type="submit" value="<?= $label_busta ?>">
											</form>
											<? if (!empty($emendamento)) { ?>
												<div class="selezionato" style="font-size:0.8em"><strong>Richiesto emendamento</strong></div>
											<? } ?>
										<? } else { ?>
											Impossibile aprire
										<? } 
									} else {
										?>Partecipante non ammesso alle fasi successive<?
									}
								} else { ?>
									<a href="/allegati/download_allegato.php?codice=<? echo $busta_partecipante["codice_allegato"] ?>" title="Scarica Allegato">
										<img src="/img/download.png" alt="Scarica Allegato" width="25">
									</a>
									<a href="/allegati/open_p7m.php?codice=<? echo $busta_partecipante["codice_allegato"] ?>" title="Estrai Contenuto">
										<img src="/img/p7m.png" alt="Download Allegato" width="25">
									</a>
									<br>Busta aperta
							<? }
							} else { ?>
								Non presentata
							<? } ?>
						</div>
						<?  if (!empty($emendamento)) { ?>
							<div id="emendamento_<? echo $record_partecipante["codice"] . "_" . $busta["codice"] ?>" style="<?= ($busta_partecipante["aperto"] == "N") ? "display:none" : "" ?>">
								<div class="selezionato" style="font-size:0.8em"><strong>Emendamento</strong></div>
								<? if ($emendamento["aperto"] == "N") {
									if ($record_partecipante["ammesso"] == "S") {
										?>
										<form action="open-emendamento.php" rel="validate" id="form_emendamento_<? echo $record_partecipante["codice"] . "_" . $busta["codice"] ?>" method="post">
											<input type="hidden" name="codice_gara" value="<? echo $record["codice"] ?>">
											<input type="hidden" name="codice_partecipante" value="<? echo $record_partecipante["codice"] ?>">
											<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto ?>">
											<input type="hidden" name="codice_busta" value="<? echo $busta["codice"] ?>">
											<input type="hidden" name="busta_originale" value="<? echo $busta_partecipante["codice"] ?>">
											<input type="hidden" name="codice_emendamento" value="<? echo $emendamento["codice"] ?>">
											<input type="hidden" name="private_key" class="private" rel="S;0;0;A" title="Chiave privata">
											<input type="submit" value="Apri emendamento">
										</form>
										<?
									}
								} 
								?>
								<button <?= ($emendamento["aperto"] == "N") ? "style=\"display:none\"" : "" ?> id="button_emendamento_<? echo $record_partecipante["codice"] . "_" . $busta["codice"] ?>" onclick="showInfoEmendamento(<?= $record["codice"] ?>,<?= $record_partecipante["codice"] ?>,<?= $emendamento["codice"] ?>)">
									Dettagli
								</button>
								<?
								?>
							</div>
						<? }  ?>
					</td>
				<?	}
				} ?>
		</tr>
<? } ?>
