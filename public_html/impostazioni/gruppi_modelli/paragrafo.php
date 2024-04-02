<?
if (isset($paragrafo)) {
	$id = $paragrafo["codice"];
	$colore = "#0C0";
	if ($paragrafo["attivo"] == "N") {
		$colore = "#c00";
	} else {
		if ($paragrafo["codice_opzione"] != "" || $paragrafo["importo_minimo"] != 0 || $paragrafo["importo_massimo"] != 0 || $paragrafo["modalita"] != 0 || $paragrafo["vincoli_soa"] != 0) {
			$colore = "#FC0";
		}
	}
?>
<tr id="paragrafo_<?= $id ?>">
	<td id="flag_paragrafo_<?= $id ?>" width="10" class="handle" style="background-color:<?= $colore ?>"></td>
	<td width="10" style="background-color:<?= (empty($paragrafo["tag_esender"]) ? "#C00" : "#0C0") ?>">
	<td>
		<input type="hidden" name="paragrafo[<? echo $id ?>][ordinamento]" id="paragrafo_<?= $id ?>_ordinamento" class="ordinamento" value="<? echo $paragrafo["ordinamento"]  ?>">
		<input type="hidden" id="paragrafo_<?= $id ?>_codice" name="paragrafo[<?= $id ?>][codice]" value="<? echo $paragrafo["codice"]; ?>">
		<input type="hidden" id="paragrafo_<?= $id ?>_id" name="paragrafo[<?= $id ?>][id]" value="<? echo $id ?>">
			<table width="100%" class="settings settings_<?= $id ?>" style="font-size:8px !important; <? if (is_numeric($id)) echo "display:none" ?>">
				<tr>
					<th>Tipo</th>
					<td><?= $paragrafo["tipo"] ?></td>
					<th>Modalita</th><td>
							<?
							if ($paragrafo["modalita"] != 0) {
								$sql = "SELECT * FROM b_modalita WHERE eliminato = 'N' AND codice = :modalita";
								$ris_modalita = $pdo->bindAndExec($sql,array(":modalita"=>$paragrafo["modalita"]));
								if ($ris_modalita->rowCount()>0) {
								$modalita = $ris_modalita->fetch(PDO::FETCH_ASSOC);
								echo $modalita["modalita"];
								}
							} else {
								?>Tutti<?
							}
							?>
						</td></tr>
						<tr>
							<th>Tag E-sender</th>
							<td colspan="3"><? echo $paragrafo["tag_esender"] ?>
							</td>
						</tr>
						<tr>
							<th>Importo minimo</th>
							<td><? echo number_format($paragrafo["importo_minimo"],2,",",".") ?>
							</td>
							<th>Importo massimo</th>
							<td><? echo number_format($paragrafo["importo_massimo"],2,",",".") ?></td>
						</tr>
						<tr>
							<th>Vincoli SOA</th>
							<? switch ($paragrafo["vincoli_soa"]) {
								case 0: echo "<td>Nessuno</td>"; break;
								case 1: echo "<td>S.I.O.S. > 15%</td>"; break;
								case 12: echo "<td>S.I.O.S. Assenti</td>"; break;
								case 2: echo "<td>S.I.O.S. che cambiano classe - 30%</td>"; break;
								case 3: echo "<td>S.I.O.S. che non cambiano classe - 30%</td>"; break;
								case 4: echo "<td>(T.A. - 70% S.I.O.S.) > 20.658.000 </td>"; break;
								case 5: echo "<td>(T.A. - 70% S.I.O.S.) < 20.658.000 </td>"; break;
								case 6: echo "<td>Scorporabili a Q.O. <> S.I.O.S.</td>"; break;
								case 7: echo "<td>Scorporabili non a Q.O.</td>"; break;
								case 8: echo "<td>OG2 - OS2-A - OS2-B - OS25</td>"; break;
								case 13: echo "<td>Tutelate Assenti</td>"; break;
								case 9: echo "<td>OG11 o OS3 - OS28 - OS30</td>"; break;
								case 10: echo "<td>Categorie Scorporabili Assenti</td>"; break;
								case 11: echo "<td>Categorie Scorporabili Presenti</td>"; break;
							}
							?>
							<th>Opzione</th>
							<td>
								<?
								if ($paragrafo["codice_opzione"] != 0 && preg_match("/^[0-9\,]+$/",$paragrafo["codice_opzione"])) {
									$sql = "SELECT b_opzioni.*, b_gruppi_opzioni.titolo AS gruppo FROM b_opzioni JOIN b_gruppi_opzioni ON b_opzioni.codice_gruppo = b_gruppi_opzioni.codice
									 WHERE b_opzioni.codice IN (" . $paragrafo["codice_opzione"] . ")";
									$ris_opzioni = $pdo->query($sql);
									if ($ris_opzioni->rowCount()>0) {
										while($opzione = $ris_opzioni->fetch(PDO::FETCH_ASSOC)) {
											echo "<strong>" . $opzione["gruppo"] . "</strong>: " . $opzione["titolo"] . "<br>";
										}
									}
								} else {
									?>Nessuna<?
								}
									?>
							</td>
						</tr>
					</table>
					<?
						switch ($paragrafo["tipo"]) {
							case "avanzato":
								echo "Script: <strong>" . $paragrafo["directory"] . "</strong>";
								break;
							case "paragrafo_avanzato":
								echo "Script: <strong>" . $paragrafo["directory"] . "</strong>";
								break;
							case "ricorsivo":
								echo "<strong>Testo libero ricorsivo</strong>";
								break;
							case "variabile":
								echo "Richiesta: <strong>" . $paragrafo["richiesta"] . "</strong>";
								?>
								<div style="border:1px solid #CCC; background-color:#fff; padding:5px; max-height:300px; overflow:auto">
									<? echo $paragrafo["contenuto"] ?>
								</div>
								<?
								break;
							case "textarea":
								echo "Richiesta: <strong>" . $paragrafo["richiesta"] . "</strong>";
								?>
								<div style="border:1px solid #CCC; background-color:#fff; padding:5px; max-height:300px; overflow:auto">
									<? echo $paragrafo["contenuto"] ?>
								</div>
								<?
								break;
							default: ?>
								<div style="border:1px solid #CCC; background-color:#fff; padding:5px; max-height:300px; overflow:auto">
									<? echo $paragrafo["contenuto"] ?>
								</div>
							<?
							break;
						}
					?>
		</td>
		<td width="10"><button class="btn-round btn-warning" onClick="$('.settings_<?= $id ?>').toggle();return false;" title="Dettagli"><span class="fa fa-search"></span></td>
		<td width="10"><button class="btn-round btn-primary"  onClick="edit_paragrafo('<? echo $id ?>');return false" title="Modifica"><span class="fa fa-pencil"></span></button></td>
		<td width="10"><button class="btn-round btn-default" onClick="disabilita('<? echo $id ?>','impostazioni/gruppi_modelli/paragrafi');return false" title="Abilita/Disabilita">
		<span class="fa fa-refresh"></span></button></td>
		<td width="10"><button class="btn-round btn-danger" onClick="elimina('<? echo $id ?>','impostazioni/gruppi_modelli/paragrafi');return false" title="Elimina"><span class="fa fa-remove"></span></button></td>
	</tr>
	<? } ?>
