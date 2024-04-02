<?
$show = false;
if (isset($record["codice"])) {

  $bind = array();
  $bind[":codice"] = $record["codice"];
  $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

  $strsql  = "SELECT codice FROM b_sopralluoghi WHERE codice_ente = :codice_ente AND codice_gara = :codice";
  $ris_sopralluoghi  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
  if ($ris_sopralluoghi->rowCount()>0) $show = true;
}
?>
