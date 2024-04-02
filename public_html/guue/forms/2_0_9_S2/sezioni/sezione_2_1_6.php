<tr>
	<td colspan="4" class="etichetta">
		<label>II.1.6) Informazioni relative ai lotti</label>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Questo appalto &egrave; suddiviso in lotti?</label>
	</td>
	<td>
		<script>
			var lot_division_option = {
				'LOT_DIVISION_ITEM_TO_IGNORE' : [
					'enable_field',
					'',
					[],
					'radio_as_select_for_lot_numbers'
				]
			};
		</script>
		<select name="guue[OBJECT_CONTRACT][radio_as_select_for_lot_division]" onchange="add_extra_info($(this).val(), lot_division_option)" rel="<?= isRequired("OBJECT_CONTRACT-radio_as_select_for_lot_division") ?>;1;0;A" title="Divisione in lotti">
			<option value="">Seleziona..</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "LOT_DIVISION_ITEM_TO_IGNORE") ? 'selected="selected"' : null ?> value="LOT_DIVISION_ITEM_TO_IGNORE">Si</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "NO_LOT_DIVISION") ? 'selected="selected"' : null ?> value="NO_LOT_DIVISION">No</option>
		</select>
	</td>
	<td class="etichetta">
		Se suddiviso in lotti, <label>le offerte vanno presentate per:</label>
	</td>
	<td>
		<script>
			var lot_numbers_option = {
				'LOT_MAX_NUMBER_ITEM_TO_IGNORE' : [
					'enable_field',
					'',
					[],
					'lot_max_number_allowed'
				]
			};
		</script>
		<?
		$lot_all = FALSE;
		$lot_max_number_item_to_ignore = FALSE;
		$lot_one_only = FALSE;
		$disabled = (!empty($guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"]) && $guue["OBJECT_CONTRACT"]["radio_as_select_for_lot_division"] == "LOT_DIVISION_ITEM_TO_IGNORE") ? FALSE : TRUE;
		if(!$disabled) {
			$lot_all = (!empty($guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["radio_as_select_for_lot_numbers"]) && $guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["radio_as_select_for_lot_numbers"] == "LOT_ALL") ? TRUE : FALSE;
			$lot_max_number_item_to_ignore = (!empty($guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["radio_as_select_for_lot_numbers"]) && $guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["radio_as_select_for_lot_numbers"] == "LOT_MAX_NUMBER_ITEM_TO_IGNORE") ? TRUE : FALSE;
			$lot_one_only = (!empty($guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["radio_as_select_for_lot_numbers"]) && $guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["radio_as_select_for_lot_numbers"] == "LOT_ONE_ONLY") ? TRUE : FALSE;
		}
		?>
		<select id="radio_as_select_for_lot_numbers" name="guue[OBJECT_CONTRACT][LOT_DIVISION][radio_as_select_for_lot_numbers]" onchange="add_extra_info($(this).val(), lot_numbers_option)" rel="S;1;0;A" title="Presentazione offerte per lotti" <?= $disabled ? 'disabled="disabled"' : null ?>>
			<option value="">Seleziona..</option>
			<option <?= $lot_all ? 'selected="selected"' : null ?> value="LOT_ALL">Tutti i lotti</option>
			<option <?= $lot_max_number_item_to_ignore ? 'selected="selected"' : null ?> value="LOT_MAX_NUMBER_ITEM_TO_IGNORE">Solo per un numero definito di lotti</option>
			<option <?= $lot_one_only ? 'selected="selected"' : null ?> value="LOT_ONE_ONLY">Un solo lotto</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="2" class="etichetta">
		<label>Numero massimo di lotti per cui &egrave; possibile presentare le offerte:</label>
	</td>
	<td colspan="2">
		<?
		$lot_max_number_allowed = FALSE;
		$lot_max_number_allowed_value = "";
		if($lot_max_number_item_to_ignore) {
			$lot_max_number_allowed = TRUE;
			$lot_max_number_allowed_value = !empty($guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_MAX_NUMBER"]) ? $guue["OBJECT_CONTRACT"]["LOT_DIVISION"]["LOT_MAX_NUMBER"] : "";
		}
		?>
		<input type="text" id="lot_max_number_allowed" name="guue[OBJECT_CONTRACT][LOT_DIVISION][LOT_MAX_NUMBER]" <?= !$lot_max_number_allowed ? 'disabled="disabled"' : null ?> rel="S;0;4;N" title="Numero massimo di lotti" value="<?= $lot_max_number_allowed_value ?>">
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