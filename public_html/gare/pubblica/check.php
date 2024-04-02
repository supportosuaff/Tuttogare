<?
  $show=false;
  if (check_configurazione_offerta($record["codice"])["status"]=="ok" || $record["nuovaOfferta"] == "N") {
    if ($record["nuovaOfferta"] == "S") {
      $bind = array();
      $bind[":codice"] = $record["codice"];
      $sql = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice AND valutazione = 'E'";
      $ris_elementi = $pdo->bindAndExec($sql,$bind);
      if ($ris_elementi->rowCount() > 0) {
        $strsql_check = "SELECT codice FROM b_elenco_prezzi WHERE codice_gara = :codice AND codice_criterio = :codice_controllo ";
        $number_ok = 0;
        while($elemento = $ris_elementi->fetch(PDO::FETCH_ASSOC)) {
          $bind[":codice_controllo"] = $elemento["codice"];
          $ris_prezzi = $pdo->bindAndExec($strsql_check,$bind);
          if ($ris_prezzi->rowCount() > 0) $number_ok++;
        }
        if ($ris_elementi->rowCount() == $number_ok) $show = true;
      } else {
        $show = true;
      }
    } else {
      $show = true;
    }
  }
?>
