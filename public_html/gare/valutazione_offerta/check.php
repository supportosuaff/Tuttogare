<?
$show=false;
if (isset($record["codice"])) {
  $bind = array();
  $bind[":codice_gara"] = $record["codice"];
  $sql_criteri = "SELECT b_valutazione_tecnica.*
            FROM b_valutazione_tecnica
            JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
            WHERE b_valutazione_tecnica.codice_gara = :codice_gara ";
  $economica = true;
  if (strpos($rec["link"],"tecnica=true")!==false) $economica = false;
  if ($economica) {
    $sql_criteri .= "AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
  } else {
    $sql_criteri .= "AND (b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N') ";
  }
  $ris_check_criteri = $pdo->bindAndExec($sql_criteri,$bind);
  $rowCount = $ris_check_criteri->rowCount();
  if ($rowCount > 0) {
    $show = true;
    if ($economica && $rowCount == 1) {
      $criterio = $ris_check_criteri->fetch(PDO::FETCH_ASSOC);
      if ($criterio["punteggio"] == 100 && ($criterio["valutazione"] == "P" || ($criterio["valutazione"] == 'E' && empty($criterio["options"])))) $show = false;
    }
    if ($economica && $record["nuovaOfferta"] == "N") $show = false;
  }
}
