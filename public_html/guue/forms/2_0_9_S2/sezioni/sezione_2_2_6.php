<tr>
	<td colspan="4" class="etichetta">
		<label>II.2.6) Valore stimato</label>
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
		<?
		$radio_as_select_for_currencies = "";
		if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["VAL_OBJECT"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"])) {
			$radio_as_select_for_currencies = $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["VAL_OBJECT"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"];
		}
		?>
		<select name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][VAL_OBJECT][ATTRIBUTE][CURRENCY][radio_as_select_for_currencies]" rel="N;0;3;A">
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
		<label>Valore, IVA esclusa:</label>	
	</td>
	<td>
		<input type="text" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][VAL_OBJECT][val]" <?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["VAL_OBJECT"]["val"]) ? 'value="'.$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["VAL_OBJECT"]["val"].'"' : null ?> rel="N;0;15;2D" title="Valore IVA esclusa">
	</td>
</tr>