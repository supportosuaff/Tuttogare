<?
  session_start();
  include_once "../../../config.php";
  include_once "{$root}/inc/funzioni.php";

  if (!is_operatore()) {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
  } else {
    $ris_operatore = $pdo->bindAndExec("SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente", array(':codice_utente' => $_SESSION["codice_utente"]));
    $operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);
    if (!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"]) && !empty($_GET["codice"])) {
      $codice_contratto = $_GET["codice"];
      $sql = "SELECT b_contratti.*, b_conf_modalita_stipula.invio_remoto, b_conf_modalita_stipula.etichetta as modalita_di_stipula
              FROM b_contratti
              JOIN b_conf_modalita_stipula ON b_conf_modalita_stipula.codice = b_contratti.modalita_stipula
              JOIN r_contratti_contraenti ON r_contratti_contraenti.codice_contratto = b_contratti.codice
              JOIN b_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente
              WHERE b_contraenti.codice_utente = :codice_utente
              AND b_contratti.codice = :codice_contratto
              AND r_contratti_contraenti.codice_capogruppo = 0";
      $ris = $pdo->bindAndExec($sql, array(':codice_utente' => $_SESSION["record_utente"]["codice"], ':codice_contratto' => $codice_contratto));
      if($ris->rowCount() > 0) {
        $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
        if($rec_contratto["invio_remoto"] == "S") {
          $bind = array(':codice_contratto' => $rec_contratto["codice"], ':codice_ente' => $_SESSION["ente"]["codice"]);
          $ris_file = $pdo->bindAndExec("SELECT b_allegati.* FROM b_allegati WHERE sezione = 'contratti' AND codice_gara = :codice_contratto AND cartella = 'contratti_da_firmare' AND codice_ente = :codice_ente", $bind);
          if($ris_file->rowCount() > 0)  {
            $rec_file = $ris_file->fetch(PDO::FETCH_ASSOC);
            $file = "{$config["arch_folder"]}/allegati_contratto/{$rec_contratto["codice"]}/{$rec_file["riferimento"]}";
            if(file_exists($file)) {
              header('Content-Description: File Transfer');
              header("Content-Type: application/force-download");
              header("Content-Type: application/octet-stream");
              header("Content-Type: application/download");
              header('Content-Disposition: attachment; filename="'.basename($rec_file["nome_file"]).'"');
              header('Expires: 0');
              header('Cache-Control: must-revalidate');
              header('Pragma: public');
              header('Content-Length: ' . filesize($file));
              readfile($file);
              die();
            } else {
              echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
            }
          } else {
            echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
          }
        } else {
          echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
        }
      } else {
        echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
      }
    } else {
      echo '<meta http-equiv="refresh" content="0;URL=/index.php">'; die();
    }
  }
?>
