<tr>
	<td class="etichetta" colspan="4"><label>II.2.13) Informazioni relative ai fondi dell&#39;Unione europea</label></td>
</tr>
<tr>
	<td colspan="3">
		L&#39;appalto &egrave; connesso ad un progetto e/o programma finanziato da fondi dell&#39;Unione europea?
	</td>
	<td>
		<script>
			var eu_union_funds_option_<?= $item ?> = {
				'EU_PROGR_RELATED_ITEM_TO_IGNORE' : [
					'enable_field',
					'',
					[],
					'item_<?= $item ?>_eu_progr_related'
				]
			};
		</script>
		<?
		$eu_progr_related = FALSE;
		$no_eu_progr_related = FALSE;
		if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_eu_union_funds"])) {
			$eu_progr_related = $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_eu_union_funds"] == "EU_PROGR_RELATED_ITEM_TO_IGNORE" ? TRUE : FALSE;
			$no_eu_progr_related = $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["radio_as_select_for_eu_union_funds"] == "NO_EU_PROGR_RELATED" ? TRUE : FALSE;
		}
		?>
		<select name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][radio_as_select_for_eu_union_funds]" rel="<?= isRequired("radio_as_select_for_eu_union_funds") ?>;1;0;A" onchange="add_extra_info($(this).val(), eu_union_funds_option_<?= $item ?>)" title="Fondi EU">
			<option value="">Seleziona..</option>
			<option <?= $eu_progr_related ? 'selected="selected"' : null ?>value="EU_PROGR_RELATED_ITEM_TO_IGNORE">Si</option>
			<option <?= $no_eu_progr_related ? 'selected="selected"' : null ?>value="NO_EU_PROGR_RELATED">No</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="3">
		Numero o riferimento del progetto:
	</td>
	<td colspan="1">
		<input id="item_<?= $item ?>_eu_progr_related" <?= ($eu_progr_related && !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["EU_PROGR_RELATED"]["val"])) ? 'value="'.$guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["EU_PROGR_RELATED"]["val"].'"' : null ?> type="text" title="N. rif. progetto" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][EU_PROGR_RELATED][val]" rel="S;1;200;A" <?= !$eu_progr_related ? 'disabled="disabled"' : null ?>>
	</td>
</tr>