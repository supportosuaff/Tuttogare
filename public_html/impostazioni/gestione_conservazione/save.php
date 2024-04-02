<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
if(empty($_SESSION["codice_utente"]) || !check_permessi("impostazioni",$_SESSION["codice_utente"])) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
  die();
} else {
  if($_SESSION["gerarchia"] === "0" && !isset($_SESSION["ente"])) $_POST["codice_ente"] = 0;
  if(!empty($_POST) && ($_SESSION["gerarchia"] > 0 || ($_SESSION["gerarchia"] === "0" && isset($_POST["codice_ente"])))) {
    $errore = FALSE;
    if(!empty($_POST["campo"])) {
      $sth = $pdo->prepare('SELECT * FROM b_schema_metadati WHERE codice = :codice');
      $salva = new salva();
      $salva->debug = FALSE;
      $salva->codop = $_SESSION["codice_utente"];
      $salva->nome_tabella = "b_schema_metadati";
      foreach ($_POST["campo"] as $campo) {
        $campo["codice_ente"] = (empty($_SESSION["record_utente"]["codice_ente"]) && isset($_POST["codice_ente"]) && $_SESSION["gerarchia"] === "0") ? $_POST["codice_ente"] : $_SESSION["record_utente"]["codice_ente"];
        $salva->operazione = "INSERT";
        if(is_numeric($campo["codice"]) && $campo["codice"] > 0) {
          $salva->operazione = "UPDATE";
          $sth->bindValue(':codice', $campo["codice"]);
          $sth->execute();
          if($sth->rowCount() > 0) {
            $rec = $sth->fetch(PDO::FETCH_ASSOC);
            if($rec["codice_ente"] != $campo["codice_ente"]) continue;
          }
        }
        $campo["codice_ente"] = (empty($_SESSION["record_utente"]["codice_ente"]) && isset($_POST["codice_ente"]) && $_SESSION["gerarchia"] === "0") ? $_POST["codice_ente"] : $_SESSION["record_utente"]["codice_ente"];
        $salva->oggetto = $campo;
        if(!is_numeric($salva->save())) $errore = TRUE;
      }
    }

    if(!empty($_POST["conservatore"]["nome"]) && !empty($_POST["conservatore"]["cognome"]) && !empty($_POST["conservatore"]["titolo"]) && !empty($_POST["conservatore"]["ruolo"])) {
      $conservatore = $_POST["conservatore"];
      $conservatore["codice_ente"] = (empty($_SESSION["record_utente"]["codice_ente"]) && isset($_POST["codice_ente"]) && $_SESSION["gerarchia"] === "0") ? $_POST["codice_ente"] : $_SESSION["record_utente"]["codice_ente"];
      $salva = new salva();
      $salva->debug = FALSE;
      $salva->codop = $_SESSION["codice_utente"];
      $salva->nome_tabella = "b_conservatori";
      $salva->operazione = !empty($conservatore["codice"]) ? "UPDATE" : "INSERT";
      $salva->oggetto = $conservatore;
      if(!is_numeric($salva->save())) $errore = TRUE;
    }

    if($errore) {
      ?>jalert('Si è verificato un errore durante il salvataggio. Si prega di verificare le informazioni fornite e di riprovare.');<?
    } else {
      ?>alert('Modifica effettuata con successo');window.location.href = window.location.href;<?
    }
  } else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    die();
  }
}
?>
