<tr>
	<td class="etichetta">
		<label>Contratto d&#39;appalto n.:</label>
	</td>
	<td>
		<input type="text" title="N. Contratto d&#39;appalto" class="espandi" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][CONTRACT_NO]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["CONTRACT_NO"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["CONTRACT_NO"] : null ?>" rel="N;1;200;A">
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
		<input type="text" title="Denominazione" class="espandi" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][TITLE]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["TITLE"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["TITLE"] : null ?>" rel="N;1;200;A">
	</td>
</tr>
<tr>
	<td id="awarded_contract_<?= $item ?>" colspan="4">
		<?
			include 'sezione_5_2_f151.php';
		?>
	</td>
</tr>
<tr>
	<td colspan="4" class="etichetta">
	<button type="button" onclick="$('#lot_award_no_<?= $item ?>').remove();$('#padding_lot_award_no_<?= $item ?>').remove();" class="submit_big" style="background-color: #CC0000; color: #FFF;">ELIMINA QUESTO LOTTO</button>
	</td>
</tr>
