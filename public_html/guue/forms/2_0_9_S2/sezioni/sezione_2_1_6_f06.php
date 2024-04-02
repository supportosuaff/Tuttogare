<tr>
	<td colspan="4" class="etichetta">
		<label>II.1.6) Informazioni relative ai lotti</label>
	</td>
</tr>
<tr>
	<td class="etichetta" colspan="2">
		<label>Questo appalto &egrave; suddiviso in lotti?</label>
	</td>
	<td colspan="2">
		<select name="guue[OBJECT_CONTRACT][radio_as_select_for_lot_division]" rel="<?= isRequired("OBJECT_CONTRACT-radio_as_select_for_lot_division") ?>;1;0;A" title="Divisione in lotti">
			<option value="">Seleziona..</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "LOT_DIVISION") ? 'selected="selected"' : null ?> value="LOT_DIVISION">Si</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "NO_LOT_DIVISION") ? 'selected="selected"' : null ?> value="NO_LOT_DIVISION">No</option>
		</select>
	</td>
</tr>