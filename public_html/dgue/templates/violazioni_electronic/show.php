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
      <? if ($show_empty) echo "<strong>In caso affermativo,</strong><br><br>"; ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              Descrivi tali misure
            </th>
            <td>
              <?= $values["_0"][1] ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Si &egrave; stati autorizzati dal giudice delegato ai sensi dell'articolo 110, comma 3, lett. a) del Codice?
            </th>
            <td>
              <label>Si</label>
              [ <?= ($values["_0"][3] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_0"][3] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              La partecipazione alla procedura di affidamento &egrave; subordinata ai sensi dell'art. 110, comma 5, all'avvalimento di altro operatore econimico?
            </th>
            <td>
              <label>Si</label>
              [ <?= ($values["_0"][4] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_0"][4] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
           <? if ($values["_0"][4] == "true" || $show_empty) { ?>
          <tr>
            <th style="<?= $styles["th"] ?>">
              <? if ($show_empty) echo "<strong>In caso affermativo,</strong><br>"; ?>Indicare l'impresa ausiliaria
            </th>
            <td>
              <?= $values["_0"][5] ?>
            </td>
          </tr>
          <? } ?>
          <? if (!empty($values["_0"][2])) { ?>
            <tr>
              <th style="<?= $styles["th"] ?>">
                Riepilogo
              </th>
              <td>
                <?= $values["_0"][2] ?>
              </td>
            </tr>
          <? } ?>
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
