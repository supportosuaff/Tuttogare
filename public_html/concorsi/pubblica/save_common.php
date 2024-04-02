<? if (isset($edit) && $edit) {
  $_POST["gara"]["codice"] = $_POST["codice_gara"];
  if (isset($_POST["fase"]["scadenza"])) $_POST["gara"]["data_scadenza"] = $_POST["fase"]["scadenza"];
  $salva = new salva();
  $salva->debug = false;
  $salva->codop = $_SESSION["codice_utente"];
  $salva->nome_tabella = "b_concorsi";
  $salva->operazione = "UPDATE";
  $salva->oggetto = $_POST["gara"];
  $codice_gara = $salva->save();
  if ($codice_gara == $_POST["codice_gara"]) {
    $bind = array();
    $bind[":codice_gara"] = $codice_gara;
    $sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara ORDER BY codice LIMIT 0,1";
    $ris = $pdo->bindAndExec($sql,$bind);
    if ($ris->rowCount() > 0) {
      $fase = $ris->fetch(PDO::FETCH_ASSOC);
      $_POST["fase"]["codice"] = $fase["codice"];
      $_POST["fase"]["attiva"] = "S";
      $salva->codop = $_SESSION["codice_utente"];
      $salva->nome_tabella = "b_fasi_concorsi";
      $salva->operazione = "UPDATE";
      $salva->oggetto = $_POST["fase"];
      $codice_fase = $salva->save();
    }

    $bind = array();
    $bind[":codice"] = $_POST["codice_gara"];
    if ($_POST["gara"]["pubblica"] > 0) {
      $sql = "UPDATE b_concorsi SET stato = 3 WHERE codice = :codice AND stato < 3 ";
      $update_stato = $pdo->bindAndExec($sql,$bind);
    }

    log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Stato pubblicazione");
  }
} ?>
