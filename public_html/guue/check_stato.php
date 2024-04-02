<?
  @session_start();
  if(empty($root)) {
    include_once '../../config.php';
    include_once $root . '/inc/funzioni.php';
  }

  $access = FALSE;
  if(!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"])) {
    $access = check_permessi("guue",$_SESSION["codice_utente"]);
    if (!$access) {
      echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
      die();
    }
  } else {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  }

  if($access) {

    include_once $root . "/guue/tedesender.class.php";

    $bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
    $sql = "SELECT * FROM b_pubb_guue WHERE (stato = 'TRASMESSO' OR (stato = 'PUBBLICATO' AND (no_doc_ojs IS NULL OR no_doc_ojs = ''))) AND codice_ente = :codice_ente AND (id_pubblicazione IS NOT NULL || id_pubblicazione <> '') AND soft_delete = FALSE";
    if(!empty($codice_gara)) {
      $sql .= " AND codice_gara = :codice_gara";
      $bind[":codice_gara"] = $codice_gara;
    }

    $ris = $pdo->bindAndExec($sql, $bind);
    if($ris->rowCount() > 0) {
      $sql_update = "UPDATE b_pubb_guue SET stato = :stato, data_pubblicazione = :data_pubblicazione, no_doc_ojs = :no_doc_ojs WHERE codice = :codice";
      $ris_update = $pdo->prepare($sql_update);

      $tedesender = new TedEsender();

      $ris_id_guue_ente = $pdo->bindAndExec(
        "SELECT id_guue FROM b_enti WHERE codice = :codice_ente AND attivo = 'S'",
        array(':codice_ente' => $_SESSION["ente"]["codice"])
      );
      if($ris_id_guue_ente->rowCount() > 0) {
        $tedesender->username .= $ris_id_guue_ente->fetch(PDO::FETCH_ASSOC)["id_guue"];
      }

      while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
        try {
          $pubblicato = TRUE;
          $notice = $tedesender->getNoticeInfo($rec["id_pubblicazione"]);
          if($notice["status"] != "RECEIVED") {
            if(!empty($notice["technical_validation_report"]["items"])) {

              foreach ($notice["technical_validation_report"]["items"] as $validation) {
                if ($pubblicato == FALSE) break;
                if((!$validation["valid"] && empty($validation["severity"])) || (! empty($validation["severity"]) && !$validation["valid"] && $validation["severity"] != "WARNING")) {
                  $pubblicato = FALSE;
                  $errore = $notice["technical_validation_report"]["items"];
                }
              }
            }
            if(!empty($notice["validation_rules_report"]["items"])) {
              foreach ($notice["validation_rules_report"]["items"] as $validation) {
                if ($pubblicato == FALSE) break;
                if((!$validation["valid"] && empty($validation["severity"])) || (! empty($validation["severity"]) && !$validation["valid"] && $validation["severity"] != "WARNING")) $pubblicato = FALSE;
              }
            }
            $ris_update->bindValue(':codice', $rec["codice"]);
            $ris_update->bindValue(':stato', $pubblicato ? 'PUBBLICATO' : 'RIFIUTATO');
            $ris_update->bindValue(':data_pubblicazione', "0000-00-00 00:00:00");
            $ris_update->bindValue(':no_doc_ojs', !empty($notice["publication_info"]["no_doc_ojs"]) ? $notice["publication_info"]["no_doc_ojs"] : "");
            if($pubblicato && !empty($notice["publication_info"]["publication_date"])) {
              $data_pubblicazione = date('Y-m-d h:i:s', strtotime($notice["publication_info"]["publication_date"]));
              $ris_update->bindValue(':data_pubblicazione', $data_pubblicazione);
            }
            $ris_update->execute();
          }
        } catch (Exception $e) {
          continue;
        }
      }
    }

    $bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
    $sql = "SELECT * FROM b_pubb_guue WHERE stato = 'PUBBLICATO' AND codice_ente = :codice_ente AND (id_pubblicazione IS NOT NULL || id_pubblicazione <> '') AND soft_delete = FALSE AND data_pubblicazione = '0000-00-00 00:00:00'";
    $ris = $pdo->bindAndExec($sql, $bind);
    if($ris->rowCount() > 0) {
      $sql_update = "UPDATE b_pubb_guue SET data_pubblicazione = :data_pubblicazione WHERE codice = :codice";
      $ris_update = $pdo->prepare($sql_update);

      $tedesender = new TedEsender();
      while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
        try {
          $pubblicato = TRUE;
          $notice = $tedesender->getNoticeInfo($rec["id_pubblicazione"]);
          if($notice["status"] == "PUBLISHED") {
            if(!empty($notice["publication_info"]["publication_date"])) {
              $data_pubblicazione = date('Y-m-d h:i:s', strtotime($notice["publication_info"]["publication_date"]));
              $ris_update->bindValue(':data_pubblicazione', $data_pubblicazione);
              $ris_update->bindValue(':codice', $rec["codice"]);
              $ris_update->execute();
            } else {
              if($_SESSION["developEnviroment"]) {
                $data_pubblicazione = $rec["data_pubblicazione"];
                $ris_update->bindValue(':data_pubblicazione', $data_pubblicazione);
                $ris_update->bindValue(':codice', $rec["codice"]);
                $ris_update->execute();
              }
            }
          } else {
            if($_SESSION["developEnviroment"]) {
              $data_pubblicazione = $rec["data_trasmissione"];
              $ris_update->bindValue(':data_pubblicazione', $data_pubblicazione);
              $ris_update->bindValue(':codice', $rec["codice"]);
              $ris_update->execute();
            }
          }
        } catch (Exception $e) {
          continue;
        }
      }
    }

  } else {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  }
?>
