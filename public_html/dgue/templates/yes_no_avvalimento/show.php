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
      <? if ($show_empty) {
        ?>
        <strong>In caso affermativo,</strong><br>
        <strong>Presentare per ciascuna impresa ausiliaria un DGUE distinto, debitamente compilato e firmato dai soggetti interessati, con le informazioni richieste dalle sezioni A e B della presente parte, dalla parte III, dalla parte IV ove pertinente e dalla parte VI.<br><br>
Si noti che dovrebbero essere indicati anche i tecnici o gli organismi tecnici che non facciano parte integrante dell'operatore economico, in particolare quelli responsabili del controllo della qualit&agrave; e, per gli appalti pubblici di lavori, quelli di cui l'operatore economico disporr&agrave; per l'esecuzione dell'opera.</strong><br><br>
      <?
      }
      ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              Indicare la denominazione degli operatori economici di cui si intende avvalersi:
            </th>
            <td>
              <?= $values["_0"][1] ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Indicare i requisiti oggetto di avvalimento:
            </th>
            <td>
              <?= $values["_0"][2] ?>
            </td>
          </tr>
        </table>
    <?
    }
  }
?>
