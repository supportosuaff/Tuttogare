<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");

$errore = FALSE;
if (empty($_SESSION["codice_utente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
  die();
} else {
  $b_allegati = array();
  if(!empty($_POST["codice_contratto"])) {
    $codice_contratto = $_POST["codice_contratto"];
    $codice_gara = !empty($_POST["codice_gara"]) ? $_POST["codice_gara"] : null;
    $sth = $pdo->prepare("UPDATE `b_allegati_contratto` SET `includi` = :includi WHERE `codice_contratto` = :codice_contratto AND `codice` = :codice_allegato");
    $sth->bindValue(':codice_contratto', $codice_contratto);
    if(!empty($_POST["allegato"])) {
      if(!empty($_POST["allegato"]["generali"])) {$b_allegati = $_POST["allegato"]["generali"]; unset($_POST["allegato"]["generali"]);}
      foreach ($_POST["allegato"] as $codice_allegato => $valore) {
        $sth->bindValue(':codice_allegato', $codice_allegato);
        $sth->bindValue(':includi', $valore["includi"]);
        $sth->execute();
      }
      if(!empty($b_allegati)) {
        $sth = $pdo->prepare("UPDATE `b_allegati` SET `includi` = :includi WHERE `codice_gara` = :codice_contratto AND `codice` = :codice_allegato");
        $sth->bindValue(':codice_contratto', $codice_contratto);
        foreach ($b_allegati as $codice_allegato => $valore) {
          $sth->bindValue(':codice_allegato', $codice_allegato);
          $sth->bindValue(':includi', $valore["includi"]);
          $sth->execute();
        }
      }
    }
  }
}
?>
window.location.reload();
<?
