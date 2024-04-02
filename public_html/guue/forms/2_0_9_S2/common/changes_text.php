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
				<label>Sostituisci:</label>
			</td>
			<td>
				<input style="font-size: 1.3em" type="text" title="Testo da sostituire" name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][OLD_VALUE][TEXT]" value="<?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["OLD_VALUE"]["TEXT"]) ? $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["OLD_VALUE"]["TEXT"] : null ?>">
			</td>
		</tr>
		<tr>
			<td class="etichetta">
				<label>Con:</label>
			</td>
			<td>
				<input style="font-size: 1.3em" type="text" title="Testo sostitutivo" name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][NEW_VALUE][TEXT]" value="<?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["NEW_VALUE"]["TEXT"]) ? $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["NEW_VALUE"]["TEXT"] : null ?>">
			</td>
		</tr>
	</tbody>
</table>