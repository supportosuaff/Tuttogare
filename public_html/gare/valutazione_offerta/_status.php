<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["danger"];
    $bind = array();
    $bind[":codice_gara"] = $record["codice"];
    $ris_lotti = $pdo->bindAndExec("SELECT codice FROM b_lotti WHERE codice_gara = :codice_gara ",array(":codice_gara"=>$record["codice"]));
    $strsql_check = "SELECT b_punteggi_criteri.*
              FROM b_valutazione_tecnica
              JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
              JOIN b_punteggi_criteri ON b_valutazione_tecnica.codice = b_punteggi_criteri.codice_criterio
              WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND (b_valutazione_tecnica.codice_lotto = 0 OR b_valutazione_tecnica.codice_lotto = :codice_lotto)";
    $economica = true;
    if (strpos($rec["link"],"tecnica=true")!==false) $economica = false;
    if ($economica) {
      $strsql_check .= " AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
    } else {
      $strsql_check .= " AND (b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N') ";
    }

    if ($ris_lotti->rowCount() > 0) {
      $number_ok = 0;
      while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
        $bind[":codice_lotto"] = $lotto["codice"];
        $ris_prezzi = $pdo->bindAndExec($strsql_check,$bind);
        if ($ris_prezzi->rowCount() > 0) $number_ok++;
      }
      if ($ris_lotti->rowCount() == $number_ok) {
        $st_color = $st_index ["ok"];
      } else if ($number_ok > 0) {
        $st_color = $st_index ["warning"];
      }
    } else {
      $bind[":codice_lotto"] = 0;
      $ris_prezzi = $pdo->bindAndExec($strsql_check,$bind);
      if ($ris_prezzi->rowCount() > 0) $st_color = $st_index ["ok"];
    }
    if ($record["stato"] > 4) $st_color = $st_index ["ok"];
  }



?>
