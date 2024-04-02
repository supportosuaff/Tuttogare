<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index ["danger"];
    $bind = array();
    $bind[":codice"]=$record["codice"];
    $strsql = "SELECT codice FROM b_rdo_ad WHERE codice_gara = :codice ";
    $ris_check = $pdo->bindAndExec($strsql,$bind);
    if ($ris_check->rowCount()>0) {
      $st_color = $st_index ["warning"];
    }
    if ($record["stato"] > 4) $st_color = $st_index ["ok"];
  }

?>
