<tr>
	<td class="etichetta">
		<label>IV.3.1) Informazioni relative ai premi:</label>
	</td>
</tr>
<tr>
	<td>
		<script type="text/javascript">
			var radio_as_select_for_prize_awarded_options = {
				'PRIZE_AWARDED' : [
					'enable_field',
					'',
					[],
					'number_value_prize'
				]
			}
		</script>
		<?
		$radio_as_select_for_prize_awarded = !empty($guue['PROCEDURE']['radio_as_select_for_prize_awarded']) ? $guue['PROCEDURE']['radio_as_select_for_prize_awarded'] : null;
		?>
		<select name="guue[PROCEDURE][radio_as_select_for_prize_awarded]" title="Informazioni relative ai premi" rel="S;1;0;A" onchange="add_extra_info($(this).val(), radio_as_select_for_prize_awarded_options)">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_prize_awarded == "PRIZE_AWARDED" ? 'selected="selected"' : null ?> value="PRIZE_AWARDED">Si</option>
			<option <?= $radio_as_select_for_prize_awarded == "NO_PRIZE_AWARDED" ? 'selected="selected"' : null ?> value="NO_PRIZE_AWARDED">No</option>
		</select>
	</td>
</tr>
<tr>
	<td class="etichetta"><label>Numero e valore dei premi da attribuire:</label></td>
</tr>
<tr>
	<td>
		<textarea id="number_value_prize" name="guue[PROCEDURE][NUMBER_VALUE_PRIZE]" class="ckeditor_simple" rel="N;0;1500;A" <?= $radio_as_select_for_prize_awarded != "PRIZE_AWARDED" ? 'disabled="disabled"' : null ?>><?= $radio_as_select_for_prize_awarded == "PRIZE_AWARDED" && !empty($guue["PROCEDURE"]["NUMBER_VALUE_PRIZE"]) ? $guue["PROCEDURE"]["NUMBER_VALUE_PRIZE"] : null ?></textarea>
	</td>
</tr>