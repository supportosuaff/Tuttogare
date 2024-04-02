<?
  session_start();
  include_once "../../../config.php";
  include_once $root . "/inc/funzioni.php";
  include_once $root . "/contratti/plicoae/array2xml.class.php";

  if(empty($_POST["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"]) || !file_exists(__DIR__."/script/".$_SESSION["ente"]["codice"]."/save.php")) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  } else {
    $codice = $_POST["codice"];
    $codice_gara = !empty($_GET["codice_gara"]) ? $_POST["codice_gara"] : null;
    $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
    $sql  = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto FROM b_contratti JOIN b_conf_modalita_stipula ON b_contratti.modalita_stipula = b_conf_modalita_stipula.codice ";
    if(!empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
      $sql .= "JOIN b_permessi ON b_contratti.codice_gara = b_permessi.codice_gara ";
    } elseif (empty($codice_gara) && $_SESSION["gerarchia"] > 1) {
      $sql .= "JOIN b_permessi_contratti ON b_contratti.codice = b_permessi_contratti.codice_contratto ";
    }
    $sql .= "WHERE b_contratti.codice = :codice ";
    $sql .= "AND b_contratti.codice_gestore = :codice_ente ";
    if ($_SESSION["gerarchia"] > 0) {
      $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
      $sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
    }
    if (!empty($codice_gara)) {
      $bind[":codice_gara"] = $codice_gara;
      $sql .= " AND b_contratti.codice_gara = :codice_gara";
      if($_SESSION["gerarchia"] > 1) {
        $bind[":codice_utente"] = $_SESSION["codice_utente"];
        $sql .= " AND (b_permessi.codice_utente = :codice_utente)";
      }
    } else {
      if($_SESSION["gerarchia"] > 1) {
        $bind[":codice_utente"] = $_SESSION["codice_utente"];
        $sql .= " AND (b_permessi_contratti.codice_utente = :codice_utente)";
      }
    }
    $ris = $pdo->bindAndExec($sql,$bind);
    $href_contratto = null;
    if($ris->rowCount() == 1) {
      $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
      include(__DIR__."/script/".$_SESSION["ente"]["codice"]."/save.php");
      if (!$error) {
        ?>alert("Trasmissione effettuata");<?
      } else {
        ?>alert("Si è verificato un errore durante la trasmissione");<?
      }
      ?>window.location.reload()<?
    }
  }
?>
