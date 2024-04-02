<?
  if (isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] <= 2 && isset($record)) {
    $bind = array();
    $bind[":codice"] = $record["codice"];
    $percentuale_offerta = 0;
    $strsql = "SELECT b_offerte_decriptate.* FROM b_offerte_decriptate WHERE codice_partecipante = :codice AND tipo = 'economica' ";
    $ris_offerte = $pdo->bindAndExec($strsql,$bind);
    if ($ris_offerte->rowCount()>0) {
      $bind = array();
      $bind[":codice_gara"] = $_POST["codice_gara"];
      $sql_tipo = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 40)";
      $ris_tipo = $pdo->bindAndExec($sql_tipo,$bind);
      $elenco_prezzi = false;
      if ($ris_tipo->rowCount() > 0) {
        $opzione = $ris_tipo->fetch(PDO::FETCH_ASSOC);
        if ($opzione["opzione"] == "58") $elenco_prezzi = true;
      }
      if ($ris_offerte->rowCount()>1 && $elenco_prezzi) {
        $totale_offerta = 0;
        while($offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC)) {
          $totale_offerta += $offerta["offerta"];
        }
        if ($_POST["codice_lotto"]==0) {
          $bind = array();
          $bind[":codice_gara"] = $_POST["codice_gara"];
          $sql = "SELECT sum(b_importi_gara.importo_base) AS importo_base,
                  sum(b_importi_gara.importo_oneri_ribasso) AS importo_oneri_ribasso,
                  sum(b_importi_gara.importo_oneri_no_ribasso) AS importo_oneri_no_ribasso,
                  sum(b_importi_gara.importo_personale) AS importo_personale
                  FROM b_importi_gara WHERE codice_gara = :codice_gara";
          $ris_importi = $pdo->bindAndExec($sql,$bind);
          if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
        } else {
          $bind = array();
          $bind[":codice_gara"] = $_POST["codice_gara"];
          $bind[":codice_lotto"] = $_POST["codice_lotto"];
          $sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto";
          $ris_importi = $pdo->bindAndExec($sql,$bind);
          if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
        }
        $costi = 0;
        if (isset($importi)) {
          $bind = array();
          $bind[":codice"] = $record["codice"];
          //$base_gara = $importi["importo_base"] + $importi["importo_oneri_ribasso"] + $importi["importo_oneri_no_ribasso"] + $importi["importo_personale"];
          $base_gara = $importi["importo_base"]; //  + $importi["importo_oneri_ribasso"] + $importi["importo_personale"];
          $costi = $importi["importo_oneri_no_ribasso"];
          //$totale_offerta = $totale_offerta - $costi;
          if ($totale_offerta < 0) $totale_offerta = 0;
          $percentuale_offerta = ($base_gara - $totale_offerta)/$base_gara * 100;
        }
      } else {
        $offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC);
        $percentuale_offerta = $offerta["offerta"];
      }
      if ($percentuale_offerta < 0) $percentuale_offerta = 0;
      $bind = array();
      $bind[":criterio"] = $_POST["criterio"];
      $sql = "SELECT * FROM b_criteri_punteggi WHERE economica = 'S' AND eliminato = 'N' AND codice_criterio = :criterio";
      $ris = $pdo->bindAndExec($sql,$bind);
      if ($ris->rowCount()>0) {
        $punteggio = $ris->fetch(PDO::FETCH_ASSOC);
        ?>
          $('#punteggio_<? echo $record["codice"] ?>_<? echo $punteggio["codice"] ?>').val('<? echo number_format($percentuale_offerta,3,".",""); ?>');
        <?
      }
    }
  }
?>
