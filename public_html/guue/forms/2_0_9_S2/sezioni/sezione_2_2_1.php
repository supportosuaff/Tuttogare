<tr>
	<td class="etichetta">
		<label>II.2.1) Denominazione:</label>
	</td>
	<td colspan="3">
		<input type="text" title="Denominazione" class="espandi" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][TITLE]" value="<?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["TITLE"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["TITLE"] : null ?>" rel="<?= isRequired("OBJECT_CONTRACT-TITLE") ?>;1;200;A">
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Lotto n.:</label>
	</td>
	<td colspan="3">
		<b><?= $item ?></b> <i>(Se il numero di lotto non &egrave; corretto si prega di salvare una bozza e riaprire il modello)</i>
		<input type="hidden" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][LOT_NO]" rel="S;1;4;N" value="<?= $item ?>">
	</td>
</tr>