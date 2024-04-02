<?
  session_start();
  include("../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  if(empty($_SESSION["codice_utente"]) || !check_permessi("scadenzario/gestione",$_SESSION["codice_utente"])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    die();
  } else {
    session_write_close();
    $bind = array();
    $bind["codice_ente"] = !empty($_POST["codice_ente"]) ? $_POST["codice_ente"] : $_SESSION["ente"]["codice"];
    $sql  = "SELECT * FROM b_enti WHERE (codice = :codice_ente OR sua = :codice_ente) ";
    if ($_SESSION["gerarchia"] > 0) {
      $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
      $sql .= " AND (codice = :codice_ente_utente OR sua = :codice_ente_utente)";
    }
    $sql .= "ORDER BY codice, denominazione";
    $ris = $pdo->bindAndExec($sql,$bind);
    if($ris->rowCount() > 0) {
      while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
        ?><option value="<?= $rec["codice"] ?>"><?= $rec["denominazione"] ?></option><?
      }
    }

  }
?>
