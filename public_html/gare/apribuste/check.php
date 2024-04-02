<?
$show = true;
if (strpos($rec["link"],"fase")!==false) {
  if (!isset($record["fasi"]) || (isset($record["fasi"]) && $record["fasi"] == "N")) $show = false;
  if ($show) {
    if ($pdo->go("SELECT codice FROM r_partecipanti_Ifase WHERE codice_gara = :codice_gara",[":codice_gara"=>$record["codice"]])->rowCount() < 1) $show = false;
  }
}
?>
