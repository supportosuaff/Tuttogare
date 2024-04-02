<tr>
	<td class="etichetta" colspan="4"><label>II.2.10) Informazioni sulle varianti</label></td>
</tr>
<tr>
	<td colspan="4">
		<label><input type="checkbox" <?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["ACCEPTED_VARIANTS"]) ? 'checked="checked"' : null ?> name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][ACCEPTED_VARIANTS]"> Sono autorizzate varianti</label>
	</td>
</tr>