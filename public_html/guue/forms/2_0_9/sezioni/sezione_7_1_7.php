<tr>
	<td class="etichetta" colspan="4">
		<label><b>VII.1.7) Denominazione e indirizzo del contraente/concessionario</b></label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<?
			$keys = '[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][CONTRACTOR][ADDRESS_CONTRACTOR]';
			$excluded_input = array('NATIONALID', 'CONTACT_POINT', 'URL_GENERAL', 'URL_BUYER', 'E_MAIL_1');
			$added_input = array("E_MAIL", "URL");
			$required = FALSE;
			$prefix = "ADDRS5-";
			include 'forms/2_0_9/common/ADDR-S1.php';
		?>
	</td>
</tr>
<tr>
	<td colspan="3">Il contraente &egrave; una PMI?</td>
	<td>
		<?
		$radio_as_select_for_is_a_sme = "";
		if(!empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["CONTRACTOR"]["radio_as_select_for_is_a_sme"])) {
			$radio_as_select_for_is_a_sme = $guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["CONTRACTOR"]["radio_as_select_for_is_a_sme"];
		}
		?>
		<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][CONTRACTOR][radio_as_select_for_is_a_sme]" rel="S;1;0;A" title="Contraente PMI">
			<option value="">Seleziona..</option>
			<option <?= $radio_as_select_for_is_a_sme == 'SME' ? 'selected="selected"' : null ?> value="SME">Si</option>
			<option <?= $radio_as_select_for_is_a_sme == 'NO_SME' ? 'selected="selected"' : null ?> value="NO_SME">No</option>
		</select>
	</td>
</tr>