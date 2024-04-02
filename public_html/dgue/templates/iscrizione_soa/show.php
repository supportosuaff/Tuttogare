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
    <?
     if ($values["_0"][0] == "true" || $show_empty) { ?>
      <? if ($show_empty) {
        ?>
        <strong>In caso affermativo:</strong><br>
        <?
      }
      ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
                a) Indicare gli estremi dell'attestazione (denominazione dell’Organismo di attestazione ovvero Sistema di qualificazione, numero e data dell'attestazione):
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][1]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              b) Se l'attestazione di qualificazione &egrave; disponibile elettronicamente, indicare:
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][2]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              c) Indicare i riferimenti in base ai quali &egrave; stata ottenuta l'iscrizione o la certificazione e, se pertinente, la classificazione ricevuta nell'elenco ufficiale:
            </th>
          </tr>
          <tr>
            <td>
              <?= $values["_0"][3]; ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              d) L'iscrizione o la certificazione comprende tutti i criteri di selezione richiesti?
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
      <? }
  }
?>
