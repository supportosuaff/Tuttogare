<?
	$ajax = FALSE;
	if (!empty($_POST["data"])) {
		session_start();
		$ajax = TRUE;
		$item = $_POST["data"]["item"];
	}
?>
<style type="text/css">
	td table.bigger * {
		font-size: 12px;
	}
</style>
<table class="bordered bigger">
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2) Aggiudicazione di appalto</b></label>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="etichetta">
			<label><b>V.2.1) Data di conclusione del contratto d&#39;appalto</b></label>
		</td>
		<td colspan="2">
			<input type="text" class="datepick" title="Data di conclusione del contratto d&#39;appalto" rel="S;2;0;D" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][DATE_CONCLUSION_CONTRACT]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["DATE_CONCLUSION_CONTRACT"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["DATE_CONCLUSION_CONTRACT"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label>
				<b>V.2.2) Informazioni sulle offerte</b>
			</label>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="3">L&#39;appalto e&egrave; stato aggiudicato a un raggruppamento di operatori economici:</td>
		<td>
			<?
			$radio_as_select_for_awarded_to_group = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_awarded_to_group"])) {
				$radio_as_select_for_awarded_to_group = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_awarded_to_group"];
			}
			?>
			<script>
				var radio_as_select_for_awarded_to_group_<?= $item ?> = {
					'AWARDED_TO_GROUP' : [
						'ajax_load',
						['sezioni', 'sezione_5_2'],
						[],
						'awarded_contract_<?= $item ?>',
						{item: '<?= $item ?>'}
					],
					'NO_AWARDED_TO_GROUP' : [
						'ajax_load',
						['sezioni', 'sezione_5_1'],
						[],
						'awarded_contract_<?= $item ?>',
						{item: '<?= $item ?>'}
					]
				};
			</script>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][radio_as_select_for_awarded_to_group]" rel="S;1;0;A" title="Aggiudicato a un raggruppamento">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_awarded_to_group == 'AWARDED_TO_GROUP' ? 'selected="selected"' : null ?> value="AWARDED_TO_GROUP">Si</option>
				<option <?= $radio_as_select_for_awarded_to_group == 'NO_AWARDED_TO_GROUP' ? 'selected="selected"' : null ?> value="NO_AWARDED_TO_GROUP">No</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2.3) Nome e indirizzo del contraente</b></label>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<?
				$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTOR][ADDRESS_CONTRACTOR]';
				$excluded_input = array('NATIONALID', 'CONTACT_POINT', 'URL_GENERAL', 'URL_BUYER', 'E_MAIL_1');
				$added_input = array("E_MAIL", "URL");
				$required = FALSE;
				$prefix = "ADDRS5-";
				if(!$ajax) {
					include 'forms/2_0_9/common/ADDR-S1.php';
				} else {
					include '../common/ADDR-S1.php';
				}
			?>
		</td>
	</tr>
	<tr>
		<td colspan="3">Il contraente &egrave; una PMI?</td>
		<td>
			<?
			$radio_as_select_for_is_an_sme = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"]["radio_as_select_for_is_an_sme"])) {
				$radio_as_select_for_is_an_sme = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"]["radio_as_select_for_is_an_sme"];
			}
			?>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][CONTRACTOR][radio_as_select_for_is_an_sme]" rel="S;1;0;A" title="Contraente PMI">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_is_an_sme == 'SME' ? 'selected="selected"' : null ?> value="SME">Si</option>
				<option <?= $radio_as_select_for_is_an_sme == 'NO_SME' ? 'selected="selected"' : null ?> value="NO_SME">No</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2.4) Informazione sul valore del contratto d&#39;appalto /lotto <i>(IVA esclusa)</i></b></label>
		</td>
	</tr>
	<tr><td class="etichetta" colspan="4"><i>(in caso di accordi quadro o sistema dinamico di acquisizione – valore massimo totale stimato per l&#39;intera durata di questo lotto)</i></td></tr>
	<tr>
		<td colspan="2">
			Valore totale del contratto d'appalto/del lotto:
		</td>
		<td colspan="2">
			<?
				$valore_totale = "";
				if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_TOTAL"]["val"])) {
					$valore_totale = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_TOTAL"]["val"];
				}
				?>
			<input type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_TOTAL][ATTRIBUTE][CURRENCY]" value="EUR">
			<input type="text" title="Valore Totale" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_TOTAL][val]"  value="<?= $valore_totale ?>" id="valore_totale_del_contaratto_item_<?= $item ?>_duration" rel="S;1;0;A">
		</td>
	</tr>
	</tr>
</table>