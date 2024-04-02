<tr>
	<td class="etichetta" colspan="4">
		<label><b>VII.2.2) Motivi della modifica</b></label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<label>
			<input type="radio" name="reason_for_modification" <?= !empty($guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["ADDITIONAL_NEED"]) ? 'checked="checked"' : null ?> onchange="toggle_field($(this), '#additional_need');">
			Necessit&agrave; di lavori, servizi o forniture supplementari da parte del contraente/concessionario originale [articolo 43, paragrafo 1, lettera b), della direttiva 2014/23/UE, articolo 72, paragrafo 1, lettera b), della direttiva 2014/24/UE, articolo 89, paragrafo 1, lettera b), della direttiva 2014/25/ UE]
		</label>
	</td>
	<td colspan="2">
		<label>
			<input type="radio" name="reason_for_modification" <?= !empty($guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["UNFORESEEN_CIRCUMSTANCE"]) ? 'checked="checked"' : null ?> onchange="toggle_field($(this), '#unforeseen_circumstance');">
			Necessit&agrave; di modifica determinata da circostanze che un&#39;amministrazione aggiudicatrice diligente non ha potuto prevedere [articolo 43, paragrafo 1, lettera c), della direttiva 2014/23/UE, articolo 72, paragrafo 1, lettera c), della direttiva 2014/24/UE, articolo 89, paragrafo 1, lettera c), della direttiva 2014/25/UE]
		</label>
	</td>
</tr>
<tr>
	<td colspan="2">
		Descrizione dei motivi economici o tecnici e dei disguidi e della duplicazione dei costi che impediscono un cambiamento di contraente:
	</td>
	<td colspan="2">
		Descrizione delle circostanze che hanno reso necessaria la modifica e spiegazione della natura imprevista di tali circostanze:
	</td>
</tr>
<tr>
	<td colspan="2">
		<textarea id="additional_need" class="ckeditor_simple" title="Descrizione dei motivi economici o tecnici e dei disguidi e della duplicazione dei costi che impediscono un cambiamento di contraente" rel="S;3;1000;A" name="guue[MODIFICATIONS_CONTRACT][INFO_MODIFICATIONS][ADDITIONAL_NEED]"><?= !empty($guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["ADDITIONAL_NEED"]) ? $guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["ADDITIONAL_NEED"] : null ?></textarea>
	</td>
	<td colspan="2">
		<textarea id="unforeseen_circumstance" class="ckeditor_simple" title="Descrizione delle circostanze che hanno reso necessaria la modifica e spiegazione della natura imprevista di tali circostanze" rel="S;3;1000;A" name="guue[MODIFICATIONS_CONTRACT][INFO_MODIFICATIONS][UNFORESEEN_CIRCUMSTANCE]"><?= !empty($guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["UNFORESEEN_CIRCUMSTANCE"]) ? $guue["MODIFICATIONS_CONTRACT"]["INFO_MODIFICATIONS"]["UNFORESEEN_CIRCUMSTANCE"] : null ?></textarea>
	</td>
</tr>