<?
@session_start();
$href = "forms/".(!empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : '2_0_9')."/common/ADDR-S1.php"
?>
<h3><b>I.1) Denominazione e indirizzi</b> <i>(di tutte le amministrazioni aggiudicatrici responsabili della procedura)</i></h3>
<table class="bordered">
	<tbody>
		<tr>
			<td>
				<?
				$prefix = "ADDRS1-";
				$keys = "[CONTRACTING_BODY][ADDRESS_CONTRACTING_BODY]";
				include 'forms/2_0_9/common/ADDR-S1.php';
				?>
			</td>
		</tr>
		<tr>
			<td id="address_contracting_body_additional"><?
			if(!empty($guue["CONTRACTING_BODY"]["ADDRESS_CONTRACTING_BODY_ADDITIONAL"])) {
				$address_item = 0;
				foreach ($guue["CONTRACTING_BODY"]["ADDRESS_CONTRACTING_BODY_ADDITIONAL"] as $gl) {
					$address_item++;
					$prefix = "ADDRS1-";
					$keys = "[CONTRACTING_BODY][ADDRESS_CONTRACTING_BODY_ADDITIONAL][ITEM_".$address_item."]";
					include 'forms/2_0_9/common/ADDR-S1.php';
				}
			}
			?></td>
		</tr>
		<tr>
			<td>
				<script type="text/javascript">
					var address_contracting_body_additional_num = 1;
				</script>
				<button type="button" class="aggiungi" onclick="lot++;aggiungi('<?= $href ?>','#address_contracting_body_additional', {chiavi:['CONTRACTING_BODY','ADDRESS_CONTRACTING_BODY_ADDITIONAL', 'ITEM_' + address_contracting_body_additional_num], item: address_contracting_body_additional_num});address_contracting_body_additional_num++;return false;" ><img src="/img/add.png" alt="Aggiungi lotto">Aggiungi Informazioni di Contatto Supplementari</button>
			</td>
		</tr>
	</tbody>
</table>