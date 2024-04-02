<tr>
	<td class="etichetta" colspan="4">
		<label>IV.1.3) Informazioni su un accordo quadro o un sistema dinamico di acquisizione</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<label><input type="checkbox" name="guue[PROCEDURE][FRAMEWORK]" <?= !empty($guue["PROCEDURE"]["FRAMEWORK"]["radio_as_select_for_operators_number"]) ? 'checked="checked"' : null ?> onchange="toggle_field($(this),['#operators_number', '#justification_checkbox'])"> L&#39;avviso comporta la conclusione di un accordo quadro</label>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Tipologia:</label>
	</td>
	<td>
		<script type="text/javascript">
			var tipo_accordo_quadro_option = {
				'SEVERAL_OPERATORS' : [
					'enable_field',
					'',
					[],
					'nb_participants'
				]
			};
		</script>
		<?
		$single_operator = FALSE;
		$several_operators = FALSE;
		if(!empty($guue["PROCEDURE"]["FRAMEWORK"]["radio_as_select_for_operators_number"])) {
			$single_operator = $guue["PROCEDURE"]["FRAMEWORK"]["radio_as_select_for_operators_number"] == "SINGLE_OPERATOR" ? TRUE : FALSE;
			$several_operators = $guue["PROCEDURE"]["FRAMEWORK"]["radio_as_select_for_operators_number"] == "SEVERAL_OPERATORS" ? TRUE : FALSE;
		}
		?>
		<select id="operators_number" onchange="add_extra_info($(this).val(), tipo_accordo_quadro_option)" <?= empty($guue["PROCEDURE"]["FRAMEWORK"]["radio_as_select_for_operators_number"]) ? 'disabled="disabled"' : null ?> rel="S;1;0;A" title="Tipologia accordo quadro" name="guue[PROCEDURE][FRAMEWORK][radio_as_select_for_operators_number]">
			<option value="">Seleziona..</option>
			<option <?= $single_operator ? 'selected="selected"' : null ?> value="SINGLE_OPERATOR">Accordo quadro con un unico operatore</option>
			<option <?= $several_operators ? 'selected="selected"' : null ?> value="SEVERAL_OPERATORS">Accordo quadro con diversi operatori</option>
		</select>
	</td>
	<td class="etichetta">
		<label>Numero massimo di partecipanti all&#39;accordo quadro previsto:</label>
	</td>
	<td>
		<input id="nb_participants" type="text" name="guue[PROCEDURE][FRAMEWORK][NB_PARTICIPANTS]" <?= !$several_operators ? 'disabled="disabled"' : null ?> title="Numero di partecipanti" rel="S;1;3;N" value="<?= (!empty($guue["PROCEDURE"]["FRAMEWORK"]["NB_PARTICIPANTS"]) && $several_operators) ? $guue["PROCEDURE"]["FRAMEWORK"]["NB_PARTICIPANTS"] : null ?>">
	</td>
</tr>
<tr>
	<td colspan="4">
		<label><input type="checkbox" name="guue[PROCEDURE][DPS]" <?= !empty($guue["PROCEDURE"]["DPS"]) ? 'checked="checked"' : null ?> onchange="toggle_field($(this),'#dps_additional_purchasers')"> L&#39;avviso comporta l&#39;istituzione di un sistema dinamico di acquisizione</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<label><input id="dps_additional_purchasers" type="checkbox" name="guue[PROCEDURE][DPS_ADDITIONAL_PURCHASERS]" <?= !empty($guue["PROCEDURE"]["DPS_ADDITIONAL_PURCHASERS"]) ? 'checked="checked"' : null ?> rel="" <?= empty($guue["PROCEDURE"]["DPS"]) ? 'disabled="disabled""' : null ?>> Il sistema dinamico di acquisizione pu&ograve; essere utilizzato da altri committenti</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<label><input type="checkbox" <?= !empty($guue["PROCEDURE"]["FRAMEWORK"]["JUSTIFICATION"]) ? 'checked="checked"' : null ?> <?= empty($guue["PROCEDURE"]["FRAMEWORK"]["radio_as_select_for_operators_number"]) ? 'disabled="disabled"' : null ?> onchange="toggle_field($(this),'#justification')" id="justification_checkbox" rel="">Si tratta di un accordo quadro per una durata superiore a 4 anni. <i>(Giustificazione:)</i></label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea id="justification" name="guue[PROCEDURE][FRAMEWORK][JUSTIFICATION]" <?= empty($guue["PROCEDURE"]["FRAMEWORK"]["JUSTIFICATION"]) ? 'disabled="disabled"' : null ?> class="ckeditor_simple" rel="S;1;400;A" title="Giustificazione"><?= !empty($guue["PROCEDURE"]["FRAMEWORK"]["JUSTIFICATION"]) ? $guue["PROCEDURE"]["FRAMEWORK"]["JUSTIFICATION"] : null ?></textarea>
	</td>
</tr>