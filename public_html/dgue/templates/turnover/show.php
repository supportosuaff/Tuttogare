<?
  if (isset($form)) {
    ?>
        <table width="100%">
          <?
          for ($i=0;$i<5;$i++) {
            if (!empty($values["_0".$i][0]) || $show_empty) {
          ?>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Anno
            </th>
            <td>
              <?= $values["_0".$i][0] ?>
            </td>
            <th style="<?= $styles["th"] ?>">
              Fatturato
            </th>
            <td>
              <?= $values["_0".$i][1]; ?>
              <?= $valute[$values["_0".$i][2]] ?>
            </td>
          </tr>
          <? }
        } ?>
        </table>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              Queste informazioni sono disponibili elettronicamente?
            </th>
            <td>
              <label>Si</label>
              [ <?= ($values["_1"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_1"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
          <? if ($values["_1"][0] == "true" || $show_empty) { ?>
          <tr>
            <th style="<?= $styles["th"] ?>">URL</th>
            <th style="<?= $styles["th"] ?>">Codice</th>
          </tr>
          <tr>
            <td>
              <a href="<?= $values["_1"][1]; ?>" target="_blank" title="Sito esterno"><?= $values["_1"][1]; ?></a>
            </td>
            <td>
              <?= $values["_1"][2]; ?>
            </td>
          </tr>
          <? } ?>
        </table>
    <?
  }
?>
