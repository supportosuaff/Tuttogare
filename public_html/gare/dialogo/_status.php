<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["danger"];
    $bind = array();
    $bind[":codice"]=$record["codice"];
    $sql = "SELECT * FROM b_dialogo WHERE codice_gara = :codice ";
    $ris_dialogo = $pdo->bindAndExec($sql,$bind);
    if ($ris_dialogo->rowCount() > 0) {
      $st_color = $st_index ["warning"];
      if ($record["dialogo_chiuso"]=="S") $st_color = $st_index ["ok"];
    }
  }

?>
