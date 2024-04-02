<?
  include_once("../../config.php");
  if (isset($_SESSION["codice_utente"]) && $_SESSION["gerarchia"] == 0) {
    if(isset($_POST["operazione"]) && ! empty($_POST["codice_gara"]) && is_numeric($_POST["codice_gara"])) {
      $operatori = $pdo->bindAndExec('SELECT COUNT(codice) FROM r_partecipanti WHERE codice_gara = :codice_gara', array(':codice_gara' => $_POST["codice_gara"]))->fetch(PDO::FETCH_COLUMN, 0);
      switch ($_POST["operazione"]) {
        case 'telematica': 
          $pdo->bindAndExec('UPDATE b_gare SET modalita = 4 WHERE codice = :codice', array(':codice' => $_POST["codice_gara"]));
          scrivilog('b_gare', 'UPDATE', $pdo->getSQL(), $_SESSION["codice_utente"]);
          ?>alert("Operazione conclusa con successo!");window.location.reload();<?
          break;
        case 'extrapiattaforma': 
          $pdo->bindAndExec('UPDATE b_gare SET modalita = 1 WHERE codice = :codice', array(':codice' => $_POST["codice_gara"]));
          scrivilog('b_gare', 'UPDATE', $pdo->getSQL(), $_SESSION["codice_utente"]);
          ?>alert("Operazione conclusa con successo!");window.location.reload();<?
          break;
        case 'backward-to-elaborazione':
          if($operatori == 0) {
            $pdo->bindAndExec('UPDATE b_gare SET stato = 2, pubblica = 0 WHERE codice = :codice', array(':codice' => $_POST["codice_gara"]));
            scrivilog('b_gare', 'UPDATE', $pdo->getSQL(), $_SESSION["codice_utente"]);
            ?>alert("Operazione conclusa con successo!");window.location.reload();<?
          } else {
            ?>jalert("ERRORCOD: #0x02. Operazione non consentita! Rilevati partecipanti alla procedura di gara.");<?
          }
          break;
        case 'reset_key':
          if($operatori == 0) {
            $key = $pdo->bindAndExec('SELECT public_key FROM b_gare WHERE codice = :codice', array(':codice' => $_POST["codice_gara"]))->fetch(PDO::FETCH_COLUMN, 0);
            if(! empty($key)) {
              $pdo->bindAndExec('UPDATE b_gare SET public_key = NULL WHERE codice = :codice', array(':codice' => $_POST["codice_gara"]));
              scrivilog('b_gare', 'UPDATE', $pdo->getSQL()." ".base64_encode($key), $_SESSION["codice_utente"]);
            }
            ?>alert("Operazione conclusa con successo!");window.location.reload();<?
          } else {
            ?>jalert("ERRORCOD: #0x02. Operazione non consentita! Rilevati partecipanti alla procedura di gara.");<?
          }
          break;
        default:
          ?>jalert("ERRORCOD: #0x01. Operazione non consentita!");<?
          break;
      }
    }
  }
?>
