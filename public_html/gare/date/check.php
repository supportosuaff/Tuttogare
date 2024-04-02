<?
  if (isset($record)) {
    $show = true;
    $sql = "SELECT * FROM b_procedure WHERE codice = :codice_procedura AND directory = 'dialogo'";
    $ris_check = $pdo->bindAndExec($sql,array(":codice_procedura"=>$record["codice_procedura"]));
    if ($ris_check->rowCount() > 0 && $record["dialogo_chiuso"]!="S") $show=false;
  }
?>
