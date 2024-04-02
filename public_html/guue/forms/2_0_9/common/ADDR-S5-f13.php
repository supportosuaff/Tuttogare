<?
	if(!empty($_POST["param"]["chiavi"])) {
		$contractor = $_POST["param"]["item"];
		$address_item = $_POST["param"]["item"];
		$item = $address_item;
		if(!empty($_POST["param"]["contractor_item"])) {
			$item = $_POST["param"]["contractor_item"];
		}
	}
	$excluded_input = array('NATIONALID', 'CONTACT_POINT', 'URL_GENERAL', 'URL_BUYER', '');
	$added_input = array("URL_AFTER_NUTS", "");
	$required = FALSE;
	$prefix = "ADDRS6-";
	$do_not_close_table = TRUE;
	include 'ADDR-S1.php';
	?>
</table>
<?
unset($do_not_close_table);
?>