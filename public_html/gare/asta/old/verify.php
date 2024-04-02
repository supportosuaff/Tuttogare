<?
  $bind = array(":codice_gara"=>$record_gara["codice"]);
  $sql_tipo = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 58";
  $ris_tipo = $pdo->bindAndExec($sql_tipo,$bind);
  $elenco_prezzi = false;
  $id_offerta = 0;
  if ($ris_tipo->rowCount() > 0) {
    $bind[":codice_lotto"] = $codice_lotto;
    $sql_elenco = "SELECT * FROM b_elenco_prezzi WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto ORDER BY tipo ";
    $ris_elenco = $pdo->bindAndExec($sql_elenco,$bind);
    if ($ris_elenco->rowCount()>0) $elenco_prezzi = true;
  }

  $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$partecipante["codice"]);

  $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_dettaglio = 0	AND tipo = 'temporale' AND stato = 0";
  $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
  if ($ris_storico->rowCount()>0) {
      $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
      $offerta_temporale = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
      $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                    WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_dettaglio = 0	AND tipo = 'temporale' AND stato = 1";
      $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
      if ($ris_storico->rowCount()>0) {
          $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
          $storico_temporale = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
      } else {
        $sql_storico = "SELECT SUM(b_offerte_decriptate.offerta) AS offerta FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
                        WHERE codice_partecipante = :codice_partecipante AND b_offerte_decriptate.tipo = 'temporale' AND r_partecipanti.codice_gara= :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
                        AND b_offerte_decriptate.codice_dettaglio = 0 GROUP BY r_partecipanti.codice_gara,r_partecipanti.codice_lotto";
        $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
        if ($ris_storico->rowCount()>0) {
          $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
          $storico_temporale = $storico["offerta"];
        }
      }
  }

  $differenza_tecnica = false;
  $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_dettaglio FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND (tipo = 'tecnica' OR tipo = 'migliorativa') AND stato = 0";
  $attuale_tecniche = $pdo->bindAndExec($sql_storico,$bind);
  if ($attuale_tecniche->rowCount()>0) {
    while($offerta_attuale = $attuale_tecniche->fetch(PDO::FETCH_ASSOC)) {
      $valore_attuale = openssl_decrypt($offerta_attuale["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
      $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_dettaglio FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                    WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND (tipo = 'tecnica' OR tipo = 'migliorativa')
                    AND codice_dettaglio = ".$offerta_attuale["codice_dettaglio"]." AND stato = 1";
      $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
      if ($ris_storico->rowCount()>0) {
          $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
          $valore = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
          if ($valore_attuale != $valore) $differenza_tecnica = true;
      } else {
        $sql_storico = "SELECT b_offerte_decriptate.offerta, b_offerte_decriptate.codice_dettaglio FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
                  WHERE codice_partecipante = :codice_partecipante AND b_offerte_decriptate.codice_dettaglio = " . $offerta_attuale["codice_dettaglio"] . " AND (b_offerte_decriptate.tipo = 'tecnica' OR b_offerte_decriptate.tipo = 'migliorativa') AND r_partecipanti.codice_gara= :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto";
        $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
        if ($ris_storico->rowCount()>0) {
          $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
          if ($valore_attuale != $storico["offerta"]) $differenza_tecnica = true;
        }
      }
    }
  }

  $totale_offerta = 0;
  $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);
  if ($elenco_prezzi) {
    $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante, b_offerte_economiche_asta.utente_modifica
                    FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                    WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND tipo = 'economica' AND stato = 1 ORDER BY codice_partecipante";
    $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
    if ($ris_storico->rowCount()>0) {
      $array_partecipanti = array();
      while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($array_partecipanti[$storico["codice_partecipante"]])) $array_partecipanti[$storico["codice_partecipante"]] = 0;
        $array_partecipanti[$storico["codice_partecipante"]] += openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($storico["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
      }
      foreach($array_partecipanti AS $offerta_partecipante) {
        if (!isset($offerta_max)) $offerta_max=$offerta_partecipante;
        if ($offerta_partecipante < $offerta_max) $offerta_max = $offerta_partecipante;
      }
    } else {
      $sql_storico = "SELECT SUM(b_offerte_decriptate.offerta) AS offerta, b_offerte_decriptate.codice_partecipante FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
                    WHERE b_offerte_decriptate.tipo = 'economica' AND r_partecipanti.codice_gara= :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto AND (r_partecipanti.conferma = TRUE or r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N'
                    GROUP BY r_partecipanti.codice,r_partecipanti.codice_gara,r_partecipanti.codice_lotto";
      $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
      if ($ris_storico->rowCount()>0) {
        while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
          if (!isset($offerta_max)) $offerta_max=$storico["offerta"];
          if ($storico["offerta"] < $offerta_max) $offerta_max = $storico["offerta"];
        }
      }
    }
    $bind = array(":codice_utente"=>$_SESSION["codice_utente"],":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);
    $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON
                  b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                  WHERE b_offerte_economiche_asta.utente_modifica = :codice_utente AND
                  codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND tipo = 'economica' AND stato = 0 ORDER BY codice_partecipante";
    $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
    if ($ris_storico->rowCount()>0) {
      $array_partecipanti = array();
      while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
        $totale_offerta += openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($storico["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
      }
    }

    $base_gara = $importi["importo_base"]; // + $importi["importo_oneri_ribasso"] + $importi["importo_personale"];
    $totale_offerta = $totale_offerta;
    $totale_offerta = ($base_gara - $totale_offerta)/$base_gara * 100;
    $offerta_max = $offerta_max;
    $offerta_max = ($base_gara - $offerta_max)/$base_gara * 100;
  } else {
    $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);

    $offerta_max = 0;
    $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante
                  FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                  WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_dettaglio = 0	AND tipo = 'economica' AND stato = 1";
    $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
    if ($ris_storico->rowCount()>0) {
      while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
        $decrypt = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($storico["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
        if ($decrypt > $offerta_max) $offerta_max = $decrypt;
      }
    } else {
      $sql_storico = "SELECT MAX(b_offerte_decriptate.offerta) as offerta FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
                      WHERE b_offerte_decriptate.tipo = 'economica' AND r_partecipanti.codice_gara= :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto AND (r_partecipanti.conferma = TRUE or r_partecipanti.conferma IS NULL)
                      AND b_offerte_decriptate.codice_dettaglio = 0 AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso= 'N' GROUP BY r_partecipanti.codice_gara,r_partecipanti.codice_lotto";
      $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
      if ($ris_storico->rowCount()>0) {
          $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
          $offerta_max = $storico["offerta"];
      }
    }
    $bind[":codice_utente"] = $_SESSION["codice_utente"];
    $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante
                  FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                  WHERE b_offerte_economiche_asta.utente_modifica = :codice_utente AND codice_gara = :codice_gara
                  AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_dettaglio = 0	AND tipo = 'economica' AND stato = 0";

    $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
    if ($ris_storico->rowCount()>0) {
      $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
      $totale_offerta = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($storico["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
    }
  }

  if ((($totale_offerta - $offerta_max) >= $asta["rilancio_minimo"]) || (isset($offerta_temporale) && isset($storico_temporale) && ($offerta_temporale <> $storico_temporale)) || ($differenza_tecnica)) $procedi = true
?>
