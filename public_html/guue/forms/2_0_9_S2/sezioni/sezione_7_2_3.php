<tr>
	<td class="etichetta" colspan="4">
		<label><b>VII.2.3) Aumento del prezzo</b></label>
	</td>
</tr>
<tr>
	<td colspan="4">
		Valore totale aggiornato dell&#39;appalto prima delle modifiche<br><i>(tenendo conto di eventuali modifiche contrattuali e adeguamenti di prezzo precedenti e, nel caso della direttiva 2014/23/UE, dell&#39;inflazione media dello Stato membro interessato)</i>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Valuta:</label>
	</td>
	<td>
		<?
		$radio_as_select_for_currencies_for_val_total_before = "";
		if(!empty($guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["VAL_TOTAL_BEFORE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies_for_val_total_before"])) {
			$radio_as_select_for_currencies_for_val_total_before = $guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["VAL_TOTAL_BEFORE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies_for_val_total_before"];
		}
		?>
		<select name="guue[MODIFICATIONS_CONTRACT][INFO_MODIFICATIONS][VAL_TOTAL_BEFORE][ATTRIBUTE][CURRENCY][radio_as_select_for_currencies_for_val_total_before]" rel="N;0;3;A">
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "GBP" ? 'selected="selected"' : null ?> value="GBP">STERLINA REGNO UNITO</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "ISK" ? 'selected="selected"' : null ?> value="ISK">CORONA ISLANDA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "LTL" ? 'selected="selected"' : null ?> value="LTL">LITAS LITUANIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "CHF" ? 'selected="selected"' : null ?> value="CHF">FRANCO SVIZZERA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "SEK" ? 'selected="selected"' : null ?> value="SEK">CORONA SVEZIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "JPY" ? 'selected="selected"' : null ?> value="JPY">YEN GIAPPONE</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "LVL" ? 'selected="selected"' : null ?> value="LVL">LATS LETTONIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "NOK" ? 'selected="selected"' : null ?> value="NOK">CORONA NORVEGIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "MTL" ? 'selected="selected"' : null ?> value="MTL">LIRA MALTA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "EEK" ? 'selected="selected"' : null ?> value="EEK">CORONA ESTONIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "CYP" ? 'selected="selected"' : null ?> value="CYP">LIRA CIPRO</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "SKK" ? 'selected="selected"' : null ?> value="SKK">CORONA REPUBBLICA SLOVACCA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "RON" ? 'selected="selected"' : null ?> value="RON">LEU ROMANIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "CZK" ? 'selected="selected"' : null ?> value="CZK">CORONA REPUBBLICA CECA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "DKK" ? 'selected="selected"' : null ?> value="DKK">CORONA DANIMARCA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "PLN" ? 'selected="selected"' : null ?> value="PLN">ZLOTY POLONIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "BGN" ? 'selected="selected"' : null ?> value="BGN">LEV BULGARIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "HUF" ? 'selected="selected"' : null ?> value="HUF">FORINT UNGHERIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "USD" ? 'selected="selected"' : null ?> value="USD">DOLLARO STATI UNITI</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "EUR" ? 'selected="selected"' : null ?> value="EUR" <?= empty($radio_as_select_for_currencies_for_val_total_before) ? 'selected="selected"' : null ?>>EURO UNIONE ECONOMICA MONETARIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "MKD" ? 'selected="selected"' : null ?> value="MKD">DINARO MACEDONIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "TRY" ? 'selected="selected"' : null ?> value="TRY">LIRA TURCHIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_before == "HRK" ? 'selected="selected"' : null ?> value="HRK">KUNA CROAZIA</option>
		</select>
	</td>
	<td class="etichetta">
		<label>Valore:</label>	
	</td>
	<td>
		<input type="text" name="guue[MODIFICATIONS_CONTRACT][INFO_MODIFICATIONS][VAL_TOTAL_BEFORE][val]" <?= !empty($guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["VAL_TOTAL_BEFORE"]["val"]) ? 'value="'.$guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["VAL_TOTAL_BEFORE"]["val"].'"' : null ?> rel="N;0;15;2D" title="Valore IVA esclusa">
	</td>
