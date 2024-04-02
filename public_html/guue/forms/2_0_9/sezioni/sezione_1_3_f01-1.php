<h3><b>I.3) Comunicazione</b></h3>
<table class="bordered">
	<tr>
		<td colspan="2" class="etichetta">
			<label>Se disponibili ulteriori informazioni indicare le modalit&agrave; di accesso:</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<script>
				var information_option = {
					'ADDRESS_FURTHER_INFO_ITEM_TO_IGNORE' : [
						'ajax_load',
						'ADDR-S1',
						['CONTRACTING_BODY','ADDRESS_FURTHER_INFO'],
						'ADDRESS_FURTHER_INFO_ITEM_TO_IGNORE'
					]
				};
			</script>
			<select id="information" title="Ulteriori informazioni per le modalit&agrave; di accesso" rel="<?= isRequired("radio_as_select_for_information") ?>;1;0;A" name="guue[CONTRACTING_BODY][radio_as_select_for_information]" onchange="add_extra_info($(this).val(), information_option)">
				<option value="">Seleziona..</option>
				<option <?= !empty($guue["CONTRACTING_BODY"]["radio_as_select_for_information"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_information"] == "ADDRESS_FURTHER_INFO_IDEM" ? 'selected="selected"' : null ?> value="ADDRESS_FURTHER_INFO_IDEM">Indirizzo sopraindicato</option>
				<option <?= !empty($guue["CONTRACTING_BODY"]["radio_as_select_for_information"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_information"] == "ADDRESS_FURTHER_INFO_ITEM_TO_IGNORE" ? 'selected="selected"' : null ?> value="ADDRESS_FURTHER_INFO_ITEM_TO_IGNORE">Altro Indirizzo</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" id="ADDRESS_FURTHER_INFO_ITEM_TO_IGNORE"><?
		if(!empty($guue["CONTRACTING_BODY"]["radio_as_select_for_information"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_information"] == "ADDRESS_FURTHER_INFO_ITEM_TO_IGNORE") {
			$keys = "[CONTRACTING_BODY][ADDRESS_FURTHER_INFO]";
			include 'forms/2_0_9/common/ADDR-S1.php';
		}
		?></td>
	</tr>
	</tr>
	<tr>
		<td class="etichetta" colspan="2">
			<label>
			<input type="checkbox" onchange="toggle_field($(this), '#contracting_body_url_tool')" <?= !empty($guue["CONTRACTING_BODY"]["URL_TOOL"]) ? 'checked="checked"' : null ?>>
				 La comunicazione elettronica richiede l&#39;utilizzo di strumenti e dispositivi che in genere non sono disponibili.
				 Questi strumenti e dispositivi sono disponibili per un accesso gratuito, illimitato e diretto presso: 
			</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="text" <?= !empty($guue["CONTRACTING_BODY"]["URL_TOOL"]) ?  null : 'disabled="disabled"' ?> name="guue[CONTRACTING_BODY][URL_TOOL]" <?= !empty($guue["CONTRACTING_BODY"]["URL_TOOL"]) ? 'value="'.$guue["CONTRACTING_BODY"]["URL_TOOL"].'"' : null ?> id="contracting_body_url_tool" title="URL strumenti per la comunicazione elettronica" rel="S;0;200;L">
		</td>
	</tr>
</table>
