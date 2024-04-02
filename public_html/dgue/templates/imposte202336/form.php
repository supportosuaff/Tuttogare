<?
  if (isset($form)) {

    ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][cbc:ID][$]" value="974c8196-9d1c-419c-9ca9-45bb9f5fd59a">
      <label>Si</label>
      <input data-show="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>No</label>
      <input data-hide="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
    </div><br>
    <div id="criteria_<?= $form["codice"] ?>" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
        <table width="100%">
          <tr>
            <td class="etichetta">
              Paese Interessato
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][cbc:ID][$]" value="6c87d3d4-e8eb-4253-b385-6373020ab886">
              <select rel="S;1;0;A" title="Paese"  name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][ccv-cbc:Code][$]"
               class="dgue_input"
               id="espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_Requirement_1_ccv_Response_ccv-cbc_Code">
                <option value="" selected="selected">---</option>
                <optgroup label="EU">
                  <option value="AT">Austria</option>
                  <option value="BE">Belgio</option>
                  <option value="BG">Bulgaria</option>
                  <option value="CY">Cipro</option>
                  <option value="HR">Croazia</option>
                  <option value="DK">Danimarca</option>
                  <option value="EE">Estonia</option>
                  <option value="FI">Finlandia</option>
                  <option value="FR">Francia</option>
                  <option value="DE">Germania</option>
                  <option value="GR">Grecia</option>
                  <option value="IE">Irlanda</option>
                  <option value="IT">Italia</option>
                  <option value="LV">Lettonia</option>
                  <option value="LT">Lituania</option>
                  <option value="LU">Lussemburgo</option>
                  <option value="MT">Malta</option>
                  <option value="NL">Paesi Bassi</option>
                  <option value="PL">Polonia</option>
                  <option value="PT">Portogallo</option>
                  <option value="GB">Regno Unito</option>
                  <option value="CZ">Repubblica ceca</option>
                  <option value="RO">Romania</option>
                  <option value="SK">Slovacchia</option>
                  <option value="SI">Slovenia</option>
                  <option value="ES">Spagna</option>
                  <option value="SE">Svezia</option>
                  <option value="HU">Ungheria</option>
                </optgroup>
                <optgroup label="EFTA">
                  <option value="NO">Norvegia</option>
                  <option value="CH">Svizzera</option>
                </optgroup>
                </select>
                <? if (!empty($values["_0"][1])) {
                  ?>
                  <script>
                    $("#espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_Requirement_1_ccv_Response_ccv-cbc_Code").val("<?= $values["_0"][1] ?>");
                  </script>
                  <?
                }
                ?>
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              Di quale importo si tratta
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][cbc:ID][$]" value="9052cc59-cfe5-41c6-a314-02a7f378ffe8">
              <input type="text" rel="N;1;0;N" title="Importo" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][ccv:Response][cbc:Amount][$]"
              value="<?= $values["_0"][2]; ?>">
              <select rel="N;3;3;A" id="espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_Requirement_2_ccv_Response_cbc_Amount_currencyID" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][ccv:Response][cbc:Amount][@currencyID]">
                <option value="">---</option>
                <option value="EUR">EUR (Euro)</option>
                <option value="ALL">ALL (Albanian lek)</option>
                <option value="AMD">AMD (Armenian dram)</option>
                <option value="AZN">AZN (Azerbaijani manat)</option>
                <option value="BAM">BAM (Bosnian convertible mark)</option>
                <option value="BGN">BGN (Bulgarian lev)</option>
                <option value="BYR">BYR (Belarusian ruble)</option>
                <option value="CHF">CHF (Swiss franc)</option>
                <option value="CZK">CZK (Czech koruna)</option>
                <option value="DKK">DKK (Danish krone)</option>
                <option value="GBP">GBP (pound sterling)</option>
                <option value="GEL">GEL (Georgian lari)</option>
                <option value="HRK">HRK (Croatian kuna)</option>
                <option value="HUF">HUF (Hungarian forint)</option>
                <option value="ISK">ISK (Icelandic króna)</option>
                <option value="MDL">MDL (Moldovan krone)</option>
                <option value="PLN">PLN (Polish zloty)</option>
                <option value="RON">RON (New Romanian leu)</option>
                <option value="RSD">RSD (Serbian dinar)</option>
                <option value="RUB">RUB (Russian ruble)</option>
                <option value="SEK">SEK (Swedish krona)</option>
                <option value="TRY">TRY (Turkish lira)</option>
                <option value="UAH">UAH (Ukrainian hryvnia)</option>
                <option value="USD">USD (US dollar)</option>
              </select>
              <? if (!empty($values["_0"][3])) {
                ?>
                <script>
                  $("#espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_Requirement_2_ccv_Response_cbc_Amount_currencyID").val("<?= $values["_0"][3] ?>");
                </script>
                <?
              }
              ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="etichietta">
              Tale inottemperanza &egrave; stata accertata in modo diverso da una sentenza giudiziaria o decisione amministrativa?
            </td>
          </tr>
          <tr>
            <td colspan="2" style="text-align:center">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][0][cbc:ID][$]" value="7c2aec9f-4876-4c33-89e6-2ab6d6cf5d02">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][0][ccv:Requirement][0][cbc:ID][$]" value="9b4497e6-a166-46f9-8581-7fc39ff975c4">
              <label>Si</label>
              <input data-show=".sub_form_<?= $form["codice"] ?>_true" data-hide=".sub_form_<?= $form["codice"] ?>_false" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_00"][0] === "true") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
              <label>No</label>
              <input data-hide=".sub_form_<?= $form["codice"] ?>_true" data-show=".sub_form_<?= $form["codice"] ?>_false" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_00"][0] === "false") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_true" <? if ($values["_00"][0] != "true") echo "style='display:none'" ?>>
            <td colspan="2" class="etichetta">
              Indicare in quale modo &egrave; stata accertata l'inottemperanza
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_true" <? if ($values["_00"][0] != "true") echo "style='display:none'" ?>>
            <td colspan="2">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][0][ccv:Requirement][1][cbc:ID][$]" value="201f11c3-1fa2-4464-acc0-f021266fd881">
              <textarea rel="N;1;0;A" title="Modalita Accertamento" class="dgue_input" rows="3"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_00"][1] ?></textarea>
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_false" <? if ($values["_00"][0] != "false") echo "style='display:none'" ?>>
            <td colspan="2" class="etichietta">
            Se l'inottemperanza &egrave; stata accertata mediante una sentenza giudiziaria o decisione amministrativa, tale sentenza o decisione &egrave; definitiva e vincolante?
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_false" <? if ($values["_00"][0] != "false") echo "style='display:none'" ?>>
            <td colspan="2" style="text-align:center">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][1][cbc:ID][$]" value="c882afa4-6971-4b00-8970-0c283eb122cc">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][1][ccv:Requirement][0][cbc:ID][$]" value="08b0c984-c5e6-4143-8493-868c39745637">
              <label>Si</label>
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_01"][0] == "true") echo "checked='checked'" ?>
                >
              <label>No</label>
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_01"][0] == "false") echo "checked='checked'" ?>
                >
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_false" <? if ($values["_00"][0] != "false") echo "style='display:none'" ?>>
            <td colspan="2" class="etichetta">
              Indicare la data della sentenza di condanna o della decisione
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_false" <? if ($values["_00"][0] != "false") echo "style='display:none'" ?>>
            <td colspan="2">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][1][ccv:Requirement][1][cbc:ID][$]" value="ecf40999-7b64-4e10-b960-7f8ff8674cf6">
              <input type="text" class="datepick" rel="N;10;10;D" title="Data della condanna"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][1][ccv:Requirement][1][ccv:Response][cbc:Date]"
              value="<?= mysql2date($values["_01"][1]); ?>">
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_false" <? if ($values["_00"][0] != "false") echo "style='display:none'" ?>>
            <td colspan="2" class="etichetta">
              Nel caso di una sentenza di condanna, se stabilita direttamente nella sentenza di condanna, la durata del periodo d'esclusione
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_false" <? if ($values["_00"][0] != "false") echo "style='display:none'" ?>>
            <td colspan="2">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][1][ccv:Requirement][2][cbc:ID][$]" value="9ca9096f-edd2-4f19-b6b1-b55c83a2d5c8">
              <input type="text" rel="N;1;0;A" title="Durata della condanna" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][1][ccv:Requirement][2][ccv:Response][cac:Period][cbc:Description]"
              value="<?= $values["_01"][2]; ?>">
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_true" <? if ($values["_00"][0] != "true") echo "style='display:none'" ?>>
            <td colspan="2" class="etichietta">
            L'operatore economico ha ottemperato ai suoi obblighi, pagando o impegnandosi in modo vincolante a pagare le imposte o i contributi previdenziali dovuti, compresi eventuali interessi o multe,
            o il suo debito si &egrave; comunque integralmente estinto, essendosi perfezionati il pagamento, l'impegno, o l'estinzione anteriormente alla scadenza del termine per la presentazione delle offerte?
            (articolo 95 comma 2 del Codice)?
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_true" <? if ($values["_00"][0] != "true") echo "style='display:none'" ?>>
            <td colspan="2" style="text-align:center">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][2][cbc:ID][$]" value="fc57e473-d63e-4a04-b589-dcf81cab8052">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][2][ccv:Requirement][0][cbc:ID][$]" value="70f8697b-8953-411a-a489-4ff62e5250d2">
              <label>Si</label>
              <input data-show=".sub_form_<?= $form["codice"] ?>_1_true" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][2][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_02"][0] === "true") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
              <label>No</label>
              <input data-hide=".sub_form_<?= $form["codice"] ?>_1_true" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][2][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_02"][0] === "false") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_true sub_form_<?= $form["codice"] ?>_1_true" <? if ($values["_02"][0] != "true") echo "style='display:none'" ?>>
            <td colspan="2" class="etichetta">
              Descrivere tali misure, specificando se è intervenuto: pagamento, compensazione o estinzione
            </td>
          </tr>
          <tr class="sub_form_<?= $form["codice"] ?>_true sub_form_<?= $form["codice"] ?>_1_true" <? if ($values["_02"][0] != "true") echo "style='display:none'" ?>>
            <td colspan="2">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][2][ccv:Requirement][1][cbc:ID][$]" value="55905dd0-38f0-4f93-8c74-5ae05a21afc5">
              <textarea rel="N;1;0;A" title="Modalita Accertamento" class="dgue_input" rows="3"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][2][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_02"][1] ?></textarea>
            </td>
          </tr>
        </table>
      </div>
        <table width="100%">
          <tr>
            <td colspan="2" class="etichetta" style="text-align:center">
              Queste informazioni sono disponibili elettronicamente?<br>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][cbc:ID][$]" value="7458d42a-e581-4640-9283-34ceb3ad4345">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][cbc:ID][$]" value="c1347b74-1872-4060-a6db-f4044edcd7c4">
              <label>Si</label>
              <input data-show=".electronic_reference_<?= $form["codice"] ?>" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_1"][0] === "true") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
              <label>No</label>
              <input data-hide=".electronic_reference_<?= $form["codice"] ?>" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_1"][0] === "false") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
            </td>
          </tr>
          <tr class="electronic_reference_<?= $form["codice"] ?>" <? if ($values["_1"][0] != "true") echo "style='display:none'" ?>>
            <td>URL</td>
            <td>Codice</td>
          </tr>
          <tr class="electronic_reference_<?= $form["codice"] ?>" <? if ($values["_1"][0] != "true") echo "style='display:none'" ?>>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][1][cbc:ID][$]" value="f4313bb6-21b6-499e-bdff-debe10e11d2c">
              <input type="text" rel="N;1;0;L" title="URL" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][1][ccv:Response][cev:Evidence][cev:EvidenceDocumentReference][cac:Attachment][cac:ExternalReference][cbc:URI]"
              value="<?= $values["_1"][1]; ?>">
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][2][cbc:ID][$]" value="1f1cd18e-3e01-4ca2-af4c-e2981924ba8d">
              <input type="text" rel="N;1;0;A" title="Codice" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][2][ccv:Response][ccv-cbc:Code]"
              value="<?= $values["_1"][2]; ?>">
            </td>
          </tr>
        </table>
    <?
  }
?>
