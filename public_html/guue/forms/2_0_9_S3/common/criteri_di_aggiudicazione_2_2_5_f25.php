<?
	if (!empty($_POST["id"])) {
		@session_start();
		$id = $_POST["id"];
		$item = $_POST["param"]["item"];
		include 'criteri_di_aggiudicazione_ac_criterion.php';
	} else {
		$i = 0;
		$id = "i_".$i;
		if(!empty($_POST["data"]["item"])) $item = $_POST["data"]["item"];
		if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["AC"]["AC_CRITERION"]) && is_array($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["AC"]["AC_CRITERION"])) {
			foreach ($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["AC"]["AC_CRITERION"] as $criterio) {
				include 'criteri_di_aggiudicazione_ac_criterion.php';
				$i++;
				$id = "i_".$i;
			}
		}
		$i++;
		?>
		<script type="text/javascript">
			var id_inserimento = <?= $i; ?>;
		</script>
		<?
	}
/*
?>
<table class="bordered" id="criterio_<?= $id ?>">
	<tr>
		<td class="etichetta" style="width: 20px;">
			<label style="font-size: 1em;">Tipologia:</label>
		</td>
		<td style="width: 50px;">
			<script>
				var <?= $id ?>_criterio_option = {
					'AC_QUALITY_ITEM_TO_IGNORE' : [
						'ajax_load',
						'criterio_di_qualita_2_2_5',
						['ITEM_<?= $item ?>'],
						'<?= $id ?>_input_criterio',
						{id:'<?= $id ?>'}
					],
					'AC_COST_ITEM_TO_IGNORE' : [
						'ajax_load',
						'criterio_costo_2_2_5',
						['ITEM_<?= $item ?>'],
						'<?= $id ?>_input_criterio',
						{id:'<?= $id ?>'}
					],
					'AC_PRICE_ITEM_TO_IGNORE' : [
						'ajax_load',
						'criterio_prezzo_2_2_5',
						['ITEM_<?= $item ?>'],
						'<?= $id ?>_input_criterio',
						{id:'<?= $id ?>'}
					]
				};
			</script>
			<select rel="N;1;0;A" title="Criteri di aggiudicazione" onchange="add_extra_info($(this).val(), <?= $id ?>_criterio_option)">
				<option value="">Seleziona..</option>
				<option value="AC_QUALITY_ITEM_TO_IGNORE">Qualit&agrave;</option>
				<option value="AC_COST_ITEM_TO_IGNORE">Costo</option>
				<option value="AC_PRICE_ITEM_TO_IGNORE">Prezzo</option>
			</select>
		</td>
		<td id="<?= $id ?>_input_criterio"></td>
		<td style="width: 20px;">
			<input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','guue/forms/2_0_9_S3/common/criterio');return false;">
		</td>
	</tr>
</table>
*/
?>