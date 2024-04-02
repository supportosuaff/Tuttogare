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
				<option <?= !empty($type) && $type == "AC_QUALITY" ? 'selected="selected"' : null ?> value="AC_QUALITY_ITEM_TO_IGNORE">Qualit&agrave;</option>
				<option <?= !empty($type) && $type == "AC_COST" ? 'selected="selected"' : null ?> value="AC_COST_ITEM_TO_IGNORE">Costo</option>
				<option <?= !empty($type) && $type == "AC_PRICE" ? 'selected="selected"' : null ?> value="AC_PRICE_ITEM_TO_IGNORE">Prezzo</option>
			</select>
		</td>
		<td id="<?= $id ?>_input_criterio">
		<?
			if(!empty($type) && $type == "AC_QUALITY") {
				include "criterio_di_qualita_2_2_5.php";
			} elseif (!empty($type) && $type == "AC_COST") {
				include "criterio_costo_2_2_5.php";
			} elseif (!empty($type) && $type == "AC_PRICE") {
				include "criterio_prezzo_2_2_5.php";
			}
		?>
		</td>
		<td style="width: 20px;">
			<input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','guue/forms/2_0_9/common/criterio');return false;">
		</td>
	</tr>
</table>