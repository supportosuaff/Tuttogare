<?
	if(empty($change_item)) $change_item = null;
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && empty($root)) {
		include_once '../../../../../config.php';
		include_once $root . '/inc/funzioni.php';
		if(!empty($_POST["data"]["item"])) {
			$change_item = $_POST["data"]["item"];
		}
	}
?>
<table class="bordered">
	<tbody>
		<tr>
			<td class="etichetta">
				<label>Sostituisci la data:</label>
			</td>
			<td>
				<input style="font-size: 1.3em" type="text" class="datepick" title="Testo da sostituire" rel="S;10;10;D" name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][OLD_VALUE][DATE]" value="<?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["OLD_VALUE"]["DATE"]) ? $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["OLD_VALUE"]["DATE"] : null ?>">
			</td>
			<td class="etichetta">
				<label>e l&#39;ora:</label>
			</td>
			<td>
				<input style="font-size: 1.3em" type="text" class="timepick" title="Testo sostitutivo" rel="S;5;5;T" name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][OLD_VALUE][TIME]" value="<?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["OLD_VALUE"]["TIME"]) ? $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["OLD_VALUE"]["TIME"] : null ?>">
			</td>
		</tr>
		<tr>
			<td class="etichetta">
				<label>Con la data:</label>
			</td>
			<td>
				<input style="font-size: 1.3em" type="text" class="datepick" title="Testo da sostituire" rel="S;10;10;D" name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][NEW_VALUE][DATE]" value="<?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["NEW_VALUE"]["DATE"]) ? $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["NEW_VALUE"]["DATE"] : null ?>">
			</td>
			<td class="etichetta">
				<label>e l&#39;ora:</label>
			</td>
			<td>
				<input style="font-size: 1.3em" type="text" class="timepick" title="Testo sostitutivo" rel="S;5;5;T" name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][NEW_VALUE][TIME]" value="<?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["NEW_VALUE"]["TIME"]) ? $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["NEW_VALUE"]["TIME"] : null ?>">
			</td>
		</tr>
	</tbody>
</table>