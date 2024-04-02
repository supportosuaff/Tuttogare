<?
  $show=false;
  if (isset($record["codice"])) {
    $bind = array();
    $bind[":codice"]=$record["codice"];
    $bind[":codice_fase"]=$fase["codice"];
    $sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice AND attiva = 'N' AND codice > :codice_fase ";
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount() > 0) $show=true;
  }
?>
