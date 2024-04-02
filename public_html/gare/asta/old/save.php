<?
  if (isset($pdo) && (isset($_POST["offerta"]))) {
    $ultima_offerta = array();
    $ultima_offerta_tecnica = array();
    foreach($_POST["offerta"] as $dettaglio_offerta) {
      if ($dettaglio_offerta["codice_dettaglio"]!=0) {
        if ($dettaglio_offerta["tipo"] == "economica") {
          $bind = array(":codice_dettaglio"=>$dettaglio_offerta["codice_dettaglio"]);
          $sql_prezzo = "SELECT * FROM b_elenco_prezzi WHERE codice = :codice_dettaglio ";
          $ris_prezzo = $pdo->bindAndExec($sql_prezzo,$bind);
          if ($prezzo = $ris_prezzo->fetch(PDO::FETCH_ASSOC)) {
            $elenco_prezzi = true;
            $totale_offerta += $dettaglio_offerta["offerta"] * $prezzo["quantita"];;
          }
          $ultima_offerta[$dettaglio_offerta["codice_dettaglio"]] =  $dettaglio_offerta["offerta"];
        } else if ($dettaglio_offerta["tipo"] == "tecnica" || $dettaglio_offerta["tipo"] == "migliorativa") {
          $ultima_offerta_tecnica[$dettaglio_offerta["codice_dettaglio"]] =  $dettaglio_offerta["offerta"];
        }
      } else {
        if ($dettaglio_offerta["tipo"] == "economica") {
          $totale_offerta = $dettaglio_offerta["offerta"];
          $ultima_offerta["economica"] =  $dettaglio_offerta["offerta"];
        } else if ($dettaglio_offerta["tipo"] == "temporale") {
          $ultima_offerta["temporale"] =  $dettaglio_offerta["offerta"];
          $offerta_temporale = $dettaglio_offerta["offerta"];
        }
      }
    }
    $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$partecipante["codice"]);
    $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta
                  JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                  WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_dettaglio = 0	AND tipo = 'temporale' AND stato = 1";
    $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
    if ($ris_storico->rowCount()>0) {
        $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
        $storico_temporale = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
    } else {
      $sql_storico = "SELECT SUM(b_offerte_decriptate.offerta) AS offerta FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
                      WHERE codice_partecipante = :codice_partecipante AND b_offerte_decriptate.tipo = 'temporale' AND r_partecipanti.codice_gara= :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
                      AND b_offerte_decriptate.codice_dettaglio = 0 AND (r_partecipanti.conferma = TRUE or r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N' GROUP BY r_partecipanti.codice_gara,r_partecipanti.codice_lotto";
      $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
      if ($ris_storico->rowCount()>0) {
        $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
        $storico_temporale = $storico["offerta"];
      }
    }

    $differenza_tecnica = false;
    $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_dettaglio FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                  WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND (tipo = 'tecnica' OR tipo = 'migliorativa') AND stato = 1";
    $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
    if ($ris_storico->rowCount()>0) {
        while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
          $valore = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
          if ($ultima_offerta_tecnica[$storico["codice_dettaglio"]] != $valore) $differenza_tecnica = true;
        }
    } else {
      $sql_storico = "SELECT b_offerte_decriptate.offerta, b_offerte_decriptate.codice_dettaglio FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
                      WHERE codice_partecipante = :codice_partecipante AND (r_partecipanti.conferma = TRUE or r_partecipanti.conferma IS NULL) AND (b_offerte_decriptate.tipo = 'tecnica' OR b_offerte_decriptate.tipo = 'migliorativa') AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N' AND r_partecipanti.codice_gara= :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto";

      $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
      if ($ris_storico->rowCount()>0) {
        while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
          if ($ultima_offerta_tecnica[$storico["codice_dettaglio"]] != $storico["offerta"]) $differenza_tecnica = true;
        }
      }
    }

    $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto);
    $sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_lotto = :codice_lotto AND codice_gara = :codice_gara AND ammesso = 'S' AND escluso = 'N' AND (r_partecipanti.conferma = TRUE or r_partecipanti.conferma IS NULL)";
    $ris_elenco_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
    if ($ris_elenco_partecipanti->rowCount() > 0) {
      $costi = 0;
      if ($elenco_prezzi) {
        $base_gara = $importi["importo_base"]; // + $importi["importo_oneri_ribasso"] + $importi["importo_personale"];
      }
      $array_partecipanti = array();
      while ($record_partecipante = $ris_elenco_partecipanti->fetch(PDO::FETCH_ASSOC)) {
        $array_partecipanti[$record_partecipante["codice"]] = 0;
        $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$record_partecipante["codice"]);
        if ($elenco_prezzi) {
          $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta
                      JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                      WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND tipo = 'economica' AND stato = 1 ORDER BY codice_partecipante";
          $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
          if ($ris_storico->rowCount()>0) {
            while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
              $array_partecipanti[$record_partecipante["codice"]] += openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($record_partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
            }
          } else {
            $sql_storico = "SELECT SUM(b_offerte_decriptate.offerta) AS offerta FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
                          WHERE b_offerte_decriptate.tipo = 'economica' AND (r_partecipanti.conferma = TRUE or r_partecipanti.conferma IS NULL) AND  r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N' AND r_partecipanti.codice = :codice_partecipante AND r_partecipanti.codice_gara= :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
                          GROUP BY r_partecipanti.codice_gara,r_partecipanti.codice_lotto";
            $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
            if ($ris_storico->rowCount()>0) {
              $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
              $array_partecipanti[$record_partecipante["codice"]] = $storico["offerta"];
            }
          }
        } else {
          $sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                        WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_dettaglio = 0	AND tipo = 'economica' AND stato = 1";
          $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
          if ($ris_storico->rowCount()>0) {
            while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
              $array_partecipanti[$record_partecipante["codice"]] += openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($record_partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
            }
          } else {
            $sql_storico = "SELECT SUM(b_offerte_decriptate.offerta) AS offerta FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
                            WHERE codice_partecipante = :codice_partecipante AND b_offerte_decriptate.tipo = 'economica' AND r_partecipanti.codice_gara= :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
                            AND b_offerte_decriptate.codice_dettaglio = 0 AND (r_partecipanti.conferma = TRUE or r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N' GROUP BY r_partecipanti.codice_gara,r_partecipanti.codice_lotto";
            $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
            if ($ris_storico->rowCount()>0) {
              $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
              $array_partecipanti[$record_partecipante["codice"]] = $storico["offerta"];
            }
          }
        }
      }
    foreach($array_partecipanti AS $index => $storico_offerta) {
      if (!isset($offerta_max)) $offerta_max = $storico_offerta;
      if ($elenco_prezzi) {
        if ($storico_offerta <= $offerta_max) $offerta_max = $storico_offerta;
      } else {
        if ($storico_offerta >= $offerta_max) $offerta_max = $storico_offerta;
      }
    }
    if ($elenco_prezzi) {
      $totale_offerta = $totale_offerta ;
      if ($totale_offerta < 0) $totale_offerta = 0;
      $totale_offerta = ($base_gara - $totale_offerta)/$base_gara * 100;
      $offerta_max = $offerta_max;
      if ($offerta_max < 0) $offerta_max = 0;
      $offerta_max = ($base_gara - $offerta_max)/$base_gara * 100;
    }
  }
  if ((($totale_offerta - $offerta_max) >= $asta["rilancio_minimo"]) || (isset($storico_temporale) && ($storico_temporale <> $offerta_temporale)) || $differenza_tecnica) {
    $busta = array();
    if ($ris->rowCount()>0) {
      $rec = $ris->fetch(PDO::FETCH_ASSOC);
      $offerta["codice_partecipante"] = $partecipante["codice"];
      $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$offerta["codice_partecipante"]);
      $sql = "UPDATE b_offerte_economiche_asta SET stato = 99 WHERE stato <> 1 AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_partecipante = :codice_partecipante";
      $ris_update_stato = $pdo->bindAndExec($sql,$bind);
      $offerta["codice_gara"] = $record_gara["codice"];
      $offerta["codice_lotto"] = $codice_lotto;

      $salva = new salva();
      $salva->debug = false;
      $salva->codop = $_SESSION["codice_utente"];
      $salva->nome_tabella = "b_offerte_economiche_asta";
      $salva->operazione = "INSERT";
      $salva->oggetto = $offerta;
      $codice_offerta = $salva->save();
      $errore_offerte = false;
      $vocabolario["tabella"] = "";
      $offerte_tecniche = "";
      $tipo_prezzo = "";
      $intestazione = false;
      $totale_offerta = 0;
      $echo_totale = false;
      if ($codice_offerta != false) {
        foreach($_POST["offerta"] as $dettaglio_offerta) {
          $dettaglio_offerta["codice_offerta"] = $codice_offerta;
          $dettaglio_offerta["codice_partecipante"] = $offerta["codice_partecipante"];
          if ($dettaglio_offerta["codice_dettaglio"]!=0) {
            $bind = array(":codice_dettaglio"=>$dettaglio_offerta["codice_dettaglio"]);
            $sql_prezzo = "SELECT * FROM b_elenco_prezzi WHERE codice = :codice_dettaglio";
            $ris_prezzo = $pdo->bindAndExec($sql_prezzo,$bind);
            if ($prezzo = $ris_prezzo->fetch(PDO::FETCH_ASSOC)) {
              $dettaglio_offerta["offerta"] = $dettaglio_offerta["offerta"] * $prezzo["quantita"];
              $totale_offerta += $dettaglio_offerta["offerta"];
              $echo_totale = false;
            }
          }
          $offerta_plain = $dettaglio_offerta["offerta"];
          $dettaglio_offerta["offerta"] = openssl_encrypt($dettaglio_offerta["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
          if ($dettaglio_offerta["offerta"] !== FALSE) {
            if ($dettaglio_offerta["codice_dettaglio"]==0) {
              if (!$echo_totale && $totale_offerta > 0) {
                $echo_totale = true;
                $vocabolario["tabella"] .= "<tr style=\"font-weight:bold\"><td colspan=\"4\"><h3>Totale offerta</h3></td><td style=\"text-align:right\"><h3>&euro; " .  number_format($totale_offerta,3,",",".") ."</h3></td></tr>";
              }
              if ($dettaglio_offerta["tipo"] == "temporale") {
                $vocabolario["tabella"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">Riduzione percentuale sui tempi di ultimazione</h3></td></tr>";
                $vocabolario["tabella"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">" . number_format($offerta_plain,3,",",".") . "</h3></td></tr>";
              } else if ($dettaglio_offerta["tipo"] == "economica") {
                $vocabolario["tabella"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">" . (($rialzo) ? "Rialzo" : "Ribasso") . " percentuale offerto</h3></td></tr>";
                $vocabolario["tabella"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">" . number_format($offerta_plain,3,",",".") . "</h3></td></tr>";;
              }
            } else {
              if ($dettaglio_offerta["tipo"] == "economica") {
                $bind = array(":codice_dettaglio"=>$dettaglio_offerta["codice_dettaglio"]);
                $sql_prezzo = "SELECT * FROM b_elenco_prezzi WHERE codice = :codice_dettaglio";
                $ris_prezzo = $pdo->bindAndExec($sql_prezzo,$bind);
                if ($ris_prezzo->rowCount()>0) {
                  if ($prezzo = $ris_prezzo->fetch(PDO::FETCH_ASSOC)) {
                      if (!$intestazione) {
                        $intestazione = true;
                        $vocabolario["tabella"] .= "<tr><td style=\"width:50%; \">Descrizione</td>";
                        $vocabolario["tabella"] .= "<td style=\"width:10%\">Unit&agrave;</td>";
                        $vocabolario["tabella"] .= "<td style=\"width:10%;text-align:right;\">Quantit&agrave;</td>";
                        $vocabolario["tabella"] .= "<td style=\"width:15%;text-align:right;\">Prezzo Unitario</td>";
                        $vocabolario["tabella"] .= "<td style=\"width:15%;text-align:right;\">Offerta totale</td></tr>";
                      }
                      if ($tipo_prezzo != $prezzo["tipo"]) {
                        $tipo_prezzo = $prezzo["tipo"];
                        $vocabolario["tabella"] .= "<tr><td colspan=\"5\"><h3>" . strtoupper($tipo_prezzo) ."</h3></td></tr>";
                      }
                      $vocabolario["tabella"] .= "<tr><td style=\"width:50%; \">" . $prezzo["descrizione"] . ": &nbsp;&nbsp;</td>";
                      $vocabolario["tabella"] .= "<td style=\"width:10%\">" . $prezzo["unita"] . "</td>";
                      $vocabolario["tabella"] .= "<td style=\"width:10%;text-align:right;\">" . number_format($prezzo["quantita"],2,",",".") . "</td>";
                      $vocabolario["tabella"] .= "<td style=\"width:15%;text-align:right;\">&euro; " . number_format($offerta_plain / $prezzo["quantita"],2,",",".") . "</td>";
                      $vocabolario["tabella"] .= "<td style=\"width:15%;text-align:right;\">&euro; " . number_format($offerta_plain,3,",",".") . "</td></tr>";
                    } else {
                      $errore_offerte = 1;
                    }
                  } else {
                    $errore_offerte = 2;
                  }
                } else if ($dettaglio_offerta["tipo"] == "tecnica" || $dettaglio_offerta["tipo"] == "migliorativa") {
                  $bind = array(":codice_dettaglio"=>$dettaglio_offerta["codice_dettaglio"]);
                  $sql_valutazione = "SELECT * FROM b_valutazione_tecnica WHERE codice = :codice_dettaglio";
                  $ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
                  if ($ris_valutazione->rowCount()>0) {
                    if ($record_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
                        $offerte_tecniche .= "<tr><td style=\"width:85%;\">" . $record_valutazione["descrizione"] . ": &nbsp;&nbsp;</td>";
                        $offerte_tecniche .= "<td style=\"width:15%;text-align:right;\">" . number_format($offerta_plain,3,",",".") . "</td></tr>";
                      } else {
                        $errore_offerte = 1;
                      }
                  } else {
                      $errore_offerte = 2;
                  }
                } else {
                  $errore_offerte = 3;
                }
              }
            } else {
              $errore_offerte = 3;
            }
            if (!$errore_offerte) {
              $salva = new salva();
              $salva->debug = false;
              $salva->codop = $_SESSION["codice_utente"];
              $salva->nome_tabella = "b_dettaglio_offerte_asta";
              $salva->operazione = "INSERT";
              $salva->oggetto = $dettaglio_offerta;
              $codice_dettaglio = $salva->save();
              if ($codice_dettaglio == false) $errore_offerte = 4;
            }
          }
          if ($errore_offerte === false) {
            $create_pdf = true;
            $vocabolario["#tabella#"] = "<table>" . $vocabolario["tabella"] . "</table>";
            if ($offerte_tecniche != "") {
              $vocabolario["#tabella#"] .= "<br><h2>Valori quantificabili automaticamente dell'offerta tecnica o economica migliorativa</h2><table>" . $offerte_tecniche . "</table>";
            }
          }
        }
      }
    } else {
      $errore_validazione = true;
      ?>
      <h2 style="color:#F00;"><img src="/img/alert.png" alt="Attenzione" style="vertical-align:middle"> L'offerta pari a <?= number_format($totale_offerta,3,",",".") ?>% non supera il rilancio minimo pari a <?= $asta["rilancio_minimo"] ?>% - Ultimo rilancio valido <?= number_format($offerta_max,3,",",".") ?>%</h2>
      <?
    }
  }
?>
