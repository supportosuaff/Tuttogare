<tr>
	<td colspan="2" class="etichetta">
		<label>Il concorso si &egrave; concluso senza l&#39;attribuzione di premi?:</label>
	</td>
	<td colspan="2">
		<?
		$radio_as_select_for_awarded_contract = "";
		if(!empty($guue["RESULTS"]["ITEM_".$item]["radio_as_select_for_awarded_contract"])) {
			$radio_as_select_for_awarded_contract = $guue["RESULTS"]["ITEM_".$item]["radio_as_select_for_awarded_contract"];
		}
		?>
		<script>
			var radio_as_select_for_awarded_contract_<?= $item ?> = {
				'AWARDED_PRIZE_ITEM_TO_IGNORE' : [
					'ajax_load',
					['sezioni', 'sezione_5_3_f132'],
					[],
					'awarded_contract_<?= $item ?>',
					{item: '<?= $item ?>'}
				],
				'NO_AWARDED_PRIZE_ITEM_TO_IGNORE' : [
					'ajax_load',
					['sezioni', 'sezione_5_1_f13'],
					[],
					'awarded_contract_<?= $item ?>',
					{item: '<?= $item ?>'}
				]
			};
		</script>
		<select name="guue[RESULTS][ITEM_<?= $item ?>][radio_as_select_for_awarded_contract]" rel="S;1;0;A" title="Contratto aggiudicato" onchange="add_extra_info($(this).val(), radio_as_select_for_awarded_contract_<?= $item ?>)">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_awarded_contract == 'NO_AWARDED_PRIZE_ITEM_TO_IGNORE' ? 'selected="selected"' : null ?> value="NO_AWARDED_PRIZE_ITEM_TO_IGNORE">Si</option>
			<option <?= $radio_as_select_for_awarded_contract == 'AWARDED_PRIZE_ITEM_TO_IGNORE' ? 'selected="selected"' : null ?> value="AWARDED_PRIZE_ITEM_TO_IGNORE">No</option>
		</select>
	</td>
</tr>
<tr>
	<td id="awarded_contract_<?= $item ?>" colspan="4"><?
		if($radio_as_select_for_awarded_contract == 'AWARDED_PRIZE_ITEM_TO_IGNORE') include 'sezione_5_3_f132.php';
		if($radio_as_select_for_awarded_contract == 'NO_AWARDED_PRIZE_ITEM_TO_IGNORE') include 'sezione_5_1_f13.php';
	?></td>
</tr>
<tr>
	<td colspan="4" class="etichetta">
	<button type="button" onclick="$('#lot_award_no_<?= $item ?>').remove();$('#padding_lot_award_no_<?= $item ?>').remove();" class="submit_big" style="background-color: #CC0000; color: #FFF;">ELIMINA QUESTO LOTTO</button>
	</td>
</tr>