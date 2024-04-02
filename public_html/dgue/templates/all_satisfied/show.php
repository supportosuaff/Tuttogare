<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <label>Si</label>
      [ <?= ($values["_0"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      &nbsp;&nbsp;
      <label>No</label>
      [ <?= ($values["_0"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
    </div>
    <?
  }
?>
