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
    <? if ($values["_0"][0] == "true" || $show_empty)  { ?>
        <table width="100%">
            <tr>
              <th style="<?= $styles["th"] ?>">
                In caso affermativo, elencare i subappaltatori proposti e le parti che si intende subappaltare:
              </th>
            </tr>
            <?
              for ($c_s=1;$c_s<=3;$c_s++) {
                ?>
                <tr>
                  <th style="<?= $styles["th"] ?>">Subappaltatore <?= $c_s ?></th>
                </tr>
                <tr>
                  <td>
                    <table width="100%">
                      <tr>
                        <td>Ragione Sociale</td>
                        <td>
                          <?= $values["_0".$c_s][0] ?>
                        </td>
                      </tr>
                      <tr>
                        <td>Partita IVA</td>
                        <td>
                          <?= $values["_0".$c_s][1] ?>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <?
              }
            ?>
            <tr>
              <th style="<?= $styles["th"] ?>">
                Elencare le prestazioni o lavorazioni che si intende subappaltare e la relativa quota (espressa in percentuale) sull'mporto contrattuale:
              </th>
            </tr>
            <tr>
              <td>
                <?= $values["_0"][2] ?>
              </td>
            </tr>
          <tr>
            <th style="<?= $styles["th"] ?>">
              <? if (!empty($values["_0"][2])) { ?>
                Riepilogo
              <? } else { ?>
                In caso affermativo elencare le prestazioni o lavorazioni che si intende subappaltare e la relativa quota (espressa in percentuale) sull'importo contrattuale.<br>
                Nel caso ricorrano le condizioni di cui all'articolo 105, comma 6, del Codice, indicare la denominazione dei subappaltatori proposti:
              <? } ?>
            </th>
          </tr>
          <tr>
            <td><?=  str_replace("\n\r", "<br><br>",$values["_0"][1]); ?>
            </td>
          </tr>
        </table>
    <?
    }
  }
?>
