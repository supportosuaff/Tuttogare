<tr>
	<td class="etichetta" colspan="4">
		<label>IV.1.3) Informazioni su un accordo quadro o un sistema dinamico di acquisizione</label>
	</td>
</tr>
<tr>
	<td colspan="4">
		<label><input type="checkbox" onchange="toggle_field($(this),'#justification_checkbox')" name="guue[PROCEDURE][FRAMEWORK]" <?= !empty($guue["PROCEDURE"]["FRAMEWORK"]) ? 'checked="checked"' : null ?>> L&#39;avviso comporta la conclusione di un accordo quadro</label>
	</td>
</tr>
<tr>
	<td>
		<label><input type="checkbox" <?= !empty($guue["PROCEDURE"]["FRAMEWORK"]["JUSTIFICATION"]) ? 'checked="checked"' : null ?> <?= empty($guue["PROCEDURE"]["FRAMEWORK"]) ? 'disabled="disabled"' : null ?> onchange="toggle_field($(this),'#justification')" id="justification_checkbox" rel="">Si tratta di un accordo quadro per una durata superiore a 4 anni. <i>(Giustificazione:)</i></label>
	</td>
	<td colspan="3">
		<input type="text" id="justification" name="guue[PROCEDURE][FRAMEWORK][JUSTIFICATION]" <?= empty($guue["PROCEDURE"]["FRAMEWORK"]["JUSTIFICATION"]) ? 'disabled="disabled"' : null ?> rel="S;1;400;A" title="Giustificazione" value="<?= !empty($guue["PROCEDURE"]["FRAMEWORK"]["JUSTIFICATION"]) ? $guue["PROCEDURE"]["FRAMEWORK"]["JUSTIFICATION"] : null ?>">
	</td>
</tr>