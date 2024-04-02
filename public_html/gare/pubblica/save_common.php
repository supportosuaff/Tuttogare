<? if (isset($edit) && $edit) {
  $_POST["gara"]["codice"] = $_POST["codice_gara"];
  if (!empty($_POST["oeManager"]["elenco"])) {
    $value = explode("-",$_POST["oeManager"]["elenco"]);
    $_POST["gara"]["tipo_elenco"] = $value[0];
    $_POST["gara"]["codice_elenco"] = $value[1];
  }
  $salva = new salva();
  $salva->debug = false;
  $salva->codop = $_SESSION["codice_utente"];
  $salva->nome_tabella = "b_gare";
  $salva->operazione = "UPDATE";
  $salva->oggetto = $_POST["gara"];
  $codice_gara = $salva->save();
  if ($codice_gara == $_POST["codice_gara"]) {
    $bind = array();
    $bind[":codice"] = $_POST["codice_gara"];
    if ($_POST["gara"]["pubblica"] > 0) {
      $sql = "UPDATE b_gare SET stato = 3 WHERE codice = :codice AND stato < 3 ";
      $update_stato = $pdo->bindAndExec($sql,$bind);
      if (class_exists("syncERP")) {
        $syncERP = new syncERP();
        if (method_exists($syncERP,"sendUpdateRequest")) {
          $syncERP->sendUpdateRequest($_POST["gara"]["codice"]);
        }
      }
    }
    //CONTRIBUTO SUA E GESTORE PUBBLICAZIONE
    include(__DIR__."/contributo.php");

    log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Stato pubblicazione");
  }
} ?>
