<tr>
	<td colspan="4" class="etichetta">
		<label>II.2.2) Codici CPV supplementari</label>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Codice CPV principale:</label>
	</td>
	<td colspan="3">
		<? 
		if(!empty($guue["supplementary_cpv"])) {
			$sql = "SELECT b_cpv.* FROM b_cpv WHERE codice IN (".implode(",", array_filter(explode(";",$guue["supplementary_cpv"]))).")";
			$risultato_cpv = $pdo->bindAndExec($sql);
		}
		?>
		<a class="link_to_cpv_table" <?= isset($risultato_cpv) && $risultato_cpv->rowCount() > 0 ? 'style="display:none;"' : null ?> href="#cpv_table">Selezionare il principale dalla tabella delle Categorie Merceologiche</a>
		<select name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][CPV_ADDITIONAL][CPV_CODE][ATTRIBUTE][CODE]" class="cpv_selection_element" <?= isset($risultato_cpv) && $risultato_cpv->rowCount() > 0 ? 'style="display:inline-block;"' : null ?> title="Codice CPV" rel="<?= isRequired("OBJECT_CONTRACT-CPV_CODE") ?>;1;0;A">
			<?
			$selected_cpv_code_additional_cpv_code = "";
			if (!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["CPV_ADDITIONAL"]["CPV_CODE"]["ATTRIBUTE"]["CODE"])) {
				$selected_cpv_code_additional_cpv_code = $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["CPV_ADDITIONAL"]["CPV_CODE"]["ATTRIBUTE"]["CODE"];
			}
			if(isset($risultato_cpv) && $risultato_cpv->rowCount() > 0) {
				?><option value="">Seleziona..</option><?
				while ($rec = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
					?><option <?= $selected_cpv_code_additional_cpv_code === $rec["codice"] ? 'selected="selected"' : null  ?> value="<?= $rec["codice"] ?>"><?= $rec["descrizione"] ?></option><?
				}
			}
			?>
		</select>
	</td>
</tr>