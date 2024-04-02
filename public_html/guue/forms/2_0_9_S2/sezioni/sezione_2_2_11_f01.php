<tr>
	<td class="etichetta" colspan="4"><label>II.2.11) Informazioni relative alle opzioni</label></td>
</tr>
<tr>
	<td class="etichetta" colspan="4">
		<label>
			<input type="checkbox" <?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["OPTIONS"]) ? 'checked="checked"' : null ?> name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][OPTIONS]" onChange="toggle_field($(this), '#item_<?= $item ?>_options_descr')"> Opzioni <i>(Se previste indicarle di seguito)</i>
		</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea id="item_<?= $item ?>_options_descr" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][OPTIONS_DESCR]" rel="S;0;400;A" title="Descrizione delle opzioni" class="ckeditor_simple" <?= empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["OPTIONS"]) ? 'disabled="disabled"' : null ?>>
			<?= (!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["OPTIONS_DESCR"]) && !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["OPTIONS"])) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["OPTIONS_DESCR"] : null ?>
		</textarea>
	</td>
</tr>