<?
  if (!empty($elabora_coda)) {
    $current_time = (int) date('Hi');
    // if($current_time >= 700 &&  $current_time <= 705) {
    //   $sql = "SELECT * FROM b_coda WHERE inviata = 'N' AND `timestamp_creazione` <= '" . date('Y-m-d h:i:s', strtotime('-1 day')) . "'";
    //   $ris = $pdo->query($sql);
    //   if($ris->rowCount() > 0) {
    //     $mailer = new Communicator();
    //     $mailer->oggetto = "VERIFICA CODA STAZIONE APPALTI";
    //     $mailer->corpo = "VERIFICA CODA STAZIONE APPALTI";
    //     $mailer->codice_pec = -2;
    //     $mailer->comunicazione = false;
    //     $mailer->coda = false;
    //     $mailer->intestazione = false;
    //     $mailer->destinatari = "";
    //     $esito = $mailer->send();
    //   }
    // }
    $pdo->go("UPDATE b_coda SET inviata = 'N' WHERE inviata = 'W' AND `timestamp` < '" . date('Y-m-d h:i:s', strtotime('-20 minutes')) . "'");
  }
?>
