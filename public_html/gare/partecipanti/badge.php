<?
if (isset($record) && isset($st_index)) {
  $bind = array();
  $bind[":codice"]=$record["codice"];
  $sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";
  $ris_badge = $pdo->bindAndExec($sql,$bind);
  if ($ris_badge->rowCount()>0) {
    echo "<span class=\"badge\">" . $ris_badge->rowCount() . "</span>";
  }
}

?>