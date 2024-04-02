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
		<select name="guue[PROCEDURE][radio_as_select_for_procedure_type]" rel="<?= isRequired("radio_as_select_for_procedure_type") ?>;1;0;A">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_OPEN" ? 'selected="selected"' : null ?> value="PT_OPEN">Procedura aperta</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_RESTRICTED" ? 'selected="selected"' : null ?> value="PT_RESTRICTED">Procedura ristretta</option>
			<option <?= $radio_as_select_for_procedure_type == "PT_COMPETITIVE_NEGOTIATION" ? 'selected="selected"' : null ?> value="PT_INVOLVING_NEGOTIATION">Procedura che comporta negoziazioni</option>
		</select>
	</td>
</tr>