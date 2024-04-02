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

    $sql  = "SELECT b_moduli.codice, b_moduli.titolo FROM b_moduli ";
    $sql .= " WHERE b_moduli.tutti_utente = 'N' AND b_moduli.nascosto = 'N' AND b_moduli.attivo = 'S' AND (";
    if (!empty($_POST["codice_ente"])) {
      $bind["codice_ente"] = $_POST["codice_ente"];
      $sql.= " (b_moduli.ente = 'S' AND (b_moduli.tutti_ente = 'S' OR (b_moduli.tutti_ente = 'N' AND b_moduli.codice IN (SELECT cod_modulo FROM r_moduli_ente WHERE cod_ente = :codice_ente))))";
    } else {
      $sql.= " b_moduli.ente = 'S' ";
    }
    $sql .= ") ORDER BY b_moduli.titolo ";
    $ris = $pdo->bindAndExec($sql,$bind);
    if($ris->rowCount() > 0) {
      while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
        ?><option value="<?= $rec["codice"] ?>"><?= $rec["titolo"] ?></option><?
      }
    }

  }
?>
