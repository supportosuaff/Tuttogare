<tr>
	<td class="etichetta" colspan="4">
		<label><b>VII.1.6) Informazioni relative al valore del contratto d&#39;appalto/del lotto/della concessione</b> <i>(IVA esclusa)</i></label>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Valuta:</label>
	</td>
	<td>
		<?
		$radio_as_select_for_currencies = "";
		if(!empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"])) {
			$radio_as_select_for_currencies = $guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"];
		}
		?>
		<select name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][VAL_TOTAL][ATTRIBUTE][CURRENCY][radio_as_select_for_currencies]" rel="N;0;3;A">
			<option <?= $radio_as_select_for_currencies == "GBP" ? 'selected="selected"' : null ?> value="GBP">STERLINA REGNO UNITO</option>
			<option <?= $radio_as_select_for_currencies == "ISK" ? 'selected="selected"' : null ?> value="ISK">CORONA ISLANDA</option>
			<option <?= $radio_as_select_for_currencies == "LTL" ? 'selected="selected"' : null ?> value="LTL">LITAS LITUANIA</option>
			<option <?= $radio_as_select_for_currencies == "CHF" ? 'selected="selected"' : null ?> value="CHF">FRANCO SVIZZERA</option>
			<option <?= $radio_as_select_for_currencies == "SEK" ? 'selected="selected"' : null ?> value="SEK">CORONA SVEZIA</option>
			<option <?= $radio_as_select_for_currencies == "JPY" ? 'selected="selected"' : null ?> value="JPY">YEN GIAPPONE</option>
			<option <?= $radio_as_select_for_currencies == "LVL" ? 'selected="selected"' : null ?> value="LVL">LATS LETTONIA</option>
			<option <?= $radio_as_select_for_currencies == "NOK" ? 'selected="selected"' : null ?> value="NOK">CORONA NORVEGIA</option>
			<option <?= $radio_as_select_for_currencies == "MTL" ? 'selected="selected"' : null ?> value="MTL">LIRA MALTA</option>
			<option <?= $radio_as_select_for_currencies == "EEK" ? 'selected="selected"' : null ?> value="EEK">CORONA ESTONIA</option>
			<option <?= $radio_as_select_for_currencies == "CYP" ? 'selected="selected"' : null ?> value="CYP">LIRA CIPRO</option>
			<option <?= $radio_as_select_for_currencies == "SKK" ? 'selected="selected"' : null ?> value="SKK">CORONA REPUBBLICA SLOVACCA</option>
			<option <?= $radio_as_select_for_currencies == "RON" ? 'selected="selected"' : null ?> value="RON">LEU ROMANIA</option>
			<option <?= $radio_as_select_for_currencies == "CZK" ? 'selected="selected"' : null ?> value="CZK">CORONA REPUBBLICA CECA</option>
			<option <?= $radio_as_select_for_currencies == "DKK" ? 'selected="selected"' : null ?> value="DKK">CORONA DANIMARCA</option>
			<option <?= $radio_as_select_for_currencies == "PLN" ? 'selected="selected"' : null ?> value="PLN">ZLOTY POLONIA</option>
			<option <?= $radio_as_select_for_currencies == "BGN" ? 'selected="selected"' : null ?> value="BGN">LEV BULGARIA</option>
			<option <?= $radio_as_select_for_currencies == "HUF" ? 'selected="selected"' : null ?> value="HUF">FORINT UNGHERIA</option>
			<option <?= $radio_as_select_for_currencies == "USD" ? 'selected="selected"' : null ?> value="USD">DOLLARO STATI UNITI</option>
			<option <?= $radio_as_select_for_currencies == "EUR" ? 'selected="selected"' : null ?> value="EUR" <?= empty($radio_as_select_for_currencies) ? 'selected="selected"' : null ?>>EURO UNIONE ECONOMICA MONETARIA</option>
			<option <?= $radio_as_select_for_currencies == "MKD" ? 'selected="selected"' : null ?> value="MKD">DINARO MACEDONIA</option>
			<option <?= $radio_as_select_for_currencies == "TRY" ? 'selected="selected"' : null ?> value="TRY">LIRA TURCHIA</option>
			<option <?= $radio_as_select_for_currencies == "HRK" ? 'selected="selected"' : null ?> value="HRK">KUNA CROAZIA</option>
		</select>
	</td>
	<td class="etichetta">
		<label>Valore:</label>	
	</td>
	<td>
		<input type="text" name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][VAL_TOTAL][val]" <?= !empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["VAL_TOTAL"]["val"]) ? 'value="'.$guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["VAL_TOTAL"]["val"].'"' : null ?> rel="N;0;15;2D" title="Valore IVA esclusa">
	</td>
</tr>
<tr>
	<td colspan="3">
		L&#39;appalto/concessione &egrave; stato aggiudicato a un raggruppamento di operatori economici?
	</td>
	<td>
		<?
		$radio_as_select_for_awarded_to_group = "";
		if(!empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["radio_as_select_for_awarded_to_group"])) {
			$radio_as_select_for_awarded_to_group = $guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["radio_as_select_for_awarded_to_group"];
		}
		?>
		<select name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][radio_as_select_for_awarded_to_group]" rel="S;1;0;A" title="Aggiudicazione a un raggruppamento di operatori economici">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_awarded_to_group == "AWARDED_TO_GROUP" ? 'selected="selected"' : null ?> value="AWARDED_TO_GROUP">Si</option>
			<option <?= $radio_as_select_for_awarded_to_group == "NO_AWARDED_TO_GROUP" ? 'selected="selected"' : null ?> value="NO_AWARDED_TO_GROUP">No</option>
		</select>
	</td>
</tr>