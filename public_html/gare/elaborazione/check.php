<?
  $show=false;
  $check_modello=false;
  $bind = array();
  $bind[":codice"] = $record["codice"];
  $sql = "SELECT b_importi_gara.*, b_tipologie_importi.titolo AS tipologia FROM b_importi_gara JOIN
          b_tipologie_importi ON b_importi_gara.codice_tipologia = b_tipologie_importi.codice
          WHERE b_importi_gara.codice_gara = :codice";
  $ris_importi = $pdo->bindAndExec($sql,$bind);
  $totale_gara = 0;
  if ($ris_importi->rowCount() > 0) {
    while($rec_importo = $ris_importi->fetch(PDO::FETCH_ASSOC)) {
      $totale_gara = $totale_gara + $rec_importo["importo_base"] + $rec_importo["importo_oneri_no_ribasso"]; // + $rec_importo["importo_oneri_ribasso"] + $rec_importo["importo_personale"];
    }
  }
  $bind = array();
  $bind[":tipologia"] = $record["codice_tipologia"];
  $bind[":criterio"] = $record["codice_criterio"];
  $bind[":procedura"] = $record["codice_procedura"];
  $bind[":totale_gara"] = $totale_gara;
  $strsql  = "SELECT b_modelli_new.* FROM b_modelli_new WHERE attivo = 'S' AND (tipologia = :tipologia OR tipologia = 0)
              AND (criterio = :criterio OR criterio = 0)  AND procedura = :procedura
              AND (importo_massimo >= :totale_gara OR importo_massimo = 0)
              AND (importo_minimo <= :totale_gara OR importo_minimo = 0)";
  switch($rec["link"]) {
    case "/gare/elaborazione/modello.php?type=Bando":
      $strsql .= " AND tipo = 'Bando' ";
      $check_modello = true;
    break;
    case "/gare/elaborazione/modello.php?type=Disciplinare":
      $strsql .= " AND tipo = 'Disciplinare' ";
      $check_modello = true;
    break;
    case "/gare/elaborazione/modello.php?type=Invito":
      $strsql .= " AND tipo = 'Invito' ";
      $check_modello = true;
    break;
    default: $show=true; break;
  }
  if ($check_modello) {
    $risultato_modelli = $pdo->bindAndExec($strsql,$bind);
    if ($risultato_modelli->rowCount() > 0) $show=true;
  }
?>
