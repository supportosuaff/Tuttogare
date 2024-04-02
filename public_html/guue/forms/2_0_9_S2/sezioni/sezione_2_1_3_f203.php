<tr>
	<td class="etichetta">
		II.1.3) Tipo di appalto
	</td>
	<td colspan="3">
		<select name="guue[OBJECT_CONTRACT][TYPE_CONTRACT][ATTRIBUTE][CTYPE][radio_as_select_for_type_contract]" rel="<?= isRequired("radio_as_select_for_type_contract") ?>;1;0;A" title="Tipo di Appalto">
			<option value="">Seleziona..</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["TYPE_CONTRACT"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_type_contract"]) && $guue["OBJECT_CONTRACT"]["TYPE_CONTRACT"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_type_contract"] == "SERVICES") ? 'selected="selected"' : null ?> value="SERVICES" >Servizi</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["TYPE_CONTRACT"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_type_contract"]) && $guue["OBJECT_CONTRACT"]["TYPE_CONTRACT"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_type_contract"] == "WORKS") ? 'selected="selected"' : null ?> value="WORKS" >Lavori</option>
		</select>
	</td>
</tr>