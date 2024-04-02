
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
			<input type="text" title="Numero di offerte" rel="S;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED"] : null ?>">
		</td>
		<td class="etichetta">Numero di offerte ricevute da PMI:</td>
		<td width="150px">
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED_SME]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_SME"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_SME"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di offerte ricevute da offerenti provenienti da altri Stati membri dell&#39;UE:</td>
		<td>
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED_OTHER_EU]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_OTHER_EU"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_OTHER_EU"] : null ?>">
		</td>
		<td class="etichetta">Numero di offerte ricevute dagli offerenti provenienti da Stati non membri dell&#39;UE:</td>
		<td>
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED_NON_EU]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_NON_EU"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_NON_EU"] : null ?>">
		</td>
	</tr>
	<tr>
		<td class="etichetta">Numero di offerte pervenute per via elettronica:</td>
		<td>
			<input type="text" title="Numero di offerte" rel="N;1;0;N" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][TENDERS][NB_TENDERS_RECEIVED_EMEANS]" value="<?= isset($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_EMEANS"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["TENDERS"]["NB_TENDERS_RECEIVED_EMEANS"] : null ?>">
		</td>
		<td class="etichetta">L'appalto è stato aggiudicato a un raggruppamento di operatori economici:</td>
		<td>
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
		<td colspan="4">
			<?
				$contractor = 1;
				$href = "forms/".(!empty($_SESSION["guue"]["v_form"]) ? $_SESSION["guue"]["v_form"] : '2_0_9')."/common/ADDR-S5-f03.php";
				if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"])) {
					$contractor_array = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"];
					$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTORS][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
					$guue[str_replace(array('[',']'), array("", "_"), $keys)] = $contractor_array["ITEM_1"]["ADDRESS_CONTRACTOR"];
					if(!$ajax) { include 'forms/2_0_9_S3/common/ADDR-S5.php'; } else { include '../common/ADDR-S5.php'; }
					$contractor++;
				} else {
					$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTORS][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
					if(!$ajax) { include 'forms/2_0_9_S3/common/ADDR-S5.php'; } else { include '../common/ADDR-S5.php'; }
					$contractor++;
				}
				?>
		</td>
	</tr>
	<tr>
		<td colspan="4" id="contractor_<?= $item ?>"><?
		if(!empty($contractor_array)) {
			$guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"] = array();
			$guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"]["ITEM_1"] = $contractor_array["ITEM_1"];

			unset($contractor_array["ITEM_1"]);
			if(!empty($contractor_array)) {
				foreach ($contractor_array as $key => $value) {
					$address_item = $contractor;
					$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTORS][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
					$guue[str_replace(array('[',']'), array("", "_"), $keys)] = $value["ADDRESS_CONTRACTOR"];
					if(!empty($value["radio_as_select_for_is_an_sme"])) {
						$guue["AWARD_CONTRACT"]['ITEM_'.$item]["AWARDED_CONTRACT"]["CONTRACTORS"]["CONTRACTOR"]["ITEM_".$contractor]["radio_as_select_for_is_an_sme"] = $value["radio_as_select_for_is_an_sme"];
					}
					if(!$ajax) { include 'forms/2_0_9_S3/common/ADDR-S5.php'; } else { include '../common/ADDR-S5.php'; }
					$contractor++;
				}
			}
		}

		// if(!empty($contractor_array)) {
		// 	for ($contr = 1; $contr < count($contractor_array); $contr++) {
		// 		$address_item = $contractor;
		// 		$keys = '[AWARD_CONTRACT][ITEM_'.$item.'][AWARDED_CONTRACT][CONTRACTORS][CONTRACTOR][ITEM_'.$contractor.'][ADDRESS_CONTRACTOR]';
		// 		if(!$ajax) { include 'forms/2_0_9_S3/common/ADDR-S5-f03.php'; } else { include '../common/ADDR-S5-f03.php'; }
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
		<td class="etichetta" colspan="2"><label>Valore totale inizialmente stimato del contratto d&#39;appalto/del lotto/della concessione: </label></td>
		<td colspan="2">
			<input type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUES][VAL_ESTIMATED_TOTAL][ATTRIBUTE][CURRENCY]" value="EUR">
			<input type="text" title="Valore Stimato" rel="N;2;0;2D" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUES][VAL_ESTIMATED_TOTAL][val]" value="<?= !empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUES"]["VAL_ESTIMATED_TOTAL"]["val"]) ? $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUES"]["VAL_ESTIMATED_TOTAL"]["val"] : null ?>">
		</td>
	</tr>
	<tr><td class="etichetta" colspan="4"><i>(in caso di accordi quadro o sistema dinamico di acquisizione – valore massimo totale stimato per l&#39;intera durata di questo lotto)</i></td></tr>
	<tr>
		<td class="etichetta" colspan="2">
			<?
			$valore_totale = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUES"]["VAL_TOTAL"]["val"])) {
				$valore_totale = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUES"]["VAL_TOTAL"]["val"];
			}
			?>
			<label>Valore totale della concessione/del lotto:</label>
		</td>
		<td colspan="2">
			<input type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUES][VAL_TOTAL][ATTRIBUTE][CURRENCY]" value="EUR">
			<input type="text" title="Valore Totale" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUES][VAL_TOTAL][val]" value="<?= $valore_totale ?>" id="valore_totale_del_contaratto_item_<?= $item ?>_duration" rel="S;1;0;2D">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="2">
			<label>Entrate derivanti dal pagamento, da parte degli utenti, di tariffe e multe:</label>
		</td>
		<td colspan="2">
			<?
			$val_revenue = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUES"]["VAL_REVENUE"]["val"])) {
				$val_revenue = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUES"]["VAL_REVENUE"]["val"];
			}
			?>
			<input type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUES][VAL_REVENUE][ATTRIBUTE][CURRENCY]" value="EUR">
			<input type="text" title="Entrate derivanti dal pagamento, da parte degli utenti, di tariffe e multe" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUES][VAL_REVENUE][val]" value="<?= $val_revenue ?>" id="valore_totale_del_contaratto_item_<?= $item ?>_duration" rel="N;1;0;2D">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="2">
			<label>Premi, pagamenti o altri vantaggi finanziari forniti dall&#39;amministrazione aggiudicatrice/ dall&#39;ente aggiudicatore:</label>
		</td>
		<td colspan="2">
			<?
			$val_price_payment = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUES"]["VAL_PRICE_PAYMENT"]["val"])) {
				$val_price_payment = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["VALUES"]["VAL_PRICE_PAYMENT"]["val"];
			}
			?>
			<input type="hidden" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUES][VAL_PRICE_PAYMENT][ATTRIBUTE][CURRENCY]" value="EUR">
			<input type="text" title="Premi, pagamenti o altri vantaggi finanziari" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][VALUES][VAL_PRICE_PAYMENT][val]" value="<?= $val_price_payment ?>" id="valore_totale_del_contaratto_item_<?= $item ?>_duration" rel="N;1;0;2D">
		</td>
	</tr>
	<tr>
		<td class="etichetta" colspan="4">
			<label>Eventuali altri dettagli relativi al valore della concessione ai sensi dell&#39;articolo 8, paragrafo 3, della direttiva:</label>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<?
			$info_add_value = "";
			if(!empty($guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["INFO_ADD_VALUE"])) {
				$info_add_value = $guue["AWARD_CONTRACT"]["ITEM_".$item]["AWARDED_CONTRACT"]["INFO_ADD_VALUE"];
			}
			?>
			<input type="text" title="Premi, pagamenti o altri vantaggi finanziari" name="guue[AWARD_CONTRACT][ITEM_<?= $item ?>][AWARDED_CONTRACT][INFO_ADD_VALUE]" value="<?= $info_add_value ?>" id="valore_totale_del_contaratto_item_<?= $item ?>_duration" rel="N;1;400;A">
		</td>
	</tr>
</table>
