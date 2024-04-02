<?
  if (isset($form)) {

    ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="6febbe4a-e715-427c-a2b1-19cfabadaef0">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="7f18c64e-ae09-4646-9400-f3666d50af51">
      <label>Si</label>
      <input data-show="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>No</label>
      <input data-hide="#criteria_<?= $form["codice"] ?>" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
    </div><br>
    <div id="criteria_<?= $form["codice"] ?>" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
        <table width="100%">
          <tr>
            <td class="etichetta">
              Qual &egrave; la percentuale corrispondente di lavoratori con disabilit&agrave; o svantaggiati?
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][cbc:ID][$]" value="4e552658-d532-4770-943b-b90efcc9788d">
              <input type="text" rel="N;1;3;N" title="Percentuale" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][ccv:Response][cbc:Percent]"
              value="<?= $values["_0"][1]; ?>">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              Se richiesto, specificare a quale categoria di lavoratori con disabilit&agrave; o svantaggiati appartengono i lavoratori interessati:
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][cbc:ID][$]" value="e01d0929-c7a9-455a-aaf9-e1f7cd966336">
              <input type="text" rel="N;1;0;A" title="Categoria lavori" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][2][ccv:Response][cbc:Description]"
              value="<?= $values["_0"][2]; ?>">
            </td>
          </tr>
        </table>
      </div>
    <?
  }
?>
