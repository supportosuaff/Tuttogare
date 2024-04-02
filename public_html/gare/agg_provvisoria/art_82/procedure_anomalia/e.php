<?
  if ($edit) {
    if (!empty($record_gara["coef_e"])){
      $coef_e = $record_gara["coef_e"];
    } else if (!empty($record_lotto["coef_e"])) {
      $coef_e = $record_lotto["coef_e"];
    } else {
      $coef_e = (!empty($_POST["coef_e"])) ? $_POST["coef_e"] : "z";
      if ($coef_e == "z") {
        if (strtotime($record_gara["data_pubblicazione"]) < strtotime('2017-05-20')) {
          $coefficenti = array("0.6","0.8","1","1.2","1.4");
          $selezione = rand(0,4);
        } else {
          $selezione = rand(0,3);
          $coefficenti = array("0.6","0.7","0.8","0.9");
          $selezione = rand(0,3);
        }
        shuffle($coefficenti);
        $coef_e = $coefficenti[$selezione];
        $coefficenti["selezione"] = $selezione;
        $sequenza_coef = json_encode($coefficenti);

        $bind = array();
        $bind[":codice_gara"] = $_POST["codice_gara"];
        $bind[":sequenza_coef"] = $sequenza_coef;
        if ($_POST["codice_lotto"] == 0) {
          $sql = "UPDATE b_gare SET sequenza_coef = :sequenza_coef WHERE codice = :codice_gara";
        } else {
          $bind[":codice_lotto"] = $_POST["codice_lotto"];
          $sql = "UPDATE b_lotti SET sequenza_coef = :sequenza_coef WHERE codice = :codice_lotto AND codice_gara = :codice_gara";
        }
      }
    }
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

    $sql = "SELECT SUM(r_punteggi_gare.punteggio) / COUNT(DISTINCT r_punteggi_gare.codice_partecipante) as media FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
    $sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara
             AND r_punteggi_gare.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 AND r_partecipanti.ammesso = 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
    if ($taglio_ali != "") $sql.= " AND r_partecipanti.codice NOT IN (" . $taglio_ali . ") ";
    $sql.= " GROUP BY r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ";

    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount()>0) {
      $rec_media = $ris->fetch(PDO::FETCH_ASSOC);
      $media = $rec_media["media"];
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
        $soglia_anomalia = $media + ($scarto_medio * $coef_e);
        if ($arrotondamento=="S") {
          $soglia_anomalia = number_format($soglia_anomalia,$decimali_graduatoria);
        } else {
          $soglia_anomalia = truncate($soglia_anomalia,$decimali_graduatoria);
        }
        $msg .= "lettera e) - Media: " . $media . " - Scarto Medio: " . $scarto_medio . " - Coefficiente: " . $coef_e . " - Soglia di anomalia: " . $soglia_anomalia . "\\n";
      } else {
        $errore_calcolo_soglia = true;
      }
    } else {
      $errore_calcolo_soglia = true;
    }
  }
?>
