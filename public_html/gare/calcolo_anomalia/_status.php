<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["danger"];
    $bind = array();
    $bind[":codice"] = $record["codice"];
    $ris_lotti = $pdo->bindAndExec("SELECT algoritmo_anomalia FROM b_lotti WHERE codice_gara = :codice_gara ",array(":codice_gara"=>$record["codice"]));

    if ($ris_lotti->rowCount() > 0) {
      $number_ok = 0;
      while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($lotto["algoritmo_anomalia"])) {
          $number_ok++;
        }
      }
      if ($ris_lotti->rowCount() == $number_ok) {
        $st_color = $st_index ["ok"];
      } else if ($number_ok > 0) {
        $st_color = $st_index ["warning"];
      }
    } else {
      if (!empty($record["algoritmo_anomalia"])) {
        $st_color = $st_index ["ok"];
      }
    }
  }

?>
