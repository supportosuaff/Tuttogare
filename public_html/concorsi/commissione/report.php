<?
  if (isset($codice_gara)) {
    $bind=array();
    $bind[":codice_gara"]=$codice_gara;
    $sql_estrazione = "SELECT * FROM b_estrazioni_commissioni_concorsi WHERE codice_gara = :codice_gara";
    $ris_estrazione = $pdo->bindAndExec($sql_estrazione,$bind);
    if ($ris_estrazione->rowCount()>0) {
      $estrazione = $ris_estrazione->fetch(PDO::FETCH_ASSOC);
      ?>
      <h2>Verbale Estrazione commissione - <?= mysql2datetime($estrazione["timestamp"]) ?></h2>
      <p>Concorso: <strong><?= !empty($gara[0]["oggetto"]) ? $gara[0]["oggetto"] : null ?></strong></p>
      <table width="100%">
      <?
      if ($estrazione["codice_albo"] != 0) {
        $bind = array();
        $bind[":codice"] = $estrazione["codice_albo"];
        $sql_bando = "SELECT * FROM b_albi_commissione WHERE codice = :codice";
        $ris_bando = $pdo->bindAndExec($sql_bando,$bind);
        if ($ris_bando->rowCount()>0) {
          $bando = $ris_bando->fetch(PDO::FETCH_ASSOC);
          ?>
          <tr><td class="etichetta">Albo di riferimento</td><td colspan="3"><?= $bando["oggetto"] ?></td></tr>
          <?
        }
      }
      ?>
      <tr>
        <td class="etichetta">Partecipanti richiesti</td><td><?= $estrazione["componenti"] ?></td>
        <td class="etichetta">di cui interni</td><td><?= $estrazione["interni"] ?><? if ($estrazione["integrazione"]=="S") echo " <strong>(alcuni interni selezionati per insufficienza di candidati esterni.)</strong>" ?></td>
      </tr>
      <tr>
        <td class="etichetta">Sequenza Interni</td><td><strong><?= $estrazione["sequenza_i"] ?></strong></td>
        <td class="etichetta">Sequenza Esterni</td><td><strong><?= $estrazione["sequenza_e"] ?></strong></td>
      </tr>
      </table>
      <?
        $bind = array();
        $bind[":codice"] = $estrazione["codice"];
        $sql_estratti = "SELECT b_commissari_albo.codice_fiscale, b_commissari_albo.nome, b_commissari_albo.cognome, r_estrazioni_commissioni_concorsi.* FROM
        b_commissari_albo JOIN r_estrazioni_commissioni_concorsi ON b_commissari_albo.codice = r_estrazioni_commissioni_concorsi.codice_commissario
        WHERE r_estrazioni_commissioni_concorsi.codice_estrazione = :codice ORDER BY tipo, escluso, identificativo = 0, identificativo ";
        $ris_estratti = $pdo->bindAndExec($sql_estratti,$bind);
        if ($ris_estratti->rowCount()>0) {
          ?>
          <h2>Risultati sorteggio</h2>
          <table width="100%">
            <?
              while($estratto = $ris_estratti->fetch(PDO::FETCH_ASSOC)) {
                $style="";
                $stato_estrazione = "";

                if ($estratto["selezionato"] == "S") {
                  if ($estratto["identificativo"]==-1) {
                    $style="style=\"background-color:#2fccc7; color:#FFF; font-weight:bold;\"";
                    $stato_estrazione = "Selezionato automaticamente";
                  } else {
                    $style="style=\"background-color:#74c27a; color:#FFF; font-weight:bold;\"";
                    $stato_estrazione = "Selezionato";
                  }
                }
                if ($estratto["escluso"] == "S") {
                  $style="style=\"background-color:#be6b70; color:#fff;\"";
                  $stato_estrazione = "Escluso";
                }
                ?>
                <tr <?= $style ?>>
                  <td width="20%"><?= $estratto["codice_fiscale"] ?></td>
                  <td width="50%"><?= $estratto["cognome"] . " " . $estratto["nome"] ?></td>
                  <td width="10%"><?= ($estratto["tipo"]=="I") ? "Interno" : "Esterno"; ?><br><strong><?= ($estratto["presidente"]=="S") ? "Presidente" : ""; ?></strong></td>
                  <td width="20%"><?= $stato_estrazione ?></td>
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
