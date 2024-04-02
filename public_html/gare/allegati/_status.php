<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["danger"];
    $bind = array();
    $bind[":codice"]=$record["codice"];
    $sql = "SELECT codice FROM b_allegati WHERE codice_gara = :codice AND sezione = 'gara' AND online = 'S' ORDER BY cartella, codice";
    $ris_allegati = $pdo->bindAndExec($sql,$bind);
    if ($ris_allegati->rowCount() > 0) $st_color = $st_index ["ok"];
  }

?>
