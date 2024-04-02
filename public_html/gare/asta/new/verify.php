<?
  if (empty($inputs) && isset($_POST["filechunk"])) {
    $sql_offerte_asta = "SELECT b_dettaglio_offerte_asta.* FROM b_dettaglio_offerte_asta
                    JOIN b_offerte_economiche_asta
                    ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                    WHERE b_offerte_economiche_asta.codice_partecipante = :codice_partecipante
                    AND b_offerte_economiche_asta.stato = 0";
    $ris_offerte = $pdo->bindAndExec($sql_offerte_asta,[":codice_partecipante"=>$partecipante["codice"]]);
    if ($ris_offerte->rowCount() > 0) {
      $inputs = [];
      while($current = $ris_offerte->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($inputs[$current["tipo"]])) $inputs[$current["tipo"]] = [];
        $inputs[$current["tipo"]][$current["codice_dettaglio"]] = openssl_decrypt($current["offerta"],$config["crypt_alg"],md5($current["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
      }
    }
  }
  if (!empty($inputs)) {
    $ris_criterio = $pdo->prepare("SELECT * FROM b_valutazione_tecnica WHERE codice = :codice_criterio");

    $sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto
                         AND ammesso = 'S' AND escluso = 'N' AND (conferma = TRUE OR conferma IS NULL)";

    $elencoPartecipanti = $pdo->bindAndExec($sql_partecipanti,[":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto])->fetchAll(PDO::FETCH_ASSOC);

    $sql_criterio_prezzo = "SELECT b_valutazione_tecnica.*
                            FROM b_elenco_prezzi
                            JOIN b_valutazione_tecnica ON b_elenco_prezzi.codice_criterio = b_valutazione_tecnica.codice
                            WHERE b_elenco_prezzi.codice = :codice_dettaglio ";
    $ris_criterio_prezzo = $pdo->prepare($sql_criterio_prezzo);

    $ris_prezzo = $pdo->prepare("SELECT * FROM b_elenco_prezzi WHERE b_elenco_prezzi.codice = :codice_dettaglio ");

    $sql_offerte_asta = "SELECT b_dettaglio_offerte_asta.* FROM b_dettaglio_offerte_asta
                    JOIN b_offerte_economiche_asta
                    ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                    WHERE b_offerte_economiche_asta.codice_partecipante = :codice_partecipante
                    AND b_dettaglio_offerte_asta.tipo = :tipo_offerta
                    AND b_dettaglio_offerte_asta.codice_dettaglio = :codice_criterio
                    AND (b_offerte_economiche_asta.stato = 1 OR b_offerte_economiche_asta.stato = 98)
                    ORDER BY b_dettaglio_offerte_asta.codice DESC LIMIT 0,1";

    $sql_offerte_originali = "SELECT b_offerte_decriptate.* FROM b_offerte_decriptate
                    WHERE b_offerte_decriptate.codice_partecipante = :codice_partecipante
                    AND b_offerte_decriptate.tipo = :tipo_offerta
                    AND b_offerte_decriptate.codice_dettaglio = :codice_criterio ";

    $ris_offerte_asta = $pdo->prepare($sql_offerte_asta);
    $ris_offerte_originali = $pdo->prepare($sql_offerte_originali);

    $procedi = false;
    $economiche = [];
    $criteri = [];
    foreach($inputs AS $tipo_offerta => $voci) {
      foreach($voci AS $codice => $valore) {
        unset($criterio);
        if ($tipo_offerta != "elenco_prezzi") {
          $ris_criterio->bindValue(":codice_criterio",$codice);
          $ris_criterio->execute();
          $criterio = $ris_criterio->fetch(PDO::FETCH_ASSOC);
        } else {
          $ris_criterio_prezzo->bindValue(":codice_dettaglio",$codice);
          $ris_criterio_prezzo->execute();
          $criterio = $ris_criterio_prezzo->fetch(PDO::FETCH_ASSOC);
        }
        if (isset($criterio)) {
          if (!isset($criteri[$criterio["codice"]])) $criteri[$criterio["codice"]] = ["criterio"=>$criterio,"offerta"=>0,"other"=>[],"tipo"=>$tipo_offerta];
          if ($tipo_offerta == "economica" || $tipo_offerta == "elenco_prezzi") $economiche[] = $criterio["codice"];

          $ris_offerte_asta->bindValue(":tipo_offerta",$tipo_offerta);
          $ris_offerte_asta->bindValue(":codice_criterio",$codice);

          $ris_offerte_originali->bindValue(":tipo_offerta",$tipo_offerta);
          $ris_offerte_originali->bindValue(":codice_criterio",$codice);

          $moltiplicatore = 1;
          if ($tipo_offerta == "elenco_prezzi") {
            $ris_prezzo->bindValue(":codice_dettaglio",$codice);
            $ris_prezzo->execute();
            if ($prezzo = $ris_prezzo->fetch(PDO::FETCH_ASSOC)) {
              $moltiplicatore = $prezzo["quantita"];
            }
          }
          $criteri[$criterio["codice"]]["offerta"] += $valore * $moltiplicatore;
          foreach($elencoPartecipanti AS $part) {
            if (!isset($criteri[$criterio["codice"]]["other"][$part["codice"]])) $criteri[$criterio["codice"]]["other"][$part["codice"]] = 0;
            $ris_offerte_asta->bindValue(":codice_partecipante",$part["codice"]);
            $ris_offerte_originali->bindValue(":codice_partecipante",$part["codice"]);
            $ris_offerte_asta->execute();
            if ($ris_offerte_asta->rowCount() === 1) {
              $off = $ris_offerte_asta->fetch(PDO::FETCH_ASSOC);
              $val = openssl_decrypt($off["offerta"],$config["crypt_alg"],md5($off["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
              $criteri[$criterio["codice"]]["other"][$part["codice"]] += $val * $moltiplicatore;
            } else {
              $ris_offerte_originali->execute();
              if ($ris_offerte_originali->rowCount() === 1) {
                $off = $ris_offerte_originali->fetch(PDO::FETCH_ASSOC);
                $criteri[$criterio["codice"]]["other"][$part["codice"]] += $off["offerta"] * $moltiplicatore;
              }
            }
          }
        }
      }
    }
    if (count($criteri) > 0) {
      $economiche = array_unique($economiche);
      if (isset($importi)) {
        $base_gara = $importi["importo_base"]; //  + $importi["importo_oneri_no_ribasso"] + $importi["importo_personale"];
      }
      $min_val = ["I","E"];
      $max_val = ["P","B","Q"];
      foreach($criteri AS $infoCriterio) {
        $criterio = $infoCriterio["criterio"];
        $off = $infoCriterio["offerta"];
        $other = $infoCriterio["other"];
        $rilancio_minimo = $asta["rilancio_minimo"];
        if (!empty($criterio["rilancio_minimo"])) $rilancio_minimo = $criterio["rilancio_minimo"];
        if ($criterio["valutazione"] == "E") $inputs_totali[$criterio["codice"]] = $off;
        if ($criterio["valutazione"] == "E" && count($economiche) == 1) {
          $off = ($base_gara - $off)/$base_gara * 100;
          $off_migliore = min($other);
          $off_migliore = ($base_gara - $off_migliore)/$base_gara * 100;
          if (($off - $off_migliore) < $rilancio_minimo) {
            $error_rilancio[] = ["offerta"=>$off,"migliore"=>$off_migliore,"rilancio_minimo"=>$rilancio_minimo];
          } else {
            $procedi = true;
          }
        } else if (in_array($criterio["valutazione"],$min_val) !== false) {
          $off_migliore = min($other);
          if (($off - $off_migliore) > $rilancio_minimo) {
            $error_rilancio[] = ["offerta"=>$off,"migliore"=>$off_migliore,"rilancio_minimo"=>$rilancio_minimo];
          } else {
            $procedi = true;
          }
        } else if (in_array($criterio["valutazione"],$max_val) !== false) {
          $off_migliore = max($other);
          if (($off - $off_migliore) < $rilancio_minimo) {
            $error_rilancio[] = ["offerta"=>$off,"migliore"=>$off_migliore,"rilancio_minimo"=>$rilancio_minimo];
          } else {
            $procedi = true;
          }
        } else {
          if ($off != $other[$partecipante["codice"]]) $procedi = true;
        }
      }
    }
  }
?>
