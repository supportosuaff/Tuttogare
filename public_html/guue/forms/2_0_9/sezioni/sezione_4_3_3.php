<tr>
	<td class="etichetta">
		<label>IV.3.3) Appalti complementari:</label>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Successivamente al concorso, gli autori dei progetti premiati avranno diritto all'attribuzione di appalti di servizi?</label>
	</td>
</tr>
<tr>
	<td>
		<?
		$radio_as_select_for_follow_up_contracts = !empty($guue['PROCEDURE']['radio_as_select_for_follow_up_contracts']) ? $guue['PROCEDURE']['radio_as_select_for_follow_up_contracts'] : null;
		?>
		<select name="guue[PROCEDURE][radio_as_select_for_follow_up_contracts]" title="Informazioni relative ai premi" rel="S;1;0;A">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_follow_up_contracts == "FOLLOW_UP_CONTRACTS" ? 'selected="selected"' : null ?> value="FOLLOW_UP_CONTRACTS">Si</option>
			<option <?= $radio_as_select_for_follow_up_contracts == "NO_FOLLOW_UP_CONTRACTS" ? 'selected="selected"' : null ?> value="NO_FOLLOW_UP_CONTRACTS">No</option>
		</select>
	</td>
</tr>