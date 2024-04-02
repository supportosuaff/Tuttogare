<tr>
	<td class="etichetta" colspan="4">
		<label>II.2.5) Criteri di aggiudicazione </label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<script>
			var award_criteria_doc_option = {
				'AWARD_CRITERIA_ITEM_TO_IGNORE' : [
					'ajax_load',
					'criteri_di_aggiudicazione_2_2_5',
					[],
					'award_criteria_item_to_ignore'
				]
			};
		</script>
		<select name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_0][radio_as_select_for_award_criteria_doc]" rel="N;1;0;A" title="Criteri di aggiudicazione" onchange="add_extra_info($(this).val(), award_criteria_doc_option)">
			<option value="">Seleziona..</option>
				<option value="AWARD_CRITERIA_ITEM_TO_IGNORE">I criteri indicati di seguito:</option>
			<option value="AC_PROCUREMENT_DOC">Il prezzo non &egrave; il solo criterio di aggiudicazione e tutti i criteri sono indicati solo nei documenti di gara</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="4" id="award_criteria_item_to_ignore"></td>
</tr>
<tr>
	<td colspan="4">
		<i>I criteri possono essere:</i>
		<ul>
		    <li>Criterio di qualit&agrave; e criterio di costo</li>
		    <li>Criterio di qualit&agrave; e criterio di prezzo (&Egrave; necessario specificare la ponderazione)</li>
		    <li>Criterio di costo</li>
		    <li>Criterio di Prezzo (Non si deve indicare la ponderazione)</li>
		</ul>
	</td>
</tr>