<tr>
	<td class="etichetta" colspan="4">
		<label>IV.1.1) Tipo di procedura</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<?
		$radio_as_select_for_procedure_type = "";
		if(!empty($guue["PROCEDURE"]["radio_as_select_for_procedure_type"])) {
			$radio_as_select_for_procedure_type = $guue["PROCEDURE"]["radio_as_select_for_procedure_type"];
		}
		?>
		<script type="text/javascript">
			var radio_as_select_for_procedure_type_option = {
					'PT_OPEN' : [
						'enable_field',
						'',
						[],
						'accelerated_proc'
					],
					'PT_RESTRICTED' : [
						'enable_field',
						'',
						[],
						'accelerated_proc'
					],
					'PT_COMPETITIVE_NEGOTIATION' : [
						'enable_field',
						'',
						[],
						'accelerated_proc'
					]
				};
		</script>
		<select name="guue[PROCEDURE][radio_as_select_for_procedure_type]" rel="<?= isRequired("radio_as_select_for_procedure_type") ?>;1;0;A" onchange="add_extra_info($(this).val(), radio_as_select_for_procedure_type_option)" title="Tipo di procedura">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_OPEN" ? 'selected="selected"' : null ?> value="PT_OPEN">Procedura aperta</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_RESTRICTED" ? 'selected="selected"' : null ?> value="PT_RESTRICTED">Procedura ristretta</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_NEGOTIATED_WITH_PRIOR_CALL" ? 'selected="selected"' : null ?> value="PT_NEGOTIATED_WITH_PRIOR_CALL">Procedura negoziata con previo avviso di indizione di gara</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_COMPETITIVE_DIALOGUE" ? 'selected="selected"' : null ?> value="PT_COMPETITIVE_DIALOGUE">Dialogo competitivo</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_INNOVATION_PARTNERSHIP" ? 'selected="selected"' : null ?> value="PT_INNOVATION_PARTNERSHIP">Partenariato per l&#39;innovazione</option>
		</select>
	</td>
</tr>