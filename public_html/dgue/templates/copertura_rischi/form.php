<?
  if (isset($form)) {

    ?>
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="42dc8062-974d-4201-91ba-7f2ea90338fd">
        <table width="100%">
          <tr>
            <td class="etichetta" style="width:auto;">
              Importo
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][cbc:ID][$]" value="42db0eaa-d2dd-48cb-83ac-38d73cab9b50">
              <input type="text" rel="N;1;0;N" title="Importo" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][ccv:Response][cbc:Amount][$]"
              value="<?= $values["_0"][0]; ?>">
              <select rel="N;3;3;A" id="espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_ccv_Requirement_ccv_Response_cbc_Amount_currencyID" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][ccv:Response][cbc:Amount][@currencyID]">
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
              <? if (!empty($values["_0"][1])) {
                ?>
                <script>
                  $("#espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_ccv_Requirement_ccv_Response_cbc_Amount_currencyID").val("<?= $values["_0"][1] ?>");
                </script>
                <?
              }
              ?>
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td colspan="2" class="etichetta" style="text-align:center">
              Queste informazioni sono disponibili elettronicamente?<br>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][cbc:ID][$]" value="9026e403-3eb6-4705-a9e9-e21a1efc867d">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][cbc:ID][$]" value="9dae5670-cb75-4c97-901b-96ddac5a633a">
              <label>Si</label>
              <input data-show=".electronic_reference_<?= $form["codice"] ?>" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_1"][0] == "true") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
              <label>No</label>
              <input data-hide=".electronic_reference_<?= $form["codice"] ?>" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_1"][0] == "false") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
            </td>
          </tr>
          <tr class="electronic_reference_<?= $form["codice"] ?>" <? if ($values["_1"][0] != "true") echo "style='display:none'" ?>>
            <td>URL</td>
            <td>Codice</td>
          </tr>
          <tr class="electronic_reference_<?= $form["codice"] ?>" <? if ($values["_1"][0] != "true") echo "style='display:none'" ?>>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][1][cbc:ID][$]" value="03bb1954-13ae-47d8-8ef8-b7fe0f22d700">
              <input type="text" rel="N;1;0;L" title="URL" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][1][ccv:Response][cev:Evidence][cev:EvidenceDocumentReference][cac:Attachment][cac:ExternalReference][cbc:URI]"
              value="<?= $values["_1"][1]; ?>">
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][2][cbc:ID][$]" value="e2d863a0-60cb-4e58-8c14-4c1595af48b7">
              <input type="text" rel="N;1;0;A" title="Codice" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][1][ccv:Requirement][2][ccv:Response][ccv-cbc:Code]"
              value="<?= $values["_1"][2]; ?>">
            </td>
          </tr>
        </table>
    <?
  }
?>
