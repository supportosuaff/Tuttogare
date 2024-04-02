<?
$show=false;
if (isset($record["codice"])) {
  if ($record["nuovaOfferta"] == "N") {
    $bind = array();
    $bind[":codice_gara"] = $record["codice"];
    $sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 58";
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount() > 0) $show=true;
  } else {
    $bind = array();
    $bind[":codice_gara"] = $record["codice"];
    $sql = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND valutazione = 'E'";
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount() > 0) $show=true;
  }
}
