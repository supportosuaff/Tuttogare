<?
	if (!empty($_POST["data"])) {
		session_start();
		$item = $_POST["data"]["item"];
	}
?>
<style type="text/css">
	td table.bigger * {
		font-size: 14px;
	}
</style>
<table class="bordered bigger">
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>Il premio/i premi non sono aggiudicati</b></label>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<?
			$radio_as_select_for_unsuccessful_discontinued = "";
			if(!empty($guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["radio_as_select_for_unsuccessful_discontinued"])) {
				$radio_as_select_for_unsuccessful_discontinued = $guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["radio_as_select_for_unsuccessful_discontinued"];
			}
			?>
			<script type="text/javascript">
				var procurement_discontinued_option_<?= $item ?> = {
									'PROCUREMENT_DISCONTINUED_ITEM_TO_IGNORE' : [
										'enable_field',
										'',
										[],
										['field_information_not_to_be_published_<?= $item ?>', 'date_dispatch_original_pubblication_no_<?= $item ?>']
									]
								};
			</script>
			<select name="guue[RESULTS][ITEM_<?= $item ?>][NO_AWARDED_PRIZE][radio_as_select_for_unsuccessful_discontinued]" id="unsuccessful_discontinued_<?= $item ?>" rel="S;1;0;A" title="Informazioni relative alla non aggiudicazione" onchange="add_extra_info($(this).val(), procurement_discontinued_option_<?= $item ?>)">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_unsuccessful_discontinued == "PROCUREMENT_UNSUCCESSFUL" ? 'selected="selected"' : null ?> value="PROCUREMENT_UNSUCCESSFUL">Non sono pervenuti piani o progetti o sono stati tutti respinti</option>
				<option <?= $radio_as_select_for_unsuccessful_discontinued == "PROCUREMENT_DISCONTINUED_ITEM_TO_IGNORE" ? 'selected="selected"' : null ?> value="PROCUREMENT_DISCONTINUED_ITEM_TO_IGNORE">Altri motivi (interruzione della procedura)</option>
			</select>
		</td>
	</tr>
	<tr>
		<td style="width: 250px;">Avviso originale spedito mediante:</td>
		<td>
			<?
			$radio_as_select_for_information_not_to_be_published = "";
			if(!empty($guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["PROCUREMENT_DISCONTINUED"]["radio_as_select_for_information_not_to_be_published"])) {
				$radio_as_select_for_information_not_to_be_published = $guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["PROCUREMENT_DISCONTINUED"]["radio_as_select_for_information_not_to_be_published"];
			}
			?>
			<script type="text/javascript">
				var information_not_to_be_published_<?= $item ?> = {
									'ORIGINAL_OTHER_MEANS_ITEM_TO_IGNORE' : [
										'enable_field',
										'',
										[],
										'original_other_means_<?= $item ?>'
									],
									'ORIGINAL_ENOTICES_PUBBLICATION_NO' : [
										'enable_field',
										'',
										[],
										'no_doc_ext_<?= $item ?>'
									],
									'ORIGINAL_TED_ESENDER_PUBBLICATION_NO' : [
										'enable_field',
										'',
										[],
										'no_doc_ext_<?= $item ?>'
									]
								};
			</script>
			<select <?= $radio_as_select_for_unsuccessful_discontinued == "PROCUREMENT_DISCONTINUED_ITEM_TO_IGNORE"  ? null : 'disabled="disabled"' ?> name="guue[RESULTS][ITEM_<?= $item ?>][NO_AWARDED_PRIZE][PROCUREMENT_DISCONTINUED][radio_as_select_for_information_not_to_be_published]" id="field_information_not_to_be_published_<?= $item ?>" rel="S;1;0;A" onchange="add_extra_info($(this).val(), information_not_to_be_published_<?= $item ?>)">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_information_not_to_be_published == "ORIGINAL_ENOTICES_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="ORIGINAL_ENOTICES_PUBBLICATION_NO">eNotices</option>
				<option <?= $radio_as_select_for_information_not_to_be_published == "ORIGINAL_TED_ESENDER_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="ORIGINAL_TED_ESENDER_PUBBLICATION_NO">Ted eSender</option>
				<option <?= $radio_as_select_for_information_not_to_be_published == "ORIGINAL_OTHER_MEANS_ITEM_TO_IGNORE" ? 'selected="selected"' : null ?> value="ORIGINAL_OTHER_MEANS_ITEM_TO_IGNORE">Altri Servizi</option>
			</select>
		</td>
		<td colspan="2">
			<input type="text" rel="S;3;200;A" value="<?= !empty($guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["PROCUREMENT_DISCONTINUED"]["ORIGINAL_OTHER_MEANS_PUBBLICATION_NO"]) ? $guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["PROCUREMENT_DISCONTINUED"]["ORIGINAL_OTHER_MEANS_PUBBLICATION_NO"] : null ?>" name="guue[RESULTS][ITEM_<?= $item ?>][NO_AWARDED_PRIZE][PROCUREMENT_DISCONTINUED][ORIGINAL_OTHER_MEANS_PUBBLICATION_NO]" id="original_other_means_<?= $item ?>" title="Altro Sistema" <?= $radio_as_select_for_information_not_to_be_published == "ORIGINAL_OTHER_MEANS_ITEM_TO_IGNORE" ? null : 'disabled="disabled"' ?>>
		</td>
	</tr>
	<tr>
		<td>Numero di riferimento dell&#39;Avviso:</td>
		<td colspan="3">
			<? 
			$no_doc_ext = "";
			if(!empty($guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["PROCUREMENT_DISCONTINUED"]["NO_DOC_EXT_PUBBLICATION_NO"])) $no_doc_ext = $guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["PROCUREMENT_DISCONTINUED"]["NO_DOC_EXT_PUBBLICATION_NO"];
			?>
			<input id="no_doc_ext_<?= $item ?>" type="text" <?= !empty($radio_as_select_for_information_not_to_be_published) && $radio_as_select_for_information_not_to_be_published != "ORIGINAL_OTHER_MEANS_ITEM_TO_IGNORE" ? null : 'disabled="disabled"'?> name="guue[RESULTS][ITEM_<?= $item ?>][NO_AWARDED_PRIZE][PROCUREMENT_DISCONTINUED][NO_DOC_EXT_PUBBLICATION_NO]" value="<?= $no_doc_ext ?>" title="Numero di riferimento dell&#39;Avviso" rel="S;5;0;A">
		</td>
	</tr>
	<tr>
		<td <td class="etichetta">
			Data di conclusione del contratto d&#39;appalto:
		</td>
		<td colspan="3">
			<input type="text" value="<?= !empty($guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["PROCUREMENT_DISCONTINUED"]["DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO"]) ? $guue["RESULTS"]["ITEM_".$item]["NO_AWARDED_PRIZE"]["PROCUREMENT_DISCONTINUED"]["DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO"] : null?>" name="guue[RESULTS][ITEM_<?= $item ?>][NO_AWARDED_PRIZE][PROCUREMENT_DISCONTINUED][DATE_DISPATCH_ORIGINAL_PUBBLICATION_NO]" title="Data di conclusione del contratto d&#39;appalto" <?= $radio_as_select_for_unsuccessful_discontinued == "PROCUREMENT_DISCONTINUED_ITEM_TO_IGNORE" ? null : 'disabled="disabled"' ?> id="date_dispatch_original_pubblication_no_<?= $item ?>" class="datepick" rel="N;5;0;D">
		</td>
	</tr>
</table>