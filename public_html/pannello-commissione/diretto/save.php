<?
  if (isset($_SESSION["codice_commissario"]) && checkCommissario($_POST["codice_gara"])) {
    $sql_value = "SELECT * FROM b_coefficienti_commissari
                  WHERE codice_partecipante = :codice_partecipante
                  AND codice_criterio = :codice_criterio
                  AND codice_commissario = :codice_commissario ";
    $sql_insert = "INSERT INTO b_coefficienti_commissari (codice_gara,codice_lotto,codice_partecipante,
                                                         codice_criterio,codice_commissario,coefficiente)
                   VALUES (:codice_gara,:codice_lotto,:codice_partecipante,:codice_criterio,:codice_commissario,:coefficiente)";
    $sql_update = "UPDATE b_coefficienti_commissari SET
                          codice_partecipante = :codice_partecipante,
                          codice_criterio = :codice_criterio,
                          codice_commissario = :codice_commissario,
                          coefficiente = :coefficiente
                          WHERE codice = :codice";
    $ris_value = $pdo->prepare($sql_value);
    $ris_value->bindValue(":codice_criterio",$criterio["codice"]);
    $ris_value->bindValue(":codice_commissario",$_SESSION["codice_commissario"]);

    $ris_insert = $pdo->prepare($sql_insert);
    $ris_insert->bindValue(":codice_gara",$gara["codice"]);
    $ris_insert->bindValue(":codice_lotto",$codice_lotto);
    $ris_insert->bindValue(":codice_criterio",$criterio["codice"]);
    $ris_insert->bindValue(":codice_commissario",$_SESSION["codice_commissario"]);

    $ris_update = $pdo->prepare($sql_update);
    $ris_update->bindValue(":codice_criterio",$criterio["codice"]);
    $ris_update->bindValue(":codice_commissario",$_SESSION["codice_commissario"]);
    foreach($_POST["partecipante"] AS $codice_partecipante => $valutazione) {
      if ($valutazione !== "") {
        $ris_value->bindValue(":codice_partecipante",$codice_partecipante);
        $ris_value->execute();
        if ($ris_value->rowCount() > 0) {
          $codice = $ris_value->fetch(PDO::FETCH_ASSOC)["codice"];
          $ris_update->bindValue(":codice_partecipante",$codice_partecipante);
          $ris_update->bindValue(":coefficiente",$valutazione);
          $ris_update->bindValue(":codice",$codice);
          $ris_update->execute();
        } else {
          $ris_insert->bindValue(":codice_partecipante",$codice_partecipante);
          $ris_insert->bindValue(":coefficiente",$valutazione);
          $ris_insert->execute();
        }
      }
    }
    $error_salvataggio = false;
  }
?>
