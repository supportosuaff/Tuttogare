<?
$show=false;
if (isset($record["codice"])) {
  $bind_badge = array();
  $bind_badge[":codice"] = $record["codice"];
  $sql = "SELECT b_date_apertura.*, b_criteri_buste.nome FROM b_date_apertura JOIN b_criteri_buste ON b_date_apertura.codice_busta = b_criteri_buste.codice 
          WHERE b_date_apertura.codice_gara = :codice ORDER BY codice";
  $ris_badge = $pdo->bindAndExec($sql,$bind_badge);
  if ($ris_badge->rowCount()>0)  $show = true;
}
