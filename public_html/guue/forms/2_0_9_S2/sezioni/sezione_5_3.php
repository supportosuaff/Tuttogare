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
			<label><b>V.3) Aggiudicazione e premi</b></label>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4"><label><b>V.3.1) Data di decisione della commissione giudicatrice:</b></label></td>
	</tr>
	<tr>
		<td colspan="4">
			<input type="text" class="datepick" title="Data di decisione della commissione giudicatrice" rel="S;2;0;D" name="guue[RESULTS][ITEM_<?= $item ?>][AWARDED_PRIZE][DATE_DECISION_JURY]" value="<?= !empty($guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["DATE_DECISION_JURY"]) ? $guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["DATE_DECISION_JURY"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.3.2) Informazioni relative ai partecipanti</b></label>
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di partecipanti previsti:</td>
		<td width="150px">
			<input type="text" title="Numero di partecipanti previsti" rel="S;1;0;N" name="guue[RESULTS][ITEM_<?= $item ?>][AWARDED_PRIZE][NB_PARTICIPANTS]" value="<?= !empty($guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["NB_PARTICIPANTS"]) ? $guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["NB_PARTICIPANTS"] : null ?>">
		</td>
		<td class="etichetta">Numero di PMI partecipanti:</td>
		<td width="150px">
			<input type="text" title="Numero di PMI partecipanti" rel="N;1;0;N" name="guue[RESULTS][ITEM_<?= $item ?>][AWARDED_PRIZE][NB_PARTICIPANTS_SME]" value="<?= !empty($guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["NB_PARTICIPANTS_SME"]) ? $guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["NB_PARTICIPANTS_SME"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di partecipanti esteri::</td>
		<td colspan="3">
			<input type="text" title="Numero di partecipanti esteri:" rel="N;1;0;N" name="guue[RESULTS][ITEM_<?= $item ?>][AWARDED_PRIZE][NB_PARTICIPANTS_OTHER_EU]" value="<?= isset($guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["NB_PARTICIPANTS_OTHER_EU"]) ? $guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["NB_PARTICIPANTS_OTHER_EU"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.3.3) Nomi e indirizzi dei vincitori del concorso</b></label>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<?
				$keys = '[RESULTS][ITEM_'.$item.'][AWARDED_PRIZE][WINNER][ADDRESS_WINNER]';
				$excluded_input = array('NATIONALID', 'CONTACT_POINT', 'URL_GENERAL', 'URL_BUYER', 'E_MAIL_1');
				$added_input = array("E_MAIL", "URL");
				$required = FALSE;
				$prefix = "ADDRS5-";
				if(!$ajax) {
					include 'forms/2_0_9_S2/common/ADDR-S5-f13.php';
				} else {
					include '../common/ADDR-S5-f13.php';
				}
			?>
		</td>
	</tr>
	<tr>
		<td colspan="3">Il contraente &egrave; una PMI?</td>
		<td>
			<?
			$radio_as_select_for_is_an_sme = "";
			if(!empty($guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["WINNER"]["radio_as_select_for_is_an_sme"])) {
				$radio_as_select_for_is_an_sme = $guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["WINNER"]["radio_as_select_for_is_an_sme"];
			}
			?>
			<select name="guue[RESULTS][ITEM_<?= $item ?>][AWARDED_PRIZE][WINNER][radio_as_select_for_is_an_sme]" rel="S;1;0;A" title="Contraente PMI">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_is_an_sme == 'SME' ? 'selected="selected"' : null ?> value="SME">Si</option>
				<option <?= $radio_as_select_for_is_an_sme == 'NO_SME' ? 'selected="selected"' : null ?> value="NO_SME">No</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.3.4) Valore dei premi</b></label>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="2"><label>Valore dei premi aggiudicati, IVA esclusa:</label></td>
		<td colspan="2">
			<input type="hidden" name="guue[RESULTS][ITEM_<?= $item ?>][AWARDED_PRIZE][VAL_PRIZE][ATTRIBUTE][CURRENCY]" value="EUR">
			<input type="text" title="Valore Stimato" rel="N;2;0;N" name="guue[RESULTS][ITEM_<?= $item ?>][AWARDED_PRIZE][VAL_PRIZE][val]" value="<?= !empty($guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["VAL_PRIZE"]["val"]) ? $guue["RESULTS"]["ITEM_".$item]["AWARDED_PRIZE"]["VAL_PRIZE"]["val"] : null ?>">
		</td>
	</tr>
</table>