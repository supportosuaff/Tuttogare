<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <label>Si</label>
      [ <?= ($values["_0"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      &nbsp;&nbsp;
      <label>No</label>
      [ <?= ($values["_0"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
    </div><br>
    <? if ($values["_0"][0] == "true" || $show_empty)  { ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              <? if (!empty($values["_0"][2])) { ?>
                Riepilogo
              <? } else { ?>
                In caso affermativo, indicare i lavori o le parti di opere ovvero i servizi e le forniture o parti di servizi e forniture che si intende subappaltare
              <? } ?>
            </th>
          </tr>
          <tr>
            <td><?=  str_replace("\n\r", "<br><br>",$values["_0"][1]); ?>
            </td>
          </tr>
        </table>
    <?
    }
  }
?>
