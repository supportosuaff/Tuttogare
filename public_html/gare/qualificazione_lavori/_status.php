<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["no"];
    $bind = array();
    $bind[":codice"]=$record["codice"];
    $strsql = "SELECT codice FROM b_qualificazione_lavori WHERE codice_gara = :codice ";
    $ris_qualificazione = $pdo->bindAndExec($strsql,$bind);
    if ($ris_qualificazione->rowCount()>0) $st_color = $st_index ["ok"];
  }

?>
