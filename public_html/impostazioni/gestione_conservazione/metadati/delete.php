<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  if(empty($_SESSION["codice_utente"]) || !check_permessi("impostazioni",$_SESSION["codice_utente"])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    die();
  } else {
    if(!empty($_POST["codice"])) {
      if(is_numeric($_POST["codice"])) {
        $check = $pdo->bindAndExec("SELECT * FROM b_schema_metadati WHERE codice = :codice", array(':codice' => $_POST["codice"]));
        if($check->rowCount() > 0) {
          $check = $check->fetch(PDO::FETCH_ASSOC);
          if(($check["codice_ente"] != 0 && ($_SESSION["record_utente"]["codice_ente"] == $check["codice_ente"] || $_SESSION["gerarchia"] === "0")) || ($check["codice_ente"] == 0 && $_SESSION["gerarchia"] === "0")) {
            $pdo->bindAndExec("UPDATE b_schema_metadati SET soft_delete = 'S' WHERE codice = :codice", array(':codice' => $_POST["codice"]));
          }
        }
      }
      ?>
      if($('#campo_<?= $_POST["codice"] ?>').length > 0) {
        $('#campo_<?= $_POST["codice"] ?>').slideUp('fast').remove();
      }
      <?
    }
  }
?>
