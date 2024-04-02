<?
  if (!empty($elabora_coda)) {
      /* INIZIO SMALTIMENTO CODA COMUNICAZIONI UTENTI */
      $sql = "SELECT * FROM b_coda WHERE inviata = 'N' AND `timestamp_creazione` > '" . date('Y-m-d h:i:s', strtotime('-5 day')) . "' ORDER BY codice DESC LIMIT 0,250";
      $ris_coda = $pdo->query($sql);
      if ($ris_coda->rowCount()>0) {
        $failed = [];
        $messaggi = $ris_coda->fetchAll(PDO::FETCH_ASSOC);
        $updateWorking = $pdo->prepare("UPDATE b_coda SET inviata = 'W' WHERE codice = :codice");
        foreach($messaggi AS $messaggio) {
          $updateWorking->bindValue(":codice",$messaggio["codice"]);
          $updateWorking->execute();
        }
        reset($messaggi);
        foreach($messaggi AS $messaggio) {
          $continua = true;
          $codice_configurazione = "";
          if ($messaggio["codice_ente"] != 0) {
            $continua = false;
            $sql = "SELECT * FROM b_enti WHERE codice = :codice AND attivo = 'S'";
            $ris_ente = $pdo->bindAndExec($sql,array(":codice"=>$messaggio["codice_ente"]));
            if ($ris_ente->rowCount() > 0) {
              $continua = true;
              $ente = $ris_ente->fetch(PDO::FETCH_ASSOC);
              $_SESSION["ente"] = $ente;
              $codice_configurazione .= $ente["codice"];
            }
          }
          $codice_configurazione .= $messaggio["codice_pec"];
          if (in_array($codice_configurazione,$failed)===false) {
            // if(date('H') > 19 || date('H') < 8 || ! in_array($_SESSION["ente"]["codice"], array(83, 274, 750)) || $messaggio["codice_pec"] < 0) {
              $mailer = new Communicator();
              $mailer->oggetto = $messaggio["oggetto"];
              $mailer->corpo = $messaggio["corpo"];
              $mailer->codice_pec = $messaggio["codice_pec"];
              $mailer->comunicazione = false;
              $mailer->coda = false;
              $mailer->intestazione = false;
              $mailer->codice_relazione = $messaggio["codice_relazione"];
              $mailer->elaborazione_coda = TRUE;
              $mailer->destinatari = $messaggio["indirizzo"];
              $mailer->codice_coda = $messaggio["codice"];
              $mailer->comunicazione_tecnica = (bool) $messaggio["comunicazione_tecnica"] ? true : false;
              $esito = $mailer->send();
              if ($esito ===true) {
                $sql = "DELETE FROM b_coda WHERE codice = :codice";
                $ris_update = $pdo->bindAndExec($sql,array(":codice"=>$messaggio["codice"]));
              } else {
                $failed[] = $codice_configurazione;
              } 
              unset($mailer);
            // }
          }
          if (isset($_SESSION["ente"])) unset($_SESSION["ente"]);
        }
    	}
      /* FINE SMALTIMENTO CODA COMUNICAZIONI UTENTI */
      unset($_SESSION["ente"]);
  }
?>
