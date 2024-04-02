<? $function_i = $i; ?>
<tr>
	<td class="etichetta" colspan="4">
		<label>II.2.5) Criteri di aggiudicazione </label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<script>
			var award_criteria_doc_option_<?= $item ?> = {
				'AWARD_CRITERIA' : [
					'ajax_load',
					'criteri_di_aggiudicazione_2_2_5',
					[],
					'award_criteria_item_to_ignore_<?= $item ?>',
					{item: '<?= $item ?>'}
				]
			};
			var award_criteria_doc_enable_option_<?= $item ?> = {
				'AWARD_CRITERIA' : [
					'enable_field',
					'',
					[],
					'add_criterion_lot_<?= $item ?>'
				]
			};
		</script>
		<?
		$award_criteria_item_to_ignore = FALSE;
		$ac_procurement_doc = FALSE;
		if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_award_criteria_doc"])) {
			$award_criteria_item_to_ignore = $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_award_criteria_doc"] == "AWARD_CRITERIA" ? TRUE : FALSE;
			$ac_procurement_doc = $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_award_criteria_doc"] == "AC_PROCUREMENT_DOC" ? TRUE : FALSE;
		}
		?>
		<select name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][radio_as_select_for_award_criteria_doc]" rel="<?= isRequired("OBJECT_CONTRACT-radio_as_select_for_award_criteria_doc") ?>;1;0;A" title="Criteri di aggiudicazione" onchange="add_extra_info($(this).val(), award_criteria_doc_option_<?= $item ?>);add_extra_info($(this).val(), award_criteria_doc_enable_option_<?= $item ?>)">
			<option value="">Seleziona..</option>
			<option <?= $award_criteria_item_to_ignore ? 'selected="selected"' : null ?> value="AWARD_CRITERIA">I criteri indicati di seguito:</option>
			<option <?= $ac_procurement_doc ? 'selected="selected"' : null ?> value="AC_PROCUREMENT_DOC">Il prezzo non &egrave; il solo criterio di aggiudicazione e tutti i criteri sono indicati solo nei documenti di gara</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="4">
		<button <?= !$award_criteria_item_to_ignore ? 'disabled' : null ?> id="add_criterion_lot_<?= $item ?>" class="aggiungi" type="button" onClick="aggiungi('forms/2_0_9_S3/common/criteri_di_aggiudicazione_2_2_5.php','#award_criteria_item_to_ignore_<?= $item ?>', {item:'<?= $item ?>'});return false;"><img src="/img/add.png" alt="Aggiungi committente">Aggiungi criterio</button>
	</td>
</tr>
<tr>
	<td colspan="4" id="award_criteria_item_to_ignore_<?= $item ?>"><?
	if($award_criteria_item_to_ignore) {
		include $root . '/guue/forms/'.$v_form.'/common/criteri_di_aggiudicazione_2_2_5.php';
	}
	?></td>
</tr>
<? $i = $function_i; ?>
