<tr>
	<td class="etichetta">
		<label>III.2.1) Informazioni relative ad una particolare professione <i>(solo per contratti di servizi)</i></label>
	</td>
</tr>
<tr class="noBorder">
	<td>
		<label>La prestazione del servizio &egrave; riservata ad una particolare professione <i>(Citare le corrispondenti disposizioni legislative, regolamentari o amministrative)</i></label>
	</td>
</tr>
<tr class="noBorder">
	<td>
		<script type="text/javascript">
			var radio_as_select_for_particular_profession = {
				'PARTICULAR_PROFESSION' : [
					'enable_field',
					'',
					[],
					'particular_profession'
				]
			}
		</script>
		<?
		$radio_as_select_for_particular_profession = !empty($guue['LEFTI']['radio_as_select_for_particular_profession']) ? $guue['LEFTI']['radio_as_select_for_particular_profession'] : null;
		?>
		<select name="guue[LEFTI][radio_as_select_for_particular_profession]" title="Informazioni relative ad una particolare professione" rel="S;1;0;A" onchange="add_extra_info($(this).val(), radio_as_select_for_particular_profession)">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_particular_profession == "PARTICULAR_PROFESSION" ? 'selected="selected"' : null ?> value="PARTICULAR_PROFESSION">Si</option>
			<option <?= $radio_as_select_for_particular_profession == "NO_PARTICULAR_PROFESSION" ? 'selected="selected"' : null ?> value="NO_PARTICULAR_PROFESSION">No</option>
		</select>
	</td>
</tr>
<tr>
	<td>
		<input rel="N;0;400;A" type="text" id="particular_profession" name="guue[LEFTI][PARTICULAR_PROFESSION]" title="Informazioni relative ad una particolare professione" <?= (!empty($radio_as_select_for_particular_profession) && $radio_as_select_for_particular_profession == "PARTICULAR_PROFESSION") ? null : 'disabled="disabled"' ?> value="<?= (!empty($guue["LEFTI"]["PARTICULAR_PROFESSION"]) && !empty($radio_as_select_for_particular_profession)) ? $guue["LEFTI"]["PARTICULAR_PROFESSION"] : null ?>">
	</td>
</tr>