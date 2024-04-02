<h3><b>I.6) Principali settori di attività</b></h3>
<table class="bordered">
	<tr class="noBorder">
		<td colspan="2">
			<script>
				var main_activity_option = {
					'ALTRO_TIPO_ITEM_TO_IGNORE' : [
						'enable_field',
						'',
						[],
						'inptut_main_activity_other'
					]
				};
			</script>
			<select name="guue[CONTRACTING_BODY][CE_TYPE][ATTRIBUTE][VALUE][radio_as_select_for_main_activity]" rel="<?= isRequired("radio_as_select_for_main_activity") ?>;1;0;A"  onchange="add_extra_info($(this).val(), main_activity_option)">
				<option value="">Seleziona..</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "DEFENCE") ? 'selected="selected"' : null ?> value="DEFENCE">Attivit&agrave; aeroportuali</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "ELECTRICITY") ? 'selected="selected"' : null ?> value="ELECTRICITY">Elettricit&agrave;</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "EXPLORATION_EXTRACTION_COAL_OTHER_SOLID_FUEL") ? 'selected="selected"' : null ?> value="EXPLORATION_EXTRACTION_COAL_OTHER_SOLID_FUEL">Esplorazione ed estrazione di carbone e altri combustibili solidi</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "EXPLORATION_EXTRACTION_GAS_OIL") ? 'selected="selected"' : null ?> value="EXPLORATION_EXTRACTION_GAS_OIL">Estrazione di gas e petrolio</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "PORT_RELATED_ACTIVITIES") ? 'selected="selected"' : null ?> value="PORT_RELATED_ACTIVITIES">Attivit&agrave; portuali</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "POSTAL_SERVICES") ? 'selected="selected"' : null ?> value="POSTAL_SERVICES">Servizi postali</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "PRODUCTION_TRANSPORT_DISTRIBUTION_GAS_HEAT") ? 'selected="selected"' : null ?> value="PRODUCTION_TRANSPORT_DISTRIBUTION_GAS_HEAT">Produzione, trasporto e distribuzione di gas e calore</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "RAILWAY_SERVICES") ? 'selected="selected"' : null ?> value="RAILWAY_SERVICES">Servizi ferroviari</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "URBAN_RAILWAY_TRAMWAY_TROLLEYBUS_BUS_SERVICES") ? 'selected="selected"' : null ?> value="URBAN_RAILWAY_TRAMWAY_TROLLEYBUS_BUS_SERVICES">Servizi di ferrovia urbana, tram, filobus o bus</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "WATER") ? 'selected="selected"' : null ?> value="WATER">Acqua</option>
				<option <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "ALTRO_TIPO_ITEM_TO_IGNORE") ? 'selected="selected"' : null ?> value="ALTRO_TIPO_ITEM_TO_IGNORE">Altre attivit&agrave;</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			<label>Altre attivit&agrave;:</label>
		</td>
		<td width="90%" style="padding: 2px;">
			<input type="text" id="inptut_main_activity_other" name="guue[CONTRACTING_BODY][CE_TYPE_OTHER]" <?= (empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) || $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] != "ALTRO_TIPO_ITEM_TO_IGNORE") ? 'disabled="disabled"' : null ?> <?= (!empty($guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"]) && $guue["CONTRACTING_BODY"]["CE_TYPE"]["ATTRIBUTE"]["VALUE"]["radio_as_select_for_main_activity"] == "ALTRO_TIPO_ITEM_TO_IGNORE" && !empty($guue["CONTRACTING_BODY"]["CE_TYPE_OTHER"])) ? 'value="'.$guue["CONTRACTING_BODY"]["CE_TYPE_OTHER"].'"' : null ?> rel="S;1;200;A" title="Altro tipo di attivit&agrave;">
		</td>
	</tr>
</table>