<?
  if (isset($form)) {

    $group_id = array();
    $group_id[0] = "96f00020-0a25-402e-b850-2378e83b5695";
    $group_id["_00"] = "ab05ff3b-f3e1-4441-9b43-ee9912e29e92";
    $group_id["_01"] = "42db0eaa-d2dd-48cb-83ac-38d73cab9b50";
    $group_id["_02"] = "42ec8116-31a7-4118-8612-5b04f5c8bde7";
    $group_id["_03"] = "a92536ab-6783-40bb-a037-5d31f421fd85";

    $group_id[1] = "c48572f9-47bf-423a-9885-2c78ae9ca718";
    $group_id["_10"] = "927def36-1fa3-4018-8b45-7ee2c5b1e0af";
    $group_id["_11"] = "4acd0a02-c267-4d05-b456-c0565c2ffd46";
    $group_id["_12"] = "8d0e5e16-85ed-4730-a784-d4db8f439c0c";
    $group_id["_13"] = "c8babafa-b6fa-4e14-8749-d913d8f1d33b";

    $group_id[2] = "2c7a3581-2954-4142-8c1b-5c52d7c7e9b7";
    $group_id["_20"] = "e6ca4034-cfee-499a-9a47-c4f2862ef4d0";
    $group_id["_21"] = "28fb4b41-5178-4b79-ba24-d9a62fa4a658";
    $group_id["_22"] = "c953e635-580b-4d7c-a30c-2edbde3b8fdf";
    $group_id["_23"] = "5157e1ff-d272-4382-98a9-6953f5a15300";

    $group_id[3] = "d67a6126-dd6d-4ed2-bda7-214a19e13a63";
    $group_id["_30"] = "b1640c24-b405-443e-bf5e-d7771f66aab6";
    $group_id["_31"] = "9f278e42-aa1d-4b2e-97cd-832248aa5393";
    $group_id["_32"] = "9b263b45-fc63-4b01-a3dc-cb9c95dda449";
    $group_id["_33"] = "a84ea948-cf03-47b5-b4cf-a35f49910d10";


    $group_id[4] = "159fc086-cf34-48a4-a41b-afed62661383";
    $group_id["_40"] = "587129bc-a5e1-43be-94ac-6e5366d30c67";
    $group_id["_41"] = "cc1a0b1e-dbfd-4313-a4fb-2e543b05549b";
    $group_id["_42"] = "056cba1d-986b-4164-92b6-26a1cbdf0690";
    $group_id["_43"] = "38a4802f-0b93-4e78-ad4e-2a057e1aa578";

    ?>
        <table width="100%">
          <tr>
            <td class="etichetta">Descrizione</td>
            <td class="etichetta">Importo</td>
            <td class="etichetta">Data</td>
            <td class="etichetta">Destinatari</td>
          </tr>
          <?
          for ($i=0;$i<5;$i++) {
          ?>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][cbc:ID][$]" value="<?= $group_id[$i] ?>">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][cbc:ID][$]" value="<?= $group_id["_".$i."0"] ?>">
              <input type="text" rel="N;1;0;A" title="Descrizione" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][ccv:Response][cbc:Description]"
               class="dgue_input"
               value="<?= $values["_0".$i][0] ?>">
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][cbc:ID][$]" value="<?= $group_id["_".$i."1"] ?>">
              <input type="text" rel="N;1;0;N" title="Importo" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][ccv:Response][cbc:Amount][$]"
              value="<?= $values["_0".$i][1]; ?>">
              <select rel="N;3;3;A" id="espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_RequirementGroup_<?= $i ?>_ccv_Requirement_1_ccv_Response_cbc_Amount_currencyID" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][ccv:Response][cbc:Amount][@currencyID]">
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
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][2][cbc:ID][$]" value="<?= $group_id["_".$i."2"] ?>">
              <input type="text" rel="N;10;10;D" title="Data" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][2][ccv:Response][cbc:Date]"
               class="dgue_input datepick"
               value="<?= mysql2date($values["_0".$i][3]) ?>">
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][3][cbc:ID][$]" value="<?= $group_id["_".$i."3"] ?>">
              <input type="text" rel="N;1;0;A" title="Destinatari" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][3][ccv:Response][cbc:Description]"
               class="dgue_input"
               value="<?= $values["_0".$i][4] ?>">
            </td>
          </tr>
          <? } ?>
        </table>
        <table width="100%">
          <tr>
            <td colspan="2" class="etichetta" style="text-align:center">
              Queste informazioni sono disponibili elettronicamente?<br>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][5][cbc:ID][$]" value="9026e403-3eb6-4705-a9e9-e21a1efc867d">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][5][ccv:Requirement][0][cbc:ID][$]" value="9dae5670-cb75-4c97-901b-96ddac5a633a">
              <label>Si</label>
              <input data-show=".electronic_reference_<?= $form["codice"] ?>" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][5][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_1"][0] == "true") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
              <label>No</label>
              <input data-hide=".electronic_reference_<?= $form["codice"] ?>" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][5][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
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
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][5][ccv:Requirement][1][cbc:ID][$]" value="03bb1954-13ae-47d8-8ef8-b7fe0f22d700">
              <input type="text" rel="N;1;0;L" title="URL" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][5][ccv:Requirement][1][ccv:Response][cev:Evidence][cev:EvidenceDocumentReference][cac:Attachment][cac:ExternalReference][cbc:URI]"
              value="<?= $values["_1"][1]; ?>">
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][5][ccv:Requirement][2][cbc:ID][$]" value="e2d863a0-60cb-4e58-8c14-4c1595af48b7">
              <input type="text" rel="N;1;0;A" title="Codice" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][5][ccv:Requirement][2][ccv:Response][ccv-cbc:Code]"
              value="<?= $values["_1"][2]; ?>">
            </td>
          </tr>
        </table>
    <?
    unset($group_id);
  }
?>
