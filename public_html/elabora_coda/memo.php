<?
  if (!empty($elabora_coda)) {
    $sql = "SELECT * FROM b_alert_scadenze WHERE data_avviso < now() AND sent = 'N' ";
    $ris_memo = $pdo->query($sql);
    $count = 0;
    if ($ris_memo->rowCount() > 0) {
      while($memo = $ris_memo->fetch(PDO::FETCH_ASSOC)) {
        $indirizzi = array();
        $sql = "SELECT * FROM b_scadenze WHERE codice = :codice_scadenza ";
        $ris_scadenza = $pdo->bindAndExec($sql,array(":codice_scadenza"=>$memo["codice_scadenza"]));
        if ($ris_scadenza->rowCount() > 0) {
          $scadenza = $ris_scadenza->fetch(PDO::FETCH_ASSOC);

          $oggetto = "PROMEMORIA: - " . $scadenza["oggetto"];
          $testo = "<strong>" . $oggetto . "</strong><br><br><strong>Scadenza:</strong> " . mysql2datetime($scadenza["data"]) . "<br><br>" . $scadenza["descrizione"] . "<br><br><strong>Note:</strong><br>" . $memo["descrizione"];
          $bind = array();
          if (!empty($scadenza["codice_utente"])) {
            $bind[":codice_utente"] = $scadenza["codice_utente"];
            $sql = "SELECT * FROM b_utenti WHERE codice = :codice_utente AND gruppo <= 3 AND attivo ='S' ";
          } else {
            $sql = "SELECT * FROM b_utenti WHERE attivo = 'S' AND gruppo <= 3 ";
            if (!empty($scadenza["codice_ente"])) {
              if (!empty($scadenza["codice_ente_destinatario"])) {
                $bind[":codice_ente"] = $scadenza["codice_ente_destinatario"];
                $sql .= "AND (codice_ente = :codice_ente) ";
              } else {
                $bind[":codice_ente"] = $scadenza["codice_ente"];
                $sql .= "AND (codice_ente = :codice_ente OR codice_ente IN (SELECT codice FROM b_enti WHERE sua = :codice_ente)) ";
              }
            }
            if (!empty($scadenza["codice_gerarchia"])) {
              $bind[":codice_gerarchia"] = $scadenza["codice_gerarchia"];
              $sql .= "AND (gruppo = :codice_gerarchia) ";
            }

            if (!empty($scadenza["codice_modulo"])) {
              $bind[":codice_modulo"] = $scadenza["codice_modulo"];
              $sql .= "AND (codice IN (SELECT cod_utente FROM r_moduli_utente WHERE cod_modulo = :codice_modulo)) ";
            }
          }
          if (!empty($sql)) {
            $sql .= "ORDER BY codice ";
            $ris_utente = $pdo->bindAndExec($sql,$bind);
            if ($ris_utente->rowCount() > 0) {
              while($utente = $ris_utente->fetch(PDO::FETCH_ASSOC)) {
                $indirizzi[$utente["codice"]] = $utente["email"];
              }
            }
          }
        }
        if (count($indirizzi) > 0) {
          $sent = array();
          foreach($indirizzi AS $last_id => $indirizzo) {
            if (!in_array($indirizzo, $sent) && $last_id > $memo["last_id"]) {
              $count++;
              $mailer = new Communicator();
              $mailer->oggetto = $oggetto;
              $mailer->corpo = $testo;
              $mailer->codice_pec = -1;
              $mailer->comunicazione = false;
              $mailer->coda = false;
              $mailer->intestazione = true;
              $mailer->destinatari = $indirizzo;
              $esito = $mailer->send();
              if ($esito ===true) {
                $sent[] = $indirizzo;
                if ($last_id > 0) {
                  $sql = "UPDATE b_alert_scadenze SET last_id = :last_id WHERE codice = :codice";
                  $ris_update = $pdo->bindAndExec($sql,array(":codice"=>$memo["codice"],":last_id"=>$last_id));
                }
              }
              unset($mailer);
            }
            if ($count > 500) die();
          }
        }
        $sql = "UPDATE b_alert_scadenze SET sent = 'S' WHERE codice = :codice";
        $ris_update = $pdo->bindAndExec($sql,array(":codice"=>$memo["codice"]));
      }
      die();
    }
  }
?>
