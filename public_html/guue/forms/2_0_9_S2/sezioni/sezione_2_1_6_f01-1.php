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
<tr>
	<td class="etichetta" colspan="2">
		<label><input type="checkbox" <?= !empty($guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_MAX_ONE_TENDERER"]) ? 'checked="checked"' : null ?> onChange="toggle_field($(this), '#lot_max_one_tenderer')">Numero massimo di lotti che possono essere aggiudicati a un offerente:</label>
	</td>
	<td colspan="2">
		<input type="text" id="lot_max_one_tenderer" name="guue[OBJECT_CONTRACT][LOT_DIVISION][LOT_MAX_ONE_TENDERER]" rel="S;0;4;N" <?= !empty($guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_MAX_ONE_TENDERER"]) ? 'value="'.$guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_MAX_ONE_TENDERER"].'"' : 'disabled="disabled"' ?> title="Numero massimo di lotti che possono essere aggiudicati a un offerente">
	</td>
</tr>
<tr>
	<td class="etichetta" colspan="4">
		<label><input type="checkbox" onChange="toggle_field($(this), '#lot_combining_contract_right')" <?= !empty($guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_COMBINING_CONTRACT_RIGHT"]) ? 'checked="checked"' : null ?>>L&#39;amministrazione aggiudicatrice si riserva la facolt&agrave; di aggiudicare i contratti d&#39;appalto combinando i seguenti lotti o gruppi di lotti:</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea id="lot_combining_contract_right" name="guue[OBJECT_CONTRACT][LOT_DIVISION][LOT_COMBINING_CONTRACT_RIGHT]" rel="S;0;400;A" title="Combinazione lotti o gruppi di lotti" class="ckeditor_simple" <?= empty($guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_COMBINING_CONTRACT_RIGHT"]) ? 'disabled="disabled"' : null ?>><?= !empty($guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_COMBINING_CONTRACT_RIGHT"]) ? $guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_COMBINING_CONTRACT_RIGHT"] : null ?></textarea>
	</td>
</tr>