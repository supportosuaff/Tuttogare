<tr>
	<td class="etichetta" colspan="4"><label>II.2.10) Informazioni sulle varianti</label></td>
</tr>
<tr>
	<td colspan="4">
		<?
		$radio_as_select_for_variants = !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_variants"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_variants"] : "";
		?>
		<select rel="<?= isRequired("radio_as_select_for_variants") ?>;1;0;A" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][radio_as_select_for_variants]">
	   	<option value="">Seleziona..</option>
	   	<option <?= $radio_as_select_for_variants == "ACCEPTED_VARIANTS" ? 'selected="selected"' : null ?> value="ACCEPTED_VARIANTS">Sono autorizzate varianti</option>
	   	<option <?= $radio_as_select_for_variants == "NO_ACCEPTED_VARIANTS" ? 'selected="selected"' : null ?> value="NO_ACCEPTED_VARIANTS">Non sono autorizzate varianti</option>
   	</select>
	</td>
</tr>