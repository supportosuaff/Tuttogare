<tr>
	<td class="etichetta">
		Avviso originale spedito mediante:
	</td>
	<td colspan="3">
		<script type="text/javascript">
			var radio_as_select_for_original_enotice_sent_via_options = {
				'ORIGINAL_OTHER_MEANS_ITEM_TO_IGNORE' : [
					'enable_field',
					'',
					[],
					['original_other_means_pubblication_no_textarea']
				]
			};
		</script>
		<select name="guue[COMPLEMENTARY_INFO][radio_as_select_for_original_enotice_sent_via]"  title="Accesso ai documenti di gara" rel="<?= isRequired("radio_as_select_for_original_enotice_sent_via") ?>;1;0;A" onchange="add_extra_info($(this).val(), radio_as_select_for_original_enotice_sent_via_options)">
			<option value="">Seleziona..</option>
			<option <?= !empty($guue["COMPLEMENTARY_INFO"]["radio_as_select_for_original_enotice_sent_via"]) && $guue["COMPLEMENTARY_INFO"]["radio_as_select_for_original_enotice_sent_via"] == "ORIGINAL_ENOTICES_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="ORIGINAL_ENOTICES_PUBBLICATION_NO">eNotices</option>
			<option <?= !empty($guue["COMPLEMENTARY_INFO"]["radio_as_select_for_original_enotice_sent_via"]) && $guue["COMPLEMENTARY_INFO"]["radio_as_select_for_original_enotice_sent_via"] == "ORIGINAL_TED_ESENDER_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="ORIGINAL_TED_ESENDER_PUBBLICATION_NO">TED eSender</option>
			<option <?= !empty($guue["COMPLEMENTARY_INFO"]["radio_as_select_for_original_enotice_sent_via"]) && $guue["COMPLEMENTARY_INFO"]["radio_as_select_for_original_enotice_sent_via"] == "ORIGINAL_OTHER_MEANS_ITEM_TO_IGNORE" ? 'selected="selected"' : null ?> value="ORIGINAL_OTHER_MEANS_ITEM_TO_IGNORE">Altri sistemi</option>
		</select>
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Altro Sistema utilizzato:</label>
	</td>
	<td colspan="3" class="etichetta">
		<input type="text" name="guue[COMPLEMENTARY_INFO][ORIGINAL_OTHER_MEANS_PUBBLICATION_NO]" id="original_other_means_pubblication_no_textarea" rel="S;1;200;A" <?= !empty($guue["COMPLEMENTARY_INFO"]["radio_as_select_for_original_enotice_sent_via"]) && $guue["COMPLEMENTARY_INFO"]["radio_as_select_for_original_enotice_sent_via"] == "ORIGINAL_OTHER_MEANS_ITEM_TO_IGNORE" ? null : 'disabled="disabled"' ?> title="Altri sistemi utilizzati per l'invio" value="<?= !empty($guue["COMPLEMENTARY_INFO"]["ORIGINAL_OTHER_MEANS_PUBBLICATION_NO"]) ? $guue["COMPLEMENTARY_INFO"]["ORIGINAL_OTHER_MEANS_PUBBLICATION_NO"] : null ?>">
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Numero di riferimento dell&#39;avviso <i>(anno-numero del documento)</i></label>
	</td>
	<td colspan="3">
		<input type="text" name="guue[COMPLEMENTARY_INFO][NO_DOC_EXT_PUBBLICATION_NO]" rel="N;1;11;A" title="Numero di riferimento" value="<?= !empty($guue["COMPLEMENTARY_INFO"]["NO_DOC_EXT_PUBBLICATION_NO"]) ? $guue["COMPLEMENTARY_INFO"]["NO_DOC_EXT_PUBBLICATION_NO"] : null ?>">
	</td>
</tr>
<tr>
	<td class="etichetta">
		<label>Numero dell&#39;avviso nella GU S:</label>
	</td>
	<td colspan="3">
		<input type="text" name="guue[COMPLEMENTARY_INFO][NOTICE_NUMBER_OJ]" rel="N;1;0;A" title="Numero di riferimento" value="<?= !empty($guue["COMPLEMENTARY_INFO"]["NOTICE_NUMBER_OJ"]) ? $guue["COMPLEMENTARY_INFO"]["NOTICE_NUMBER_OJ"] : null ?>">
	</td>
</tr>
<tr>
	<td style="width: 30%;" class="etichetta">
		<label>Data di spedizione dell&#39;avviso originale</label>
	</td>
	<td colspan="3">
		<input type="text" name="guue[COMPLEMENTARY_INFO][DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO]" class="datepick" rel="N;1;11;A" title="Data di spedizione" value="<?= !empty($guue["COMPLEMENTARY_INFO"]["DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO"]) ? $guue["COMPLEMENTARY_INFO"]["DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO"] : null ?>">
	</td>
</tr>