<?
  $show = false;
  $bind = array();
  $bind[":criterio"] = $record["codice_criterio"];
  $sql = "SELECT * FROM b_criteri WHERE codice = :criterio AND directory = 'art_82'";
  $ris_scelta = $pdo->bindAndExec($sql,$bind);
  if ($ris_scelta->rowCount() > 0 && (strtotime($record["data_pubblicazione"]) > strtotime('2016-04-20')) && (strtotime($record["data_pubblicazione"]) < strtotime('2019-04-19'))) {
    $show = true;
  }
  if ($record["norma"] == "2023-36" && $ris_scelta->rowCount() > 0 && strtolower($record["tipologia"]) != "forniture") {
    $show = true;
  }
?>
