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
      <? if ($show_empty) echo "<strong>In caso affertmativo,<br><br></strong>"; ?>
        <table width="100%">
          <tr>
            <th style="<?= $styles["th"] ?>">
              Paese Interessato
            </th>
            <td>
            <?= $paesi[$values["_0"][1]] ?>
            </td>
          </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              Di quale importo si tratta
            </th>
            <td>
              <?= $values["_0"][2]; ?>
              <?= $valute[$values["_0"][3]] ?>
            </td>
          </tr>
          <tr>
            <th colspan="2" style="<?= $styles["th"] ?>">
              Tale inottemperanza &egrave; stata accertata in modo diverso da una sentenza giudiziaria o decisione amministrativa?
            </th>
          </tr>
          <tr>
            <td colspan="2" style="text-align:center">
              <label>Si</label>
              [ <?= ($values["_00"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_00"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
          <? if ($values["_00"][0] == "true" || $show_empty) { ?>
          <? if ($show_empty) { ?>
            <tr>
              <th colspan="2" style="<?= $styles["th"] ?>">
                Nel caso l'inottemperanza sia stata accertata in modo diverso da una sentenza giudiziaria o decisione amministrativa?
              </th>
            </tr>
          <? } ?>
          <tr>
            <th colspan="2" style="<?= $styles["th"] ?>">
              Indicare in quale modo &egrave; stata accertata l'inottemperanza
            </th>
          </tr>
          <tr>
            <td colspan="2">
              <?= $values["_00"][1] ?>
            </td>
          </tr>
          <tr>
            <th colspan="2" style="<?= $styles["th"] ?>">
            L'operatore economico ha ottemperato ai suoi obblighi, pagando o impegnandosi in modo vincolante a pagare le imposte o i contributi previdenziali dovuti, compresi eventuali interessi o multe,
            o il suo debito si &egrave; comunque integralmente estinto, essendosi perfezionati il pagamento, l'impegno, o l'estinzione anteriormente alla scadenza del termine per la presentazione delle offerte?
            (articolo 95 comma 2 del Codice)?
            </th>
          </tr>
          <tr>
            <td colspan="2" style="text-align:center">
              <label>Si</label>
              [ <?= ($values["_02"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              &nbsp;&nbsp;
              <label>No</label>
              [ <?= ($values["_02"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
            </td>
          </tr>
          <? if ($values["_02"][0] == "true" || $show_empty) { ?>
            <tr>
                <th colspan="2" style="<?= $styles["th"] ?>">
                <? if ($show_empty) echo "In caso affermativo, <br>"; ?>
                Descrivere tali misure, specificando se è intervenuto: pagamento, compensazione o estinzione
              </th>
            </tr>
            <tr>
              <td colspan="2">
                <?= $values["_02"][1] ?>
              </td>
            </tr>
            <? }
          } ?>
          <? if ($values["_00"][0] == "false" || $show_empty) { ?>
            <tr class="sub_form_<?= $form["codice"] ?>_false" <? if ($values["_00"][0] != "false") echo "style='display:none'" ?>>
              <th colspan="2" style="<?= $styles["th"] ?>">
              Se l'inottemperanza &egrave; stata accertata mediante una sentenza giudiziaria o decisione amministrativa, tale sentenza o decisione &egrave; definitiva e vincolante?
            </th>
            </tr>
            <tr>
              <td colspan="2" style="text-align:center">
                <label>Si</label>
                [ <?= ($values["_01"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                &nbsp;&nbsp;
                <label>No</label>
                [ <?= ($values["_01"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              </td>
            </tr>
            <tr>
                <th colspan="2" style="<?= $styles["th"] ?>">
                Indicare la data della sentenza di condanna o della decisione
              </th>
            </tr>
            <tr>
              <td colspan="2">
                <?= mysql2date($values["_01"][1]); ?>
              </td>
            </tr>
            <tr>
              <th colspan="2" style="<?= $styles["th"] ?>">
                Nel caso di una sentenza di condanna, se stabilita direttamente nella sentenza di condanna, la durata del periodo d'esclusione
              </th>
            </tr>
            <tr>
              <td colspan="2">
                <?= $values["_01"][2]; ?>
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
