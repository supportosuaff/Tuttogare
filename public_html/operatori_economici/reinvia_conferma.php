<?
session_start();
session_write_close();

include_once("../../config.php");
include_once($root."/inc/funzioni.php");

$edit = false;
if(isset($_SESSION["ente"])) {
  $codice_ente = $_SESSION["ente"]["codice"];
  if(! empty($_SESSION["codice_utente"])) {
    $edit = check_permessi("operatori_economici",$_SESSION["codice_utente"]);
  } else if (!empty($_POST["pec_recupero"])) {
    $tmp = $pdo->go("SELECT codice FROM b_utenti WHERE pec = :pec AND attivo = 'N' ",[":pec"=>$_POST["pec_recupero"]])->fetch(PDO::FETCH_ASSOC);
    if (!empty($tmp)) {
      $edit = true;
      $_GET["id"] = $tmp["codice"];
    }
  }
} else {
  if(! empty($_SESSION["codice_utente"])) {
    if(check_permessi("supporto",$_SESSION["codice_utente"]) && in_array($_SESSION["tipo_utente"], array('SAD', 'SUP'))) {
      $edit = true;
    }
  }
}

$msg = "Operazione non permessa";
if($edit) {
  $dominio = $_SERVER["SERVER_NAME"];
  if(! empty($_GET["id_ente"])) {
    $ente = $pdo->bindAndExec("SELECT b_enti.codice, b_enti.dominio FROM b_enti WHERE codice = :codice_ente AND attivo = 'S'", array(':codice_ente' => $_GET["id_ente"]))->fetch(PDO::FETCH_ASSOC);
    if(! empty($ente["dominio"])) $dominio = $ente["dominio"];
    if(! empty($ente["codice"])) $codice_ente = $ente["codice"];
  }
  if(isset($_GET["id"]) && !empty($codice_ente)) {
    $codice_utente = $_GET["id"];
    $bind = array(':codice_utente' => $codice_utente,":codice_ente"=>$codice_ente);
    $strsql  = "SELECT b_utenti.* ";
    $strsql .= "FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
    $strsql .= "JOIN r_enti_operatori ON r_enti_operatori.cod_utente = b_utenti.codice ";
    $strsql .= "WHERE b_gruppi.gerarchia > 2 AND b_utenti.attivo = 'N' AND b_utenti.codice = :codice_utente ";
    $strsql .= " AND r_enti_operatori.cod_ente = :codice_ente ";
    $strsql .= " GROUP BY b_utenti.codice ";
    $risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
    if($risultato->rowCount()>0){
      if ($record = $risultato->fetch(PDO::FETCH_ASSOC)){
        $corpo = "Salve " . $record["cognome"] . " " . $record["nome"] . ",<br><br>";
        $corpo .= "In data " . date("d-m-Y") . " hai effettuato la registrazione su " . $config["nome_sito"] . "<br>";
        $corpo .= "Prima di continuare &egrave; necessario confermare la tua iscrizione<br><br>";
        $corpo .= "<a title=\"Link di conferma - Sito esterno\" href=\"https://" . $dominio . "/operatori_economici/conferma.php?id=" . $record["codice"] . "&email=" . urlencode($record["pec"]) . "\">https://" . $dominio . "/operatori_economici/conferma.php?id=" . $record["codice"] . "&email=" . urlencode($record["pec"]) . "</a><br><br>";
        $corpo .= "Clicca o incolla il link nel tuo browser per continuare.<br>";
        $corpo .= "Il link sar&agrave; valido per le prossime 48 ore";

        $mailer = new Communicator();
        $mailer->oggetto = "Conferma iscrizione";
        $mailer->corpo = $corpo;
        $mailer->codice_pec = -1;
        $mailer->destinatari = $record["pec"];
        $esito = $mailer->send();
        if ($esito !== true) {
          $msg = "Errore nell'invio della pec";
        } else {
          $bind = array(':codice' => $record["codice"]);
          $sql_update = "UPDATE b_utenti SET timestamp = now() WHERE codice = :codice";
          $ris_update = $pdo->bindAndExec($sql_update,$bind);
          $msg = "Reinvio della PEC completato";
        }
      }
    } else {
      $msg = "Utente gi&agrave; attivo";
    }
  }
}
if (!empty($msg)) {
  if (isset($_POST["pec_recupero"])) {
    ?>
    alert("<?= $msg ?>");
    window.location.reload();
    <?
  } else {
    echo $msg;
  }
}