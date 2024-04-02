<?
  if (isset($record_gara)) {
    $inputs = false;
    if (isset($_POST["offerta"])) $inputs = $_POST["offerta"];
    if (!$errore_validazione) {
      $sql_storico = "SELECT b_dettaglio_offerte_asta.* FROM b_dettaglio_offerte_asta
                      JOIN b_offerte_economiche_asta ON
                      b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                      WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante
                      AND (b_offerte_economiche_asta.stato = 0 OR b_offerte_economiche_asta.stato = 1)
                      ORDER BY b_offerte_economiche_asta.stato";
      $ris_storico = $pdo->bindAndExec($sql_storico,[":codice_partecipante"=>$partecipante["codice"]]);
      $decrypt = true;
      if ($ris_storico->rowCount()==0) {
        $decrypt = false;
        $sql_storico = "SELECT * FROM b_offerte_decriptate WHERE codice_partecipante = :codice_partecipante";
        $ris_storico = $pdo->bindAndExec($sql_storico,[":codice_partecipante"=>$partecipante["codice"]]);
      }
      if ($ris_storico->rowCount()>0) {
        $inputs = [];
        while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
          if ($decrypt) {
            $storico["offerta"] = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
          }
          $inputs[$storico["tipo"]][$storico["codice_dettaglio"]] = $storico["offerta"];
        }
      }
    }
    echo getFormOfferta($record_gara["codice"],$codice_lotto,null,true,$inputs);
  } ?>
