<tr>
	<td class="etichetta">
		<label>III.2.1) Informazioni relative ad una particolare professione <i>(solo per contratti di servizi)</i></label>
	</td>
</tr>
<tr class="noBorder">
	<td>
		<label><input type="checkbox" <?= !empty($guue["LEFTI"]["PARTICULAR_PROFESSION"]["ATTRIBUTE"]["CTYPE"]) ? 'checked="checked"' : null ?> name="guue[LEFTI][PARTICULAR_PROFESSION][ATTRIBUTE][CTYPE]" value="SERVICES" onchange="toggle_field($(this), '#reference_to_law')"> La prestazione del servizio &egrave; riservata ad una particolare professione <i>(Citare le corrispondenti disposizioni legislative, regolamentari o amministrative)</i></label>
	</td>
</tr>
<tr>
	<td>
		<textarea id="reference_to_law" name="guue[LEFTI][REFERENCE_TO_LAW]" title="Disposizioni legislative, regolamentari o amministrative" <?= !empty($guue["LEFTI"]["PARTICULAR_PROFESSION"]["ATTRIBUTE"]["CTYPE"]) ? null : 'disabled="disabled"' ?> rel="N;0;400;A" class="ckeditor_simple"><?= (!empty($guue["LEFTI"]["REFERENCE_TO_LAW"]) && !empty($guue["LEFTI"]["PARTICULAR_PROFESSION"]["ATTRIBUTE"]["CTYPE"])) ? $guue["LEFTI"]["REFERENCE_TO_LAW"] : null ?></textarea>
	</td>
</tr>