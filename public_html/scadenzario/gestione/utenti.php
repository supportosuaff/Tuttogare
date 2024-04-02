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

    $sql  = "SELECT b_utenti.* FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice ";
    if(!empty($_POST["codice_modulo"])) {$sql .= "JOIN r_moduli_utente ON r_moduli_utente.cod_utente = b_utenti.codice ";}
    $sql .="WHERE b_gruppi.disponibile = 'S' AND b_utenti.attivo = 'S' ";
    if (!empty($_POST["codice_ente"]) || isset($_SESSION["ente"]["codice"])) {
      $bind["codice_ente"] = !empty($_POST["codice_ente"]) ? $_POST["codice_ente"] : $_SESSION["ente"]["codice"];
      if(!empty($_POST["codice_ente_destinatario"])) $bind["codice_ente"] = $_POST["codice_ente_destinatario"];
      $sql .= "AND b_utenti.codice_ente = :codice_ente ";
    } else {
      $sql .= "AND b_utenti.codice_ente = 0 ";
    }
    if(!empty($_POST["codice_gerarchia"])) { $sql .= "AND b_utenti.gruppo = :codice_gerarchia "; $bind["codice_gerarchia"] = $_POST["codice_gerarchia"];}
    $sql .= "ORDER BY b_utenti.gruppo, b_utenti.nome, b_utenti.cognome ";
    $ris = $pdo->bindAndExec($sql, $bind);
    if($ris->rowCount() > 0) {
      while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
        if(!empty($rec["nome"]) && !empty($rec["cognome"])) {
          ?><option value="<?= $rec["codice"] ?>"><?= ucwords(strtolower(html_entity_decode($rec["nome"]." ".$rec["cognome"], ENT_QUOTES, 'UTF-8'))) ?></option><?
        }
      }
    }

  }
?>
