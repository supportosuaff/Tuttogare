<?
  if (isset($form)) {

    $group_id = array();
    $group_id[0] = "1689194b-6ecf-4ab4-ab38-7656610c25bb";
    $group_id["_00"] = "5aacceb3-280e-42f1-b2da-3d8ac7877fe9";
    $group_id["_01"] = "42db0eaa-d2dd-48cb-83ac-38d73cab9b50";

    $group_id[1] = "c628dd27-8016-4d80-8660-7461f2e3ee0f";
    $group_id["_10"] = "49a57870-7fb8-451f-a7af-fa0e7f8b97e7";
    $group_id["_11"] = "4acd0a02-c267-4d05-b456-c0565c2ffd46";

    $group_id[2] = "9dd09f9f-3326-4865-9d5a-f0836076fb19";
    $group_id["_20"] = "9d0cf1cb-27bc-4747-8579-47dce4d8d490";
    $group_id["_21"] = "28fb4b41-5178-4b79-ba24-d9a62fa4a658";

    $group_id[3] = "962011c9-9e2e-4e7b-818e-30e8506e874f";
    $group_id["_30"] = "17a7353d-a7a4-43ee-9cc8-b9db83eeafb3";
    $group_id["_31"] = "9f278e42-aa1d-4b2e-97cd-832248aa5393";

    $group_id[4] = "343795e2-98e9-4cc9-8ef2-8817cec8f49a";
    $group_id["_40"] = "34825634-5151-4e31-af1b-7eafadcf15be";
    $group_id["_41"] = "cc1a0b1e-dbfd-4313-a4fb-2e543b05549b";

    ?>
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
        <table width="100%">
          <?
          for ($i=0;$i<5;$i++) {
          ?>
          <tr>
            <td class="etichetta" style="width:auto;">
              Anno
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][cbc:ID][$]" value="<?= $group_id[$i] ?>">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][cbc:ID][$]" value="<?= $group_id["_".$i."0"] ?>">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][ccv:Response][cbc:Quantity][@unitCode]" value="YEAR">
              <select rel="N;1;0;A" title="Anno" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][ccv:Response][cbc:Quantity][$]"
               class="dgue_input"
               id="espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_RequirementGroup_<?= $i ?>_ccv_Requirement_0_ccv_Response_cbc_Quantity">
                <option value="">---</option>
                  <?
                    for ($year = (date("Y"));$year >= 2011;$year--) { ?>
                      <option><?= $year ?></option>
                    <? }
                  ?>
                </select>
                <? if (!empty($values["_0".$i][0])) {
                  ?>
                  <script>
                    $("#espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_RequirementGroup_<?= $i ?>_ccv_Requirement_0_ccv_Response_cbc_Quantity").val("<?= $values["_0".$i][0] ?>");
                  </script>
                  <?
                }
                ?>
            </td>
            <td class="etichetta" style="width:auto;">
              Fatturato
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][cbc:ID][$]" value="<?= $group_id["_".$i."1"] ?>">
              <input type="text" rel="N;1;0;N" title="Importo" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][ccv:Response][cbc:Amount][$]"
              value="<?= $values["_0".$i][1]; ?>">
              <select rel="N;3;3;A" id="espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_RequirementGroup_<?= $i ?>_ccv_Requirement_1_ccv_Response_cbc_Amount_currencyID" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][ccv:Response][cbc:Amount][@currencyID]">
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
              <? if (!empty($values["_0".$i][2])) {
                ?>
                <script>
                  $("#espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_RequirementGroup_<?= $i ?>_ccv_Requirement_1_ccv_Response_cbc_Amount_currencyID").val("<?= $values["_0".$i][2] ?>");
                </script>
                <?
              }
              ?>
            </td>
          </tr>
          <? } ?>
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
    unset($group_id);
  }
?>
