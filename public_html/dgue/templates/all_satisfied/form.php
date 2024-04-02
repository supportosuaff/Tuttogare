<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      Risposta fornita?<br>
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][cbc:ID][$]" value="f3a6836d-2de2-4cd1-81ca-fb06178d05c5">
      <input type="hidden" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][cbc:ID][$]" value="15335c12-ad77-4728-b5ad-3c06a60d65a4">
      <label>Si</label>
      <input data-hide='.SELECTION' data-show=".ALL_SATISFIED" type="radio" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][ccv:Response][ccv-cbc:Indicator]"
        value="true" <? if ($values["_0"][0] == "true") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
      <label>No</label>
      <input data-show=".SELECTION"  type="radio" name="espd[ccv:Criterion][<?= $form["uuid"] ?>][ccv:RequirementGroup][ccv:Requirement][ccv:Response][ccv-cbc:Indicator]"
        value="false" <? if ($values["_0"][0] == "false") echo "checked='checked'" ?>
        onclick="show_hide($(this));">
    </div><br>
    <?
  }
?>
