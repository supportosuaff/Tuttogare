<?
  session_start();
	include '../../../config.php';
  include_once $root . '/inc/funzioni.php';

  if(empty($_POST["codice_contratto"]) || !isset($_POST["codice_pec"]) || empty($_SESSION["codice_utente"]) || !isset($_SESSION["ente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		?>
    jalert('Non hai i permessi per eseguire questa operazione!');
    <?
		die();
	} else {
		$codice = $_POST["codice_contratto"];
		$codice_gara = !empty($_POST["codice_gara"]) ? $_POST["codice_gara"] : null;

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
      $bind = array(':codice' => $codice, ':tipo' => 'contratto', ':sezione' => 'contratti');
			$ris_documento = $pdo->bindAndExec("SELECT b_documentale.codice, b_allegati.nome_file, b_allegati.riferimento FROM b_documentale JOIN b_allegati ON b_allegati.codice = b_documentale.codice_allegato WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0 AND attivo = 'S'", $bind);
			if($ris_documento->rowCount() > 0) {
        $rec_documento = $ris_documento->fetch(PDO::FETCH_ASSOC);
        $file = "{$config["arch_folder"]}/allegati_contratto/{$codice}/{$rec_documento["riferimento"]}";
				if(file_exists($file)) {
          if(!empty($_POST["email"])) {

  					$mailer = new Communicator();
  					$mailer->oggetto = "INVITO A STIPULARE - CONTRATTO #".$rec_contratto["codice"];
  					$mailer->corpo = "<h2>INVITO A STIPULARE</h2>";
            $mailer->corpo .= "Si trasmette in allegato il file .pdf del contratto:<br>";
  					$mailer->corpo .= "<br><strong>" . $rec_contratto["oggetto"] . "</strong><br><br>";
            $mailer->corpo .= "Una volta firmato il file deve essere caricato sul sitema dell&#39;amministrazione al link:";
            $mailer->corpo .= '<a href="' . $config["protocollo"] .$_SERVER["SERVER_NAME"].'/contratti_operatore/pannello.php?codice='.$rec_contratto["codice"].'">'.$config["protocollo"].$_SERVER["SERVER_NAME"].'/contratti_operatore/pannello.php?codice='.$rec_contratto["codice"].'</a>';
  					$mailer->corpo .= "<br><br>";
  					$mailer->corpo .= "Distinti Saluti<br><br>";
  					$mailer->codice_pec = $_POST["codice_pec"];
            $mailer->attachment = $file;
  					$mailer->comunicazione = true;
  					$mailer->coda = FALSE;
  					$mailer->sezione = "contratti";
  					$mailer->codice_gara = $rec_contratto["codice"];
            $mailer->destinatari = $_POST["email"];
  					$esito = $mailer->send();
            if ($esito) {
              ?>
              alert('Contratto inviato correttamente!');
              <?
            } else {
              ?>
              jalert('Non è stato possibile inviare la comunicazione! Errore indirizzo pec destinatario.');
              <?
              die();
            }
          } else {
            ?>
            jalert('Non è stato possibile inviare la comunicazione! Errore indirizzo pec destinatario.');
            <?
            die();
          }
        } else {
          ?>
          jalert('Non è stato possibile recuperare le informazioni sul contratto!');
          <?
      		die();
        }
      } else {
        ?>
        jalert('Non è stato possibile recuperare le informazioni sul contratto!');
        <?
    		die();
      }
    } else {
      ?>
      jalert('Non hai i permessi per eseguire questa operazione!');
      <?
  		die();
    }
  }
?>
