<tr>
	<td class="etichetta">
		II.1.1) Denominazione:
	</td>
	<td>
		<input type="text" name="guue[OBJECT_CONTRACT][TITLE]" value="<?= !empty($guue["OBJECT_CONTRACT"]["TITLE"]) ? $guue["OBJECT_CONTRACT"]["TITLE"] : null ?>" title="Denominazione" class="espandi" rel="<?= isRequired("OBJECT_CONTRACT-TITLE-SECTION-II-1") ?>;1;200;A">
	</td>
	<td class="etichetta">
		Numero di riferimento:
	</td>
	<td>
		<input type="text" name="guue[OBJECT_CONTRACT][REFERENCE_NUMBER]" value="<?= !empty($guue["OBJECT_CONTRACT"]["REFERENCE_NUMBER"]) ? $guue["OBJECT_CONTRACT"]["REFERENCE_NUMBER"] : null ?>" title="Numero di riferimento" rel="<?= isRequired("OBJECT_CONTRACT-REFERENCE_NUMBER") ?>;1;100;A" class="espandi">
	</td>
</tr>