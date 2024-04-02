<tr>
	<td class="etichetta" colspan="4">
		<label>IV.1.1) Tipo di procedura</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<?
		$pt_restricted = FALSE;
		$pt_competitive_negotiation = FALSE;
		if(!empty($guue["PROCEDURE"]["radio_as_select_for_procedure_type"])) {
			$pt_restricted = $guue["PROCEDURE"]["radio_as_select_for_procedure_type"] == "PT_RESTRICTED" ? TRUE : FALSE;
			$pt_competitive_negotiation = $guue["PROCEDURE"]["radio_as_select_for_procedure_type"] == "PT_NEGOTIATED_WITH_PRIOR_CALL" ? TRUE : FALSE;
		}
		?>
		<select name="guue[PROCEDURE][radio_as_select_for_procedure_type]" title="Tipo di procedura" rel="<?= isRequired("radio_as_select_for_procedure_type") ?>;1;0;A">
			<option value="">Seleziona..</option>
			<option <?= $pt_restricted ? 'selected="selected"' : null ?> value="PT_RESTRICTED">Procedura ristretta</option>
			<option <?= $pt_competitive_negotiation ? 'selected="selected"' : null ?> value="PT_NEGOTIATED_WITH_PRIOR_CALL">Procedura negoziata con previo avviso di indizione di gara</option>
		</select>
	</td>
</tr>