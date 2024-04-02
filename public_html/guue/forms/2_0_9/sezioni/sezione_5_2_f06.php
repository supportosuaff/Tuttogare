<?
	$ajax = FALSE;
	if (!empty($_POST["data"])) {
		session_start();
		$ajax = TRUE;
		$item = $_POST["data"]["item"];
	}
?>
<style type="text/css">
	td table.bigger * {
		font-size: 12px;
	}
</style>
<table class="bordered bigger">
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2) Aggiudicazione di appalto</b></label>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="etichetta">V.2.1) Data di conclusione del contratto d&#39;appalto</td>
		<td colspan="2">
			<input type="text" title="Data di conclusione del contratto d&#39;appalto" class="datepick" rel="S;2;0;D" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][DATE_CONCLUSION_CONTRACT]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["DATE_CONCLUSION_CONTRACT"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["DATE_CONCLUSION_CONTRACT"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			V.2.2) Informazioni sulle offerte
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="2">
			<label>Consentire la pubblicazione delle informazioni sulle offerte?</label>
		</td>
		<td colspan="2">
			<?
			$radio_as_select_for_tenders_publication = !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["ATTRIBUTE"]["PUBLICATION"]["radio_as_select_for_tenders_publication"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["ATTRIBUTE"]["PUBLICATION"]["radio_as_select_for_tenders_publication"] : null;
			?>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][ATTRIBUTE][PUBLICATION][radio_as_select_for_tenders_publication]" rel="S;1;0;A" title="Consentire la pubblicazione delle informazioni sulle offerte">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_tenders_publication == "YES" ? 'selected="selected"' : null ?> value="YES">Si</option>
				<option <?= $radio_as_select_for_tenders_publication == "NO" ? 'selected="selected"' : null ?> value="NO">No</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di offerte pervenute:</td>
		<td width="150px">
			<input type="text" title="Numero di offerte" rel="S;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED"] : null ?>">
		</td>
		<td class="etichetta">Numero di offerte ricevute da PMI:</td>
		<td width="150px">
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED_SME]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_SME"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_SME"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di offerte ricevute da offerenti provenienti da altri Stati membri dell&#39;UE:</td>
		<td>
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED_OTHER_EU]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_OTHER_EU"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_OTHER_EU"] : null ?>">
		</td>
		<td class="etichetta">Numero di offerte ricevute dagli offerenti provenienti da Stati non membri dell&#39;UE:</td>
		<td>
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED_NON_EU]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_NON_EU"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_NON_EU"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di offerte pervenute per via elettronica:</td>
		<td colspan="3">
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED_EMEANS]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_EMEANS"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_EMEANS"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="2">
			<label>Consentire la pubblicazione del nome e indirizzo del contraente?</label>
		</td>
		<td colspan="2">
			<?
			$radio_as_select_for_contractors_publication = !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["ATTRIBUTE"]["PUBLICATION"]["radio_as_select_for_contractors_publication"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["ATTRIBUTE"]["PUBLICATION"]["radio_as_select_for_contractors_publication"] : null;
			?>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][CONTRACTORS][ATTRIBUTE][PUBLICATION][radio_as_select_for_contractors_publication]" rel="S;1;0;A" title="Consentire la pubblicazione del nome e indirizzo del contraente">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_contractors_publication == "YES" ? 'selected="selected"' : null ?> value="YES">Si</option>
				<option <?= $radio_as_select_for_contractors_publication == "NO" ? 'selected="selected"' : null ?> value="NO">No</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta">L'appalto è stato aggiudicato a un raggruppamento di operatori economici:</td>
		<td colspan="3">
			<?
			$radio_as_select_for_awarded_to_group = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["radio_as_select_for_awarded_to_group"])) {
				$radio_as_select_for_awarded_to_group = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["radio_as_select_for_awarded_to_group"];
			}
			?>
			<script>
				var radio_as_select_for_awarded_to_group_<?= $item ?> = {
					'AWARDED_TO_GROUP' : [
						'ajax_load',
						['sezioni', 'sezione_5_2'],
						[],
						'awarded_contract_<?= $item ?>',
						{item: '<?= $item ?>'}
					],
					'NO_AWARDED_TO_GROUP' : [
						'ajax_load',
						['sezioni', 'sezione_5_1'],
						[],
						'awarded_contract_<?= $item ?>',
						{item: '<?= $item ?>'}
					]
				};
			</script>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][CONTRACTORS][radio_as_select_for_awarded_to_group]" rel="S;1;0;A" title="Aggiudicato a un raggruppamento">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_awarded_to_group == 'AWARDED_TO_GROUP' ? 'selected="selected"' : null ?> value="AWARDED_TO_GROUP">Si</option>
				<option <?= $radio_as_select_for_awarded_to_group == 'NO_AWARDED_TO_GROUP' ? 'selected="selected"' : null ?> value="NO_AWARDED_TO_GROUP">No</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2.3) Nome e indirizzo del contraente</b></label>
		</td>
	</tr>
	<tr>
		<td colspan="4" id="contractor_<?= $item ?>"><?
		$contractor = 1;
		$href = "forms/".(!empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : '2_0_9')."/common/ADDR-S5-f06.php";
		if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"])) {
			$contractor_array = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"];
			$guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"] = array();
			foreach ($contractor_array as $contractor_values) {
				$address_item = $contractor;
				$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTORS][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
				$guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"]["ITEM_".$contractor] = $contractor_values;
				$guue[str_replace(array('[',']'), array("", "_"), $keys)] = $contractor_values["ADDRESS_CONTRACTOR"];
				if(!empty($value["radio_as_select_for_is_an_sme"])) {
					$guue["AWARD_CONTRACT"]['ITEM_'.$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"]["ITEM_".$contractor]["radio_as_select_for_is_an_sme"] = $value["radio_as_select_for_is_an_sme"];
				}
				if(!$ajax) { include 'forms/2_0_9/common/ADDR-S5-f06.php'; } else { include '../common/ADDR-S5-f06.php'; }
				$contractor++;
			}
		} else {
			$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTORS][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
			if(!$ajax) { include 'forms/2_0_9/common/ADDR-S5-f06.php'; } else { include '../common/ADDR-S5-f06.php'; }
			$contractor++;
		}
		?></td>
	</tr>
	<? /**?>
	<tr>
		<td colspan="4">
			<?
				$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTOR][ADDRESS_CONTRACTOR]';
				$excluded_input = array('NATIONALID', 'CONTACT_POINT', 'URL_GENERAL', 'URL_BUYER', 'E_MAIL_1');
				$added_input = array("E_MAIL", "URL");
				$required = FALSE;
				$prefix = "ADDRS5-";
				if(!$ajax) {
					include 'forms/2_0_9/common/ADDR-S1.php';
				} else {
					include '../common/ADDR-S1.php';
				}
			?>
		</td>
	</tr>
	
	<tr>
		<td colspan="3">Il contraente &egrave; una PMI?</td>
		<td>
			<?
			$radio_as_select_for_is_an_sme = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"]["radio_as_select_for_is_an_sme"])) {
				$radio_as_select_for_is_an_sme = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"]["radio_as_select_for_is_an_sme"];
			}
			?>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][CONTRACTOR][radio_as_select_for_is_an_sme]" rel="S;1;0;A" title="Contraente PMI">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_is_an_sme == 'SME' ? 'selected="selected"' : null ?> value="SME">Si</option>
				<option <?= $radio_as_select_for_is_an_sme == 'NO_SME' ? 'selected="selected"' : null ?> value="NO_SME">No</option>
			</select>
		</td>
	</tr>
	<? */ ?>
	<tr>
		<td colspan="4">
			<script type="text/javascript">
				var contractor_<?= $item ?> = <?= $contractor ?>
			</script>
			<button type="button" class="aggiungi" onclick="contractor_<?= $item ?>++;aggiungi('<?= $href ?>','#contractor_<?= $item ?>', {chiavi:['AWARD_CONTRACT', 'ITEM_<?= $item ?>', 'AWARDED_CONTRACT', 'CONTRACTORS', 'CONTRACTOR', 'ITEM_' + contractor_<?= $item ?>, 'ADDRESS_CONTRACTOR'], item: contractor_<?= $item ?>, contractor_item: <?= $item ?>});return false;" ><img src="/img/add.png" alt="Aggiungi lotto">Aggiungi Informazioni di Contatto Supplementari</button>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2.4) Informazione sul valore del contratto d&#39;appalto /lotto <i>(IVA esclusa)</i></b></label>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="etichetta">
			Consentire la pubblicazione?
		</td>
		<td colspan="2">
			<? $award_contract_value_publication = !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUE"]["ATTRIBUTE"]["PUBLICATION"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUE"]["ATTRIBUTE"]["PUBLICATION"] : ""; ?>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUE][ATTRIBUTE][PUBLICATION]" rel="S;1;0;A" title="Consentire la pubblicazione">
				<option value="">Seleziona..</option>
				<option <?= $award_contract_value_publication == "YES" ? 'selected="selected"' : null ?> value="YES">SI</option>
				<option <?= $award_contract_value_publication == "NO" ? 'selected="selected"' : null ?> value="NO">NO</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="2"><label>Valore totale inizialmente stimato del contratto d&#39;appalto/lotto</label></td>
		<td colspan="2">
			<input type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUE][VAL_ESTIMATED_TOTAL][ATTRIBUTE][CURRENCY]" value="EUR">
			<input type="text" title="Valore Stimato" rel="N;2;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUE][VAL_ESTIMATED_TOTAL][val]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUE"]["VAL_ESTIMATED_TOTAL"]["val"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUE"]["VAL_ESTIMATED_TOTAL"]["val"] : null ?>">
		</td>
	</tr>
	<tr><td class="etichetta" colspan="4"><i>(in caso di accordi quadro o sistema dinamico di acquisizione – valore massimo totale stimato per l&#39;intera durata di questo lotto)</i></td></tr>
	<tr>
		<td colspan="4">
			<table class="bordered valida" title="Valore totale inizialmente stimato del contratto d&#39;appalto/lotto" rel="<?= isRequired("total_final_value_of_the_contract") ?>;0;0;checked;group_validate">
				<tbody>
					<tr>
						<?
						$valore_totale = FALSE;
						$valore_valore_totale = "";
						if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUE"]["VAL_TOTAL"]["val"])) {
							$valore_totale = TRUE;
							$valore_valore_totale = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUE"]["VAL_TOTAL"]["val"];
						}
						?>
						<td class="etichetta">
							<label colspan="2" style="font-size: 12px;"><input <?= $valore_totale ? 'checked="checked"' : null ?> type="radio" name="valore_totale_del_contaratto<?= $item ?>" onchange="toggle_field($(this), ['#valore_totale_del_contaratto_item_<?= $item ?>_duration', '#valore_totale_del_contaratto_item_<?= $item ?>_currency']);"> Valore totale del contratto d'appalto/del lotto:</label>
						</td>
						<td colspan="2">
							<input id="valore_totale_del_contaratto_item_<?= $item ?>_currency" <?= !$valore_totale ? 'disabled="disabled"' : null ?> type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUE][VAL_TOTAL][ATTRIBUTE][CURRENCY]" value="EUR">
							<input type="text" title="Valore Totale" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUE][VAL_TOTAL][val]" <?= !$valore_totale ? 'disabled="disabled"' : null ?> value="<?= $valore_valore_totale ?>" id="valore_totale_del_contaratto_item_<?= $item ?>_duration" rel="S;1;0;A">
						</td>
					</tr>
					<tr>
						<?
						$val_range_total = FALSE;
						$valore_val_range_total_low = "";
						$valore_val_range_total_high = "";
						if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["VAL_RANGE_TOTAL"]["HIGH"]) && !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["VAL_RANGE_TOTAL"]["LOW"])) {
							$val_range_total = TRUE;
							$valore_val_range_total_low = $guue["AWARD_CONTRACT"]["ITEM_".$item]["VAL_RANGE_TOTAL"]["LOW"];
							$valore_val_range_total_high = $guue["AWARD_CONTRACT"]["ITEM_".$item]["VAL_RANGE_TOTAL"]["HIGH"];
						}
						?>
						<td class="etichetta" style="width:350px;">
							<label style="font-size: 12px;"><input type="radio" <?= $val_range_total ? 'checked="checked"' : null ?> name="valore_totale_del_contaratto<?= $item ?>" onchange="toggle_field($(this), ['#item_<?= $item ?>_valore_val_range_total_low', '#item_<?= $item ?>_valore_val_range_total_high', '#item_<?= $item ?>_val_range_total_currency']);">Offerta pi&ugrave; bassa / Offerta pi&ugrave; alta:</label>
							<input id="item_<?= $item ?>_val_range_total_currency" type="hidden" <?= !$val_range_total ? 'disabled="disabled"' : null ?> name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUE][VAL_RANGE_TOTAL][ATTRIBUTE][CURRENCY]" value="EUR">
						</td>
						<td>
							<input type="text" title="Offerta pi&ugrave; bassa" value="<?= $valore_val_range_total_low ?>" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUE][VAL_RANGE_TOTAL][LOW]" <?= !$val_range_total ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_valore_val_range_total_low" rel="S;1;0;D">
						</td>
						<td>
							<input type="text" title="Offerta pi&ugrave; bassa" value="<?= $valore_val_range_total_high ?>" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUE][VAL_RANGE_TOTAL][HIGH]" <?= !$val_range_total ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_valore_val_range_total_high" rel="S;1;0;D">
						</td>
					</tr>
					<tr>
						<td class="etichetta" colspan="4">
							(in caso di accordi quadro – valore massimo totale per questo lotto)<br>
							(in caso di un sistema dinamico di acquisizione – valore dell&#39;appalto per questo lotto non incluso nei precedenti avvisi di aggiudicazione di appalti)<br>
							(in caso di appalti basati su accordi quadro, se richiesto – valore dell&#39;appalto per questo lotto non incluso nei precedenti avvisi di aggiudicazione di appalti)
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2.5) Informazioni sui subappalti</b></label>
		</td>
	</tr>
	<tr>
		<td colspan="4" class="etichetta">
			<label>
				<script type="text/javascript">
					var fields_subcontracted = [
						'#item_<?= $item ?>_subcontracting',
						'#item_<?= $item ?>_pct_subcontracting',
						'#item_<?= $item ?>_info_add_subcontracting',
						'#val_subcontracting_<?= $item ?>_currency'
					];
				</script>
				<?
					$fields_subcontracted = FALSE;
					$val_subcontracting = !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_SUBCONTRACTING"]["val"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_SUBCONTRACTING"]["val"] : "";
					$pct_subcontracting = !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["PCT_SUBCONTRACTING"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["PCT_SUBCONTRACTING"] : "";
					$info_add_subcontracting = !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["INFO_ADD_SUBCONTRACTING"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["INFO_ADD_SUBCONTRACTING"] : "";
					if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["LIKELY_SUBCONTRACTED"])) {
						$fields_subcontracted = TRUE;
					}
				?>
				<input type="checkbox" <?= $fields_subcontracted ? 'checked="checked"' : null ?> name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][LIKELY_SUBCONTRACTED]" title="&Egrave; probabile che il contratto d&#39;appalto venga subappaltato" onchange="toggle_field($(this), fields_subcontracted);"> &Egrave; probabile che il contratto d&#39;appalto venga subappaltato
			</label>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="etichetta"><label>Valore o percentuale del contratto d&#39;appalto da subappaltare a terzi</label></td>
		<td colspan="2">
			<input type="hidden" id="val_subcontracting_<?= $item ?>_currency" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_SUBCONTRACTING][ATTRIBUTE][CURRENCY]" value="EUR" <?= !$fields_subcontracted ? 'disabled="disabled"' : null ?>>
			<input type="text" title="Valore o percentuale del contratto d&#39;appalto da subappaltare a terzi" value="<?= $val_subcontracting ?>" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_SUBCONTRACTING][val]" <?= !$fields_subcontracted ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_subcontracting" rel="N;1;0;N">
		</td>
	</tr>
	<tr>
		<td colspan="2" class="etichetta"><label>Percentuale:</label></td>
		<td colspan="2">
			<input type="text" title="Percentuale" value="<?= $pct_subcontracting ?>" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][PCT_SUBCONTRACTING]" <?= !$fields_subcontracted ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_pct_subcontracting" rel="N;1;0;N">
		</td>
	</tr>
	<tr>
		<td colspan="4" class="etichetta">
			Breve descrizione della porzione del contratto d&#39;appalto da subappaltare:
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<textarea id="item_<?= $item ?>_info_add_subcontracting" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][INFO_ADD_SUBCONTRACTING]" rel="S;0;400;A" title="Descrizione subappalto" class="ckeditor_simple" <?= !$fields_subcontracted ? 'disabled="disabled"' : null ?>><?= $info_add_subcontracting ?></textarea>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2.6) Prezzo pagato per gli acquisti di opportunita&agrave;</b></label>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			<label>Valuta:</label>
		</td>
		<td>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_BARGAIN_PURCHASE][ATTRIBUTE][CURRENCY][radio_as_select_for_currencies]" rel="N;0;3;A">
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "GBP") ? 'selected="selected"' : null ?> value="GBP">STERLINA REGNO UNITO</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "ISK") ? 'selected="selected"' : null ?> value="ISK">CORONA ISLANDA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "LTL") ? 'selected="selected"' : null ?> value="LTL">LITAS LITUANIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CHF") ? 'selected="selected"' : null ?> value="CHF">FRANCO SVIZZERA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "SEK") ? 'selected="selected"' : null ?> value="SEK">CORONA SVEZIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "JPY") ? 'selected="selected"' : null ?> value="JPY">YEN GIAPPONE</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "LVL") ? 'selected="selected"' : null ?> value="LVL">LATS LETTONIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "NOK") ? 'selected="selected"' : null ?> value="NOK">CORONA NORVEGIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "MTL") ? 'selected="selected"' : null ?> value="MTL">LIRA MALTA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "EEK") ? 'selected="selected"' : null ?> value="EEK">CORONA ESTONIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CYP") ? 'selected="selected"' : null ?> value="CYP">LIRA CIPRO</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "SKK") ? 'selected="selected"' : null ?> value="SKK">CORONA REPUBBLICA SLOVACCA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "RON") ? 'selected="selected"' : null ?> value="RON">LEU ROMANIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "CZK") ? 'selected="selected"' : null ?> value="CZK">CORONA REPUBBLICA CECA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "DKK") ? 'selected="selected"' : null ?> value="DKK">CORONA DANIMARCA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "PLN") ? 'selected="selected"' : null ?> value="PLN">ZLOTY POLONIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "BGN") ? 'selected="selected"' : null ?> value="BGN">LEV BULGARIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "HUF") ? 'selected="selected"' : null ?> value="HUF">FORINT UNGHERIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "USD") ? 'selected="selected"' : null ?> value="USD">DOLLARO STATI UNITI</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "EUR") ? 'selected="selected"' : null ?> value="EUR" <?= empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) ? 'selected="selected"' : null ?>>EURO UNIONE ECONOMICA MONETARIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "MKD") ? 'selected="selected"' : null ?> value="MKD">DINARO MACEDONIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "TRY") ? 'selected="selected"' : null ?> value="TRY">LIRA TURCHIA</option>
				<option <?= (!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["ATTRIBUTE"]["CURRENCY"]["radio_as_select_for_currencies"] == "HRK") ? 'selected="selected"' : null ?> value="HRK">KUNA CROAZIA</option>
			</select>
		</td>
		<td class="etichetta">
			<label>Valore, IVA esclusa:</label>	
		</td>
		<td>
			<input type="text" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_BARGAIN_PURCHASE][val]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["val"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_BARGAIN_PURCHASE"]["val"] : null ?>" rel="N;0;15;2D" title="Valore IVA esclusa">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="2">
			<label><b>V.2.7) Numero di contratti d&#39;appalto aggiudicati</b></label>
		</td>
		<td colspan="2">
			<input type="text" title="Numero di contratti d&#39;appalto aggiudicati" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][NB_CONTRACT_AWARDED_PUBBLICATION_NO]" rel="S;1;3;N" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_CONTRACT_AWARDED_PUBBLICATION_NO"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_CONTRACT_AWARDED_PUBBLICATION_NO"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2.8) Paese di origine del prodotto o del servizio</b></label>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="2">
			<label>Consentire la pubblicazione delle informazioni sul Paese di origine?</label>
		</td>
		<td colspan="2">
			<?
			$radio_as_select_for_community_origin_publication = !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["COUNTRY_ORIGIN"]["ATTRIBUTE"]["PUBLICATION"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["COUNTRY_ORIGIN"]["ATTRIBUTE"]["PUBLICATION"] : null;
			?>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][COUNTRY_ORIGIN][ATTRIBUTE][PUBLICATION]" rel="S;1;0;A" title="Consentire la pubblicazione delle informazioni sulle offerte">
				<option <?= $radio_as_select_for_community_origin_publication == "NO" ? 'selected="selected"' : null ?> value="NO">No</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<script type="text/javascript">
				var radio_as_select_for_community_origin_<?= $item ?> = {
					'NON_COMMUNITY_ORIGIN_ITEM_TO_IGNORE' : [
						'enable_field',
						'',
						[],
						'non_community_origin_<?= $item ?>',
					]
				};
			</script>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][COUNTRY_ORIGIN][radio_as_select_for_community_origin]" onchange="add_extra_info($(this).val(), radio_as_select_for_community_origin_<?= $item ?>)">
				<option value="">Seleziona..</option>
				<option <?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["COUNTRY_ORIGIN"]["radio_as_select_for_community_origin"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["COUNTRY_ORIGIN"]["radio_as_select_for_community_origin"] == "COMMUNITY_ORIGIN" ? 'selected="selected"' : null ?>value="COMMUNITY_ORIGIN">Origine comunitaria</option>
				<option <?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["COUNTRY_ORIGIN"]["radio_as_select_for_community_origin"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["COUNTRY_ORIGIN"]["radio_as_select_for_community_origin"] == "NON_COMMUNITY_ORIGIN_ITEM_TO_IGNORE" ? 'selected="selected"' : null ?>value="NON_COMMUNITY_ORIGIN_ITEM_TO_IGNORE">Origine extra-comunitaria</option>
			</select>
		</td>
		<td colspan="2">
			<select <?= empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_community_origin"]) || $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_community_origin"] != "NON_COMMUNITY_ORIGIN_ITEM_TO_IGNORE" ? 'disabled="disabled"' : null ?> name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][NON_COMMUNITY_ORIGIN][ATTRIBUTE][VALUE]" id="non_community_origin_<?= $item ?>" rel="S;1;2;A" title="Origine extra-comunitaria">
				<option>Seleziona..</option>
				<?
				include $root . '/guue/countries.php';
				foreach ($countries as $key => $country) {
					?>
					<option <?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NON_COMMUNITY_ORIGIN"]["ATTRIBUTE"]["VALUE"]) && $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NON_COMMUNITY_ORIGIN"]["ATTRIBUTE"]["VALUE"] == $key ? 'selected="selected"' : null ?> value="<?= $key ?>"><?= $country ?></option>
					<?
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<b>V.2.9) Il contratto d&#39;appalto &egrave; stato aggiudicato a un offerente che ha proposto una variante?</b>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<?
			$radio_as_select_for_awarded_tenderer_variant = !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_awarded_tenderer_variant"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_awarded_tenderer_variant"] : "";
			?>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][radio_as_select_for_awarded_tenderer_variant]" title="Variante nella proposta" rel="S;1;0;A">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_awarded_tenderer_variant == "AWARDED_TENDERER_VARIANT_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="AWARDED_TENDERER_VARIANT_PUBBLICATION_NO">Si</option>
				<option <?= $radio_as_select_for_awarded_tenderer_variant == "NO_AWARDED_TENDERER_VARIANT_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="NO_AWARDED_TENDERER_VARIANT_PUBBLICATION_NO">No</option>
			</select> 
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<b>V.2.10) Sono state escluse offerte in quanto anormalmente basse?</b>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<?
			$radio_as_select_for_tenders_excluded = !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_tenders_excluded"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_tenders_excluded"] : "";
			?>
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][radio_as_select_for_tenders_excluded]" title="Esclusione offerte anomale" rel="S;1;0;A">
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_tenders_excluded == "TENDERS_EXCLUDED_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="TENDERS_EXCLUDED_PUBBLICATION_NO">Si</option>
				<option <?= $radio_as_select_for_tenders_excluded == "NO_TENDERS_EXCLUDED_PUBBLICATION_NO" ? 'selected="selected"' : null ?> value="NO_TENDERS_EXCLUDED_PUBBLICATION_NO">No</option>
			</select> 
		</td>
	</tr>
</table>