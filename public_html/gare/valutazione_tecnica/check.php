<?
$show=false;
if (isset($record["codice"]) && $record["nuovaOfferta"] == "N") {
  $bind = array();
  $bind[":codice"] = $record["codice"];
  $sql = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice";
  $ris = $pdo->bindAndExec($sql,$bind);
  if ($ris->rowCount() > 0) $show=true;
}
