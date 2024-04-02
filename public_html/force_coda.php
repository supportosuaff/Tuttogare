<?
  session_start();
  if ($_SESSION["gerarchia"] === "0") {
    if (isset($_GET["codice_ente"])) {
      include_once("../config.php");
      include_once($root."/inc/funzioni.php");

      function system_shutdown_and_connection_closing() {
        if(isset($pdo)) unset($pdo);
      }

      register_shutdown_function('system_shutdown_and_connection_closing');

      /* INIZIO SMALTIMENTO CODA COMUNICAZIONI UTENTI */
      $sql = "SELECT * FROM b_coda WHERE codice_ente = :codice_ente LIMIT 0,100"; // CAMBIARE CODICE ENTE
      $ris_coda = $pdo->go($sql,[":codice_ente"=>$_GET["codice_ente"]]);
      if ($ris_coda->rowCount()>0) {
        while($messaggio = $ris_coda->fetch(PDO::FETCH_ASSOC)) {
          $continua = true;
          if ($messaggio["codice_ente"] != 0) {
            $continua = false;
            $sql = "SELECT * FROM b_enti WHERE codice = :codice AND attivo = 'S'";
            $ris_ente = $pdo->bindAndExec($sql,array(":codice"=>$messaggio["codice_ente"]));
            if ($ris_ente->rowCount() > 0) {
              $continua = true;
              $ente = $ris_ente->fetch(PDO::FETCH_ASSOC);
              $_SESSION["ente"] = $ente;
            }
          }
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
          $esito = $mailer->send();
          if ($esito ===true) {
            $sql = "DELETE FROM b_coda WHERE codice = :codice";
            $ris_update = $pdo->bindAndExec($sql,array(":codice"=>$messaggio["codice"]));
          } else {
            echo $esito . "<br>";
          }
          unset($mailer);
          if (isset($_SESSION["ente"])) unset($_SESSION["ente"]);
        }
      }
      /* FINE SMALTIMENTO CODA COMUNICAZIONI UTENTI */
      unset($_SESSION["ente"]);
    }
  }
?>
