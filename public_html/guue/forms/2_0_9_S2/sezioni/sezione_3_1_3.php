<tr>
	<td class="etichetta">
		<label>III.1.3) Capacit&agrave; professionale e tecnica </label>
	</td>
</tr>
<tr class="noBorder">
	<td>
		<script>
			var technical_criteria_criteria_option = {
				'TECHNICAL_CRITERIA_DOC_ELEMENT_TO_IGNORE' : [
					'ajax_load',
					'technical_criteria',
					[],
					'technical_criteria_doc_element_to_ignore'
				]
			};
		</script>
		<?
		$technical_criteria_doc_element_to_ignore = FALSE;
		$technical_criteria_doc = FALSE;
		if(!empty($guue["LEFTI"]["radio_as_select_for_tecnical_criteria"])){
			$technical_criteria_doc = ($guue["LEFTI"]["radio_as_select_for_tecnical_criteria"] == "TECHNICAL_CRITERIA_DOC") ? TRUE : FALSE;
			$technical_criteria_doc_element_to_ignore = ($guue["LEFTI"]["radio_as_select_for_tecnical_criteria"] == "TECHNICAL_CRITERIA_DOC_ELEMENT_TO_IGNORE") ? TRUE : FALSE;
		}
		?>
		<select name="guue[LEFTI][radio_as_select_for_tecnical_criteria]" rel="<?= isRequired("radio_as_select_for_tecnical_criteria") ?>;0;0;A" onchange="add_extra_info($(this).val(), technical_criteria_criteria_option)">
			<option value="">Seleziona..</option>
			<option <?= $technical_criteria_doc ? 'selected="selected"' : null ?> value="TECHNICAL_CRITERIA_DOC">Criteri di selezione indicati nei documenti di gara</option>
			<option <?= $technical_criteria_doc_element_to_ignore ? 'selected="selected"' : null ?> value="TECHNICAL_CRITERIA_DOC_ELEMENT_TO_IGNORE">Come di seguito:</option>
		</select>
	</td>
</tr>
<tr>
	<td id="technical_criteria_doc_element_to_ignore"><?
		if($technical_criteria_doc_element_to_ignore) {
			include 'forms/2_0_9_S2/common/technical_criteria.php';
		}
	?></td>
</tr>