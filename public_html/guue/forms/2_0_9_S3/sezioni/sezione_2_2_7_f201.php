<tr>
	<td colspan="4">
		<table class="bordered valida" title="Durata del contratto" rel="<?= isRequired("durata_del_contratto") ?>;0;0;checked;group_validate">
			<tbody>
				<tr>
					<td colspan="4" class="etichetta">
						<label style="font-size: 14px;">II.2.7) Durata del contratto d&#39;appalto, dell&#39;accordo quadro o del sistema dinamico di acquisizione</label>
					</td>
				</tr>
				<tr>
					<td class="etichetta">
						<?
						$radio_as_select_for_type_of_duration = "";
						$month = FALSE;
						$day = FALSE;
						if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DURATION"]["ATTRIBUTE"]["TYPE"]["radio_as_select_for_type_of_duration"])) {
							$radio_as_select_for_type_of_duration = $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DURATION"]["ATTRIBUTE"]["TYPE"]["radio_as_select_for_type_of_duration"];
							$month = $radio_as_select_for_type_of_duration == "MONTH" ? TRUE : FALSE;
							$day = $radio_as_select_for_type_of_duration == "DAY" ? TRUE : FALSE;
						}
						?>
						<label style="font-size: 14px;"><input <?= !empty($radio_as_select_for_type_of_duration) ? 'checked="checked"' : null ?> type="radio" name="durata_item_<?= $item ?>" onchange="toggle_field($(this), ['#item_<?= $item ?>_duration_type', '#item_<?= $item ?>_duration']);"> Mesi / Giorni:</label>
					</td>
					<td>
						<select style="font-size: 1.3em;" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][DURATION][ATTRIBUTE][TYPE][radio_as_select_for_type_of_duration]" rel="S;1;0;A" title="Tipologia della durata" id="item_<?= $item ?>_duration_type" <?= empty($radio_as_select_for_type_of_duration) ? 'disabled="disabled"' : null ?>>
							<option value="">Seleziona..</option>
							<option <?= $month ? 'selected="selected"' : null ?> value="MONTH">Mesi</option>
							<option <?= $day ? 'selected="selected"' : null ?> value="DAY">Giorni</option>
						</select>
					</td>
					<td colspan="2">
						<input style="font-size: 1.3em;" type="text" title="Durata" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][DURATION][val]" <?= empty($radio_as_select_for_type_of_duration) ? 'disabled="disabled"' : null ?> <?= !empty($radio_as_select_for_type_of_duration) && !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DURATION"]["val"]) ? 'value="'.$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DURATION"]["val"].'"' : null ?> id="item_<?= $item ?>_duration" rel="S;1;0;A">
					</td>
				</tr>
				<tr>
					<?
					$radio_durata_date = FALSE;
					if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DATE_START"]) || !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DATE_END"])) {
						$radio_durata_date = TRUE;
					}
					?>
					<td class="etichetta">
						<label style="font-size: 14px;"><input type="radio" <?= $radio_durata_date ? 'checked="checked"' : null ?> name="durata_item_<?= $item ?>" onchange="toggle_field($(this), ['#item_<?= $item ?>_date_start', '#item_<?= $item ?>_date_end']);">Date di inizio e fine:</label>
					</td>
					<td colspan="2">
						<input style="font-size: 1.3em;" type="text" title="Data di Inizio" <?= ($radio_durata_date && !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DATE_START"])) ? 'value="'.$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DATE_START"].'"' : null ?> name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][DATE_START]" <?= !$radio_durata_date ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_date_start" rel="S;1;0;D">
					</td>
					<td>
						<input style="font-size: 1.3em;" type="text" title="Data di Fine" <?= ($radio_durata_date && !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DATE_END"])) ? 'value="'.$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["DATE_END"].'"' : null ?> name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][DATE_END]" <?= !$radio_durata_date ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_date_end" rel="S;1;0;D">
					</td>
				</tr>
				<tr>
					<td class="etichetta" colspan="4">
						<label style="font-size: 14px;">
							<input type="checkbox" <?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["JUSTIFICATION"]) ? 'checked="checked"' : null ?> onChange="toggle_field($(this), '#item_<?= $item ?>_justification')"> Direttiva 2014/25/UE – In caso di accordi quadro – giustificazione per una durata superiore a 8 anni:
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<textarea id="item_<?= $item ?>_justification" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][JUSTIFICATION]" rel="S;0;400;A" title="Descrizione dei rinnovi" class="ckeditor_simple" <?= empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["JUSTIFICATION"]) ? 'disabled="disabled"' : null ?>>
							<?= (!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["JUSTIFICATION"])) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["JUSTIFICATION"] : null ?>
						</textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>