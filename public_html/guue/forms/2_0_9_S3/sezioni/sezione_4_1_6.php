<tr>
	<td colspan="4" class="etichetta">
		<label>IV.1.6) Informazioni sull&#39;asta elettronica</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<label><input type="checkbox" name="guue[PROCEDURE][EAUCTION_USED]" <?= !empty($guue["PROCEDURE"]["EAUCTION_USED"]) ? 'checked="checked"' : null ?> onchange="toggle_field($(this),'#info_add_eauction')"> Ricorso ad un&#39;asta elettronica</label>
	</td>
</tr>
<tr>
	<td class="etichetta"  colspan="4">
		<label>Ulteriori informazioni sull&#39;asta elettronica:</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea <?= empty($guue["PROCEDURE"]["EAUCTION_USED"]) ? 'disabled="disabled""' : null ?> name="guue[PROCEDURE][INFO_ADD_EAUCTION]" rel="S;1;400;A" id="info_add_eauction" title="Ulteriori informazioni" class="ckeditor_simple"><?= (!empty($guue["PROCEDURE"]["EAUCTION_USED"]) && !empty($guue["PROCEDURE"]["INFO_ADD_EAUCTION"])) ? $guue["PROCEDURE"]["INFO_ADD_EAUCTION"] : null ?></textarea>
	</td>
</tr>