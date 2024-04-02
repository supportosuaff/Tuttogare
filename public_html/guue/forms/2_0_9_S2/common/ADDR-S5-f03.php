<?
	if(!empty($_POST["param"]["chiavi"])) {
		$contractor = $_POST["param"]["item"];
		$address_item = $_POST["param"]["item"];
		$item = $address_item;
		if(!empty($_POST["param"]["contractor_item"])) {
			$item = $_POST["param"]["contractor_item"];
		}
	}
	$excluded_input = array('NATIONALID', 'CONTACT_POINT', 'URL_GENERAL', 'URL_BUYER', 'E_MAIL_1');
	$added_input = array("E_MAIL", "URL");
	$required = FALSE;
	$prefix = "ADDRS5-";
	$do_not_close_table = TRUE;
	include 'ADDR-S1.php';
	?>
	<tr>
		<td colspan="2">Il contraente &egrave; una PMI?</td>
		<td colspan="6">
			<?
			$radio_as_select_for_is_an_sme = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"]["ITEM_".$contractor]["radio_as_select_for_is_an_sme"])) {
				$radio_as_select_for_is_an_sme = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"]["ITEM_".$contractor]["radio_as_select_for_is_an_sme"];
			}
			?>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][CONTRACTOR][ITEM_<?= $contractor ?>][radio_as_select_for_is_an_sme]" rel="S;1;0;A" title="Contraente PMI">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_is_an_sme == 'SME' ? 'selected="selected"' : null ?> value="SME">Si</option>
				<option <?= $radio_as_select_for_is_an_sme == 'NO_SME' ? 'selected="selected"' : null ?> value="NO_SME">No</option>
			</select>
		</td>
	</tr>
</table>
<?
unset($do_not_close_table);
?>