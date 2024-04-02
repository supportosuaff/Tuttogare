<tr>
	<td class="etichetta" colspan="4">
		<label>II.2.14) Informazioni complementari:</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea class="ckeditor_simple" rel="N;0;400;A" title="Informazioni Complementari" name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][INFO_ADD]">
		<?= !empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["INFO_ADD"]) ? $guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["INFO_ADD"] : null ?>
		</textarea>
	</td>
</tr>
<?
	if(!empty($root)) {
		include $root . '/guue/forms/'.$_SESSION["guue"]["v_form"].'/common/delete_lot.php';
	} else {
		include '../common/delete_lot.php';
	}
?>