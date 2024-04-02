<tr>
	<td class="etichetta" colspan="4">
		<label>IV.1.2) Tipo di concorso</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<?
		$procedure_type = null;
		if(!empty($guue["PROCEDURE"]["radio_as_select_for_procedure_type"])) {
			$procedure_type = $guue["PROCEDURE"]["radio_as_select_for_procedure_type"];
		}
		?>
		<select id="radio_as_select_for_procedure_type_options" name="guue[PROCEDURE][radio_as_select_for_procedure_type]" rel="<?= isRequired("radio_as_select_for_procedure_type") ?>;1;0;A">
			<option value="">Seleziona..</option>
			<option <?= $procedure_type == "PT_OPEN" ? 'selected="selected"' : null ?> value="PT_OPEN">Procedura aperta</option>
			<option <?= $procedure_type == "PT_RESTRICTED" ? 'selected="selected"' : null ?> value="PT_RESTRICTED">Procedura ristretta</option>
		</select>
	</td>
</tr>