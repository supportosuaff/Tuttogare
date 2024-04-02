<tr>
	<td colspan="4" class="etichetta">
		<label>II.1.7) Valore totale dell&#39;appalto</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<table class="bordered valida" title="Valore totale dell&#39;appalto" rel="<?= isRequired("valore_totale_appalto") ?>;0;0;checked;group_validate">
			<tbody>
				<tr>
					<td class="etichetta">
						<?
						$total_val = false;
						if(!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["val"])) {
							$total_val = TRUE;
						}
						?>
						<label style="font-size: 14px;"><input <?= !empty($total_val) ? 'checked="checked"' : null ?> type="radio" name="valore_totale_appalto" onchange="toggle_field($(this), ['#valore_totale_appalto','#val_total_currency']);"> Valore totale:</label>
					</td>
					<td>
						<select name="guue[OBJECT_CONTRACT][VAL_TOTAL][ATTRIBUTE][CURRENCY][radio_as_select_for_currencies]" rel="S;0;3;A" id="val_total_currency" <?= !$total_val ? 'disabled="disabled"' : null ?>>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "GBP") ? 'selected="selected"' : null ?> value="GBP">STERLINA REGNO UNITO</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "ISK") ? 'selected="selected"' : null ?> value="ISK">CORONA ISLANDA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "LTL") ? 'selected="selected"' : null ?> value="LTL">LITAS LITUANIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CHF") ? 'selected="selected"' : null ?> value="CHF">FRANCO SVIZZERA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "SEK") ? 'selected="selected"' : null ?> value="SEK">CORONA SVEZIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "JPY") ? 'selected="selected"' : null ?> value="JPY">YEN GIAPPONE</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "LVL") ? 'selected="selected"' : null ?> value="LVL">LATS LETTONIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "NOK") ? 'selected="selected"' : null ?> value="NOK">CORONA NORVEGIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "MTL") ? 'selected="selected"' : null ?> value="MTL">LIRA MALTA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "EEK") ? 'selected="selected"' : null ?> value="EEK">CORONA ESTONIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CYP") ? 'selected="selected"' : null ?> value="CYP">LIRA CIPRO</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "SKK") ? 'selected="selected"' : null ?> value="SKK">CORONA REPUBBLICA SLOVACCA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "RON") ? 'selected="selected"' : null ?> value="RON">LEU ROMANIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CZK") ? 'selected="selected"' : null ?> value="CZK">CORONA REPUBBLICA CECA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "DKK") ? 'selected="selected"' : null ?> value="DKK">CORONA DANIMARCA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "PLN") ? 'selected="selected"' : null ?> value="PLN">ZLOTY POLONIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "BGN") ? 'selected="selected"' : null ?> value="BGN">LEV BULGARIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "HUF") ? 'selected="selected"' : null ?> value="HUF">FORINT UNGHERIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "USD") ? 'selected="selected"' : null ?> value="USD">DOLLARO STATI UNITI</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "EUR") ? 'selected="selected"' : null ?> value="EUR" <?= empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) ? 'selected="selected"' : null ?>>EURO UNIONE ECONOMICA MONETARIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "MKD") ? 'selected="selected"' : null ?> value="MKD">DINARO MACEDONIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "TRY") ? 'selected="selected"' : null ?> value="TRY">LIRA TURCHIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "HRK") ? 'selected="selected"' : null ?> value="HRK">KUNA CROAZIA</option>
						</select>
					</td>
					<td colspan="2">
						<input type="text" name="guue[OBJECT_CONTRACT][VAL_TOTAL][val]" style="font-size: 1.3em" title="Valore totale" <?= empty($total_val) ? 'disabled="disabled"' : null ?> id="valore_totale_appalto" rel="S;0;0;2D" value="<?= !empty($guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["val"]) ? $guue["OBJECT_CONTRACT"]["VAL_TOTAL"]["val"] : null ?>">
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<i style="font-size: 12px;">(Indicare il valore totale finale dell'appalto. Per informazioni sui singoli appalti utilizzare la sezione V)</i>
					</td>
				</tr>
				<tr>
					<td class="etichetta">
						<?
						$max_and_min_val = FALSE;
						if(!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["HIGH"]) && !empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["LOW"])) {
							$max_and_min_val = TRUE;
						}
						?>
						<label style="font-size: 14px;"><input <?= !empty($max_and_min_val) ? 'checked="checked"' : null ?> type="radio" name="valore_totale_appalto" onchange="toggle_field($(this), ['#valore_offerta_bassa', '#valore_offerta_alta', '#val_range_total_currency']);"> Valore totale:</label>
					</td>
					<td>
						<select name="guue[OBJECT_CONTRACT][VAL_RANGE_TOTAL][ATTRIBUTE][CURRENCY][radio_as_select_for_currencies]" rel="S;0;3;A" <?= !$max_and_min_val ? 'disabled="disabled"' : null ?> id="val_range_total_currency">
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "GBP") ? 'selected="selected"' : null ?> value="GBP">STERLINA REGNO UNITO</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "ISK") ? 'selected="selected"' : null ?> value="ISK">CORONA ISLANDA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "LTL") ? 'selected="selected"' : null ?> value="LTL">LITAS LITUANIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CHF") ? 'selected="selected"' : null ?> value="CHF">FRANCO SVIZZERA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "SEK") ? 'selected="selected"' : null ?> value="SEK">CORONA SVEZIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "JPY") ? 'selected="selected"' : null ?> value="JPY">YEN GIAPPONE</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "LVL") ? 'selected="selected"' : null ?> value="LVL">LATS LETTONIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "NOK") ? 'selected="selected"' : null ?> value="NOK">CORONA NORVEGIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "MTL") ? 'selected="selected"' : null ?> value="MTL">LIRA MALTA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "EEK") ? 'selected="selected"' : null ?> value="EEK">CORONA ESTONIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CYP") ? 'selected="selected"' : null ?> value="CYP">LIRA CIPRO</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "SKK") ? 'selected="selected"' : null ?> value="SKK">CORONA REPUBBLICA SLOVACCA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "RON") ? 'selected="selected"' : null ?> value="RON">LEU ROMANIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CZK") ? 'selected="selected"' : null ?> value="CZK">CORONA REPUBBLICA CECA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "DKK") ? 'selected="selected"' : null ?> value="DKK">CORONA DANIMARCA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "PLN") ? 'selected="selected"' : null ?> value="PLN">ZLOTY POLONIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "BGN") ? 'selected="selected"' : null ?> value="BGN">LEV BULGARIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "HUF") ? 'selected="selected"' : null ?> value="HUF">FORINT UNGHERIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "USD") ? 'selected="selected"' : null ?> value="USD">DOLLARO STATI UNITI</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "EUR") ? 'selected="selected"' : null ?> value="EUR" <?= empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) ? 'selected="selected"' : null ?>>EURO UNIONE ECONOMICA MONETARIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "MKD") ? 'selected="selected"' : null ?> value="MKD">DINARO MACEDONIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "TRY") ? 'selected="selected"' : null ?> value="TRY">LIRA TURCHIA</option>
							<option <?= (!empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "HRK") ? 'selected="selected"' : null ?> value="HRK">KUNA CROAZIA</option>
						</select>
					</td>
					<td>
						<input type="text" name="guue[OBJECT_CONTRACT][VAL_RANGE_TOTAL][LOW]" style="font-size: 1.3em" title="Offerta pi&ugrave; bassa" <?= empty($max_and_min_val) ? 'disabled="disabled"' : null ?> id="valore_offerta_bassa" rel="S;0;0;2D" value="<?= !empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["LOW"]) ? $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["LOW"] : null ?>">
					</td>
					<td>
						<input type="text" name="guue[OBJECT_CONTRACT][VAL_RANGE_TOTAL][HIGH]" style="font-size: 1.3em" title="Offerta pi&ugrave; alta" <?= empty($max_and_min_val) ? 'disabled="disabled"' : null ?> id="valore_offerta_alta" rel="S;0;0;2D" value="<?= !empty($guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["HIGH"]) ? $guue["OBJECT_CONTRACT"]["VAL_RANGE_TOTAL"]["HIGH"] : null ?>">
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<tr>
	<td colspan="4">
		<i style="font-size: 12px;">(in caso di accordi quadro – valore massimo totale per la loro intera durata)</i><br>
		<i style="font-size: 12px;">(in caso di un sistema dinamico di acquisizione – valore dell&#39;appalto non incluso nei precedenti avvisi di aggiudicazione di appalti)</i><br>
		<i style="font-size: 12px;">(in caso di appalti basati su accordi quadro, se richiesto – valore dell&#39;appalto non incluso nei precedenti avvisi di aggiudicazione di appalti)</i><br>
	</td>
</tr>
<tr>
	<td colspan="4">
		<script type="text/javascript">
			function remove_2_1_7() {
				$("input:radio[name='valore_totale_appalto']").each(function(e) {
					$(this).prop('checked',false).attr('checked',false);
					$(this).removeAttr('checked').removeProp('checked');
					$(this).trigger('change');
				});
			}
		</script>
		<button type="button" class="submit_big" style="background-color: #999" onclick="remove_2_1_7()">Nessun Valore</button>
	</td>
</tr>