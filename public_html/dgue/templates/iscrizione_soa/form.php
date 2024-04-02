<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][cbc:ID][$]" value="64162276-7014-408f-a9af-080426bfe1fd">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][cbc:ID][$]" value="7f18c64e-ae09-4646-9400-f3666d50af51">
      <label>Si</label>
      <input data-show=".criteria_<?= $form["codice"] ?>_true" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="show_hide($(this));" class="radio_<?= $form["codice"] ?>">
      <label>No</label>
      <input data-hide=".criteria_<?= $form["codice"] ?>_true" type="radio"
        name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
        value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="show_hide($(this));" class="radio_<?= $form["codice"] ?>">
    </div><br>
    <div class="criteria_<?= $form["codice"] ?> criteria_<?= $form["codice"] ?>_true" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
        <table width="100%">
          <tr>
            <td class="etichetta">
              a) Indicare gli estremi dell'attestazione (denominazione dell’Organismo di attestazione ovvero Sistema di qualificazione, numero e data dell'attestazione)
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][cbc:ID][$]" value="30064ad3-fc11-4579-8528-fdd0b9a5ba75">
              <input type="text" rel="N;1;0;A" title="Numero di iscrizione o certificazione" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][1][ccv:Response][cbc:Description]"
              value="<?= $values["_0"][1]; ?>">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              b) Se l'attestazione di qualificazione &egrave; disponibile elettronicamente, indicare:
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][cbc:ID][$]" value="b3403349-cbc0-4d84-879e-fc0f2d90ecbd">
              <input type="text" rel="N;1;0;A" title="Certificato disponibile elettronicamente" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][2][ccv:Response][cbc:Description]"
              value="<?= $values["_0"][2]; ?>">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              c) Indicare i riferimenti in base ai quali &egrave; stata ottenuta l'iscrizione o la certificazione e, se pertinente, la classificazione ricevuta nell'elenco ufficiale:
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][3][cbc:ID][$]" value="792ff522-6f3f-4a62-ab6e-a8b272bc290e">
              <input type="text" rel="N;1;0;A" title="Riferimenti iscrizione" class="dgue_input"
              name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:Requirement][3][ccv:Response][cbc:Description]"
              value="<?= $values["_0"][3]; ?>">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              d) L'iscrizione o la certificazione comprende tutti i criteri di selezione richiesti?
            </td>
          </tr>
          <tr>
            <td>
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][cbc:ID][$]" value="92e44d3b-af8e-4a29-91a8-24d27aa27fee">
              <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][cbc:ID][$]" value="d9996ef5-49f9-4cf8-a2f5-31c9f4efd894">
              <label>Si</label>
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][ccv:Response][ccv-cbc:Indicator]"
                value="true" <? if ($values["_01"][0] == "true") echo "checked='checked'" ?>>
              <label>No</label>
              <input type="radio"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][0][ccv:RequirementGroup][ccv:Requirement][ccv:Response][ccv-cbc:Indicator]"
                value="false" <? if ($values["_01"][0] == "false") echo "checked='checked'" ?>>
            </td>
          </tr>
        </table>
      </div>
    <?
  }
?>
