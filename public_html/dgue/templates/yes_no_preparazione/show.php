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
    <? if ($values["_0"][0] == "true" || $show_empty) { ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              <? if ($show_empty) echo "In caso affermativo," ?> Fornire indicazioni dettagliate sulle misure adottate per prevenire le possibili distorsioni della concorrenza:
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
