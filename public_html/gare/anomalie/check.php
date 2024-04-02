<?
$show=false;
if (isset($record["codice"])) {
  $bind=array();
  $bind[":codice"] = $record["codice"];
  $strsql  = "SELECT r_partecipanti.* ";
  $strsql .= "FROM r_partecipanti ";
  $strsql .= "WHERE codice_gara = :codice AND anomalia = 'S'";
  $ris_anomalie  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
  if ($ris_anomalie->rowCount()>0) $show=true;
}
