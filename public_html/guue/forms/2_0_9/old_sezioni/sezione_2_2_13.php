<tr>
	<td class="etichetta" colspan="4"><label>II.2.13) Informazioni relative ai fondi dell&#39;Unione europea</label></td>
</tr>
<tr>
	<td colspan="3" class="etichetta">
		<label>L&#39;appalto e&egrave; connesso ad un progetto e/o programma finanziato da fondi dell&#39;Unione europea?</label>
	</td>
	<td>
		<script>
			var eu_union_funds_option = {
				'EU_PROGR_RELATED' : [
					'enable_field',
					'',
					[],
					'item_0_eu_progr_related'
				]
			};
		</script>
		<select name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_0][radio_as_select_for_eu_union_funds]" re="S;1;0;A" onchange="add_extra_info($(this).val(), eu_union_funds_option)" title="Fondi EU">
			<option value="">Seleziona..</option>
			<option value="EU_PROGR_RELATED">Si</option>
			<option value="NO_EU_PROGR_RELATED">No</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="2" class="etichetta">
		<label>Numero o riferimento del progetto:</label>
	</td>
	<td colspan="2">
		<input id="item_0_eu_progr_related" type="text" title="N. rif. progetto" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_0][EU_PROGR_RELATED]" rel="S;1;200;A" disabled="disabled">
	</td>
</tr>