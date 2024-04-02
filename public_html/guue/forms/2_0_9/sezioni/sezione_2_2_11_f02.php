<tr>
	<td class="etichetta" colspan="4"><label>II.2.11) Informazioni relative alle opzioni</label></td>
</tr>
<tr>
	<td colspan="4">
		<?
		$radio_as_select_for_options = !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_options"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_options"] : "";
		?>
		<script type="text/javascript">
			var item_<?= $item ?>_options_option = {
					'OPTIONS' : [
						'enable_field',
						'',
						[],
						'item_<?= $item ?>_options_descr'
					]
				};
		</script>
		<select rel="<?= isRequired("radio_as_select_for_options") ?>;1;0;A" title="Informazioni relative alle opzioni" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][radio_as_select_for_options]" onchange="add_extra_info($(this).val(), item_<?= $item ?>_options_option)">
	   	<option value="">Seleziona..</option>
	   	<option <?= $radio_as_select_for_options == "OPTIONS" ? 'selected="selected"' : null ?> value="OPTIONS">Si</option>
	   	<option <?= $radio_as_select_for_options == "NO_OPTIONS" ? 'selected="selected"' : null ?> value="NO_OPTIONS">No</option>
   	</select>
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea id="item_<?= $item ?>_options_descr" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][OPTIONS_DESCR]" rel="S;0;400;A" title="Descrizione delle opzioni" class="ckeditor_simple" <?= $radio_as_select_for_options == "OPTIONS" ? null : 'disabled="disabled"' ?>>
			<?= (!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["OPTIONS_DESCR"]) && $radio_as_select_for_options == "OPTIONS") ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["OPTIONS_DESCR"] : null ?>
		</textarea>
	</td>
</tr>