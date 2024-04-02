<?
  if (isset($_SESSION["codice_commissario"]) && !empty($partecipanti) && isset($criterio)) {
    ?>
    <form action="save.php" rel="validate" method="post">
      <input type="hidden" name="codice_gara" value="<?= $gara["codice"] ?>">
      <input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
      <input type="hidden" name="codice_criterio" value="<?= $criterio["codice"] ?>">
      <table width="100%">
        <thead>
          <tr>
            <td width="10">#</td>
            <td width="120">Partita IVA</td>
            <td>Ragione Sociale</td>
            <td width="10">Offerta</td>
          </tr>
        </thead>
        <tbody>
          <?
            $i = "A";
            foreach($partecipanti AS $partecipante) {
              ?>
              <tr>
                <td><?= $i ?></td>
                <td style="text-align:center"><?= $partecipante["partita_iva"] ?></td>
                <td><?= (!empty($partecipante["tipo"])) ? "<strong>RAGGRUPPAMENTO</strong> - " : "" ?><?= $partecipante["ragione_sociale"] ?></td>
                <td><a target="_blank" href="/pannello-commissione/download.php?codice=<?= $gara["codice"] ?>&busta=tecnica&partecipante=<?= $partecipante["codice"] ?>" class="btn-round btn-primary" title="Download Busta">
                    <span class="fa fa-download"></span>
                  </a>
                </td>
              </tr>
              <?
              $i++;
            }
          ?>
        </tbody>
      </table>
      <table width="100%">
        <tr>
          <th class="etichetta">#</th>
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
              <th class="etichetta"><?= $col ?></th>
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
          $ris_value->bindValue(":codice_criterio",$criterio["codice"]);
          $ris_value->bindValue(":codice_commissario",$_SESSION["codice_commissario"]);

          $last = end($partecipanti);
          foreach($partecipanti AS $part_a) {
            $ris_value->bindValue(":codice_row",$part_a["codice"]);
            reset($columns);
            if ($part_a["codice"]!=$last["codice"]) {
            ?>
            <tr>
              <th class="etichetta"><?= $row ?></th>
              <?
                $col = "A";
                foreach($columns AS $part_b) {
                  $ris_value->bindValue(":codice_col",$part_b["codice"]);
                  $ris_value->execute();
                  $value = "";
                  if ($ris_value->rowCount() == 1) {
                    $value = $ris_value->fetch(PDO::FETCH_ASSOC);
                    $value = $value["punteggio_partecipante_1"] . "_" . $value["punteggio_partecipante_2"];
                  }
                  if ($col != "A") {
                    echo "<td>";
                    if ($col > $row) {
                    ?>
                    <select name="valutazione[<?= $part_a["codice"] ?>_<?= $part_b["codice"] ?>]" id="val_<?= $part_a["codice"] ?>_<?= $part_b["codice"] ?>" title="<?= $row ?> - <?= $col ?>">
                      <option value="">Seleziona...</option>
                      <?
                        for($round=1;$round<=2;$round++) {
                          for($i=2;$i<=6;$i++) {
                            if ($round==1) {
                              $val = $i . "_0";
                              $label = $row . " +".$i;
                            } else {
                              $val = "0_".$i;
                              $label = $col . " +".$i;
                            }
                            ?>
                            <option style="text-align:center" value="<?= $val ?>" <?= ($val==$value) ? "selected" : "" ?>><?= $label ?></option>
                            <?
                          }
                          if ($round == 1) {
                          ?>
                            <option style="text-align:center" value="1_1" <?= ("1_1"==$value) ? "selected" : "" ?>>Equivalenti - 1</option>
                          <?
                          }
                        }
                      ?>
                    </select>
                    <?
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
      <input type="submit" class="submit_big" value="Salva">
      <a href="pannello.php?codice=<?= $gara["codice"] ?>&codice_lotto=<?= $codice_lotto ?>" class="submit_big" style="background-color:#333">Indietro</a>
    </form>
    <?
  }
?>
