<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["danger"];
    $ris_lotti = $pdo->bindAndExec("SELECT codice FROM b_lotti WHERE codice_gara = :codice_gara ",array(":codice_gara"=>$record["codice"]));
    $ris_aste = $pdo->bindAndExec("SELECT codice FROM b_aste WHERE codice_gara = :codice_gara ",array(":codice_gara"=>$record["codice"]));
    if ($ris_aste->rowCount() > 0) {
      if ($ris_lotti->rowCount() > 0) {
        if ($ris_lotti->rowCount() == $ris_aste->rowCount()) {
          $st_color = $st_index ["ok"];
        } else {
          $st_color = $st_index ["warning"];
        }
      } else {
        $st_color = $st_index ["ok"];
      }
    }
  }

?>
