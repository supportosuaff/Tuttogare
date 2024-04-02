<?
  session_start();
  include_once "../../../config.php";
  include_once $root . "/inc/funzioni.php";

  if(empty($_GET["codice_allegato"]) || empty($_GET["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
  } else {
    $codice = $_GET["codice"];
    $codice_allegato = $_GET["codice_allegato"];
    $codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;
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
      $ris_allegato = $pdo->bindAndExec("SELECT * FROM b_allegati_contratto WHERE codice = :codice", array(':codice' => $codice_allegato));
      if($ris_allegato->rowCount() > 0) {
        $rec_allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
        $file = "{$config["arch_folder"]}/allegati_contratto/{$rec_allegato["codice_contratto"]}/{$rec_allegato["riferimento"]}";
        header('Content-Description: File Transfer');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment; filename="'.$rec_allegato["nome_file"].'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
      } else {
        include_once $root . "/layout/top.php";
        ?><h3 class="ui-state-error">Permessi insufficienti per il download del file. #2</h3><?
        include_once $root . "/contratti_operatore/ritorna_pannello_contratto.php";
        include_once $root . "/layout/bottom.php";
      }
    } else {
      include_once $root . "/layout/top.php";
      ?><h3 class="ui-state-error">Permessi insufficienti per il download del file. #4</h3><?
      include_once $root . "/contratti_operatore/ritorna_pannello_contratto.php";
      include_once $root . "/layout/bottom.php";
    }
  }
?>
