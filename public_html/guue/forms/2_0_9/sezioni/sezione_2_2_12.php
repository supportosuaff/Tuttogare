<tr>
	<td class="etichetta" colspan="4"><label>II.2.12) Informazioni relative ai cataloghi elettronici</label></td>
</tr>
<tr>
	<td colspan="4">
		<label>
			<?
			$ecatalogue_required = FALSE;
			if(!empty($guue["OBJECT_CONTRACT"]["OBJECT_DESCR"]["ITEM_".$item]["ECATALOGUE_REQUIRED"])) {
				$ecatalogue_required = TRUE;
			}
			?>
			<input type="checkbox" <?= $ecatalogue_required ? 'checked="checked"' : null ?> name="guue[OBJECT_CONTRACT][OBJECT_DESCR][ITEM_<?= $item ?>][ECATALOGUE_REQUIRED]" title="Le offerte devono essere presentate in forma di cataloghi elettronici">
			Le offerte devono essere presentate in forma di cataloghi elettronici o includere un catalogo elettronico
		</label>
	</td>
</tr>