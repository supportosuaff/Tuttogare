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
            <td width="10">Valutazione</td>
          </tr>
        </thead>
        <tbody>
          <?
            $i = "A";
            $coefficienti = ["0",
                             "0.1",
                             "0.15",
                             "0.2",
                             "0.25",
                             "0.3",
                             "0.35",
                             "0.4",
                             "0.45",
                             "0.5",
                             "0.55",
                             "0.6",
                             "0.65",
                             "0.7",
                             "0.75",
                             "0.8",
                             "0.85",
                             "0.9",
                             "0.95",
                             "1"];
            $sql_value = "SELECT * FROM b_coefficienti_commissari
                          WHERE codice_partecipante = :codice_partecipante
                          AND codice_criterio = :codice_criterio
                          AND codice_commissario = :codice_commissario ";
            $ris_value = $pdo->prepare($sql_value);
            $ris_value->bindValue(":codice_criterio",$criterio["codice"]);
            $ris_value->bindValue(":codice_commissario",$_SESSION["codice_commissario"]);
            foreach($partecipanti AS $partecipante) {
              $valutazione = "";
              $ris_value->bindValue(":codice_partecipante",$partecipante["codice"]);
              $ris_value->execute();
              if ($ris_value->rowCount() == 1) $valutazione = floatval($ris_value->fetch(PDO::FETCH_ASSOC)["coefficiente"]);
              ?>
              <tr>
                <td><?= $i ?></td>
                <td style="text-align:center"><?= $partecipante["partita_iva"] ?></td>
                <td><?= (!empty($partecipante["tipo"])) ? "<strong>RAGGRUPPAMENTO</strong> - " : "" ?><?= $partecipante["ragione_sociale"] ?></td>
                <td><a target="_blank" href="/pannello-commissione/download.php?codice=<?= $gara["codice"] ?>&busta=tecnica&partecipante=<?= $partecipante["codice"] ?>" class="btn-round btn-primary" title="Download Busta">
                    <span class="fa fa-download"></span>
                  </a>
                </td>
                <td>
                  <input name="partecipante[<?= $partecipante["codice"] ?>]" id="partecipante-<?= $partecipante["codice"] ?>" title="Valutazione <?= $i ?>" value="<?= $valutazione ?>"
                    rel="N;0;0;N;1;<=">
                  <?
                    /*
                      <select name="partecipante[<?= $partecipante["codice"] ?>]" id="partecipante-<?= $partecipante["codice"] ?> title="Valutazione <?= $i ?>">
                      <option value="">Seleziona...</option>
                      <?
                        foreach($coefficienti AS $value) {
                          ?>
                          <option <?= ($valutazione === $value) ? "selected" : "" ?>><?= $value ?></option>
                          <?
                        }
                      ?>
                      </select>
                    */
                  ?>
                </td>
              </tr>
              <?
              $i++;
            }
          ?>
        </tbody>
      </table>
      <input type="submit" class="submit_big" value="Salva">
      <a href="pannello.php?codice=<?= $gara["codice"] ?>&codice_lotto=<?= $codice_lotto ?>" class="submit_big" style="background-color:#333">Indietro</a>
    </form>
    <?
  }
?>
