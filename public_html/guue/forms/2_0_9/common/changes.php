<?
	if(empty($change_item)) $change_item = null;
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && empty($root)) {
		include_once '../../../../../config.php';
		include_once $root . '/inc/funzioni.php';
		if(!empty($_POST["param"]["item"])) {
			$change_item = $_POST["param"]["item"];
		}
	}
?>
<table id="<?= !empty($change_item) ? 'change_item_'.$change_item : null ?>" class="bordered">
	<tbody>
		<tr>
			<td class="etichetta">
				<label><b>Tipologia di Variazione</b></label>
			</td>
			<td colspan="3">
				<script type="text/javascript">
					var type_item_<?= $change_item ?> = {
						'TESTO_ITEM_TO_IGNORE' : [
							'ajax_load',
							'changes_text',
							[],
							'value_change_item_<?= $change_item ?>',
							{item: '<?= $change_item ?>'}
						],
						'DATA_ITEM_TO_IGNORE' : [
							'ajax_load',
							'changes_date',
							[],
							'value_change_item_<?= $change_item ?>',
							{item: '<?= $change_item ?>'}
						],
						'CPV_MAIN_ITEM_TO_IGNORE' : [
							'ajax_load',
							'changes_cpv_main',
							[],
							'value_change_item_<?= $change_item ?>',
							{item: '<?= $change_item ?>'}
						]
					};
				</script>
				<select name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][radio_as_select_for_type]"  title="Tipologia di Variazione" rel="S;1;0;A" onchange="add_extra_info($(this).val(), type_item_<?= $change_item ?>)">
					<option value="">Seleziona..</option>
					<option <?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["radio_as_select_for_type"]) && $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["radio_as_select_for_type"] == "TESTO_ITEM_TO_IGNORE" ? 'selected="selected"' : null ?> value="TESTO_ITEM_TO_IGNORE">Testo</option>
					<option <?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["radio_as_select_for_type"]) && $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["radio_as_select_for_type"] == "DATA_ITEM_TO_IGNORE" ? 'selected="selected"' : null ?> value="DATA_ITEM_TO_IGNORE">Date</option>
					<option <?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["radio_as_select_for_type"]) && $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["radio_as_select_for_type"] == "CPV_MAIN_ITEM_TO_IGNORE" ? 'selected="selected"' : null ?> value="CPV_MAIN_ITEM_TO_IGNORE">Codice CPV</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="etichetta">
				<label>Numero della Sezione:</label>
			</td>
			<td>
				<input style="font-size: 1.3em" type="text" name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][WHERE][SECTION]" value="<?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["WHERE"]["SECTION"]) ? $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["WHERE"]["SECTION"] : null?>" rel="S;0;20;A" title="Numero della Sezione">
			</td>
			<td class="etichetta">
				<label>Lotto:</label>
			</td>
			<td>
				<input style="font-size: 1.3em" type="text" name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][WHERE][LOT_NO]" value="<?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["WHERE"]["LOT_NO"]) ? $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["WHERE"]["SECTION"] : null?>" rel="N;0;11;N" title="Numero del lotto">
			</td>
		</tr>
		<tr>
			<td class="etichetta">
				<label>Punto in cui si trova il testo da modificare:</label>
			</td>
			<td colspan="3">
				<input style="font-size: 1.3em" type="text" name="guue[CHANGES][CHANGE][ITEM_<?= $change_item ?>][WHERE][LABEL]" value="<?= !empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["WHERE"]["LABEL"]) ? $guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["WHERE"]["LABEL"] : null?>" rel="S;0;20;A" title="Numero della Sezione">
			</td>
		</tr>
		<tr>
			<td colspan="4" id="value_change_item_<?= $change_item ?>"><?
				if(!empty($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["radio_as_select_for_type"])) {
					switch ($guue["CHANGES"]["CHANGE"]["ITEM_".$change_item]["radio_as_select_for_type"]) {
						case "TESTO_ITEM_TO_IGNORE":
							include $root . "/guue/forms/".(!empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : '2_0_9')."/common/changes_text.php";
							break;
						case "DATA_ITEM_TO_IGNORE":
							include $root . "/guue/forms/".(!empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : '2_0_9')."/common/changes_date.php";
							break;
						case "CPV_MAIN_ITEM_TO_IGNORE":
							include $root . "/guue/forms/".(!empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : '2_0_9')."/common/changes_cpv_main.php";
							break;
					}
				}
			?></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">
				<button type="button" onclick="$('#change_item_<?= $change_item ?>').remove();" class="submit_big" style="background-color: #CC0000; color: #FFF;">ELIMINA INFORMAZIONI DA VARIARE</button>
			</td>
		</tr>
	</tfoot>
</table>
