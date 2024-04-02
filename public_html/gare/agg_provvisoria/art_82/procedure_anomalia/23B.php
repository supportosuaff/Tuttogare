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
        $posDecimale = strpos($somma,".");
        $limit = strlen($somma);
        if (strpos($somma,".")!==false) {
          $decimali_somma = explode(".",$somma);
          $limit = strlen($decimali_somma[0]) + 2;
          $decimali_somma = $decimali_somma[1];
          $first = (int)substr($decimali_somma,0,1);
          $second = (int)substr($decimali_somma,1,1);
          $perDecSomma = $first * $second;
        }
        $valoreE = 0;
        $sommaRiferimento = str_replace(".","",$somma);
        for($i = 0; $i < $limit; $i++) {
          $valoreE += (int)substr($sommaRiferimento, $i, 1);
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
        if ($valoreE % 2 == 0) {
          if (empty($settings["interpretazione_anomalia"]) || $settings["interpretazione_anomalia"] == "M") {
            $soglia_anomalia -= $ribasso_soglia;
          } else if ($settings["interpretazione_anomalia"] == "T") {
            $soglia_anomalia = $soglia_anomalia - (($soglia_anomalia * $ribasso_soglia) / 100);
          }
        } else {
          if (empty($settings["interpretazione_anomalia"]) || $settings["interpretazione_anomalia"] == "M") {
            $soglia_anomalia += $ribasso_soglia;
          } else if ($settings["interpretazione_anomalia"] == "T") {
            $soglia_anomalia = $soglia_anomalia + (($soglia_anomalia * $ribasso_soglia) / 100);
          }
        }

        if ($arrotondamento=="S") {
          $soglia_anomalia = number_format($soglia_anomalia,$decimali_graduatoria);
        } else {
          $soglia_anomalia = truncate($soglia_anomalia,$decimali_graduatoria);
        }
        $bind = array();
        $bind[":codice_gara"] = $_POST["codice_gara"];
        $bind[":codice_lotto"] = $_POST["codice_lotto"];
        $bind[":soglia"] = $soglia_anomalia;

        $sql = "SELECT SUM(r_punteggi_gare.punteggio) as totale_punteggio 
                FROM r_partecipanti 
                JOIN r_punteggi_gare ON r_partecipanti.codice = r_punteggi_gare.codice_partecipante
                WHERE r_punteggi_gare.codice_gara = :codice_gara
                AND r_punteggi_gare.codice_lotto = :codice_lotto 
                AND r_partecipanti.codice_capogruppo = 0 
                AND r_partecipanti.ammesso = 'S'
                AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)
                GROUP BY r_punteggi_gare.codice_partecipante, r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto
                HAVING totale_punteggio <= :soglia
                ORDER BY totale_punteggio DESC";
        $risSecondoPrezzo = $pdo->bindAndExec($sql,$bind);
        $second_max = null;
        if ($risSecondoPrezzo->rowCount() > 0) {
          $i = 0;
          while($prezzo = $risSecondoPrezzo->fetch(PDO::FETCH_COLUMN)) {
            if ($i > 0 && $prezzo != $lastPrezzo) {
              $second_max = $prezzo;
              $table = (!empty($_POST["codice_lotto"])) ? "b_lotti" : "b_gare";
              $pdo->go("UPDATE {$table} SET ribasso = :ribasso WHERE codice = :codice",[":ribasso"=>$second_max,":codice"=>(!empty($_POST["codice_lotto"])) ? $_POST["codice_lotto"] : $_POST["codice_gara"]]);
              break;
            }
            $i++;
            $lastPrezzo = $prezzo;
          }
        }
        $msg = "Anomalia calcolata ai sensi dell'allegato II.2 del D.Lgs 36/2023 - Metodo B - Sommatoria: {$somma} - Somma cifre: {$valoreE} - Media: {$media} - Scarto medio: {$scarto_medio} - Variazione: {$ribasso_soglia} - Soglia individuata: {$soglia_anomalia} - Ribasso di aggiudicazione: {$second_max}";
        $msg .=  "\\n";
      } else {
        $errore_calcolo_soglia = true;
      }
    } else {
      $errore_calcolo_soglia = true;
    }
  }
?>
