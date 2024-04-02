<h3><b>I.3) Comunicazione</b></h3>
<table class="bordered">
	<tr>
		<td colspan="2" class="etichetta">
			<label>Se disponibile indicare la tipoligia di accesso ai documenti di gara:</label>
		</td>
	</tr>
	<tr>
		<td>
			<script>
				var url_opt_option = {
					'DOCUMENT_FULL' : [
						'enable_field',
						'',
						[],
						'url_opt_url_document'
					],
					'DOCUMENT_RESTRICTED' : [
						'enable_field',
						'',
						[],
						'url_opt_url_document'
					]
				};
			</script>
			<select name="guue[CONTRACTING_BODY][radio_as_select_for_document_url_opt]" title="Accesso ai documenti di gara" rel="<?= isRequired("radio_as_select_for_document_url_opt") ?>;1;0;A" onchange="add_extra_info($(this).val(), url_opt_option)">
				<option value="">Seleziona..</option>
				<option <?= !empty($guue["CONTRACTING_BODY"]["radio_as_select_for_document_url_opt"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_document_url_opt"] == "DOCUMENT_FULL" ? 'selected="selected"' : null ?> value="DOCUMENT_FULL">I documenti di gara sono disponibili per un accesso gratuito, illimitato e diretto presso</option>
				<option <?= !empty($guue["CONTRACTING_BODY"]["radio_as_select_for_document_url_opt"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_document_url_opt"] == "DOCUMENT_RESTRICTED" ? 'selected="selected"' : null ?> value="DOCUMENT_RESTRICTED">L&#39;accesso ai documenti di gara &egrave; limitato. Ulteriori informazioni sono disponibili presso</option>
			</select>
		</td>
		<td width="50%">
			<input type="text" title="URL" id="url_opt_url_document" <?= (!empty($guue["CONTRACTING_BODY"]["URL_DOCUMENT"]) && !empty($guue["CONTRACTING_BODY"]["radio_as_select_for_document_url_opt"])) ? 'value="'.$guue["CONTRACTING_BODY"]["URL_DOCUMENT"].'"' : null ?> name="guue[CONTRACTING_BODY][URL_DOCUMENT]" rel="<?= isRequired("URL_DOCUMENT") ?>;1;0;L" <?= empty($guue["CONTRACTING_BODY"]["radio_as_select_for_document_url_opt"]) ? 'disabled="disabled"' : null ?>>
		</td>
	</tr>
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
			include 'forms/2_0_9_S2/common/ADDR-S1.php';
		}
		?></td>
	</tr>
	<tr><td colspan="2" class="etichetta"><label>Le offerte o le domande di partecipazione vanno inviate:</label></td></tr>
	<tr>
		<td colspan="2">
			<script>
				var tenders_request_option = {
					'URL_PARTICIPATION_ITEM_TO_IGNORE' : [
						'ajax_load',
						'type_of_communication',
						'URL_PARTICIPATION',
						'type_of_communication'
					],
					'ADDRESS_PARTICIPATION_ITEM_TO_IGNORE' : [
						'ajax_load',
						'type_of_communication',
						'ADDRESS_PARTICIPATION',
						'type_of_communication'
					],
					'URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_IDEM_ITEM_TO_IGNORE' : [
						'ajax_load',
						'type_of_communication',
						'URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_IDEM_ITEM_TO_IGNORE',
						'type_of_communication'
					],
					'URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_ITEM_TO_IGNORE' : [
						'ajax_load',
						'type_of_communication',
						'URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_ITEM_TO_IGNORE',
						'type_of_communication'
					]
				};
			</script>
			<select id="tenders_request" title="Invio delle offerte o domande di partecipazione" name="guue[CONTRACTING_BODY][radio_as_select_for_tenders_request]" rel="<?= isRequired("radio_as_select_for_tenders_request") ?>;1;0;A" onchange="add_extra_info($(this).val(), tenders_request_option)">
				<option value="">Seleziona..</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"] == "URL_PARTICIPATION_ITEM_TO_IGNORE") ? 'selected="selected"' : null ?>value="URL_PARTICIPATION_ITEM_TO_IGNORE">In versione elettronica: (Indicare URL)</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"] == "ADDRESS_PARTICIPATION_IDEM") ? 'selected="selected"' : null ?>value="ADDRESS_PARTICIPATION_IDEM">All&#39;indirizzo sopraindicato</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"] == "ADDRESS_PARTICIPATION_ITEM_TO_IGNORE") ? 'selected="selected"' : null ?>value="ADDRESS_PARTICIPATION_ITEM_TO_IGNORE">Altro Indirizzo</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"] == "URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_IDEM_ITEM_TO_IGNORE") ? 'selected="selected"' : null ?>value="URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_IDEM_ITEM_TO_IGNORE">In versione elettronica (Indicare URL) e all&#39;indirizzo sopraindicato</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"]) && $guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"] == "URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_ITEM_TO_IGNORE") ? 'selected="selected"' : null ?>value="URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_ITEM_TO_IGNORE">In versione elettronica (Indicare URL) e un altro indirizzo (specificare)</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" id="type_of_communication"><?
		if(!empty($guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"])) {
			switch ($guue["CONTRACTING_BODY"]["radio_as_select_for_tenders_request"]) {
				case "URL_PARTICIPATION_ITEM_TO_IGNORE":
					$chiavi = "URL_PARTICIPATION";
					include 'forms/2_0_9_S2/common/type_of_communication.php';
					break;
				case "ADDRESS_PARTICIPATION_IDEM":
					break;
				case "ADDRESS_PARTICIPATION_ITEM_TO_IGNORE":
					$chiavi = "ADDRESS_PARTICIPATION";
					include 'forms/2_0_9_S2/common/type_of_communication.php';
					break;
				case "URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_IDEM_ITEM_TO_IGNORE":
					$chiavi = "URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_IDEM_ITEM_TO_IGNORE";
					include 'forms/2_0_9_S2/common/type_of_communication.php';
					break;
				case "URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_ITEM_TO_IGNORE":
					$chiavi = "URL_PARTICIPATION_AND_ADDRESS_PARTICIPATION_ITEM_TO_IGNORE";
					include 'forms/2_0_9_S2/common/type_of_communication.php';
					break;
				default:
					break;
			}
		}
		?></td>
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
