<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][cbc:ID][$]" value="15335c12-ad77-4728-b5ad-3c06a60d65a4">
      <label>Si</label>
      <input data-hide="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>No</label>
      <input data-show="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
    </div><br>
    <div id="criteria_<?= $form["codice"] ?>" <? if ($values["_0"][0] != "false") echo "style='display:none'" ?>>
        <table width="100%">
          <tr>
            <td class="etichetta">
              In caso negativo, spiegare perch&eacute; e precisare di quali altri mezzi di prova si dispone:
            </td>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][cbc:ID][$]" value="b9dec4cb-2f6f-47d7-a023-e9099b19b338">
              <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_0"][1] ?></textarea>
            </td>
          </tr>
        </table>
      </div>
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
