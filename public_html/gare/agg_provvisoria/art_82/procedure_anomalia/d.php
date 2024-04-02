<?
  if ($edit) {

    $bind = array();
    $bind[":codice_gara"] = $_POST["codice_gara"];
    $bind[":codice_lotto"] = $_POST["codice_lotto"];

    $sql = "SELECT r_partecipanti.codice, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
    $sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara
             AND r_punteggi_gare.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 AND r_partecipanti.ammesso = 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
    $sql.= " GROUP BY r_punteggi_gare.codice_partecipante, r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ORDER BY totale_punteggio DESC";
    $ris = $pdo->bindAndExec($sql,$bind);
    $somma_punteggi=0;
    while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) $somma_punteggi += $rec["totale_punteggio"];

    $media = $somma_punteggi / $ris->rowCount();
    if ($solo_soglia == "N") {
      if ($arrotondamento=="S") {
        $media = number_format($media,$decimali_graduatoria);
      } else {
        $media = truncate($media,$decimali_graduatoria);
      }
    }
    if (strtotime($record_gara["data_pubblicazione"]) < strtotime('2017-05-20')) {
      $soglia_anomalia = $media * 0.80;
    } else {
      $soglia_anomalia = $media * 1.10;
    }
    if ($arrotondamento=="S") {
      $soglia_anomalia = number_format($soglia_anomalia,$decimali_graduatoria);
    } else {
      $soglia_anomalia = truncate($soglia_anomalia,$decimali_graduatoria);
    }
    $msg .= "lettera d) - Media: " . $media . " - Soglia di anomalia: " . $soglia_anomalia . "\\n";
  }
?>
