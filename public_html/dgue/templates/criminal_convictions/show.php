<?
  if (isset($form)) {
    $to = 1;
    $valori[0] = findValues(@$dgue["ccv:Criterion"][$form["uuid"]],$json);
    if (!empty($dgue["ccv:Criterion"][$form["uuid"]][0]) && !$show_empty) {
      $i_element = 0;
      foreach($dgue["ccv:Criterion"][$form["uuid"]] AS $elemento) {
        $valori[$i_element] = findValues($elemento,$json);
        $i_element++;
      }
      $to = $i_element;
    }
    for ($count_element = 0;$count_element < $to;$count_element++) {
      if (!empty($valori[$count_element])) $values = $valori[$count_element];
      if ($count_element == 0) {
      ?>
      <div style="text-align:center">
        <label>Si</label>
        [ <?= ($values["_0"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
        &nbsp;&nbsp;
        <label>No</label>
        [ <?= ($values["_0"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
      </div><br>
      <? } else {
        ?><br><br><?
      } ?>
      <? if ($values["_0"][0] == "true" || $show_empty) { ?>
        <? if ($show_empty) echo "<strong>In caso affermativo,</strong><br><br>"; ?>
          <table width="100%">
            <tr>
              <th style="<?= $styles["th"] ?>">
                Data della condanna
              </th>
              <td>
                <?= mysql2date($values["_0"][1]); ?>
              </td>
            </tr>
            <tr>
              <th style="<?= $styles["th"] ?>">
                Motivo
              </th>
              <td>
                <?= $values["_0"][2] ?>
              </td>
            </tr>
            <tr>
              <th style="<?= $styles["th"] ?>">
                Chi &egrave; stato condannato
              </th>
              <td>
                <?= $values["_0"][3] ?>
              </td>
            </tr>
            <tr>
              <th style="<?= $styles["th"] ?>">
                Durata della condanna
              </th>
              <td>
                <?= $values["_0"][4]; ?>
              </td>
            </tr>
          </table>
          <table width="100%">
            <tr>
              <th style="<?= $styles["th"] ?>">
                L'operatore economico ha preso misure per dimostrare la propria affidabilit&agrave; ("autodisciplina")?
              </th>
              <td>
                <label>Si</label>
                [ <?= ($values["_01"][0] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                &nbsp;&nbsp;
                <label>No</label>
                [ <?= ($values["_01"][0] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
              </td>
            </tr>
             <? if ($values["_01"][0] == "true" || $show_empty) { ?>
               <? if ($show_empty) {?>
                 <tr>
                   <th colspan="2" style="<?= $styles["th"] ?>">In caso affermativo,</th>
                 </tr>
               <? } ?>
                <tr>
                  <th style="<?= $styles["th"] ?>">
                    <strong>
                      1) La sentenza di condanna definitiva ha riconosciuto l'attenuante della collaborazione come definita dalle singole fattispecie di reato?
                    </strong>
                  </th>
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
                    <strong>
                      2) La sentenza definitiva di condanna prevede una pena detentiva non superiore a 18 mesi?
                     </strong>
                  </th>
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
                    <strong>
                      3) in caso di risposta affermativa per le ipotesi 1) e/o 2):
                    </strong>
                  </th>
                  <td>
                    E' stato risarcito interamente il danno?<br>
                    <label>Si</label>
                    [ <?= ($values["_01"][4] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                    &nbsp;&nbsp;
                    <label>No</label>
                    [ <?= ($values["_01"][4] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                    <br><br>
                    <br>Si &egrave; impegnato formalmente a risarcire il danno?<br>
                    <label>Si</label>
                    [ <?= ($values["_01"][5] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                    &nbsp;&nbsp;
                    <label>No</label>
                    [ <?= ($values["_01"][5] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                  </td>
                 </tr>
                 <tr>
                   <th style="<?= $styles["th"] ?>">
                     <strong>
                       4) per le ipotesi 1) e 2 l'operatore economico ha adottato misure di carattere tecnico o organizzativo e relativi al personale idonei a prevenire ulteriori illeciti o reati?
                     </strong>
                   </th>
                    <td>
                     <label>Si</label>
                    [ <?= ($values["_01"][6] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                    &nbsp;&nbsp;
                    <label>No</label>
                    [ <?= ($values["_01"][6] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                <? if ($values["_01"][6] == "true" || $show_empty) { ?>
                    <? if ($show_empty) {
                      ?>
                      <br><strong>In caso affermativo, descrivi tali misure:</strong>
                      <?
                    }
                    ?>
                    <br>
                    <?= $values["_01"][7] ?>
                    <br>
                 <? } ?>
                 </td>
               </tr>
                 <tr>
                   <th style="<?= $styles["th"] ?>">
                     <strong>
                       5) se le sentenze di condanne sono state emesse nei confronti dei soggetti cessati di cui all'art. 80 comma 3, indicare le misure che dimostrano la completa ed effettiva dissociazione dalla condotta penalmente sanzionata:
                     </strong>
                   </th>
                    <td>
                     <?= $values["_01"][8] ?>
                   </td>
                 </tr>
                 <? if (!$show_empty && !empty($values["_01"][1])) { ?>
                       <tr>
                         <th colspan="2" style="<?= $styles["th"] ?>">Riepilogo</th>
                       </tr>
                       <tr>
                      <td colspan="2">
                        <?= $values["_01"][1] ?>
                      </td>
                    </tr>
                  <? } ?>
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
  }
?>
