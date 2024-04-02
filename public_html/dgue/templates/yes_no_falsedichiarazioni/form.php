<?
  if (isset($form)) {
    if (!empty($form["sub_uuid"])) {

      ?>
      <div style="text-align:center">
        <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="<?= $form["sub_uuid"] ?>">
        <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][cbc:ID][$]" value="974c8196-9d1c-419c-9ca9-45bb9f5fd59a">
        <label>Si</label>
        <input type="radio" data-show="#criteria_<?= $form["codice"] ?>"
          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][ccv:Response][ccv-cbc:Indicator]"
          value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
          onclick="show_hide($(this));">
        <label>No</label>
        <input type="radio" data-hide="#criteria_<?= $form["codice"] ?>"
          name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][ccv:Response][ccv-cbc:Indicator]"
          value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
          onclick="show_hide($(this));">
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
