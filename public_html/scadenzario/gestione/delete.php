<?
  session_start();
  include_once '../../../config.php';
  include_once "{$root}/inc/funzioni.php";
  if(empty($_SESSION["codice_utente"]) || !check_permessi("scadenzario/gestione",$_SESSION["codice_utente"]) || empty($_POST["codice"])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 500);
    die();
  } else {
    $bind = array(':codice_ente' => $_SESSION["record_utente"]["codice_ente"], ':codice_sua' => $_SESSION["ente"]["codice"], ':codice' => $_POST["codice"]);
    $sql = "DELETE FROM b_scadenze WHERE codice = :codice AND (codice_ente = :codice_ente OR codice_ente = :codice_sua OR codice_ente IS NULL OR codice_ente = 0)";
    $ris = $pdo->bindAndExec($sql, $bind);

    scrivilog("b_scadenze","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
    ?>
    table.ajax.reload();
    <?
  }
?>
