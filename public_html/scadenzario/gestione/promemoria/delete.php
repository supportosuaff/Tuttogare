<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  if(empty($_SESSION["codice_utente"]) || !check_permessi("scadenzario/gestione",$_SESSION["codice_utente"]) || empty($_POST["codice"])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    die();
  } else {
    $codice = $_POST["codice"];
    if(is_numeric($_POST["codice"])) {
      $sql = "DELETE FROM b_alert_scadenze WHERE codice = :codice";
      $pdo->bindAndExec($sql, array(':codice' => $codice));
    }
    ?>
    if($("#promemoria_<?= $codice ?>").length > 0){
      $("#promemoria_<?= $codice ?>").slideUp().remove();
    }
    <?
  }
?>
