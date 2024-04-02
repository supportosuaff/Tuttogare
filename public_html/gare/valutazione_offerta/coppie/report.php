<?
  $generale = [];
  if (isset($codice_criterio) && isset($partecipanti) && isset($commissari)) {
    $criterio = $criteri[$codice_criterio];
    $totali = [];
    $riparametra = true;
    if ($_POST["riparametraMedie"] == "N") $riparametra = false; 
    ?>
    <h2><?= $criterio["descrizione"] ?> - <?= $criterio["punteggio"] ?> max</h2>
    <hr>
    <?
    $count = 0;
    foreach ($commissari as $commissario) {
      $count++;
      if (count($commissari) > 1) {
        ?>
        <h3><?= $count ?> - <?= $commissario["cognome"] . " " . $commissario["nome"] ?></h3>
        <?
      }
      ?>
      <table width="100%">
        <tr>
          <th style="text-align:center" class="etichetta">#</th>
        <?
          $row = "A";
          $i = 0;
          $columns = $partecipanti;
          next($columns);
          next($columns);
          $col = "A";
          foreach($columns AS $part_b)   {
            if ($col != "A") {
            ?>
              <th style="text-align:center" class="etichetta"><?= $col ?></th>
            <?
            }
            $col++;
          }
          ?>
        </tr>
        <?
          $sql_value = "SELECT * FROM b_confronto_coppie
                        WHERE codice_criterio = :codice_criterio
                        AND codice_commissario = :codice_commissario
                        AND codice_partecipante_1 = :codice_row
                        AND codice_partecipante_2 = :codice_col ";
          $ris_value = $pdo->prepare($sql_value);
          $ris_value->bindValue(":codice_criterio",$codice_criterio);
          $ris_value->bindValue(":codice_commissario",$commissario["codice"]);

          $last = end($partecipanti);
          foreach($partecipanti AS $part_a) {
            $ris_value->bindValue(":codice_row",$part_a["codice"]);
            reset($columns);
            if ($part_a["codice"]!=$last["codice"]) {
            ?>
            <tr>
              <th style="text-align:center" class="etichetta"><?= $row ?></th>
              <?
                $col = "A";
                foreach($columns AS $part_b) {
                  $ris_value->bindValue(":codice_col",$part_b["codice"]);
                  $ris_value->execute();
                  $val = [];
                  if ($ris_value->rowCount() == 1) {
                    $value = $ris_value->fetch(PDO::FETCH_ASSOC);
                    $val[0] = $value["punteggio_partecipante_1"];
                    $val[1] = $value["punteggio_partecipante_2"];
                    if (!isset($totali[$commissario["codice"]])) $totali[$commissario["codice"]] = [];
                    if (!isset($totali[$commissario["codice"]][$part_a["codice"]])) $totali[$commissario["codice"]][$part_a["codice"]] = 0;
                    if (!isset($totali[$commissario["codice"]][$part_b["codice"]])) $totali[$commissario["codice"]][$part_b["codice"]] = 0;
                    $totali[$commissario["codice"]][$part_a["codice"]] += $val[0];
                    $totali[$commissario["codice"]][$part_b["codice"]] += $val[1];
                  }
                  if ($col != "A") {
                    if ($col > $row) {
                      echo "<td style='text-align:center'>";
                      if ($val[0] > $val[1]) {
                        echo $part_a["id"].$val[0];
                      } else if ($val[1] > $val[0]) {
                        echo $part_b["id"].$val[1];
                      } else {
                        echo $part_a["id"].$part_b["id"]."1";
                      }
                    } else {
                      echo "<td class='etichetta'>";
                    }
                    echo "</td>";
                  }
                  $col++;
                }
              ?>
            </tr>
            <?
            $row++;
            }
          }
        ?>
      </table>
    <?
    }
    ?>
    <h3>Riepilogo</h3>
    <table width="100%">
      <thead>
        <tr>
          <td style="text-align:center" class="etichetta">#</td>
          <td class="etichetta">Partita IVA</td>
          <td class="etichetta">Ragione Sociale</td>
          <?
            if (count($commissari) > 1) {
              $cont = 1;
              foreach($commissari AS $codice_commissario) {
                ?>
                <td style="text-align:center" class="etichetta"><?= $cont ?></td>
                <?
                $cont++;
              }
              ?>
              <td style="text-align:center" class="etichetta" <?= ($riparametra) ? 'colspan="2"' : '' ?>>Media</td>
              <?
            }
          ?>
          <td style="text-align:center" class="etichetta">Valutazione</td>
        </tr>
      </thead>
      <tbody>
        <?
          $i = "A";
          $medie = [];
          foreach($totali AS $key => $sub) {
            $tmpMedie = normalizza($sub,1,$criterio["decimali"]);
            foreach($tmpMedie AS $cod_p => $val) {
              if (!isset($medie[$cod_p])) $medie[$cod_p] = 0;
              $medie[$cod_p] += $val;
            }
          }
          foreach($medie AS $cod_p => $media) $medie[$cod_p] = $media / count($commissari);
          if ($riparametra) $ripar = normalizza($medie,1,999);
          foreach($partecipanti AS $codice_partecipante => $partecipante) {
            $totale_partecipante = 0;
            ?>
            <tr>
              <td style="text-align:center"><?= $partecipante["id"] ?></td>
              <td style="text-align:center"><?= $partecipante["partita_iva"] ?></td>
              <td><?= (!empty($partecipante["tipo"])) ? "<strong>RAGGRUPPAMENTO</strong> - " : "" ?><?= $partecipante["ragione_sociale"] ?></td>
              <?
                $media = 0;
                foreach($totali AS $sub) {
                  $max = max($sub);
                  $coef = truncate($sub[$partecipante["codice"]] / $max,$criterio["decimali"]);
                  ?>
                  <td style="text-align:center">
                    <strong><?= $coef ?></strong><br>
                    <small>(<?= $sub[$partecipante["codice"]] ?>)</small>
                  </td>
                  <?
                }
                if (count($commissari) > 1) {
                  if ($riparametra) {
                    $punteggio = truncate($ripar[$partecipante["codice"]] * $criterio["punteggio"],$criterio["decimali"]);
                  } else {
                    $punteggio = truncate($medie[$partecipante["codice"]] * $criterio["punteggio"],$criterio["decimali"]);
                  }
                  ?>
                  <td style="text-align:center">
                    <?= truncate($medie[$partecipante["codice"]],$criterio["decimali"]) ?>
                  </td>
                  <? if ($riparametra) { ?>
                    <td style="text-align:center">
                      <?= $ripar[$partecipante["codice"]] ?>
                    </td>
                  <? } ?>
                  <td style="text-align:center">
                    <strong><?= ($punteggio) ?></strong>
                  </td>
                  <?
                }
              ?>
            </tr>
            <?
            $i++;
          }
        ?>
      </tbody>
    </table>
    <?
  }
?>
