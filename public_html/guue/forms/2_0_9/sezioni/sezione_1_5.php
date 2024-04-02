<h3><b>I.5) Principali settori di attivita&agrave;</b></h3>
<table class="bordered">
	<tr class="noBorder">
		<td colspan="2">
			<script>
				var ca_activity_option = {
					'ALTRO_TIPO_ITEM_TO_IGNORE' : [
						'enable_field',
						'',
						[],
						'inptut_ca_activity_other'
					]
				};
			</script>
			<select title="Principali settori di attivita&agrave;" name="guue[CONTRACTING_BODY][CA_ACTIVITY][ATTRIBUTE][VALUE][radio_as_select_for_ca_activity]" rel="<?= isRequired("radio_as_select_for_ca_activity") ?>;1;0;A"  onchange="add_extra_info($(this).val(), ca_activity_option)">
				<option value="">Seleziona..</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "DEFENCE") ? 'selected="selected"' : null ?> value="DEFENCE">Difesa</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "ECONOMIC_AND_FINANCIAL_AFFAIRS") ? 'selected="selected"' : null ?> value="ECONOMIC_AND_FINANCIAL_AFFAIRS">Affari economici e finanziari</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "EDUCATION") ? 'selected="selected"' : null ?> value="EDUCATION">Istruzione</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "ENVIRONMENT") ? 'selected="selected"' : null ?> value="ENVIRONMENT">Ambiente</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "GENERAL_PUBLIC_SERVICES") ? 'selected="selected"' : null ?> value="GENERAL_PUBLIC_SERVICES">Servizi generali delle amministrazioni pubbliche</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "HEALTH") ? 'selected="selected"' : null ?> value="HEALTH">Salute</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "HOUSING_AND_COMMUNITY_AMENITIES") ? 'selected="selected"' : null ?> value="HOUSING_AND_COMMUNITY_AMENITIES">Edilizia abitativa e strutture per le collettivita&agrave;</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "PUBLIC_ORDER_AND_SAFETY") ? 'selected="selected"' : null ?> value="PUBLIC_ORDER_AND_SAFETY">Ordine pubblico e sicurezza</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "RECREATION_CULTURE_AND_RELIGION") ? 'selected="selected"' : null ?> value="RECREATION_CULTURE_AND_RELIGION">Servizi ricreativi, cultura e religione</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "SOCIAL_PROTECTION") ? 'selected="selected"' : null ?> value="SOCIAL_PROTECTION">Protezione sociale</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "ALTRO_TIPO_ITEM_TO_IGNORE") ? 'selected="selected"' : null ?> value="ALTRO_TIPO_ITEM_TO_IGNORE">Altre attivit&agrave;</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			<label>Altre attivit&agrave;:</label>
		</td>
		<td width="90%" style="padding: 2px;">
			<input type="text" id="inptut_ca_activity_other" name="guue[CONTRACTING_BODY][CA_ACTIVITY_OTHER]" <?= (empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) || $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] != "ALTRO_TIPO_ITEM_TO_IGNORE") ? 'disabled="disabled"' : null ?> <?= (!empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"]) && $guue["CONTRACTING_BODY"]["CA_ACTIVITY"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_ca_activity"] == "ALTRO_TIPO_ITEM_TO_IGNORE" && !empty($guue["CONTRACTING_BODY"]["CA_ACTIVITY_OTHER"])) ? 'value="'.$guue["CONTRACTING_BODY"]["CA_ACTIVITY_OTHER"].'"' : null ?> rel="S;1;200;A" title="Altre Attivit&agrave;">
		</td>
	</tr>
</table>