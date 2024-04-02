<tr>
	<td colspan="4" class="etichetta">
		<label>II.2.3) Luogo di esecuzione</label>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Codice NUTS:</label>
	</td>
	<td>
		<select name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][NUTS][ATTRIBUTE][CODE]" title="Codice NUTS" rel="<?= isRequired("OBJECT_CONTRACT-NUTS") ?>;1;0;A">
			<option value="">Seleziona...</option>
			<?
			$sql_nuts = "SELECT * FROM b_nuts ORDER BY descrizione";
			$ris_nuts = $pdo->query($sql_nuts);
			if ($ris_nuts->rowCount() > 0)
			{
				while ($nuts = $ris_nuts->fetch(PDO::FETCH_ASSOC)) {
					?><option <?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NUTS"]["ATTRIBUTE"]["CODE"]) && $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["NUTS"]["ATTRIBUTE"]["CODE"] == $nuts["nuts"] ? 'selected="selected"' : null ?> value="<?= $nuts["nuts"] ?>"><?= $nuts["nuts"] ?> - <?= $nuts["descrizione"] ?> <? if (!empty($nuts["data_fine_validita"])) { ?> - scadenza: <?= mysql2date($nuts["data_fine_validita"]) ?><? } ?></option><?
				}
			}
			?>
		</select>
	</td>
	<td class="etichetta">
		<label>Luogo principale di esecuzione:</label>
	</td>
	<td>
		<input type="text" title="Luogo" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][MAIN_SITE]" <?= (!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["MAIN_SITE"])) ? 'value="'.$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["MAIN_SITE"].'"' : null ?> rel="<?= isRequired("OBJECT_CONTRACT-MAIN_SITE") ?>;0;200;A">
	</td>
</tr>
