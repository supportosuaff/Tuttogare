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
			<input type="text" class="datepick" title="Data di conclusione del contratto d&#39;appalto" rel="S;2;0;D" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][DATE_CONCLUSION_CONTRACT]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["DATE_CONCLUSION_CONTRACT"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["DATE_CONCLUSION_CONTRACT"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			V.2.2) Informazioni sulle offerte
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di offerte pervenute:</td>
		<td width="150px">
			<input type="text" title="Numero di offerte" rel="S;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][NB_TENDERS_RECEIVED]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED"] : null ?>">
		</td>
		<td class="etichetta">Numero di offerte ricevute da PMI:</td>
		<td width="150px">
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][NB_TENDERS_RECEIVED_SME]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED_SME"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED_SME"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di offerte ricevute da offerenti provenienti da altri Stati membri dell&#39;UE:</td>
		<td>
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][NB_TENDERS_RECEIVED_OTHER_EU]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED_OTHER_EU"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED_OTHER_EU"] : null ?>">
		</td>
		<td class="etichetta">Numero di offerte ricevute dagli offerenti provenienti da Stati non membri dell&#39;UE:</td>
		<td>
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][NB_TENDERS_RECEIVED_NON_EU]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED_NON_EU"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED_NON_EU"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di offerte pervenute per via elettronica:</td>
		<td>
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][NB_TENDERS_RECEIVED_EMEANS]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED_EMEANS"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["NB_TENDERS_RECEIVED_EMEANS"] : null ?>">
		</td>
		<td class="etichetta">L'appalto è stato aggiudicato a un raggruppamento di operatori economici:</td>
		<td>
			<?
			$radio_as_select_for_awarded_to_group = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_awarded_to_group"])) {
				$radio_as_select_for_awarded_to_group = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["radio_as_select_for_awarded_to_group"];
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
			<select name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][radio_as_select_for_awarded_to_group]" rel="S;1;0;A" title="Aggiudicato a un raggruppamento">
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
		<td colspan="4">
			<?
				$contractor = 1;
				$href = "forms/".(!empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : '2_0_9')."/common/ADDR-S5-f03.php";
				if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"])) {
					$contractor_array = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"];
					$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
					$guue[str_replace(array('[',']'), array("", "_"), $keys)] = $contractor_array["ITEM_1"]["ADDRESS_CONTRACTOR"];
					if(!$ajax) { include 'forms/2_0_9_S2/common/ADDR-S5.php'; } else { include '../common/ADDR-S5.php'; }
					$contractor++;
				} else {
					$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
					if(!$ajax) { include 'forms/2_0_9_S2/common/ADDR-S5.php'; } else { include '../common/ADDR-S5.php'; }
					$contractor++;
				}
				?>
		</td>
	</tr>
	<tr>
		<td colspan="4" id="contractor_<?= $item ?>"><?
		if(!empty($contractor_array)) {
			$guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"] = array();
			$guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTOR"]["ITEM_1"] = $contractor_array["ITEM_1"];

			unset($contractor_array["ITEM_1"]);
			if(!empty($contractor_array)) {
				foreach ($contractor_array as $key => $value) {
					$address_item = $contractor;
					$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
					$guue[str_replace(array('[',']'), array("", "_"), $keys)] = $value["ADDRESS_CONTRACTOR"];
					if(!empty($value["radio_as_select_for_is_an_sme"])) {
						$guue["AWARD_CONTRACT"]['ITEM_'.$item]["AWARDED_CONTRACT"]["CONTRACTOR"]["ITEM_".$contractor]["radio_as_select_for_is_an_sme"] = $value["radio_as_select_for_is_an_sme"];
					}
					if(!$ajax) { include 'forms/2_0_9_S2/common/ADDR-S5.php'; } else { include '../common/ADDR-S5.php'; }
					$contractor++;
				}
			}
		}

		// if(!empty($contractor_array)) {
		// 	for ($contr = 1; $contr < count($contractor_array); $contr++) { 
		// 		$address_item = $contractor;
		// 		$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
		// 		if(!$ajax) { include 'forms/2_0_9_S2/common/ADDR-S5-f03.php'; } else { include '../common/ADDR-S5-f03.php'; }
		// 		$contractor++;
		// 	}
		// }
		?></td>
	</tr>
	<tr>
		<td colspan="4">
			<script type="text/javascript">
				var contractor_<?= $item ?> = <?= $contractor ?>
			</script>
			<button type="button" class="aggiungi" onclick="contractor_<?= $item ?>++;aggiungi('<?= $href ?>','#contractor_<?= $item ?>', {chiavi:['AWARD_CONTRACT', 'ITEM_<?= $item ?>', 'AWARDED_CONTRACT', 'CONTRACTOR', 'ITEM_' + contractor_<?= $item ?>, 'ADDRESS_CONTRACTOR'], item: contractor_<?= $item ?>, contractor_item: <?= $item ?>});return false;" ><img src="/img/add.png" alt="Aggiungi lotto">Aggiungi Informazioni di Contatto Supplementari</button>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label><b>V.2.4) Informazione sul valore del contratto d&#39;appalto /lotto <i>(IVA esclusa)</i></b></label>
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="2"><label>Valore totale inizialmente stimato del contratto d&#39;appalto/lotto</label></td>
		<td colspan="2">
			<input type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_ESTIMATED_TOTAL][ATTRIBUTE][CURRENCY]" value="EUR">
			<input type="text" title="Valore Stimato" rel="N;2;0;2D" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_ESTIMATED_TOTAL][val]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["val"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_ESTIMATED_TOTAL"]["val"] : null ?>">
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
						if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_TOTAL"]["val"])) {
							$valore_totale = TRUE;
							$valore_valore_totale = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VAL_TOTAL"]["val"];
						}
						?>
						<td class="etichetta">
							<label colspan="2" style="font-size: 12px;"><input <?= $valore_totale ? 'checked="checked"' : null ?> type="radio" name="valore_totale_del_contaratto<?= $item ?>" onchange="toggle_field($(this), ['#valore_totale_del_contaratto_item_<?= $item ?>_duration']);"> Valore totale del contratto d'appalto/del lotto:</label>
						</td>
						<td colspan="2">
							<input type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_TOTAL][ATTRIBUTE][CURRENCY]" value="EUR">
							<input type="text" title="Valore Totale" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_TOTAL][val]" <?= !$valore_totale ? 'disabled="disabled"' : null ?> value="<?= $valore_valore_totale ?>" id="valore_totale_del_contaratto_item_<?= $item ?>_duration" rel="S;1;0;2D">
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
							<label style="font-size: 12px;"><input type="radio" <?= $val_range_total ? 'checked="checked"' : null ?> name="valore_totale_del_contaratto<?= $item ?>" onchange="toggle_field($(this), ['#item_<?= $item ?>_valore_val_range_total_low', '#item_<?= $item ?>_valore_val_range_total_high', '#val_range_total_currency_item_<?= $item ?>']);">Offerta pi&ugrave; bassa / Offerta pi&ugrave; alta:</label>
						</td>
						<td>
							<input type="text" title="Offerta pi&ugrave; bassa" value="<?= $valore_val_range_total_low ?>" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_RANGE_TOTAL][LOW]" <?= !$val_range_total ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_valore_val_range_total_low" rel="S;1;0;2D">
						</td>
						<td>
							<input type="text" title="Offerta pi&ugrave; bassa" value="<?= $valore_val_range_total_high ?>" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_RANGE_TOTAL][HIGH]" <?= !$val_range_total ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_valore_val_range_total_high" rel="S;1;0;2D">
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
			<input type="hidden" <?= !$val_range_total ? 'disabled="disabled"' : null ?> id="val_range_total_currency_item_<?= $item ?>" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_RANGE_TOTAL][ATTRIBUTE][CURRENCY]" value="EUR">
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
						'#currency_item_<?= $item ?>_subcontracting',
						'#item_<?= $item ?>_pct_subcontracting',
						'#item_<?= $item ?>_info_add_subcontracting'
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
			<input type="hidden" id="currency_item_<?= $item ?>_subcontracting" <?= !$fields_subcontracted ? 'disabled="disabled"' : null ?> name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_SUBCONTRACTING][ATTRIBUTE][CURRENCY]" value="EUR">
			<input type="text" title="Valore o percentuale del contratto d&#39;appalto da subappaltare a terzi" value="<?= $val_subcontracting ?>" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VAL_SUBCONTRACTING][val]" <?= !$fields_subcontracted ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_subcontracting" rel="N;1;0;N">
		</td>
	</tr>
	<tr>
		<td colspan="2" class="etichetta"><label>Percentuale:</label></td>
		<td colspan="2">
			<input type="text" title="Percentuale" value="" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][PCT_SUBCONTRACTING]" <?= !$fields_subcontracted ? 'disabled="disabled"' : null ?> id="item_<?= $item ?>_pct_subcontracting" rel="N;1;0;N">
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
</table>