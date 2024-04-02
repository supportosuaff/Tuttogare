<?
  if (isset($form)) {

    $group_id = array();
    $group_id[0] = "1689194b-6ecf-4ab4-ab38-7656610c25bb";
    $group_id["_00"] = "ab05ff3b-f3e1-4441-9b43-ee9912e29e92";
    $group_id["_01"] = "5461b973-7067-457e-93cc-8338da2c3eef";

    $group_id[1] = "c628dd27-8016-4d80-8660-7461f2e3ee0f";
    $group_id["_10"] = "927def36-1fa3-4018-8b45-7ee2c5b1e0af";
    $group_id["_11"] = "295d82b7-5ee6-4977-8aea-bac4acf6ecdf";

    $group_id[2] = "9dd09f9f-3326-4865-9d5a-f0836076fb19";
    $group_id["_20"] = "e6ca4034-cfee-499a-9a47-c4f2862ef4d0";
    $group_id["_21"] = "2b792afb-87ba-47b5-a80c-aee76a6f2cc8";

    $group_id[3] = "4564d79e-5db6-4a31-93ee-ac1f0019bdcb";
    $group_id["_30"] = "b1640c24-b405-443e-bf5e-d7771f66aab6";
    $group_id["_31"] = "22dc4bef-182d-4b81-bddc-cc30b218f9bb";

    $group_id[4] = "bc43685e-8473-40e3-b174-3233aead6207";
    $group_id["_40"] = "587129bc-a5e1-43be-94ac-6e5366d30c67";
    $group_id["_41"] = "990eef0a-14c6-41af-8bf2-b8311332d152";

    ?>
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="cf00f7bb-c2cf-4565-91bb-221d78d8dd2f">
        <table width="100%">
          <?
          for ($i=0;$i<5;$i++) {
          ?>
          <tr>
            <td class="etichetta" style="width:auto;">
              Descrizione
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][cbc:ID][$]" value="<?= $group_id[$i] ?>">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][cbc:ID][$]" value="<?= $group_id["_".$i."0"] ?>">
              <input type="text" rel="N;1;0;A" title="Descrizione" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][0][ccv:Response][cbc:Description]"
               class="dgue_input" value="<?= $values["_0".$i][0] ?>"
            </td>
            <td class="etichetta" style="width:auto;">
              Indice
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][cbc:ID][$]" value="<?= $group_id["_".$i."1"] ?>">
              <input type="text" rel="N;1;0;N" title="Indice" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][<?= $i ?>][ccv:Requirement][1][ccv:Response][cbc:Quantity]"
              value="<?= $values["_0".$i][1]; ?>">
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
