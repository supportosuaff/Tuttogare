<?
  if (isset($form)) {
    if (!empty($form["sub_uuid"])) {

      ?>
      <div style="text-align:center">
        <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
        <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][cbc:ID][$]" value="974c8196-9d1c-419c-9ca9-45bb9f5fd59a">
        <label>Si</label>
        <input type="radio" data-show="#criteria_<?= $form["codice"] ?>"
          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
          value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
          onclick="show_hide($(this));">
        <label>No</label>
        <input type="radio" data-hide="#criteria_<?= $form["codice"] ?>"
          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][0][ccv:Response][ccv-cbc:Indicator]"
          value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
          onclick="show_hide($(this));">
      </div>
      <div id="criteria_<?= $form["codice"] ?>" <? if ($values["_0"][0] != "true") echo "style='display:none'" ?>>
          <table width="100%">
            <tr>
              <td class="etichetta">
                Fornire informazioni dettagliate sulle modalit&agrave; con cui &egrave; stato risolto il conflitto di interessi:
              </td>
              <td>
                <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][cbc:ID][$]" value="PERS_CONFLITTO_1">
                <textarea rel="N;1;0;A" title="Descrizione" class="dgue_input"
                name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][1][ccv:Response][cbc:Description]"><?= $values["_0"][1] ?></textarea>
              </td>
            </tr>
          </table>
        </div>
      <?
    } else {
      ?>
      <div class="errore padding" style="text-align:center">
        <strong>ERRORE DI CONFIGURAZIONE.</strong>
      </div>
      <?
    }
  }
?>
