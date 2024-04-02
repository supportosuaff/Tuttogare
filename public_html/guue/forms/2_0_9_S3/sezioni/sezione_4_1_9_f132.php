<tr>
	<td colspan="4" class="etichetta">
		<label>IV.1.9) Criteri da applicare alla valutazione dei progetti:</label>
	</td>
</tr>
<tr>
	<td class="etichetta" colspan="2">
		<label>Consentire la pubblicazione criteri di valutazione dei progetti?</label>
	</td>
	<td colspan="2">
		<?
		$radio_as_select_for_contractors_publication = !empty($guue["PROCEDURE"]["CRITERIA_EVALUATION"]["ATTRIBUTE"]["PUBLICATION"]["radio_as_select_for_contractors_publication"]) ? $guue["PROCEDURE"]["CRITERIA_EVALUATION"]["ATTRIBUTE"]["PUBLICATION"]["radio_as_select_for_contractors_publication"] : null;
		?>
		<select name="guue[PROCEDURE][CRITERIA_EVALUATION][ATTRIBUTE][PUBLICATION][radio_as_select_for_contractors_publication]" rel="S;1;0;A" title="Consentire la pubblicazione criteri di valutazione dei progetti">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_contractors_publication == "YES" ? 'selected="selected"' : null ?> value="YES">Si</option>
			<option <?= $radio_as_select_for_contractors_publication == "NO" ? 'selected="selected"' : null ?> value="NO">No</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea name="guue[PROCEDURE][CRITERIA_EVALUATION][val]" rel="S;1;1500;A" title="Criteri di valutazione dei progetti" class="ckeditor_simple"><?= !empty($guue["PROCEDURE"]["CRITERIA_EVALUATION"]["val"]) ? $guue["PROCEDURE"]["CRITERIA_EVALUATION"]["val"] : null ?></textarea>
	</td>
</tr>
