<?
$show = false;
if (isset($record["codice"])) {

  $bind = array();
  $bind[":codice"] = $record["codice"];
  $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

  $strsql  = "SELECT b_quesiti_concorsi.* ";
  $strsql .= "FROM b_quesiti_concorsi ";
  $strsql .= "WHERE codice_ente = :codice_ente AND codice_gara = :codice";
  $strsql .= " ORDER BY timestamp DESC " ;
  $ris_chiarimenti  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
  if ($ris_chiarimenti->rowCount()>0) $show = true;
}
?>
