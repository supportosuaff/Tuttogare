
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
      $i++;
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
      $i++;  
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
      if ($solo_soglia == "N") {
        if ($arrotondamento=="S") {
          $media = number_format($media,$decimali_graduatoria);
        } else {
          $media = truncate($media,$decimali_graduatoria);
        }
      }
      
      $somma = strval($somma);
      $modificaSoglia = 0;
      if (strpos($somma,".")!==false) {
        $decimali_somma = explode(".",$somma);
        $decimali_somma = $decimali_somma[1];
        $first = (int)substr($decimali_somma,0,1);
        $second = (int)substr($decimali_somma,1,1);
        if ($first != "0") {
          $modificaSoglia = $media * $first / 100;
          if($second%2){
            $modificaSoglia *= 1;
          }else{
            $modificaSoglia *= -1;
          }
        }
      }
      $soglia_anomalia = $media + $modificaSoglia;
      if ($arrotondamento=="S") {
        $soglia_anomalia = number_format($soglia_anomalia,$decimali_graduatoria);
      } else {
        $soglia_anomalia = truncate($soglia_anomalia,$decimali_graduatoria);
      }
      $msg .= " - Somma: " . truncate($somma,2) . " - Media: " . $media . " - Soglia di anomalia: " . $soglia_anomalia . "\\n";
      $esclusione_automatica = true;
    } else {
      $errore_calcolo_soglia = true;
    }
  }
?>
