<?
	if(empty($change_item)) $change_item = null;
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && empty($root)) {
		include_once '../../../../../config.php';
		include_once $root . '/inc/funzioni.php';
		if(!empty($_POST["data"]["item"])) {
			$change_item = $_POST["data"]["item"];
		}
	}
$sql = "SELECT * FROM b_cpv";
$ris = $pdo->bindAndExec($sql);
if($ris->rowCount() > 0) {
	$rec_cpv = $ris->fetchAll(PDO::FETCH_ASSOC);
}
?>
<table class="bordered">
	<tbody>
		<tr>
			<td class="etichetta">
				<label>Sostituisci il codice CPV:</label>
			</td>
			<td>
				<select name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][OLD_VALUE][CPV_MAIN][CPV_CODE][ATTRIBUTE][CODE]" title="Codice CPV da sostituire" rel="S;1;0;A">
					<option value="">Seleziona..</option>
					<?
						foreach ($rec_cpv as $cpv) {
							?>
							<option <?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["OLD_VALUE"]["CPV_MAIN"]["CPV_CODE"]["ATTRIBUTE"]["CODE"]) ? 'selected="selected"' : null ?> value="<?= $cpv["codice"] ?>"><?= $cpv["descrizione"] ?></option>
							<?
						}
					?>
				</select>
			</td>
			<td class="etichetta">
				<label>Con il codice CPV:</label>
			</td>
			<td>
				<select name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][NEW_VALUE][CPV_MAIN][CPV_CODE][ATTRIBUTE][CODE]" title="Codice CPV da sostituire" rel="S;1;0;A">
					<option value="">Seleziona..</option>
					<?
						foreach ($rec_cpv as $cpv) {
							?>
							<option <?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["NEW_VALUE"]["CPV_MAIN"]["CPV_CODE"]["ATTRIBUTE"]["CODE"]) ? 'selected="selected"' : null ?> value="<?= $cpv["codice"] ?>"><?= $cpv["descrizione"] ?></option>
							<?
						}
					?>
				</select>
			</td>
		</tr>
	</tbody>
</table>