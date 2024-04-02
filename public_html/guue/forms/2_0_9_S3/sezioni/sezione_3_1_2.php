<tr>
	<td class="etichetta">
		<label>III.1.2) Capacit&agrave; economica e finanziaria</label>
	</td>
</tr>
<tr class="noBorder">
	<td>
		<script>
			var economic_criteria_option = {
				'ECONOMIC_CRITERIA_DOC_ELEMENT_TO_IGNORE' : [
					'ajax_load',
					'economic_criteria',
					[],
					'economic_criteria_doc_element_to_ignore'
				]
			};
		</script>
		<?
		$economic_criteria_doc_element_to_ignore = FALSE;
		$economic_criteria_doc = "";
		if(!empty($guue["LEFTI"]["radio_as_select_for_economic_criteria"])){
			$economic_criteria_doc = ($guue["LEFTI"]["radio_as_select_for_economic_criteria"] == "ECONOMIC_CRITERIA_DOC") ? TRUE : FALSE;
			$economic_criteria_doc_element_to_ignore = ($guue["LEFTI"]["radio_as_select_for_economic_criteria"] == "ECONOMIC_CRITERIA_DOC_ELEMENT_TO_IGNORE") ? TRUE : FALSE;
		}
		?>
		<select name="guue[LEFTI][radio_as_select_for_economic_criteria]" rel="<?= isRequired("radio_as_select_for_economic_criteria") ?>;0;0;A" onchange="add_extra_info($(this).val(), economic_criteria_option)">
			<option value="">Seleziona..</option>
			<option <?= $economic_criteria_doc ? 'selected="selected"' : null ?> value="ECONOMIC_CRITERIA_DOC">Criteri di selezione indicati nei documenti di gara</option>
			<option <?= $economic_criteria_doc_element_to_ignore ? 'selected="selected"' : null ?> value="ECONOMIC_CRITERIA_DOC_ELEMENT_TO_IGNORE">Come di seguito:</option>
		</select>
	</td>
</tr>
<tr>
	<td id="economic_criteria_doc_element_to_ignore"><?
		if($economic_criteria_doc_element_to_ignore) {
			include 'forms/2_0_9_S3/common/economic_criteria.php';
		}
	?></td>
</tr>