<?
$show=false;
if (isset($record["codice"])) {
  $bind = array();
  $bind[":codice"] = $record["codice"];
  $sql = "SELECT SUM(importo_base) AS importo_base FROM b_importi_gara ";
  $sql .= "WHERE (codice_tipologia = \"24\" OR codice_tipologia = \"26\" OR codice_tipologia = \"27\" OR codice_tipologia = \"21\") AND codice_gara = :codice GROUP BY codice_gara ";
  $ris_importi = $pdo->bindAndExec($sql,$bind);
  if ($ris_importi->rowCount()>0) $show=true;
}
