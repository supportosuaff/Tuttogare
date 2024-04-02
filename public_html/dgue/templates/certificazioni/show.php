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
    <? if ($values["_0"][0] == "false" || $show_empty) { ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              In caso negativo, spiegare perch&eacute; e precisare di quali altri mezzi di prova relativi al programma di garanzia della qualit&agrave; si dispone:
            </th>
            <td>
              <?= $values["_0"][1] ?>
            </td>
          </tr>
        </table>
    <? } ?>
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