</tr>
<tr>
	<td colspan="4">
		Valore totale dell&#39;appalto dopo le modifiche
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Valuta:</label>
	</td>
	<td>
		<?
		$radio_as_select_for_currencies_for_val_total_after = "";
		if(!empty($guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["VAL_TOTAL_AFTER"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies_for_val_total_after"])) {
			$radio_as_select_for_currencies_for_val_total_after = $guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["VAL_TOTAL_AFTER"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies_for_val_total_after"];
		}
		?>
		<select name="guue[MODIFICATIONS_CONTRACT][INFO_MODIFICATIONS][VAL_TOTAL_AFTER][ATTRIBUTE][CURRENCY][radio_as_select_for_currencies_for_val_total_after]" rel="N;0;3;A">
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "GBP" ? 'selected="selected"' : null ?> value="GBP">STERLINA REGNO UNITO</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "ISK" ? 'selected="selected"' : null ?> value="ISK">CORONA ISLANDA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "LTL" ? 'selected="selected"' : null ?> value="LTL">LITAS LITUANIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "CHF" ? 'selected="selected"' : null ?> value="CHF">FRANCO SVIZZERA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "SEK" ? 'selected="selected"' : null ?> value="SEK">CORONA SVEZIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "JPY" ? 'selected="selected"' : null ?> value="JPY">YEN GIAPPONE</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "LVL" ? 'selected="selected"' : null ?> value="LVL">LATS LETTONIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "NOK" ? 'selected="selected"' : null ?> value="NOK">CORONA NORVEGIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "MTL" ? 'selected="selected"' : null ?> value="MTL">LIRA MALTA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "EEK" ? 'selected="selected"' : null ?> value="EEK">CORONA ESTONIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "CYP" ? 'selected="selected"' : null ?> value="CYP">LIRA CIPRO</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "SKK" ? 'selected="selected"' : null ?> value="SKK">CORONA REPUBBLICA SLOVACCA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "RON" ? 'selected="selected"' : null ?> value="RON">LEU ROMANIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "CZK" ? 'selected="selected"' : null ?> value="CZK">CORONA REPUBBLICA CECA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "DKK" ? 'selected="selected"' : null ?> value="DKK">CORONA DANIMARCA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "PLN" ? 'selected="selected"' : null ?> value="PLN">ZLOTY POLONIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "BGN" ? 'selected="selected"' : null ?> value="BGN">LEV BULGARIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "HUF" ? 'selected="selected"' : null ?> value="HUF">FORINT UNGHERIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "USD" ? 'selected="selected"' : null ?> value="USD">DOLLARO STATI UNITI</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "EUR" ? 'selected="selected"' : null ?> value="EUR" <?= empty($radio_as_select_for_currencies_for_val_total_after) ? 'selected="selected"' : null ?>>EURO UNIONE ECONOMICA MONETARIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "MKD" ? 'selected="selected"' : null ?> value="MKD">DINARO MACEDONIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "TRY" ? 'selected="selected"' : null ?> value="TRY">LIRA TURCHIA</option>
			<option <?= $radio_as_select_for_currencies_for_val_total_after == "HRK" ? 'selected="selected"' : null ?> value="HRK">KUNA CROAZIA</option>
		</select>
	</td>
	<td class="etichetta">
		<label>Valore:</label>	
	</td>
	<td>
		<input type="text" name="guue[MODIFICATIONS_CONTRACT][INFO_MODIFICATIONS][VAL_TOTAL_AFTER][val]" <?= !empty($guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["VAL_TOTAL_AFTER"]["val"]) ? 'value="'.$guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["VAL_TOTAL_AFTER"]["val"].'"' : null ?> rel="N;0;15;2D" title="Valore IVA esclusa">
	</td>
</tr>