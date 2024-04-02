<tr>
	<td colspan="4" class="etichetta">
		<label>IV.1.7) Nominativi dei partecipanti già selezionati <i>(Da indicare solo nel caso di procedura ristretta)</i>: </label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<table class="bordered">
			<tbody id="already_selected_participant_name"><?
			$selected_participant_names_numb = 0;
			$add_participant_name = FALSE;
			if(!empty($guue["PROCEDURE"]["radio_as_select_for_procedure_type"]) && $guue["PROCEDURE"]["radio_as_select_for_procedure_type"] == "PT_RESTRICTED") {
				$add_participant_name = TRUE;
				if(!empty($guue['PROCEDURE']['PARTICIPANT_NAME'])) {
					foreach ($guue['PROCEDURE']['PARTICIPANT_NAME'] as $item_name => $item_name_value) {
						include $root.'/guue/getparticipantname.php';
						$selected_participant_names_numb = $item_name;
					}
				}
			}
			?></tbody>
		</table>
	</td>
</tr>
<tr>
	<td colspan="4">
		<script type="text/javascript">
			var selected_participant_names_numb = <?= $selected_participant_names_numb ?>;
		</script>
		<button <?= !$add_participant_name ? 'disabled="disabled"' : null ?> id="add_participant_name" type="button" class="aggiungi" onclick="selected_participant_names_numb++;aggiungi('getparticipantname.php','#already_selected_participant_name', {item:selected_participant_names_numb});return false;" ><img src="/img/add.png" alt="Aggiungi lotto">Aggiungi Nome Partecipante</button>
	</td>
</tr>