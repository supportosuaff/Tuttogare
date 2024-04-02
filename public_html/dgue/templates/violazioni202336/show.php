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
      <?if ($show_empty) { echo "<strong>In caso affermativo</strong><br><br>"; } ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              Descrivi tali misure
            </th>
            <td>
              <?= $values["_0"][1] ?>
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              L'operatore economico ha preso misure per dimostrare la propria affidabilit&agrave; (\"autodisciplina\")?
            </th>
          </tr>
          <tr>
            <td>
              <label>Si</label>
              [ <?= ($values["_01"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_01"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
        </table>
    <? if ($values["_01"][0] == "true" || $show_empty) { ?>
      <?if ($show_empty) { echo "<strong>In caso affermativo</strong><br><br>"; } ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              E' stato risarcito interamente il danno?
            </th>
          </tr>
          <tr>
            <td>
              <label>Si</label>
              [ <?= ($values["_01"][2] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_01"][2] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Si è impegnato formalmente a risarcire il danno?
            </th>
          </tr>
          <tr>
            <td>
              <label>Si</label>
              [ <?= ($values["_01"][3] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_01"][3] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Ha dichiarato i fatti e le circostanze in modo globale collaborando attivamente con le autorità investigative?
            </th>
          </tr>
          <tr>
            <td>
              <label>Si</label>
              [ <?= ($values["_01"][4] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_01"][4] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Indicare le misure di carattere tecnico o organizzativo e relative al personale, eventualmente adottate, idonee a prevenire ulteriori illeciti o reati, la relativa documentazione e se disponibile elettronicamente indicare (indirizzo web, autorit&agrave; o organismo di emanazione, riferimento preciso della documentazione):
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_01"][1] ?>
            </td>
          </tr>
        </table>
    <? }
    }
  }
?>
