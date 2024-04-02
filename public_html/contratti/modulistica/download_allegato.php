<?
  session_start();
  include_once "../../../config.php";
  include_once $root . "/inc/funzioni.php";

  if(empty($_GET["codice_contratto"]) || empty($_GET["codice_allegato"])) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
  } else {
    $codice = $_GET["codice_contratto"];
    $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
    $sql  = "SELECT b_contratti.* FROM b_contratti ";
    if (empty($codice_gara) && $_SESSION["gerarchia"] > 1) $sql .= "JOIN b_permessi_contratti ON b_contratti.codice = b_permessi_contratti.codice_contratto ";
    $sql .= "WHERE b_contratti.codice = :codice ";
    $sql .= "AND b_contratti.codice_gestore = :codice_ente ";
    if ($_SESSION["gerarchia"] > 0) {
      $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
      $sql .= "AND (b_contratti.codice_ente = :codice_ente_utente OR b_contratti.codice_gestore = :codice_ente_utente) ";
    }
    if($_SESSION["gerarchia"] > 1) {
      $bind[":codice_utente"] = $_SESSION["codice_utente"];
      $sql .= " AND (b_permessi_contratti.codice_utente = :codice_utente)";
    }
    $ris = $pdo->bindAndExec($sql,$bind);
    if($ris->rowCount() == 1) {
      $ris_file = $pdo->bindAndExec("SELECT b_allegati_contratto.* FROM b_allegati_contratto WHERE codice = :codice", array(':codice' => $_GET["codice_allegato"]));
      if($ris_file->rowCount() > 0) {
        $rec_file = $ris_file->fetch(PDO::FETCH_ASSOC);
        $file = "{$config["arch_folder"]}/allegati_contratto/{$codice}/{$rec_file["riferimento"]}";
        if(file_exists($file)) {
          header('Content-Description: File Transfer');
          header("Content-Type: application/force-download");
          header("Content-Type: application/octet-stream");
          header("Content-Type: application/download");
          header('Content-Disposition: attachment; filename="'.$rec_file["nome_file"].'"');
          header('Content-Transfer-Encoding: binary');
          header('Expires: 0');
          header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
          header('Pragma: public');
          header('Content-Length: ' . filesize($file));
          readfile($file);
          echo '#0'; die();
        } else {
          echo '#1'; die();
          echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
        }
      } else {
        echo '#2'; die();
        echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
      }
    } else {
      echo '#2'; die();
      echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
    }
  }
?>
