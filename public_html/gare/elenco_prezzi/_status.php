<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["danger"];
    $bind = array();
    $bind[":codice"] = $record["codice"];
    if ($record["nuovaOfferta"] == "S") {
      $ris_elementi = $pdo->bindAndExec("SELECT codice FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND valutazione = 'E'",array(":codice_gara"=>$record["codice"]));
      $codice_controllo = "codice_criterio";
    } else {
      $ris_elementi = $pdo->bindAndExec("SELECT codice FROM b_lotti WHERE codice_gara = :codice_gara ",array(":codice_gara"=>$record["codice"]));
      $codice_controllo = "codice_lotto";
    }
    $strsql_check = "SELECT codice FROM b_elenco_prezzi WHERE codice_gara = :codice AND {$codice_controllo} = :codice_controllo ";
    if ($ris_elementi->rowCount() > 0) {
      $number_ok = 0;
      while($elemento = $ris_elementi->fetch(PDO::FETCH_ASSOC)) {
        $bind[":codice_controllo"] = $elemento["codice"];
        $ris_prezzi = $pdo->bindAndExec($strsql_check,$bind);
        if ($ris_prezzi->rowCount() > 0) $number_ok++;
      }
      if ($ris_elementi->rowCount() == $number_ok) {
        $st_color = $st_index ["ok"];
      } else if ($number_ok > 0) {
        $st_color = $st_index ["warning"];
      }
    } else if($codice_controllo=="codice_lotto") {
      $bind[":codice_controllo"] = 0;
      $ris_prezzi = $pdo->bindAndExec($strsql_check,$bind);
      if ($ris_prezzi->rowCount() > 0) $st_color = $st_index ["ok"];
    }
  }

?>
