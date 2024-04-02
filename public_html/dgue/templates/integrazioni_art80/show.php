<?
  if (isset($form)) {
    ?>
    <div style="text-align:center">
      <label>Si</label>
      [ <?= ($values["_0"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      &nbsp;&nbsp;
      <label>No</label>
      [ <?= ($values["_0"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      <? if ($form["uuid"] == "PERS_ART80_C5_i") { ?>
        <label>Non &egrave; tenuto alla disciplina legge 68/1999</label>
        [ <?= ($values["_0"][0] == "na") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      <? } ?>
    </div><br>
    <? if ($values["_0"][0] != "false" || $show_empty) {
      if ($show_empty) echo "<strong>In caso affermativo,</strong> <br><br>"?>
        <table width="100%">
          <? if ($form["uuid"] == "PERS_ART80_C5_h") {
            ?>
            <tr>
              <th style="<?= $styles["th"] ?>">
                Indicare la data dell'accertamento definitivo e l'autorit&agrave; o l'organismo di emanazione:
              </th>
            </tr>
            <tr>
              <td>
                <?= $values["_0"][2] ?>
              </td>
            </tr>
            <tr>
              <th style="<?= $styles["th"] ?>">
                La violazione &egrave; stata rimossa?
              </th>
            </tr>
            <tr>
              <td>
                <label>Si</label>
                [ <?= ($values["_0"][3] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                &nbsp;&nbsp;
                <label>No</label>
                [ <?= ($values["_0"][3] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              </td>
            </tr>
            <?
          } ?>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Descrivi tali misure<? if ($form["uuid"] == "PERS_ART80_C5_i") { ?>, se del caso, le motivazioni per cui l'operatore non &egrave; tenuto alla disciplina legge 68/1999<? } ?> e se &egrave; disponibile elettronicamente la documentazione pertinente indicare (indirizzo web, autorit&agrave; o organismo di emanazione, riferimento preciso della documentazione):
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][1] ?>
            </td>
          </tr>
        </table>
    <? } ?>
    <?
  }
?>
