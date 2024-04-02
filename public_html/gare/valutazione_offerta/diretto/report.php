<?
  $generale = [];
  if (isset($codice_criterio) && isset($partecipanti) && isset($commissari)) {
    $criterio = $criteri[$codice_criterio];
    $sql_value = "SELECT b_coefficienti_commissari.coefficiente, b_coefficienti_commissari.codice_partecipante, b_coefficienti_commissari.codice_commissario
                  FROM b_coefficienti_commissari
                  WHERE b_coefficienti_commissari.codice_gara = :codice_gara
                  AND b_coefficienti_commissari.codice_lotto = :codice_lotto
                  AND b_coefficienti_commissari.codice_criterio = :codice_criterio";
    $bind = array();
    $bind[":codice_gara"] = $_POST["codice_gara"];
    $bind[":codice_lotto"] = $_POST["codice_lotto"];
    $bind[":codice_criterio"] = $criterio["codice"];
    $ris_values = $pdo->bindAndExec($sql_value,$bind);
    $medie = $totali = $valutazioni = [];
    $riparametra = true;
    if ($_POST["riparametraMedie"] == "N") $riparametra = false; 
    if ($ris_values->rowCount() > 0) {
      while($coef = $ris_values->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($valutazioni[$coef["codice_partecipante"]])) $valutazioni[$coef["codice_partecipante"]] = [];
        $valutazioni[$coef["codice_partecipante"]][$coef["codice_commissario"]] = $coef["coefficiente"];
        if (!isset($totali[$coef["codice_partecipante"]])) $totali[$coef["codice_partecipante"]] = 0;
        $totali[$coef["codice_partecipante"]] += $coef["coefficiente"];
      }
      foreach($totali AS $cod_partecipante => $somma) {
        $medie[$cod_partecipante] = $somma/count($commissari);
      }
    }
    if ($riparametra) $ripar = normalizza($medie,1,999);
    ?>
    <h2><?= $criterio["descrizione"] ?> - <?= $criterio["punteggio"] ?> max</h2>
    <hr>
      <table width="100%">
        <thead>
          <tr>
            <td style="text-align:center" class="etichetta">#</td>
            <td class="etichetta" width="120">Partita IVA</td>
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
          foreach($partecipanti AS $codice_partecipante => $partecipante) {
            $totale_partecipante = 0;
            ?>
            <tr>
              <td style="text-align:center"><?= $partecipante["id"] ?></td>
              <td style="text-align:center"><?= $partecipante["partita_iva"] ?></td>
              <td><?= (!empty($partecipante["tipo"])) ? "<strong>RAGGRUPPAMENTO</strong> - " : "" ?><?= $partecipante["ragione_sociale"] ?></td>
              <?
                $count = 0;
                foreach ($commissari as $commissario) {
                  $count++;
                  ?>
                  <td style="text-align:center">
                    <strong><?= floatval($valutazioni[$codice_partecipante][$commissario["codice"]]) ?></strong><br>
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
                    <?= $medie[$partecipante["codice"]] ?>
                  </td>
                  <? if ($riparametra) {  ?>
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
