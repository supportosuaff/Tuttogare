<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index["danger"];
    $bind = array();
    $bind[":codice"] = $record["codice"];

    $ris_lotti = $pdo->bindAndExec("SELECT codice FROM b_lotti WHERE codice_gara = :codice_gara ",array(":codice_gara"=>$record["codice"]));
    $strsql_check = "SELECT codice,bozza FROM b_documentale WHERE tipo= 'svincolo_fideiussione' AND attivo = 'S' AND sezione = 'gara' AND codice_gara = :codice AND codice_lotto = :codice_lotto ";
    if ($ris_lotti->rowCount() > 0) {
      $number_ok = 0;
      while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
        $bind[":codice_lotto"] = $lotto["codice"];
        $ris_check = $pdo->bindAndExec($strsql_check,$bind);
        if ($ris_check->rowCount() > 0) {
          $st_color = $st_index["warning"];
          $rec_check = $ris_check->fetch(PDO::FETCH_ASSOC);
          if($rec_check["bozza"] === "N") $number_ok++;
        }
      }
      if ($ris_lotti->rowCount() == $number_ok) {
        $st_color = $st_index["ok"];
      } else if ($number_ok > 0) {
        $st_color = $st_index["warning"];
      }
    } else {
      $bind[":codice_lotto"] = 0;
      $ris_check = $pdo->bindAndExec($strsql_check,$bind);
      if ($ris_check->rowCount() > 0) $st_color = $st_index["ok"];
      $rec_check = $ris_check->fetch(PDO::FETCH_ASSOC);
      if($rec_check["bozza"] === "S") $st_color = $st_index["warning"];
    }
  }
?>
