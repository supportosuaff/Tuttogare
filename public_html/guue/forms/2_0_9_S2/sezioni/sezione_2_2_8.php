<tr>
	<td colspan="2" class="etichetta">
		<label>
			II.2.8) Durata del sistema di qualificazione
		</label>
	</td>
	<td colspan="2">
		<?
		$radio_as_select_for_qs_duration = !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["radio_as_select_for_qs_duration"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["radio_as_select_for_qs_duration"] : "";
		?>
		<script type="text/javascript">
			var radio_as_select_for_qs_duration_<?= $item ?> = {
				'START_END_ITEM_TO_IGNORE' : [
					'enable_field',
					'',
					[],
					['qs_date_start_<?= $item ?>', 'qs_date_end_<?= $item ?>']
				]
			};
		</script>
		<select name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][QS][radio_as_select_for_qs_duration]" rel="S;1;0;A" title="Tipologia della durata" id="item_<?= $item ?>_qs_duration" onchange="add_extra_info($(this).val(), radio_as_select_for_qs_duration_<?= $item ?>)">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_qs_duration == "INDEFINITE_DURATION" ? 'selected="selected"' : null ?> value="INDEFINITE_DURATION">Durata indeterminata</option>
			<option <?= $radio_as_select_for_qs_duration == "START_END_ITEM_TO_IGNORE" ? 'selected="selected"' : null ?> value="START_END_ITEM_TO_IGNORE">Data di inizio/fine indicate di seguito</option>
		</select>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Data di Inizio:</label>
	</td>
	<td>
		<input type="text" id="qs_date_start_<?= $item ?>" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][QS][DATE_START]" class="datepick" title="Data di Inizio" rel="S;0;0;D" <?= $radio_as_select_for_qs_duration != "START_END_ITEM_TO_IGNORE" ? 'disabled="disabled"' : null ?> value="<?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["DATE_START"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["DATE_START"] : null ?>">
	</td>
	<td class="etichetta">
		<label>Data di Fine:</label>
	</td>
	<td>
		<input type="text" id="qs_date_end_<?= $item ?>" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][QS][DATE_END]" class="datepick" title="Data di Fine" rel="S;0;0;D" <?= $radio_as_select_for_qs_duration != "START_END_ITEM_TO_IGNORE" ? 'disabled="disabled"' : null ?> value="<?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["DATE_END"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["DATE_END"] : null ?>">
	</td>
</tr>
<tr>
	<td colspan="4">
		<label>
			<input type="checkbox" <?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["RENEWAL_DESCR"]) ? 'checked="checked"' : null ?> onchange="toggle_field($(this),'#renewal_descr_<?= $item ?>')" id="justification_checkbox" rel="">
			Rinnovo del sistema di qualificazione
		</label>
	</td>
</tr>
<tr>
	<td colspan="4" class="etichetta">
		<label>Formalit&agrave; necessarie per valutare la conformit&agrave; ai requisiti</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea id="renewal_descr_<?= $item ?>" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][QS][RENEWAL_DESCR]" <?= empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["RENEWAL_DESCR"]) ? 'disabled="disabled"' : null ?> class="ckeditor_simple" rel="S;1;400;A" title="Rinnovo del sistema di qualificazione"><?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["RENEWAL_DESCR"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["QS"]["RENEWAL_DESCR"] : null ?></textarea>
	</td>
</tr>