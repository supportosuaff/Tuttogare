<?
  if (!empty($elabora_coda)) {
    $ente = $pdo->go("SELECT codice FROM b_enti WHERE dominio IS NOT NULL AND dominio <> ''")->fetch(PDO::FETCH_ASSOC);
    if (!empty($ente)) {
      $pathSyncClass = "{$root}/inc/integrazioni/{$ente["codice"]}/syncERP.class.php";
      if (file_exists($pathSyncClass)) include_once($pathSyncClass);
      if (class_exists("syncERP")) {
        $sync = new syncERP();
        $gareScadute = $pdo->go("SELECT codice FROM b_gare WHERE data_scadenza < now() AND sendScaduta = 'N' AND id_suaff > 0 AND stato = 3");
        $updateScaduta = $pdo->prepare("UPDATE b_gare SET sendScaduta = 'S' WHERE codice = :codice");
        if ($gareScadute->rowCount() > 0) {
          while($garaScaduta = $gareScadute->fetch(PDO::FETCH_ASSOC)) {
            $sync->sendUpdateRequest($garaScaduta["codice"],"scaduta");
            $updateScaduta->bindValue(":codice",$garaScaduta["codice"]);
            $updateScaduta->execute();
          }
        }
      }
    }
  }
?>