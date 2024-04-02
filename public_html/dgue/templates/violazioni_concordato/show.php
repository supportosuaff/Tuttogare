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
      <? if ($show_empty) echo "<strong>In caso affermativo,</strong><br><br>"; /* ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              Si &egrave; stati autorizzati dal giudice delegato ai sensi dell'articolo 110, comma 3, lett. a) del Codice?
            </th>
            <td>
              <label>Si</label>
              [ <?= ($values["_0"][1] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_0"][1] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
        </table>
        <? */ ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              La partecipazione alla procedura di affidamento &egrave; subordinata ai requisiti di cui all'art. 110, comma 6, nonostante sia stato emesso il decreto di ammissione al concordato preventivo di cui all'art. 47 D.Lgs 14/2019?
            </th>
            <td>
              <label>Si</label>
              [ <?= ($values["_0"][2] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_0"][2] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              La partecipazione alla procedura di affidamento &egrave; subordinata all'avvalimento dei requisiti di un altro soggetto in quanto non ancora depositato il decreto di ammissione al concordato preventivo cui all’art. 47 D.Lgs 14/2019, come previsto dall'art. 110 comma 4?
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
