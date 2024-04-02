<?
  if (isset($record) && isset($st_index)) {
    $st_color = "";
    $bind = array();
    $bind[":codice"]=$record["codice"];
    $sql = "SELECT * FROM b_integrazioni WHERE codice_gara = :codice ";
    $ris_check = $pdo->bindAndExec($sql,$bind);
    if ($ris_check->rowCount() > 0) {
      $st_color = $st_index ["warning"];
      $sql = "SELECT * FROM b_integrazioni WHERE codice_gara = :codice AND data_scadenza < now() ";
      $ris_check_sca = $pdo->bindAndExec($sql,$bind);
      if ($ris_check_sca->rowCount() == $ris_check->rowCount()) $st_color = $st_index ["ok"];
    }
  }

?>
