<tr>
	<td colspan="4" class="etichetta">
		<label>IV.3.5) Nomi dei membri della commissione giudicatrice selezionati: </label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<? $get_member_name_numb = 0; ?>
		<table class="bordered">
			<tbody id="member_name"><?
			if(!empty($guue['PROCEDURE']['MEMBER_NAME'])) {
				$item_member_name = 1;
				foreach ($guue['PROCEDURE']['MEMBER_NAME'] as $item_member_name_value) {
					include $root.'/guue/get_member_name.php';
					$get_member_name_numb++;
				}
			}
			?></tbody>
		</table>
	</td>
</tr>
<tr>
	<td colspan="4">
		<script type="text/javascript">
			var get_member_name = <?= $get_member_name_numb ?>;
		</script>
		<button type="button" class="aggiungi" onclick="get_member_name++;aggiungi('get_member_name.php','#member_name', {item:get_member_name});return false;" ><img src="/img/add.png" alt="Aggiungi lotto">Aggiungi Nome Membro della Commissione</button>
	</td>
</tr>