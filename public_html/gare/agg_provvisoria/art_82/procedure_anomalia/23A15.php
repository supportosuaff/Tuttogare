<?
  if ($edit) {
    $num_soglia = ceil($numero_partecipanti * 10 /100);
    // Codici partecipanti per le offerte più alte

    $bind = array();
    $bind[":codice_gara"] = $_POST["codice_gara"];
    $bind[":codice_lotto"] = $_POST["codice_lotto"];

    $sql = "SELECT r_partecipanti.codice, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
    $sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara
             AND r_punteggi_gare.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 AND r_partecipanti.ammesso = 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
    $sql.= " GROUP BY r_punteggi_gare.codice_partecipante, r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ORDER BY totale_punteggio DESC";
    $ris = $pdo->bindAndExec($sql,$bind);
    $i=0;
    $codici_taglio_ali = array();
    $punteggio_attuale = "";
    while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
      if ($rec["totale_punteggio"] != $punteggio_attuale) {
        $i++;
        $punteggio_attuale = $rec["totale_punteggio"];
      }
      if ($i<=$num_soglia) {
        $codici_taglio_ali[] = $rec["codice"];
      } else {
        break;
      }
    }

    $bind = array();
    $bind[":codice_gara"] = $_POST["codice_gara"];
    $bind[":codice_lotto"] = $_POST["codice_lotto"];

    // Codici partecipanti per le offerte più basse
    $sql = "SELECT r_partecipanti.codice, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
    $sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara AND r_punteggi_gare.codice_lotto = :codice_lotto
             AND r_partecipanti.codice_capogruppo = 0  AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S'";
    $sql.= " GROUP BY r_punteggi_gare.codice_partecipante, r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ORDER BY totale_punteggio ASC";
    $ris = $pdo->bindAndExec($sql,$bind);
    $i=0;
    $punteggio_attuale = "";
    while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
      if ($rec["totale_punteggio"] != $punteggio_attuale) {
        $i++;
        $punteggio_attuale = $rec["totale_punteggio"];
      }
      if ($i<=$num_soglia) {
        $codici_taglio_ali[] = $rec["codice"];
      } else {
        break;
      }
    }
    $taglio_ali = implode(",",$codici_taglio_ali);
    // Calcolo della media dei punteggi con esclusione delle offerte rientranti nel taglio delle ali

    $bind = array();
    $bind[":codice_gara"] = $_POST["codice_gara"];
    $bind[":codice_lotto"] = $_POST["codice_lotto"];

    $sql = "SELECT SUM(r_punteggi_gare.punteggio) AS sommatoria, SUM(r_punteggi_gare.punteggio) / COUNT(DISTINCT r_punteggi_gare.codice_partecipante) as media  FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
    $sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara
             AND r_punteggi_gare.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 AND r_partecipanti.ammesso = 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
    if ($taglio_ali != "") $sql.= " AND r_partecipanti.codice NOT IN (" . $taglio_ali . ") ";
    $sql.= " GROUP BY r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ";

    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount()>0) {
      $rec_media = $ris->fetch(PDO::FETCH_ASSOC);
      $somma = $rec_media["sommatoria"];
      $media = $rec_media["media"];
      $decimali_arrotonamento_somma = ($decimali_graduatoria >= 2) ? $decimali_graduatoria : 2;
      if ($solo_soglia == "N") {
        if ($arrotondamento=="S") {
          $somma = number_format($somma,$decimali_arrotonamento_somma);
        } else {
          $somma = truncate($somma,$decimali_arrotonamento_somma);
        }
      }
      if ($solo_soglia == "N") {
        if ($arrotondamento=="S") {
          $media = number_format($media,$decimali_graduatoria);
        } else {
          $media = truncate($media,$decimali_graduatoria);
        }
      }
      // Calcolo la media della differenza tra i punteggi sopra la media totale e la media stessa

      $bind = array();
      $bind[":codice_gara"] = $_POST["codice_gara"];
      $bind[":codice_lotto"] = $_POST["codice_lotto"];
      $bind[":media"] = $media;
      $sql = "SELECT SUM(r_punteggi_gare.punteggio) AS totale_punteggi, COUNT(DISTINCT r_punteggi_gare.codice_partecipante) as totale_partecipanti FROM ";
      $sql.= "r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = r_punteggi_gare.codice_partecipante ";
      $sql.= "WHERE r_punteggi_gare.codice_gara = :codice_gara AND ";
      $sql.= " r_punteggi_gare.codice_lotto = :codice_lotto AND ";
      $sql.= " r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S'";
      if ($taglio_ali != "") $sql.= " AND r_partecipanti.codice NOT IN (" . $taglio_ali . ") ";
      $sql .= "AND r_partecipanti.codice IN (";
      $sql .= "SELECT r_partecipanti.codice FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
      $sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara
               AND r_punteggi_gare.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";
      $sql.= " AND r_partecipanti.ammesso = 'S' ";
      $sql.= " GROUP BY r_punteggi_gare.codice_partecipante, r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto";
      $sql.= " HAVING SUM(r_punteggi_gare.punteggio) > :media)";
      $sql.= " GROUP BY r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ";
      $ris = $pdo->bindAndExec($sql,$bind);
      if ($ris->rowCount()>0) {
        $rec_range = $ris->fetch(PDO::FETCH_ASSOC);
        $scarto_medio = ($rec_range["totale_punteggi"] - ($media * $rec_range["totale_partecipanti"])) / $rec_range["totale_partecipanti"];
        if ($solo_soglia == "N") {
          if ($arrotondamento=="S") {
            $scarto_medio = number_format($scarto_medio,$decimali_graduatoria);
          } else {
            $scarto_medio = truncate($scarto_medio,$decimali_graduatoria);
          }
        }
        $somma = strval($somma);
        $perDecSomma = 0;
        if (strpos($somma,".")!==false) {
          $decimali_somma = explode(".",$somma);
          $decimali_somma = $decimali_somma[1];
          $first = (int)substr($decimali_somma,0,1);
          $second = (int)substr($decimali_somma,1,1);
          $perDecSomma = $first * $second;
        }
        $soglia_anomalia = ($media + $scarto_medio);
        
        $ribasso_soglia = $scarto_medio * $perDecSomma / 100;
        if ($solo_soglia == "N") {
          if ($arrotondamento=="S") {
            $ribasso_soglia = number_format($ribasso_soglia,$decimali_graduatoria);
          } else {
            $ribasso_soglia = truncate($ribasso_soglia,$decimali_graduatoria);
          }
        }

        if (empty($_POST["interpretazione_anomalia"]) || $_POST["interpretazione_anomalia"] == "M") {
          // I interpretazione: sottrazione
          $soglia_anomalia -= $ribasso_soglia;
        } else if ($_POST["interpretazione_anomalia"] == "T") {
          // II interpretazione: decremento %
          $soglia_anomalia = $soglia_anomalia - (($soglia_anomalia * $ribasso_soglia) / 100);
        }
          

        // III interpretazione: modltiplicazione del prodotto percentuale * scarto e decremento percentuale
        // $ribasso_soglia = ($perDecSomma / 100) * $scarto_medio / 100;
        // $soglia_anomalia = $soglia_anomalia - (($soglia_anomalia * $ribasso_soglia) / 100);


        if ($arrotondamento=="S") {
          $soglia_anomalia = number_format($soglia_anomalia,$decimali_graduatoria);
        } else {
          $soglia_anomalia = truncate($soglia_anomalia,$decimali_graduatoria);
        }
        $msg = "Anomalia calcolata ai sensi dell'allegato II.2 del D.Lgs 36/2023 - Metodo A c. 1 - Somma: " . $somma . " - Media: " . $media . " - Scarto Medio: " . $scarto_medio . " - Decremento soglia: " . $ribasso_soglia . " - Soglia di anomalia: " . $soglia_anomalia;
        // if (empty($_POST["interpretazione_anomalia"]) || $_POST["interpretazione_anomalia"] == "M") {
        //   $msg .= "Circolare MIT del 24 Ottobre 2019 - Decremento del valore assoluto risultante dal decremento dello scarto medio";
        // } else if ($_POST["interpretazione_anomalia"] == "T") {
        //   $msg .= "Sentenza TAR Marche 622/2019 - Ulteriore decremento percentuale risultate dal decremento dello scarto medio";
        // }
        $msg .=  "\\n";
      } else {
        $errore_calcolo_soglia = true;
      }
    } else {
      $errore_calcolo_soglia = true;
    }
  }
?>
