<table class="bordered">
	<tr class="noBorder">
		<td colspan="2">
			<script>
				var ca_type_option = {
					'ALTRO_TIPO_ITEM_TO_IGNORE' : [
						'enable_field',
						'',
						[],
						'inptut_ca_type_other'
					]
				};
			</script>
			<select name="guue[CONTRACTING_BODY][CA_TYPE][ATTRIBUTE][VALUE][radio_as_select_for_ca_type]"  onchange="add_extra_info($(this).val(), ca_type_option)" rel="<?= isRequired('radio_as_select_for_ca_type') ?>;1;0;A">
				<option value="">Seleziona..</option>
				<option value="MINISTRY">Ministero o qualsiasi altra autorit&agrave; nazionale o federale, inclusi gli uffici a livello locale o regionale</option>
				<option value="NATIONAL_AGENCY">Agenzia/ufficio nazionale o federale</option>
				<option value="REGIONAL_AUTHORITY">Autorit&agrave; regionale o locale</option>
				<option value="REGIONAL_AGENCY">Agenzia/ufficio regionale o locale</option>
				<option value="BODY_PUBLIC">Organismo di diritto pubblico</option>
				<option value="EU_INSTITUTION">Istituzione/agenzia europea o organizzazione internazionale</option>
				<option value="ALTRO_TIPO_ITEM_TO_IGNORE">Altro tipo</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			<label>Altro tipo:</label>
		</td>
		<td width="90%" style="padding: 2px;">
			<input type="text" id="inptut_ca_type_other" name="guue[CONTRACTING_BODY][CA_TYPE_OTHER]" disabled="disabled" rel="S;1;200;A" title="Altro tipo di amministrazione aggiudicatrice">
		</td>
	</tr>
</table>