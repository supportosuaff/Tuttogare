<?
  if (isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] <= 2 && isset($numero_partecipanti)) {
    $punteggio_economico = 0;
    $punteggio_temporale = 0;

    $bind = array();
    $bind[":codice_criterio"] = $_POST["criterio"];

    $sql = "SELECT * FROM b_criteri_punteggi WHERE economica = 'S' AND migliorativa = 'N' AND eliminato = 'N' AND codice_criterio = :codice_criterio";
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount()>0) {
      $codice_economica = $ris->fetch(PDO::FETCH_ASSOC);
      $codice_economica = $codice_economica["codice"];

      $bind = array();
      $bind[":codice_gara"] = $_POST["codice_gara"];
      $bind[":codice_economica"] = $codice_economica;

      $strsql = "SELECT sum(punteggio) AS punteggio FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND punteggio_riferimento = :codice_economica";
      $ris = $pdo->bindAndExec($strsql,$bind);
      if ($ris->rowCount()>0) {
        $punteggio = $ris->fetch(PDO::FETCH_ASSOC);
        $punteggio_economico = $punteggio["punteggio"];
      }
    }

    $bind = array();
    $bind[":codice_criterio"] = $_POST["criterio"];

    $sql = "SELECT * FROM b_criteri_punteggi WHERE temporale = 'S' AND eliminato = 'N' AND codice_criterio = :codice_criterio";
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount()>0) {
      $codice_temporale = $ris->fetch(PDO::FETCH_ASSOC);
      $codice_temporale = $codice_temporale["codice"];

      $bind = array();
      $bind[":codice_gara"] = $_POST["codice_gara"];
      $bind[":codice_temporale"] = $codice_temporale;

      $strsql = "SELECT sum(punteggio) AS punteggio FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND punteggio_riferimento = :codice_temporale";
      $ris = $pdo->bindAndExec($strsql,$bind);
      if ($ris->rowCount()>0) {
        $punteggio = $ris->fetch(PDO::FETCH_ASSOC);
        $punteggio_temporale = $punteggio["punteggio"];
      }
    }

    $bind = array();
    $bind[":codice_criterio"] = $_POST["criterio"];

    $sql = "SELECT * FROM b_criteri_punteggi WHERE economica = 'S' AND migliorativa = 'S' AND eliminato = 'N' AND codice_criterio = :codice_criterio";
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount()>0) {
      $codice_migliorativo = $ris->fetch(PDO::FETCH_ASSOC);
      $codice_migliorativo = $codice_migliorativo["codice"];
    }

    $offerte_economica = array();
    $offerte_temporale = array();
    foreach($risultato AS $record) {

      // INIZIO VALUTAZIONE ECONOMICA

      $bind = array();
      $bind[":codice_partecipante"] = $record["codice"];

      $strsql = "SELECT b_offerte_decriptate.* FROM b_offerte_decriptate WHERE codice_partecipante = :codice_partecipante AND tipo = 'economica' ORDER BY b_offerte_decriptate.timestamp DESC";
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

            $sql = "SELECT sum(b_importi_gara.importo_base) AS importo_base, ";
            $sql.= " sum(b_importi_gara.importo_oneri_ribasso) AS importo_oneri_ribasso, ";
            $sql.= " sum(b_importi_gara.importo_oneri_no_ribasso) AS importo_oneri_no_ribasso, ";
            $sql.= " sum(b_importi_gara.importo_personale) AS importo_personale ";
            $sql.= " FROM b_importi_gara WHERE codice_gara = :codice_gara";
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
            // $base_gara = $importi["importo_base"] + $importi["importo_oneri_ribasso"] + $importi["importo_oneri_no_ribasso"] + $importi["importo_personale"];
            $base_gara = $importi["importo_base"]; // + $importi["importo_oneri_ribasso"] + $importi["importo_personale"];
            $costi = 0;

            $bind = array();
            $bind[":codice_gara"] = $_POST["codice_gara"];

            $costi += $importi["importo_oneri_no_ribasso"];
            // $costi += $importi["importo_personale"];

            // $totale_offerta = $totale_offerta - $costi;
            if ($totale_offerta > 0) {
              $percentuale_offerta = ($base_gara - $totale_offerta)/$base_gara * 100;
            } else {
              $percentuale_offerta = 0;
            }

          }
        } else {
          $offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC);
          $percentuale_offerta = $offerta["offerta"];
        }
        if ($percentuale_offerta < 0) $percentuale_offerta = 0;
        $offerte_economica[$record["codice"]] = $percentuale_offerta;
      }

      // INIZIO VALUTAZIONE TEMPORALE

      $bind = array();
      $bind[":codice_partecipante"] = $record["codice"];

      $strsql = "SELECT b_offerte_decriptate.* FROM b_offerte_decriptate WHERE codice_partecipante = :codice_partecipante AND tipo = 'temporale'  ORDER BY b_offerte_decriptate.timestamp DESC ";
      $ris_offerte = $pdo->bindAndExec($strsql,$bind);
      $percentuale_offerta = 0;
      if ($ris_offerte->rowCount()>0) {
          $offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC);
          $percentuale_offerta = $offerta["offerta"];
        }
      if ($percentuale_offerta < 0) $percentuale_offerta = 0;
      $offerte_temporale[$record["codice"]] = $percentuale_offerta;


    }

      // ATTRIBUZIONE PUNTEGGI
      if (count($offerte_economica) > 0) {
        $max_economica = max($offerte_economica);
        $chiavi = array_keys($offerte_economica);
        $somma_offerte = array_sum($offerte_economica);
        $media_offerte = $somma_offerte / count($offerte_economica);
        foreach ($chiavi as $chiave) {
          $punteggio = 0;
          if ($max_economica>0) {
            if ($coefficienteX > 0) {
              if ($offerte_economica[$chiave] > $media_offerte) {
                $punteggio = ($coefficienteX + (1 - $coefficienteX) * (($offerte_economica[$chiave] - $media_offerte) / ($max_economica - $media_offerte))) * $punteggio_economico;
              } else {
                $punteggio = (($coefficienteX * $offerte_economica[$chiave]) / $media_offerte) * $punteggio_economico;
              }
            } else {
              $punteggio = $offerte_economica[$chiave] * $punteggio_economico / $max_economica;
            }
          }
          ?>
            $('#punteggio_<? echo $chiave ?>_<? echo $codice_economica ?>').val('<? echo number_format($punteggio,3,".",""); ?>');
          <?
        }
      }
      $max_temporale = max($offerte_temporale);
      $chiavi = array_keys($offerte_temporale);
      foreach ($chiavi as $chiave) {
        $punteggio = 0;
        if ($max_temporale > 0) $punteggio = $offerte_temporale[$chiave] * $punteggio_temporale / $max_temporale;
        ?>
          $('#punteggio_<? echo $chiave ?>_<? echo $codice_temporale ?>').val('<? echo number_format($punteggio,3,".",""); ?>');
        <?
      }

      $bind=array();
      $bind[":codice_gara"] = $_POST["codice_gara"];
      $strsql = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.valutazione <> '' AND b_criteri_punteggi.economica = 'S' AND  b_criteri_punteggi.migliorativa = 'S'";
      $ris = $pdo->bindAndExec($strsql,$bind);
      if ($ris->rowCount()>0 && !empty($codice_migliorativo)) {
        $punteggi = array();
        while($punteggio = $ris->fetch(PDO::FETCH_ASSOC)) {
          $punteggio_max = $punteggio["punteggio"];
          $offerte = array();
          foreach ($risultato as $record) {
            $bind=array();
            $bind[":codice"] = $record["codice"];
            $bind[":codice_dettaglio"] = $punteggio["codice"];
            $strsql = "SELECT b_offerte_decriptate.* FROM b_offerte_decriptate WHERE codice_partecipante = :codice AND tipo = 'migliorativa' AND codice_dettaglio = :codice_dettaglio  ORDER BY b_offerte_decriptate.timestamp DESC ";
            $ris_offerte = $pdo->bindAndExec($strsql,$bind);
            if ($ris_offerte->rowCount()>0) {
              $offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC);
              $offerte[$record["codice"]] = $offerta["offerta"];
            }
          }
          switch($punteggio["valutazione"]) {
            case "P":
              $max = max($offerte);
              $chiavi = array_keys($offerte);
              foreach ($chiavi as $chiave) {
                $punteggio_ottenuto = 0;
                if ($max>0) $punteggio_ottenuto = $offerte[$chiave] * $punteggio_max / $max;
                if (empty($punteggi[$chiave])) {
                  $punteggi[$chiave] = $punteggio_ottenuto;
                } else {
                  $punteggi[$chiave] += $punteggio_ottenuto;
                }
              }
            break;
            case "I":
              $min = min($offerte);
              $chiavi = array_keys($offerte);
              foreach ($chiavi as $chiave) {
                $punteggio_ottenuto = $min * $punteggio_max / $offerte[$chiave];
                if (empty($punteggi[$chiave])) {
                  $punteggi[$chiave] = $punteggio_ottenuto;
                } else {
                  $punteggi[$chiave] += $punteggio_ottenuto;
                }
              }
            break;
            case "S":
              $chiavi = array_keys($offerte);
              foreach ($chiavi as $chiave) {
                $bind = array();
                $bind[":codice_criterio"] = $punteggio["codice"];
                $bind[":chiave"] = $offerte[$chiave];
                $offerte[$chiave] = 0;
                $sql_step = "SELECT * FROM r_step_valutazione WHERE codice_criterio = :codice_criterio AND minimo <= :chiave AND (massimo >= :chiave OR massimo = 0) ORDER BY massimo DESC LIMIT 0,1";
                $ris_step = $pdo->bindAndExec($sql_step,$bind);
                if ($ris_step->rowCount()>0) {
                  $rec_step = $ris_step->fetch(PDO::FETCH_ASSOC);
                  $offerte[$chiave] = $rec_step["punteggio"];
                }
              }
              $max = max($offerte);
              foreach ($chiavi as $chiave) {

                $bind = array();
                $bind[":codice_gara"] = $_POST["codice_gara"];
                $sql_check = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 161";
                $ris_check = $pdo->bindAndExec($sql_check,$bind);
                if ($ris_check->rowCount() > 0) {
                  $punteggio_ottenuto = 0;
                  if ($max>0) $punteggio_ottenuto = $offerte[$chiave] * $punteggio_max / $max;
                } else {
                  $punteggio_ottenuto = $offerte[$chiave];
                }
                if (empty($punteggi[$chiave])) {
                  $punteggi[$chiave] = $punteggio_ottenuto;
                } else {
                  $punteggi[$chiave] += $punteggio_ottenuto;
                }
              }
            break;
          }
        }
        foreach ($punteggi as $chiave => $punteggio) {
          ?>
            $('#punteggio_<? echo $chiave ?>_<? echo $codice_migliorativo ?>').val('<? echo number_format($punteggio,3,".",""); ?>');
          <?
        }
      }
  }
?>
