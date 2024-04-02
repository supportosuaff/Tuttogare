<?
if (isset($lotto)) {
?>
<div class="box" id="lotti_<? echo $id ?>">
	<table width="100%">
		<tr>
			<td width="10">
			<? echo $lotto["cig"] ?>
			</td>
			<td>
				<strong><? echo strip_tags($lotto["oggetto"]) ?></strong>
			</td>
				<td width="10"><button class="btn-round btn-primary" onClick="edit_lotto(<?= $id ?>);return false;"  title="Modifica"><span class="fa fa-pencil"></span></td>
				<td width="10"><button class="btn-round btn-warning espandi"  onClick="$(this).parents('div').children('table.dettaglio').toggle(); return false;" title="Visualizza"><span class="fa fa-search"></span></button></td>
				<td width="10"><button class="btn-round btn-danger" onClick="elimina('<? echo $id ?>','gare/lotti');return false" title="Elimina"><span class="fa fa-remove"></span></button></td></tr>
			</table>
			<table width="100%" class="dettaglio" style="display:none">
				<tr>
					<td class="etichetta" colspan="3">
						<strong>Breve descrizione</strong></td></tr>
						<tr><td colspan="3">
							<? echo $lotto["descrizione"] ?>
						</td>
					</tr>
					<tr>
						<td class="etichetta"><strong>Importo base</strong></td>
						<td class="etichetta"><strong>Costo della manodopera</strong></td>
						<td class="etichetta"><strong>Oneri di sicurezza NON soggetti a ribasso</strong></td>
						<!-- <td class="etichetta"><strong>Oneri di sicurezza soggetti a ribasso</strong></td> -->
					</tr>
					<tr>
						<td class="importo_base"><? echo $lotto["importo_base"]; $importo_base += $lotto["importo_base"]; ?></td>
						<td class="importo_personale"><? echo $lotto["importo_personale"]; $importo_personale += $lotto["importo_personale"] ?></td>
						<td class="importo_oneri_no_ribasso"><? echo $lotto["importo_oneri_no_ribasso"]; $importo_oneri_no_ribasso += $lotto["importo_oneri_no_ribasso"] ?></td>
						<!-- <td class="importo_oneri_ribasso"><? echo $lotto["importo_oneri_ribasso"]; $importo_oneri_ribasso += $lotto["importo_oneri_ribasso"] ?></td> -->
					</tr>
					<tr><td class="etichetta" colspan="3"><strong>Categoria Merciologica</strong></td></tr>
					<tr>
						<td colspan="3">
							<?
							$bind = array();
							$bind[":codice"] = $lotto["cpv"];
							$strsql = "SELECT b_cpv.* FROM b_cpv WHERE b_cpv.codice = :codice";
							$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
							if ($risultato_cpv->rowCount()>0) {
								$rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC);
								echo "<strong>" . $rec_cpv["codice"] . "</strong> - " . $rec_cpv["descrizione"];
							}
							?>
						</td>
					</tr>
					<tr><td class="etichetta" colspan="3"><strong>Durata</strong></td></tr>
					<tr><td colspan="3">
						<? echo $lotto["durata"]; ?> <?= $lotto["unita_durata"] ?>
					</td>
				</tr>
				<tr><td class="etichetta" colspan="3">
					<strong>Ulteriori informazioni</strong></td></tr>
					<tr><td colspan="3">
						<? echo $lotto["ulteriori_informazioni"] ?>
					</td>
				</tr>
			</table></div>
<? } ?>
