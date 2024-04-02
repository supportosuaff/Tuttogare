<tr>
	<td class="etichetta" colspan="4">
		<label>IV.1.1) Tipo di procedura</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<script type="text/javascript">
			var radio_as_select_for_procedure_type_options = {
				'PT_AWARD_CONTRACT_WITHOUT_PUBLICATION' : [
					'ajax_load',
					['sezioni', 'annex_d4_part1'],
					[],
					'allegato_d'
				]
			}
		</script>
		<?
		$radio_as_select_for_procedure_type = null;
		if(!empty($guue["PROCEDURE"]["radio_as_select_for_procedure_type"])) {
			$radio_as_select_for_procedure_type = $guue["PROCEDURE"]["radio_as_select_for_procedure_type"];
		}
		?>
		<select id="radio_as_select_for_procedure_type" name="guue[PROCEDURE][radio_as_select_for_procedure_type]" rel="S;1;0;A" onchange="add_extra_info($(this).val(), radio_as_select_for_procedure_type_options)">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_AWARD_CONTRACT_WITH_PRIOR_PUBLICATION" ? 'selected="selected"' : null ?> value="PT_AWARD_CONTRACT_WITH_PRIOR_PUBLICATION">Procedura di aggiudicazione con previa pubblicazione di bando di concessione</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_AWARD_CONTRACT_WITHOUT_PUBLICATION" ? 'selected="selected"' : null ?> value="PT_AWARD_CONTRACT_WITHOUT_PUBLICATION">Procedura di aggiudicazione senza previa pubblicazione di bando di concessione nei casi elencati di seguito (completare l&#39;allegato D4)</option>
		</select>
	</td>
</tr>