<?
  if (isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] <= 2 && isset($record)) {
    unset($punteggio);
    if ($sum_punteggi) {
      $sql_criteri = "SELECT b_punteggi_criteri.punteggio AS punteggio_ottenuto, b_valutazione_tecnica.punteggio_riferimento
                      FROM b_punteggi_criteri JOIN b_valutazione_tecnica ON b_punteggi_criteri.codice_criterio = b_valutazione_tecnica.codice
                      JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
                      WHERE b_punteggi_criteri.codice_partecipante = :codice_partecipante
                      AND b_criteri_punteggi.economica = 'S' ";
      $ris_punteggi = $pdo->bindAndExec($sql_criteri,[":codice_partecipante"=>$record["codice"]]);
      if ($ris_punteggi->rowCount() > 0) {
        $punteggio = ["punteggio_riferimento"=>0,"punteggio_ottenuto"=>0];
        while($tmp = $ris_punteggi->fetch(PDO::FETCH_ASSOC)) {
          $punteggio["punteggio_riferimento"] = $tmp["punteggio_riferimento"];
          $punteggio["punteggio_ottenuto"] += $tmp["punteggio_ottenuto"];
        }
      }
    } else {
      if ($criterio["valutazione"] == "P") {
        $strsql = "SELECT *
                   FROM b_offerte_decriptate
                   WHERE codice_partecipante = :codice
                   AND codice_dettaglio = :codice_dettaglio
                   AND tipo <> 'elenco_prezzi'
                   ORDER BY timestamp DESC";
        $tmp = $pdo->bindAndExec($strsql,[":codice"=>$record["codice"],":codice_dettaglio"=>$criterio["codice"]])->fetch(PDO::FETCH_ASSOC)["offerta"];
        $punteggio = ["punteggio_riferimento"=>$criterio["punteggio_riferimento"],"punteggio_ottenuto"=>$tmp];
      } else if ($criterio["valutazione"] =="E" && empty($criterio["options"])) {
        $strsql = "SELECT b_offerte_decriptate.codice_partecipante, SUM(b_offerte_decriptate.offerta * b_elenco_prezzi.quantita) AS offerta
                   FROM b_offerte_decriptate
                   JOIN b_elenco_prezzi ON b_offerte_decriptate.codice_dettaglio = b_elenco_prezzi.codice
                   WHERE b_offerte_decriptate.codice_partecipante = :codice
                   AND b_elenco_prezzi.codice_criterio = :codice_dettaglio
                   AND b_offerte_decriptate.tipo = 'elenco_prezzi'
                   GROUP BY b_offerte_decriptate.codice_partecipante ";
       $tmp = $pdo->bindAndExec($strsql,[":codice"=>$record["codice"],":codice_dettaglio"=>$criterio["codice"]])->fetch(PDO::FETCH_ASSOC)["offerta"];
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
      if (isset($importi)) {
        $bind = array();
        $bind[":codice"] = $record["codice"];
        $base_gara = $importi["importo_base"]; // + $importi["importo_oneri_ribasso"] + $importi["importo_personale"];
        $costi = $importi["importo_oneri_no_ribasso"];
        if ($tmp < 0) $tmp = 0;
        $tmp = ($base_gara - $tmp)/$base_gara * 100;
        $tmp = truncate($tmp,$criterio["decimali"]);
        $punteggio = ["punteggio_riferimento"=>$criterio["punteggio_riferimento"],"punteggio_ottenuto"=>$tmp];
      }
     }
    }
    if (isset($punteggio)) {
      ?>
        $('#punteggio_<? echo $record["codice"] ?>_<? echo $punteggio["punteggio_riferimento"] ?>').val('<?= $punteggio["punteggio_ottenuto"] ?>');
      <?
    }
  }
?>
