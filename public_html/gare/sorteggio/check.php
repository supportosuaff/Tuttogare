<?
$show=false;
if (isset($record["codice"]) && $record["codice_procedura"] != 7) {
  $numero_sorteggio = $record["numero_sorteggio"];
  $bind = array();
  $bind[":codice"] = $record["codice"];
  $sql = "SELECT COUNT(codice) AS primi FROM r_partecipanti WHERE primo = 'S' AND codice_gara = :codice GROUP BY codice_lotto HAVING primi > 1 ";
  $ris_sorteggio  = $pdo->bindAndExec($sql,$bind); //invia la query contenuta in $strsql al database apero e connesso
  $ris_sorteggio_casuale = $pdo->go("SELECT * FROM b_sorteggi WHERE codice_gara = :codice ",$bind);
  if ($ris_sorteggio_casuale->rowCount()>0 || $ris_sorteggio->rowCount()>0 || $numero_sorteggio > 0) $show=true;
}
