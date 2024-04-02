<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["danger"];
    $bind = array();
    $bind[":codice_gara"]=$record["codice"];
    $sql = "SELECT * FROM b_commissioni WHERE codice_gara = :codice_gara AND valutatore = ";
    $sql .= (strpos($rec["link"],"tecnica=true")!==false) ? "'S'" : "'N'";
    $ris_stat = $pdo->bindAndExec($sql,$bind);
    if ($ris_stat->rowCount() > 0) {
      $st_color = $st_index ["ok"];
      if ($ris_stat->rowCount() < 3) $st_color = $st_index ["warning"];
    }
  }

?>
