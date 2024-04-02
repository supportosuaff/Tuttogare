<?
  if (isset($record) && isset($st_index)) {
    $bind = array();
    $bind[":codice_gara"] = $record["codice"];
    $sql_check = "SELECT codice FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'gare'";
    $ris_check = $pdo->bindAndExec($sql_check,$bind);
    $st_color = $st_index ["danger"];
    if ($ris_check->rowCount() > 0) $st_color = $st_index ["ok"];
  }

?>
