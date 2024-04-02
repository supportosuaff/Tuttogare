<tr>
	<td colspan="4" class="etichetta">
		<label>II.1.5) Valore totale stimato</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<i>(in caso di accordi quadro o sistema dinamico di acquisizione – valore massimo totale stimato per l'intera durata dell'accordo quadro o del sistema dinamico di acquisizione)</i>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Valuta:</label>
	</td>
	<td>
		<select name="guue[OBJECT_CONTRACT][VAL_ESTIMATED_TOTAL][ATTRIBUTE][CURRENCY][radio_as_select_for_currencies]" rel="S;0;3;A">
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "GBP") ? 'selected="selected"' : null ?> value="GBP">STERLINA REGNO UNITO</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "ISK") ? 'selected="selected"' : null ?> value="ISK">CORONA ISLANDA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "LTL") ? 'selected="selected"' : null ?> value="LTL">LITAS LITUANIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CHF") ? 'selected="selected"' : null ?> value="CHF">FRANCO SVIZZERA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "SEK") ? 'selected="selected"' : null ?> value="SEK">CORONA SVEZIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "JPY") ? 'selected="selected"' : null ?> value="JPY">YEN GIAPPONE</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "LVL") ? 'selected="selected"' : null ?> value="LVL">LATS LETTONIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "NOK") ? 'selected="selected"' : null ?> value="NOK">CORONA NORVEGIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "MTL") ? 'selected="selected"' : null ?> value="MTL">LIRA MALTA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "EEK") ? 'selected="selected"' : null ?> value="EEK">CORONA ESTONIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CYP") ? 'selected="selected"' : null ?> value="CYP">LIRA CIPRO</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "SKK") ? 'selected="selected"' : null ?> value="SKK">CORONA REPUBBLICA SLOVACCA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "RON") ? 'selected="selected"' : null ?> value="RON">LEU ROMANIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CZK") ? 'selected="selected"' : null ?> value="CZK">CORONA REPUBBLICA CECA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "DKK") ? 'selected="selected"' : null ?> value="DKK">CORONA DANIMARCA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "PLN") ? 'selected="selected"' : null ?> value="PLN">ZLOTY POLONIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "BGN") ? 'selected="selected"' : null ?> value="BGN">LEV BULGARIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "HUF") ? 'selected="selected"' : null ?> value="HUF">FORINT UNGHERIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "USD") ? 'selected="selected"' : null ?> value="USD">DOLLARO STATI UNITI</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "EUR") ? 'selected="selected"' : null ?> value="EUR" <?= empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) ? 'selected="selected"' : null ?>>EURO UNIONE ECONOMICA MONETARIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "MKD") ? 'selected="selected"' : null ?> value="MKD">DINARO MACEDONIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "TRY") ? 'selected="selected"' : null ?> value="TRY">LIRA TURCHIA</option>
			<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "HRK") ? 'selected="selected"' : null ?> value="HRK">KUNA CROAZIA</option>
		</select>
	</td>
	<td class="etichetta">
		<label>Valore, IVA esclusa:</label>	
	</td>
	<td>
		<input type="text" name="guue[OBJECT_CONTRACT][VAL_ESTIMATED_TOTAL][val]" value="<?= !empty($guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["val"]) ? $guue["OBJECT_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["val"] : null ?>" rel="<?= isRequired("VAL_ESTIMATED_TOTAL") ?>;0;15;2D" title="Valore IVA esclusa">
	</td>
</tr>