<tr>
	<td  colspan="4" class="etichetta">
		<label>IV.1.8) Informazioni relative all&#39;accordo sugli appalti pubblici (AAP)</label>
	</td>
</tr>
<tr>
	<td colspan="2" >
		L&#39;appalto &egrave; disciplinato dall&#39;accordo sugli appalti pubblici?
	</td>
	<td colspan="2" >
		<?
		$contract_covered_gpa = $no_contract_covered_gpa = FALSE;
		if(!empty($guue["PROCEDURE"]["radio_as_select_for_public_agreement"])) {
			$contract_covered_gpa = $guue["PROCEDURE"]["radio_as_select_for_public_agreement"] == "CONTRACT_COVERED_GPA" ? TRUE : FALSE;
			$no_contract_covered_gpa = $guue["PROCEDURE"]["radio_as_select_for_public_agreement"] == "NO_CONTRACT_COVERED_GPA" ? TRUE : FALSE;
		}
		?>
		<select name="guue[PROCEDURE][radio_as_select_for_public_agreement]" rel="<?= isRequired("radio_as_select_for_public_agreement") ?>;1;0;A"  title="Appalti Pubblici">
			<option value="">Seleziona..</option>
			<option <?= $contract_covered_gpa ? 'selected="selected"' : null ?> value="CONTRACT_COVERED_GPA">Si</option>
			<option <?= $no_contract_covered_gpa ? 'selected="selected"' : null ?> value="NO_CONTRACT_COVERED_GPA">No</option>
		</select>
	</td>
</tr>