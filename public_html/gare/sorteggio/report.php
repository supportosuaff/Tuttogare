<?
  if (isset($codice_gara) && isset($codice_lotto)) {
    $bind=array();
    $bind[":codice_gara"]=$codice_gara;
    $gara = $pdo->bindAndExec("SELECT oggetto FROM b_gare WHERE codice = :codice_gara LIMIT 0,1", $bind)->fetchAll(PDO::FETCH_ASSOC);
    $bind[":codice_lotto"]=$codice_lotto;
    $lotto = $pdo->bindAndExec("SELECT oggetto FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto LIMIT 0,1", $bind)->fetchAll(PDO::FETCH_ASSOC);
    $sql_estrazione = "SELECT * FROM b_sorteggi WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto";
    $ris_estrazione = $pdo->bindAndExec($sql_estrazione,$bind);
    if ($ris_estrazione->rowCount()>0) {
      $estrazione = $ris_estrazione->fetch(PDO::FETCH_ASSOC);
      ?>
      <h2>Verbale Estrazione aggiudicatario - <?= mysql2datetime($estrazione["timestamp"]) ?></h2>
      <p>Gara: <strong><?= !empty($gara[0]["oggetto"]) ? $gara[0]["oggetto"] : null ?></strong></p>
      <p><strong><?= !empty($lotto[0]["oggetto"]) ? "Lotto: {$lotto[0]["oggetto"]}" : null ?></strong></p>
      <table width="100%">
      <tr><td class="etichetta">Sequenza</td><td colspan="3"><strong><?= $estrazione["sequenza"] ?></strong></td></tr>
      </table>
      <?
        $bind = array();
        $bind[":codice"] = $estrazione["codice"];
        $sql_estratti = "SELECT r_partecipanti.ragione_sociale, r_partecipanti.partita_iva, r_sorteggi.* FROM
        r_partecipanti JOIN r_sorteggi ON r_partecipanti.codice = r_sorteggi.codice_partecipante
        WHERE r_sorteggi.codice_estrazione = :codice ";
        $ris_estratti = $pdo->bindAndExec($sql_estratti,$bind);
        if ($ris_estratti->rowCount()>0) {
          ?>
          <h2>Risultati sorteggio</h2>
          <table width="100%">
            <thead>
              <tr>
                <th width="5%">#</th>
                <th width="5%">Sequenza</th>
                <th width="15%">Partita IVA</th>
                <th width="70%">Ragione sociale</th>
                <th width="15%">Esito</th>
              </tr>
            </thead>
            <?
            $estratti = $pdo->prepare("SELECT r_partecipanti.ragione_sociale, r_partecipanti.partita_iva, r_sorteggi.* FROM
            r_partecipanti JOIN r_sorteggi ON r_partecipanti.codice = r_sorteggi.codice_partecipante
            WHERE r_sorteggi.codice_estrazione = :codice AND r_sorteggi.identificativo = :id ");
            $estratti->bindValue(":codice",$estrazione["codice"]);
            $sequenza = explode(" - ",$estrazione["sequenza"]);
            $i = 0;
            foreach($sequenza AS $identificativo) {
                $i++;
                $estratti->bindValue(":id",$identificativo);
                $estratti->execute();
                $estratto = $estratti->fetch(PDO::FETCH_ASSOC);
                $style="";
                $stato_estrazione = "";
                if ($estratto["selezionato"] == "S") {
                  $style="color:#FFF; font-weight:bold; background-color:#74c27a";
                  $stato_estrazione = "Aggiudicatario";
                }
                if (!isset($html)) $style .= " !important";
                $style="style=\"" . $style . ";\"";
                ?>
                <tr <?= $style ?>>
                  <td style="text-align:center" width="5%"><?= $i ?></td>
                  <td style="text-align:center" width="5%"><?= $estratto["identificativo"] ?></td>
                  <td width="15%"><?= $estratto["partita_iva"] ?></td>
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
