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
		<script type="text/javascript">
			$('#radio_as_select_for_procedure_type_options').change(function (e) {
				if ($(this).val() == "PT_OPEN") {
					$('#durata_del_contratto').removeAttr('rel');
					$('#durata_del_contratto').removeProp('rel');
					$('#participants_radio').prop('checked', false).attr('checked', false).trigger('change');
					$('#participants_radio_max_min').prop('checked', false).attr('checked', false).trigger('change');
					$('#add_participant_name').prop('checked', false).attr('checked', false).trigger('change');
					disable_field($('#participants_radio'));
					disable_field($('#participants_radio_max_min'));
					disable_field($('#add_participant_name'));
				} else {
					$('#durata_del_contratto').prop('rel', '<?= isRequired("durata_del_contratto") ?>;0;0;checked;group_validate');
					$('#durata_del_contratto').attr('rel', '<?= isRequired("durata_del_contratto") ?>;0;0;checked;group_validate');
					enable_field($('#participants_radio'));
					enable_field($('#participants_radio_max_min'));
					enable_field($('#add_participant_name'));
				}
			});
			$(document).ready(function(e) {
				$('#radio_as_select_for_procedure_type_options').trigger('change');
			});
		</script>
	</td>
</tr>
<tr>
	<td colspan="4">
		<table id="durata_del_contratto" class="bordered valida" title="Durata del contratto" <? if(!empty($procedure_type) && $procedure_type == "PT_RESTRICTED") {?>rel="<?= isRequired("durata_del_contratto") ?>;0;0;checked;group_validate"<?} ?>>
			<tbody>
				<tr>
					<td colspan="4" class="etichetta">
						<label style="font-size: 14px;">Numero di partecipanti:</label>
					</td>
				</tr>
				<?
					$participants_radio = FALSE;
					$nb_participants = FALSE;
					$nb_participants_max_min = FALSE;
					if($procedure_type == "PT_RESTRICTED") {
						$participants_radio = TRUE;
						if(!empty($guue["PROCEDURE"]["NB_PARTICIPANTS"])) {
							$nb_participants = TRUE;
						}
						if(!empty($guue["PROCEDURE"]["NB_MIN_PARTICIPANTS"]) && !empty($guue["PROCEDURE"]["NB_MAX_PARTICIPANTS"])) {
							$nb_participants_max_min = TRUE;
						}
					}
				?>
				<tr>
					<td class="etichetta">
						<label style="font-size: 14px;"><input id="participants_radio" <?= $nb_participants ? 'checked="checked"' : null ?> <?= !$participants_radio ? 'disabled="disabled"' : null  ?> type="radio" name="nb_participants" onchange="toggle_field($(this), '#nb_participants');"> Partecipanti previsti:</label>
					</td>
					<td colspan="3">
						<input style="font-size: 1.3em;" type="text" title="Durata" name="guue[PROCEDURE][NB_PARTICIPANTS]" <?= !$nb_participants ? 'disabled="disabled"' : null ?> <?= $nb_participants ? 'value="'.$guue["PROCEDURE"]["NB_PARTICIPANTS"].'"' : null ?> id="nb_participants" rel="S;1;0;A">
					</td>
				</tr>
				<tr>
					<td class="etichetta">
						<label style="font-size: 14px;"><input id="participants_radio_max_min" <?= !$participants_radio ? 'disabled="disabled"' : null  ?> type="radio" <?= $nb_participants_max_min ? 'checked="checked"' : null ?> name="nb_participants" onchange="toggle_field($(this), ['#nb_min_participants', '#nb_max_participants']);">Max / Min numero di partecipanti:</label>
					</td>
					<td colspan="2">
						<input style="font-size: 1.3em;" type="text" title="Minimo numero di partecipanti" <?= ($nb_participants_max_min && !empty($guue["PROCEDURE"]["NB_MIN_PARTICIPANTS"])) ? 'value="'.$guue["PROCEDURE"]["NB_MIN_PARTICIPANTS"].'"' : null ?> name="guue[PROCEDURE][NB_MIN_PARTICIPANTS]" <?= !$nb_participants_max_min ? 'disabled="disabled"' : null ?> id="nb_min_participants" rel="S;1;0;N">
					</td>
					<td>
						<input style="font-size: 1.3em;" type="text" title="Massimo numero di partecipanti" <?= ($nb_participants_max_min && !empty($guue["PROCEDURE"]["NB_MAX_PARTICIPANTS"])) ? 'value="'.$guue["PROCEDURE"]["NB_MAX_PARTICIPANTS"].'"' : null ?> name="guue[PROCEDURE][NB_MAX_PARTICIPANTS]" <?= !$nb_participants_max_min ? 'disabled="disabled"' : null ?> id="nb_max_participants" rel="S;1;0;N">
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>