<tr>
	<td colspan="4" class="etichetta">
		<label>II.2.4) Descrizione dell'appalto:</label>
	</td>
</tr>
<tr>
<td colspan="4">
		<textarea class="ckeditor_simple" title="Breve descrizione" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][SHORT_DESCR]" rel="<?= isRequired("OBJECT_CONTRACT-SHORT_DESCR") ?>;0;1000;A">
			<?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["SHORT_DESCR"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["SHORT_DESCR"] : null ?>
		</textarea>
	</td>
</tr>
