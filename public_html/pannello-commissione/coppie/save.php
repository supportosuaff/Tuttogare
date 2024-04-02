<?
  if (isset($_SESSION["codice_commissario"]) && checkCommissario($_POST["codice_gara"])) {
    $sql_value = "SELECT * FROM b_confronto_coppie
                  WHERE codice_criterio = :codice_criterio
                  AND codice_commissario = :codice_commissario
                  AND codice_partecipante_1 = :codice_row
                  AND codice_partecipante_2 = :codice_col ";
    $sql_insert = "INSERT INTO b_confronto_coppie (codice_gara,codice_lotto,codice_partecipante_1,punteggio_partecipante_1,
                               codice_partecipante_2,punteggio_partecipante_2,
                               codice_criterio,codice_commissario)
                   VALUES (:codice_gara,:codice_lotto,:codice_partecipante_1,:punteggio_partecipante_1,
                           :codice_partecipante_2,:punteggio_partecipante_2,
                           :codice_criterio,:codice_commissario)";

    $sql_update = "UPDATE b_confronto_coppie SET
                          codice_partecipante_1 = :codice_partecipante_1,
                          codice_partecipante_2 = :codice_partecipante_2,
                          punteggio_partecipante_1 = :punteggio_partecipante_1,
                          punteggio_partecipante_2 = :punteggio_partecipante_2,
                          codice_criterio = :codice_criterio,
                          codice_commissario = :codice_commissario
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
    foreach($_POST["valutazione"] AS $codici => $valutazioni) {
      if ($valutazioni !== "") {
        $codici = explode("_", $codici);
        $valutazioni = explode("_", $valutazioni);
        $ris_value->bindValue(":codice_row",$codici[0]);
        $ris_value->bindValue(":codice_col",$codici[1]);
        $ris_value->execute();
        if ($ris_value->rowCount() > 0) {
          $codice = $ris_value->fetch(PDO::FETCH_ASSOC)["codice"];
          $ris_update->bindValue(":codice_partecipante_1",$codici[0]);
          $ris_update->bindValue(":codice_partecipante_2",$codici[1]);
          $ris_update->bindValue(":punteggio_partecipante_1",$valutazioni[0]);
          $ris_update->bindValue(":punteggio_partecipante_2",$valutazioni[1]);
          $ris_update->bindValue(":codice",$codice);
          $ris_update->execute();
        } else {
          $ris_insert->bindValue(":codice_partecipante_1",$codici[0]);
          $ris_insert->bindValue(":codice_partecipante_2",$codici[1]);
          $ris_insert->bindValue(":punteggio_partecipante_1",$valutazioni[0]);
          $ris_insert->bindValue(":punteggio_partecipante_2",$valutazioni[1]);
          $ris_insert->execute();
        }
      }
    }
    $error_salvataggio = false;
  }
?>
