<?
  session_start();
	include '../../../config.php';
  include_once $root . '/inc/funzioni.php';

  if(empty($_GET["codice"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		header('Location: /contratti');
		die();
	} else {
		$codice = (int) $_GET["codice"];
		$codice_gara = (int) (!empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null);

	  $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice);
	  $sql  = "SELECT b_contratti.* FROM b_contratti ";
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
    if($ris->rowCount() == 1) {
      $rec_contratto = $ris->fetch(PDO::FETCH_ASSOC);
      if(!empty($_GET["dafirmare"])) {
        $bind = array(':codice' => $codice, ':tipo' => 'contratto', ':sezione' => 'contratti');
        $ris_documento = $pdo->bindAndExec("SELECT b_documentale.codice, b_allegati.nome_file, b_allegati.riferimento FROM b_documentale JOIN b_allegati ON b_allegati.codice = b_documentale.codice_allegato WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0 AND attivo = 'S'", $bind);
        if($ris_documento->rowCount() > 0) {
          $rec_documento = $ris_documento->fetch(PDO::FETCH_ASSOC);
          $file = "{$config["arch_folder"]}/allegati_contratto/{$codice}/{$rec_documento["riferimento"]}";
          $nome_File = $rec_documento["nome_file"];
        }
      } else {
        $check_firmato = $pdo->bindAndExec("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $codice));
        if($check_firmato->rowCount() > 0) {
          $rec_contratto_firmato = $check_firmato->fetch(PDO::FETCH_ASSOC);
          $file = "{$config["arch_folder"]}/allegati_contratto/{$codice}/{$rec_contratto_firmato["riferimento"]}";
          $nome_File = $rec_contratto_firmato["nome_file"];
        }
      }
			if(file_exists($file)) {
        header('Content-Description: File Transfer');
  	    header("Content-Type: application/force-download");
  			header("Content-Type: application/octet-stream");
  			header("Content-Type: application/download");
  	    header('Content-Disposition: attachment; filename="'.$nome_File.'"');
  	    header('Expires: 0');
  	    header('Cache-Control: must-revalidate');
  	    header('Pragma: public');
  	    header('Content-Length: ' . filesize($file));
  	    readfile($file);
  	    die();
      } else {
        header('Location: /contratti');
    		die();
      }
    } else {
      header('Location: /contratti');
  		die();
    }
  }
?>
