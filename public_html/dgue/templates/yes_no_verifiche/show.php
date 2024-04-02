<?
  if (isset($form)) {
    if (!empty($dgue["ccv:Criterion"][$form["uuid"]])&& !$show_empty) {
      $values = $dgue["ccv:Criterion"][$form["uuid"]]["ccv:RequirementGroup"]["ccv:Requirement"];
    }
    if (empty($values["ccv:Response"]["ccv-cbc:Indicator"])) $values["ccv:Response"]["ccv-cbc:Indicator"] = "";

    ?>
    <div style="text-align:center">
      Il compilatore acconsente alle verifiche?<br>
      <label>Si</label>
      [ <?= ($values["ccv:Response"]["ccv-cbc:Indicator"] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      &nbsp;&nbsp;
      <label>No</label>
      [ <?= ($values["ccv:Response"]["ccv-cbc:Indicator"] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
    </div><br>
    <?
  }
?>
