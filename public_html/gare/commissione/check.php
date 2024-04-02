<?
$show=false;
if (isset($record["codice"])) {
  $tecnica = false;
  if (strpos($rec["link"],"tecnica=true")!==false) $tecnica = true;
  if (!$tecnica) {
    $show = true;
  } else {
    $bind = array();
    $bind[":codice_gara"] = $record["codice"];
    $sql_criteri = "SELECT b_valutazione_tecnica.*
              FROM b_valutazione_tecnica
              JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
              WHERE b_valutazione_tecnica.codice_gara = :codice_gara
              AND (b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N') ";
    $ris_check_criteri = $pdo->bindAndExec($sql_criteri,$bind);
    $rowCount = $ris_check_criteri->rowCount();
    if ($rowCount > 0) $show = true;
  }
}
