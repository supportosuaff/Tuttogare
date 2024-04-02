<tr>
	<td class="etichetta" colspan="4">
		<label><b>VII.1.5) Durata del contratto d&#39;appalto, dell&#39;accordo quadro, del sistema dinamico di acquisizione o della concessione</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<table class="bordered valida" title="Durata del contratto" rel="<?= isRequired("durata_del_contratto") ?>;0;0;checked;group_validate">
			<tbody>
				<tr>
					<td class="etichetta">
						<?
						$radio_as_select_for_type_of_duration = "";
						if(!empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DURATION"]["ATTRIBUTE"]["TYPE"]["radio_as_select_for_type_of_duration"])) {
							$radio_as_select_for_type_of_duration = $guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DURATION"]["ATTRIBUTE"]["TYPE"]["radio_as_select_for_type_of_duration"];
						}
						?>
						<label style="font-size: 14px;"><input <?= !empty($radio_as_select_for_type_of_duration) ? 'checked="checked"' : null ?> name="durata_item" type="radio" onchange="toggle_field($(this), ['#duration_type', '#duration']);"> Mesi / Giorni:</label>
					</td>
					<td>
						<select style="font-size: 1.3em;" name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][DURATION][ATTRIBUTE][TYPE][radio_as_select_for_type_of_duration]" rel="S;1;0;A" title="Tipologia della durata" id="duration_type" <?= empty($radio_as_select_for_type_of_duration) ? 'disabled="disabled"' : null ?>>
							<option value="">Seleziona..</option>
							<option <?= $radio_as_select_for_type_of_duration == "MONTH" ? 'selected="selected"' : null ?> value="MONTH">Mesi</option>
							<option <?= $radio_as_select_for_type_of_duration == "DAY" ? 'selected="selected"' : null ?> value="DAY">Giorni</option>
						</select>
					</td>
					<td colspan="2">
						<input style="font-size: 1.3em;" type="text" title="Durata" name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][DURATION][val]" <?= empty($radio_as_select_for_type_of_duration) ? 'disabled="disabled"' : null ?> <?= !empty($radio_as_select_for_type_of_duration) && !empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DURATION"]["val"]) ? 'value="'.$guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DURATION"]["val"].'"' : null ?> id="duration" rel="S;1;0;A">
					</td>
				</tr>
				<tr>
					<?
					$radio_durata_date = FALSE;
					if(!empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DATE_START"]) || !empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DATE_END"])) {
						$radio_durata_date = TRUE;
					}
					?>
					<td class="etichetta">
						<label style="font-size: 14px;"><input type="radio" <?= $radio_durata_date ? 'checked="checked"' : null ?> name="durata_item" onchange="toggle_field($(this), ['#date_start', '#date_end']);">Date di inizio e fine:</label>
					</td>
					<td colspan="2">
						<input style="font-size: 1.3em;" class="datepick" type="text" title="Data di Inizio" <?= ($radio_durata_date && !empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DATE_START"])) ? 'value="'.$guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DATE_START"].'"' : null ?> name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][DATE_START]" <?= !$radio_durata_date ? 'disabled="disabled"' : null ?> id="date_start" rel="S;1;0;D">
					</td>
					<td>
						<input style="font-size: 1.3em;" class="datepick" type="text" title="Data di Fine" <?= ($radio_durata_date && !empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DATE_END"])) ? 'value="'.$guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["DATE_END"].'"' : null ?> name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][DATE_END]" <?= !$radio_durata_date ? 'disabled="disabled"' : null ?> id="date_end" rel="S;1;0;D">
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>