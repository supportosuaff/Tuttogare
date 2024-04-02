<?
  if (isset($codice_gara) && isset($codice_lotto)) {
    $bind=array();
    $bind[":codice_gara"]=$codice_gara;
    $gara = $pdo->bindAndExec("SELECT oggetto FROM b_gare WHERE codice = :codice_gara LIMIT 0,1", $bind)->fetchAll(PDO::FETCH_ASSOC);
    $bind[":codice_lotto"]=$codice_lotto;
    $lotto = $pdo->bindAndExec("SELECT oggetto FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto LIMIT 0,1", $bind)->fetchAll(PDO::FETCH_ASSOC);
    $sql_estrazione = "SELECT * FROM b_estrazioni_campioni WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto";
    $ris_estrazione = $pdo->bindAndExec($sql_estrazione,$bind);
    if ($ris_estrazione->rowCount()>0) {
      $estrazione = $ris_estrazione->fetch(PDO::FETCH_ASSOC);
      ?>
      <h2>Verbale Estrazione controlli a campione- <?= mysql2datetime($estrazione["timestamp"]) ?></h2>
      <p>Gara: <strong><?= !empty($gara[0]["oggetto"]) ? $gara[0]["oggetto"] : null ?></strong></p>
      <p><strong><?= !empty($lotto[0]["oggetto"]) ? "Lotto: {$lotto[0]["oggetto"]}" : null ?></strong></p>
      <table width="100%">
      <tr>
        <td class="etichetta">Partecipanti richiesti</td><td><?= $estrazione["numero_partecipanti"] ?></td>
      </tr>
      <tr><td class="etichetta">Sequenza</td><td colspan="3"><strong><?= $estrazione["sequenza"] ?></strong></td></tr>
      </table>
      <?
        $bind = array();
        $bind[":codice"] = $estrazione["codice"];
        $sql_estratti = "SELECT r_partecipanti.ragione_sociale, r_partecipanti.partita_iva, r_estrazioni_campioni.* FROM
        r_partecipanti JOIN r_estrazioni_campioni ON r_partecipanti.codice = r_estrazioni_campioni.codice_partecipante
        WHERE r_estrazioni_campioni.codice_estrazione = :codice ORDER BY identificativo = 0, identificativo ";
        $ris_estratti = $pdo->bindAndExec($sql_estratti,$bind);
        if ($ris_estratti->rowCount()>0) {
          ?>
          <h2>Risultati sorteggio</h2>
          <table width="100%">
            <thead>
              <tr>
                <th width="10%">#</th>
                <th width="15%">Partita IVA</th>
                <th width="70%">Ragione sociale</th>
                <th width="15%">Esito</th>
              </tr>
            </thead>
            <?
              while($estratto = $ris_estratti->fetch(PDO::FETCH_ASSOC)) {
                $style="";
                $stato_estrazione = "";
                if ($estratto["selezionato"] == "S") {
                  if ($estratto["identificativo"]==0) {
                    $style="color:#FFF; font-weight:bold; background-color:#2fccc7";
                    $stato_estrazione = "Selezionato automaticamente";
                  } else {
                    $style="color:#FFF; font-weight:bold; background-color:#74c27a";
                    $stato_estrazione = "Selezionato";
                  }
                }
                if (!isset($html)) $style .= " !important";
                $style="style=\"" . $style . ";\"";
                ?>
                <tr <?= $style ?>>
                  <td width="10%"><?= $estratto["identificativo"] ?></td>
                  <td width="70%"><?= $estratto["partita_iva"] ?></td>
                  <td width="70%"><?= $estratto["ragione_sociale"] ?></td>
                  <td width="15%"><?= $stato_estrazione ?></td>
                </tr>
                <?
              }
            ?>
          </table>
          <?
        }
      }
    }
?>
