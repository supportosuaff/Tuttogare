<?
  if (isset($record) && isset($st_index)) {
    $st_color = $st_index["danger"];
    $bind=array();
    $bind[":codice"] = $record["codice"];
    $sql = "SELECT b_incaricati.*, r_incarichi.ruolo,r_incarichi.numero_atto, r_incarichi.data_atto,r_incarichi.codice AS codice_incarico FROM b_incaricati JOIN r_incarichi ON b_incaricati.codice = r_incarichi.codice_incaricato
    WHERE codice_riferimento = :codice AND sezione = 'gare' ";
    $ris_incarico = $pdo->bindAndExec($sql,$bind);
    if ($ris_incarico->rowCount()>0) $st_color = $st_index["ok"];
  }
?>
