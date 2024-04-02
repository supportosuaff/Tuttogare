<tr>
	<td class="etichetta">
		<label>Contratto d&#39;appalto n.:</label>
	</td>
	<td>
		<input type="text" title="N. Contratto d&#39;appalto" class="espandi" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][CONTRACT_NO]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["CONTRACT_NO"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["CONTRACT_NO"] : null ?>" rel="<?= isRequired("OBJECT_CONTRACT-radio_as_select_for_lot_division") ?>;1;200;A">
	</td>
	<td class="etichetta">
		<label>Lotto n.:</label>
	</td>
	<td>
		<b><?= $item ?></b>
		<input type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][LOT_NO]" rel="S;1;4;N" value="<?= $item ?>">
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Denominazione:</label>
	</td>
	<td colspan="3">
		<input type="text" title="Denominazione" class="espandi" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][TITLE]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["TITLE"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["TITLE"] : null ?>" rel="<?= isRequired("OBJECT_CONTRACT-radio_as_select_for_lot_division") ?>;1;200;A">
	</td>
</tr>
<tr>
	<td colspan="2" class="etichetta">
		<label>Un contratto d&#39;appalto/lotto &egrave; stato aggiudicato:</label>
	</td>
	<td colspan="2">
		<?
		$radio_as_select_for_awarded_contract = "";
		if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["radio_as_select_for_awarded_contract"])) {
			$radio_as_select_for_awarded_contract = $guue["AWARD_CONTRACT"]["ITEM_".$item]["radio_as_select_for_awarded_contract"];
		}
		?>
		<script>
			var radio_as_select_for_awarded_contract_<?= $item ?> = {
				'AWARDED_CONTRACT_ITEM_TO_IGNORE' : [
					'ajax_load',
					['sezioni', 'sezione_5_2_f06'],
					[],
					'awarded_contract_<?= $item ?>',
					{item: '<?= $item ?>'}
				],
				'NO_AWARDED_CONTRACT_ITEM_TO_IGNORE' : [
					'ajax_load',
					['sezioni', 'sezione_5_1'],
					[],
					'awarded_contract_<?= $item ?>',
					{item: '<?= $item ?>'}
				]
			};
		</script>
		<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][radio_as_select_for_awarded_contract]" rel="S;1;0;A" title="Contratto aggiudicato" onchange="add_extra_info($(this).val(), radio_as_select_for_awarded_contract_<?= $item ?>)">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_awarded_contract == 'AWARDED_CONTRACT_ITEM_TO_IGNORE' ? 'selected="selected"' : null ?> value="AWARDED_CONTRACT_ITEM_TO_IGNORE">Si</option>
			<option <?= $radio_as_select_for_awarded_contract == 'NO_AWARDED_CONTRACT_ITEM_TO_IGNORE' ? 'selected="selected"' : null ?> value="NO_AWARDED_CONTRACT_ITEM_TO_IGNORE">No</option>
		</select>
	</td>
</tr>
<tr>
	<td id="awarded_contract_<?= $item ?>" colspan="4"><?
		if($radio_as_select_for_awarded_contract == 'NO_AWARDED_CONTRACT_ITEM_TO_IGNORE') include 'sezione_5_1.php';
		if($radio_as_select_for_awarded_contract == 'AWARDED_CONTRACT_ITEM_TO_IGNORE') include 'sezione_5_2_f06.php';
	?></td>
</tr>
<tr>
	<td colspan="4" class="etichetta">
		<script type="text/javascript">
			var lotaward = <?= $item ?>;
		</script>
		<button type="button" onclick="$('#lot_award_no_<?= $item ?>').remove();$('#padding_lot_award_no_<?= $item ?>').remove();" class="submit_big" style="background-color: #CC0000; color: #FFF;">ELIMINA QUESTO LOTTO</button>
	</td>
</tr>