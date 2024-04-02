<table class="bordered">
	<tr>
		<td class="etichetta" style="width: 70%">
			<label>
				<?
				$no_tenders_or_no_suitable = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["radio_as_select_for_no_tenders_or_no_suitable"]) ? $guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["radio_as_select_for_no_tenders_or_no_suitable"] : null;
				?>
				<input type="checkbox" <?= !empty($no_tenders_or_no_suitable) ? 'checked="checked"' : null ?> onchange="toggle_field($(this), '#radio_as_select_for_no_tenders_or_no_suitable')">
				Non &egrave; stata presentata alcuna offerta o alcuna offerta appropriata, n&eacute; alcuna domanda di partecipazione o alcuna domanda di partecipazione appropriata in risposta ad una procedura:
			</label>
		</td>
		<td>
			<select id="radio_as_select_for_no_tenders_or_no_suitable" name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][radio_as_select_for_no_tenders_or_no_suitable]" <?= empty($no_tenders_or_no_suitable) ? 'disabled="disabled"' : null ?>>
				<option value="">Seleziona..</option>
				<option <?= $no_tenders_or_no_suitable == "D_PROC_OPEN" ? 'selected="selected"' : null ?> value="D_PROC_OPEN">Procedura Aperta</option>
				<option <?= $no_tenders_or_no_suitable == "D_PROC_RESTRICTED" ? 'selected="selected"' : null ?> value="D_PROC_RESTRICTED">Procedura Ristretta</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label>
				<?
				$d_manuf_for_research = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_MANUF_FOR_RESEARCH"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_d_manuf_for_research"]) ? TRUE : FALSE;
				?>
				<input type="checkbox" <?= $d_manuf_for_research ? 'checked="checked"' : null ?> name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][D_MANUF_FOR_RESEARCH][ATTRIBUTE][CTYPE][radio_as_select_for_d_manuf_for_research]" value="SUPPLIES">
				I prodotti in questione sono fabbricati esclusivamente a scopo di ricerca, di sperimentazione, di studio o di sviluppo, nel rispetto delle condizioni fissate dalla direttiva <i>(unicamente per le forniture)</i>
			</label>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			<label>
				<?
				$radio_as_select_for_reason = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["radio_as_select_for_reason"]) ? $guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["radio_as_select_for_reason"] : null;
				?>
				<input type="checkbox" <?= !empty($radio_as_select_for_reason) ? 'checked="checked"' : null ?> onchange="toggle_field($(this), '#radio_as_select_for_reason')">
				 I lavori, le forniture o i servizi possono essere forniti unicamente da un determinato operatore economico per una delle seguenti ragioni:
			</label>
		</td>
		<td>
			<select id="radio_as_select_for_reason" rel="S;1;0;A" title="Ragione per cui i lavori o i servizi possono essere forniti unicamente da un determinato operatore economico" name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][radio_as_select_for_reason]" <?= empty($radio_as_select_for_reason) ? 'disabled="disabled"' : null ?>>
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_reason ==  "D_TECHNICAL" ? 'selected="selected"' : null ?> value="D_TECHNICAL">la concorrenza &egrave; assente per motivi tecnici</option>
				<option <?= $radio_as_select_for_reason ==  "D_ARTISTIC" ? 'selected="selected"' : null ?> value="D_ARTISTIC">lo scopo dell&#39;appalto nella creazione o nell&#39;acquisizione di un&#39;opera d&#39;arte o di una rappresentazione artistica unica</option>
				<option <?= $radio_as_select_for_reason ==  "D_PROTECT_RIGHTS" ? 'selected="selected"' : null ?> value="D_PROTECT_RIGHTS">tutela di diritti esclusivi, inclusi i diritti di propriet&agrave; intellettuale</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label>
				<?
				$d_extreme_urgency = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_EXTREME_URGENCY"]) ? TRUE : FALSE;
				?>
				<input type="checkbox" <?= $d_extreme_urgency ? 'checked="checked"' : null ?> name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][D_EXTREME_URGENCY]">
				Ragioni di estrema urgenza derivanti da eventi imprevedibili dall&#39;amministrazione aggiudicatrice, nel rispetto delle rigorose condizioni fissate dalla direttiva
			</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label>
				<?
				$d_add_deliveries_ordered = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_ADD_DELIVERIES_ORDERED"]) ? TRUE : FALSE;
				?>
				<input type="checkbox" <?= $d_add_deliveries_ordered ? 'checked="checked"' : null ?> name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][D_ADD_DELIVERIES_ORDERED]">
				Consegne complementari effettuate dal fornitore originario, nel rispetto delle rigorose condizioni fissate dalla direttiva
			</label>
		</td>
	</tr>
  <tr>
    <td colspan="2" class="etichetta">
      <label>
        <?
				$repetition_of_existing = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_REPETITION_EXISTING"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_d_repetition_existing"]) ? TRUE : FALSE;
				?>
				<input type="checkbox" <?= !empty($repetition_of_existing) ? 'checked="checked"' : null ?> onchange="toggle_field($(this), '.repetition_of_existing')">
        Ripetizione di servizi/lavori precedenti:
      </label>
    </td>
  </tr>
  <?
	$radio_as_select_for_d_repetition_existing = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_REPETITION_EXISTING"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_d_repetition_existing"]) ? $guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_REPETITION_EXISTING"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_d_repetition_existing"] : null;
	?>
  <tr>
    <td colspan="2">
      <table class="borderd" width="100%">
        <tr>
          <td>
            <label>
              <input class="repetition_of_existing" <?= ! $repetition_of_existing ? 'disabled="disabled"' : null ?> type="radio" <?= $radio_as_select_for_d_repetition_existing == "SERVICES" ? 'checked="checked"' : null ?> name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][D_REPETITION_EXISTING][ATTRIBUTE][CTYPE][radio_as_select_for_d_repetition_existing]" value="SERVICES">
              Nuovi servizi consistenti nella ripetizione di servizi precedenti, nel rispetto delle rigorose condizioni fissate dalla direttiva
            </label>
          </td>
        </tr>
        <tr>
          <td>
            <label>
              <input class="repetition_of_existing" <?= ! $repetition_of_existing ? 'disabled="disabled"' : null ?> type="radio" <?= $radio_as_select_for_d_repetition_existing == "WORKS" ? 'checked="checked"' : null ?> name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][D_REPETITION_EXISTING][ATTRIBUTE][CTYPE][radio_as_select_for_d_repetition_existing]" value="WORKS">
              Nuovi lavori consistenti nella ripetizione di lavori precedenti, nel rispetto delle rigorose condizioni fissate dalla direttiva
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
	<tr>
		<td colspan="2">
			<label>
				<?
				$d_contract_awarded_design_contest = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_CONTRACT_AWARDED_DESIGN_CONTEST"]["ATTRIBUTE"]["CTYPE"]) ? TRUE : FALSE;
				?>
				<input type="checkbox" <?= $d_contract_awarded_design_contest ? 'checked="checked"' : null ?> name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][D_CONTRACT_AWARDED_DESIGN_CONTEST][ATTRIBUTE][CTYPE]" value="SERVICES">
				Appalto di servizi da aggiudicare al vincitore o a uno dei vincitori in base alle norme previste nel concorso di progettazione
			</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label>
				<?
				$d_commodity_market = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_COMMODITY_MARKET"]["ATTRIBUTE"]["CTYPE"]) ? TRUE : FALSE;
				?>
				<input type="checkbox" <?= $d_commodity_market ? 'checked="checked"' : null ?> name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][D_COMMODITY_MARKET][ATTRIBUTE][CTYPE]" value="SUPPLIES">
				Forniture quotate e acquistate sul mercato delle materie prime
			</label>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			<label>
				<?
				$radio_as_select_for_d_from_winding_provider = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_FROM_WINDING_PROVIDER"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_d_from_winding_provider"]) ? $guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_FROM_WINDING_PROVIDER"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_d_from_winding_provider"] : null;
				?>
				<input type="checkbox" <?= !empty($radio_as_select_for_d_from_winding_provider) ? 'checked="checked"' : null ?> onchange="toggle_field($(this), '#radio_as_select_for_d_from_winding_provider')">
				 Acquisto a condizioni particolarmente vantaggiose presso un fornitore che cessa definitivamente l&#39;attivit&agrave;
			</label>
		</td>
		<td>
			<select id="radio_as_select_for_d_from_winding_provider" rel="S;1;0;A" title="Tipologia di acquisto a condizioni particolarmente vantaggiose" name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][D_FROM_WINDING_PROVIDER][ATTRIBUTE][CTYPE][radio_as_select_for_d_from_winding_provider]" <?= empty($radio_as_select_for_d_from_winding_provider) ? 'disabled="disabled"' : null ?>>
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_d_from_winding_provider ==  "SERVICES" ? 'selected="selected"' : null ?> value="SERVICES">Servizi</option>
				<option <?= $radio_as_select_for_d_from_winding_provider ==  "SUPPLIES" ? 'selected="selected"' : null ?> value="SUPPLIES">Forniture</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="etichetta">
			<label>
				<?
				$radio_as_select_for_d_from_liquidator_creditor = !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_FROM_LIQUIDATOR_CREDITOR"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_d_from_liquidator_creditor"]) ? $guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_ACCORDANCE_ARTICLE"]["D_FROM_LIQUIDATOR_CREDITOR"]["ATTRIBUTE"]["CTYPE"]["radio_as_select_for_d_from_liquidator_creditor"] : null;
				?>
				<input type="checkbox" <?= !empty($radio_as_select_for_d_from_liquidator_creditor) ? 'checked="checked"' : null ?> onchange="toggle_field($(this), '#radio_as_select_for_d_from_liquidator_creditor')">
				 Acquisto a condizioni particolarmente vantaggiose presso il curatore o il liquidatore di un fallimento, di un concordato giudiziario o di una procedura analoga prevista nelle legislazioni o regolamentazioni nazionali
			</label>
		</td>
		<td>
			<select id="radio_as_select_for_d_from_liquidator_creditor" rel="S;1;0;A" title="Tipologia di acquisto a condizioni particolarmente vantaggiose" name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_ACCORDANCE_ARTICLE][D_FROM_LIQUIDATOR_CREDITOR][ATTRIBUTE][CTYPE][radio_as_select_for_d_from_liquidator_creditor]" <?= empty($radio_as_select_for_d_from_liquidator_creditor) ? 'disabled="disabled"' : null ?>>
				<option value="">Seleziona..</option>
				<option <?= $radio_as_select_for_d_from_liquidator_creditor ==  "SERVICES" ? 'selected="selected"' : null ?> value="SERVICES">Servizi</option>
				<option <?= $radio_as_select_for_d_from_liquidator_creditor ==  "SUPPLIES" ? 'selected="selected"' : null ?> value="SUPPLIES">Forniture</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="etichetta">
			<label><b>3. Spiegazione:</b></label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea name="guue[PROCEDURE][PT_AWARD_CONTRACT_WITHOUT_CALL][D_JUSTIFICATION]" rel="S;3;2500;A" title="Spiegazione" class="ckeditor_simple">
				<?= !empty($guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_JUSTIFICATION"]) ? $guue["PROCEDURE"]["PT_AWARD_CONTRACT_WITHOUT_CALL"]["D_JUSTIFICATION"] : null?>
			</textarea>
		</td>
	</tr>
</table>