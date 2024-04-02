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
        <?if ($show_empty) { echo "<strong>In caso affermativo,</strong><br><br>"; } ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              si applica quanto previsto dell’art.95 del Codice della Crisi di Impresa (D.lgs 14/2019)?
            </th>
          </tr>
          <tr>
            <td style="text-align:center">
              <label>Si</label>
              [ <?= ($values["_0"][2] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_0"][2] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              <? if ($values["_0"][2] == "true" || $show_empty) { ?>
                <br><br><?if ($show_empty) { echo "<strong>In caso affermativo</strong>"; } ?>
                <strong>indicare il provvedimento autorizzativo</strong><br><br>
                <?= $values["_0"][3] ?>
              <? } ?>
            </td>
          </tr>
        </table>
      <?
    }
  }
?>
