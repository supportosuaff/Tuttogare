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
      if (!empty($valori[$count_element])) { $values = $valori[$count_element]; }
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
         <?if ($show_empty) { echo "<strong>In caso affermativo,</strong><br><br>"; } ?>
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
               Chi è stato condannato
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
             <tr>
               <th style="<?= $styles["th"] ?>">
               E’ stata comminata la pena accessoria della incapacità a contrattare con la pubblica amministrazione"); ?>?
               </th>
               <td>
                 <label>Si</label>
                 [ <?= ($values["_0"][5] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                 &nbsp;&nbsp;
                 <label>No</label>
                 [ <?= ($values["_0"][5] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
               </td>
             </tr>
             <? if ($values["_0"][5] == "true" || $show_empty) { ?>
                <? if ($show_empty) {?>
                  <tr>
                    <th colspan="2" style="<?= $styles["th"] ?>">In caso affermativo,</th>
                  </tr>
                <? } ?>
               <tr>
                 <th style="<?= $styles["th"] ?>">
                   <strong>
                     1) Durata della pena accessoria
                   </strong>
                 </th>
                 <td>
                  <?= $values["_0"][6] ?>
                 </td>
               </tr>
             <? } ?>
           </table>
           <table width="100%">
             <tr>
               <th style="<?= $styles["th"] ?>">
               L'operatore economico ha preso misure per dimostrare la propria affidabilità ("autodisciplina")?"); ?>
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
                       1) E' stato risarcito interamente il danno?
                     </strong>
                   </th>
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
                     <strong>
                       2) Si è impegnato formalmente a risarcire il danno?
                     </strong>
                   </th>
                   <td>
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
                       3) Ha chiarito i fatti e le circostanze in modo globale e collaborando attivamente con le autorità investigative?
                     </strong>
                   </th>
                   <td>
                    <label>Si</label>
                    [ <?= ($values["_01"][9] == "true") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                    &nbsp;&nbsp;
                    <label>No</label>
                    [ <?= ($values["_01"][9] == "false") ? "X" : "&nbsp;&nbsp;&nbsp;" ?> ]
                   </td>
                 </tr>
                  <tr>
                    <th style="<?= $styles["th"] ?>">
                      <strong>
                        4) L'operatore economico ha adottato misure di carattere tecnico o organizzativo e relativi al personale idonei a prevenire ulteriori illeciti o reati?
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
