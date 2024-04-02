<?
  if (isset($form)) {

    $group_id = array();

    $group_id[0] = "96defecc-7d32-4957-82e9-aad5f3c5b736";
    $group_id["_00"] = "5aacceb3-280e-42f1-b2da-3d8ac7877fe9";
    $group_id["_01"] = "42037f41-53af-44df-b6b8-2395cee98087";

    $group_id[1] = "dac727d8-2cd2-43e0-8561-6f17e25870a4f";
    $group_id["_10"] = "49a57870-7fb8-451f-a7af-fa0e7f8b97e7";
    $group_id["_11"] = "0bb2d3bf-160f-4904-a4e8-ee672bd5cb30";

    $group_id[2] = "b799d324-358c-48b0-bd5e-6d205969b4a5";
    $group_id["_20"] = "9d0cf1cb-27bc-4747-8579-47dce4d8d490";
    $group_id["_21"] = "668dbc0d-2a3a-49b9-b8e1-8ebbeccd712a";

    ?>
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
        <table width="100%">
          <?
          for ($i=0;$i<3;$i++) {
          ?>
          <tr>
            <td class="etichetta" style="width:auto;">
              Anno
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][cbc:ID][$]" value="<?= $group_id[$i] ?>">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][cbc:ID][$]" value="<?= $group_id["_".$i."0"] ?>">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][ccv:Response][cbc:Quantity][@unitCode]" value="YEAR">
              <select rel="N;1;0;A" title="Anno" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][ccv:Response][cbc:Quantity][$]"
               class="dgue_input"
               id="espd_ccv_Criterion_<?= $form["uuid"] ?>_ccv_RequirementGroup_0_ccv_RequirementGroup_<?= $i ?>_ccv_Requirement_0_ccv_Response_cbc_Quantity">
                <option value="">---</option>
                  <?
                    for ($year = (date("Y")-1);$year >= 2011;$year--) { ?>
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
              Numero
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][cbc:ID][$]" value="<?= $group_id["_".$i."1"] ?>">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][ccv:Response][cbc:Quantity][@unitCode]" value="NUMBER">
              <input type="text" rel="N;1;0;N" title="Importo" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][ccv:Response][cbc:Quantity][$]"
              value="<?= $values["_0".$i][1]; ?>">
            </td>
          </tr>
          <? } ?>
        </table>
        <table width="100%">
          <tr>
            <td colspan="2" class="etichetta" style="text-align:center">
              Queste informazioni sono disponibili elettronicamente?<br>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][3][cbc:ID][$]" value="9026e403-3eb6-4705-a9e9-e21a1efc867d">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][3][ccv:Requirement][0][cbc:ID][$]" value="9dae5670-cb75-4c97-901b-96ddac5a633a">
              <label>Si</label>
              <input data-show=".electronic_reference_<?= $form["codice"] ?>" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][3][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_1"][0] == "true") echo "checked='checked'" ?>
                onclick="show_hide($(this));">
              <label>No</label>
              <input data-hide=".electronic_reference_<?= $form["codice"] ?>" type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][3][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
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
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][3][ccv:Requirement][1][cbc:ID][$]" value="03bb1954-13ae-47d8-8ef8-b7fe0f22d700">
              <input type="text" rel="N;1;0;L" title="URL" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][3][ccv:Requirement][1][ccv:Response][cev:Evidence][cev:EvidenceDocumentReference][cac:Attachment][cac:ExternalReference][cbc:URI]"
              value="<?= $values["_1"][1]; ?>">
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][3][ccv:Requirement][2][cbc:ID][$]" value="e2d863a0-60cb-4e58-8c14-4c1595af48b7">
              <input type="text" rel="N;1;0;A" title="Codice" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][3][ccv:Requirement][2][ccv:Response][ccv-cbc:Code]"
              value="<?= $values["_1"][2]; ?>">
            </td>
          </tr>
        </table>
    <?
    unset($group_id);
  }
?>
