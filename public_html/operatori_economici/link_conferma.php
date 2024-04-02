<?
  include_once("../../config.php");
  include_once($root."/layout/top.php");
  if (! empty($_SESSION["oe"]["user"])) {
    $user = $_SESSION["oe"]["user"];
    unset($_SESSION["oe"]["user"]);
    $corpo = "Salve " . $user["cognome"] . " " . $user["nome"] . ",<br><br>";
    $corpo .= "In data " . date("d-m-Y") . " hai richiesto la rigenerazione del link per la conferma della registrazione su " . $config["nome_sito"] . "<br>";
    $corpo .= "<a title=\"Link di conferma - Sito esterno\" href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/operatori_economici/conferma.php?id=" . $user["codice"] . "&email=" . urlencode($user["pec"]) . "\">" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/operatori_economici/conferma.php?id=" . $user["codice"] . "&email=" . urlencode($user["pec"]) . "</a><br><br>";
    $corpo .= "Clicca o incolla il link nel tuo browser per continuare.<br>";
    $corpo .= "Il link sar&agrave; valido per le prossime 48 ore";

    $mailer = new Communicator();
    $mailer->oggetto = "Invio link di conferma iscrizione";
    $mailer->corpo = $corpo;
    $mailer->codice_pec = -1;
    $mailer->destinatari = $user["pec"];
    $esito = $mailer->send();
    if($esito) {
      $bind = array(':codice' => $user["codice"]);
      $sql_update = "UPDATE b_utenti SET timestamp = now() WHERE codice = :codice";
      $ris_update = $pdo->bindAndExec($sql_update,$bind);
      ?>
      <h1 style="text-align:center;"><?= traduci("registrazione-oe") ?></h1>
      <h2 style="color:#0C3; text-align:center; font-size: 2rem">
        <?= traduci('conferma-invio-link-accesso') ?></h2>
      <?
    }
  } else {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  }
  include_once($root."/layout/bottom.php");
?>
