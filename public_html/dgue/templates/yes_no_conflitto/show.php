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
    if ($values["_0"][0] == "true" || $show_empty) {
      ?><br>
      <table width="100%">
        <tr>
          <th style="<?= $styles["th"] ?>">
            <? if ($show_empty) echo "In caso affermativo," ?>Fornire informazioni dettagliate sulle modalit&agrave; con cui &egrave; stato risolto il conflitto di interessi:
          </th>
          <td>
            <?= $values["_0"][1] ?>
          </td>
        </tr>
      </table>
      <?
    }
  }
?>
