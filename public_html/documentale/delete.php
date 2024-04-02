<?
  session_start();
  include_once '../../config.php';
  include_once $root.'/inc/funzioni.php';
  if(empty($_SESSION["codice_utente"]) || !check_permessi("manage_documentale",$_SESSION["codice_utente"]) || !is_numeric($_POST["codice"])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    die();
  } else {
    $bind = array();
    $bind["codice"] = $_POST["codice"];
    $sql = "DELETE FROM b_allegati WHERE codice = :codice ";
    if ($_SESSION["gerarchia"] > 0) {
      $bind["codice_ente"] = $_SESSION["record_utente"]["codice_ente"];
      $bind["codice_sua"] = $_SESSION["ente"]["codice"];
      $sql .= " AND (codice_ente = :codice_ente OR codice_ente = :codice_sua)";
    }
    $ris = $pdo->bindAndExec($sql, $bind);
    scrivilog("b_allegati","DELETE",$pdo->getSQL(),$_SESSION["record_utente"]["codice"]);
    
    ?>
    window.location.href=window.location.href;
    <?
  }
?>
