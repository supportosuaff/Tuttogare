<?
  $show = false;
  if ($apertura=="S") {
    $show = true;
  } else {
    $sql_check = "SELECT * FROM b_integrazioni_concorsi WHERE codice_gara = :codice_gara ";
    $ris_check = $pdo->bindAndExec($sql_check,array(":codice_gara"=>$record["codice"]));
    if ($ris_check->rowCount() > 0) $show = true;
  }
?>
