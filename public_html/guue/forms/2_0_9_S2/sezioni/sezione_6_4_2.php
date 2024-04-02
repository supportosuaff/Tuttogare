<tr>
	<td class="etichetta" colspan="6">
		<label>VI.4.2) Organismo responsabile delle procedure di mediazione</label>
	</td>
</tr>
<tr>
	<td colspan="6">
		<?
			$keys = '[COMPLEMENTARY_INFO][ADDRESS_MEDIATION_BODY]';
			$excluded_input = array('NATIONALID', 'NUTS', 'CONTACT_POINT', 'URL_GENERAL', 'URL_BUYER');
			$added_input = array("URL");
			$required = FALSE;
			$prefix = "ADDRS6-";
			include 'forms/2_0_9_S2/common/ADDR-S1.php';
		?>
	</td>
</tr>