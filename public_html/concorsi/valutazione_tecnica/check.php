<?
$show=false;
if (isset($record["codice"])) {
  $bind = array();
  $bind[":codice"] = $record["codice"];
  $sql = "SELECT * FROM b_criteri_valutazione_concorsi WHERE codice_gara = :codice";
  $ris = $pdo->bindAndExec($sql,$bind);
  if ($ris->rowCount() > 0) $show=true;
}
