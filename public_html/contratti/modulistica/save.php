<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("contratti",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["modulo"]) && !empty($_POST["codice_contratto"])) {
			$codice_contratto = $_POST["codice_contratto"];
      if(!empty($codice_contratto)) {
        $oe = $ore = 0;
        $oe = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "oe" GROUP BY b_contraenti.codice', array(':codice_contratto' => $codice_contratto))->rowCount();
        $ore = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "ore" GROUP BY b_contraenti.codice', array(':codice_contratto' => $codice_contratto))->rowCount();
        if($oe > 0 && $ore == 1) {
          $bind = array(':codice' => $codice_contratto, ':tipo' => 'contratto', ':sezione' => 'contratti');
          $ris = $pdo->bindAndExec("SELECT b_documentale.codice FROM b_documentale WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0", $bind);
          if($ris->rowCount() > 0) {
            ?>jalert('<b>Attenzione!</b><br>Non &egrave; possibile modificare le informazioni relative alla modulistica nello stato attuale!');<? die();
            // $ris = $pdo->bindAndExec("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $_POST["codice"]));
            // if($ris->rowCount() > 0) {}
          }
        }
      }

      $codice_gara = !empty($_GET["codice_gara"]) ? $_GET["codice_gara"] : null;

      $bind = array(":codice_ente" => $_SESSION["ente"]["codice"], ':codice' => $codice_contratto);
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
      $ris_contratto = $pdo->bindAndExec($sql,$bind);
      if($ris_contratto->rowCount() == 1) {
        $rec_contratto = $ris_contratto->fetch(PDO::FETCH_ASSOC);

        $comunicazione = FALSE;
        $salva = new salva();
        $salva->debug = FALSE;
        $salva->codop = $_SESSION["codice_utente"];
        $salva->nome_tabella = "b_modulistica_contratto";
        foreach ($_POST["modulo"] as $modulo) {
          $array = array();
          $array["titolo"] = $modulo["titolo"];
          $array["obbligatorio"] = $modulo["obbligatorio"];
          $array["codice_contratto"] = $codice_contratto ;
          $array["codice_gestore"] = $_SESSION["ente"]["codice"];
          $codice = 0;
          if (is_numeric($modulo["codice"])) $array["codice"] = $modulo["codice"];
          if ($modulo["filechunk"] != "") {
            $percorso = $config["pub_doc_folder"]."/allegati/contratti/" . $array["codice_contratto"];
            if (!is_dir($percorso)) mkdir($percorso,0777,true);
            $copy = copiafile_chunck($modulo["filechunk"],$percorso."/",$config["chunk_folder"]."/".$_SESSION["codice_utente"]);
						$array["riferimento"] = $copy["nome_fisico"];
						$array["nome_file"] = $copy["nome_file"];
          }
          $salva->operazione = is_numeric($modulo["codice"]) ? 'UPDATE' : 'INSERT';
          if($salva->operazione == "INSERT") $comunicazione = TRUE;
          $salva->oggetto = $array;
          $codice_modulo = $salva->save();
        }
        $href = "/contratti/pannello.php?codice=" . $_POST["codice_contratto"];
        $confirm_msg = "Modifica effettuata con successo.";

        if($comunicazione) {
          $oggetto = "Richiesa documentazione per la stipula del contatto id {$codice_contratto} - " . $rec_contratto["oggetto"];

          ob_start();
          ?>
          Relativamente alla procedura di cui all&#39;oggetto si invita la signoria vostra a presentare la documentazione richiesta per la stipula del contratto: <br>
          <strong><?= $rec_contratto["oggetto"] ?></strong><br><br>
          Distinti Saluti.<br><br>
          <?
          $corpo = ob_get_clean();

          $bind = array();
          $bind[":codice_contratto"] = $codice_contratto;
          $strsql = "SELECT b_utenti.pec
                     FROM r_contratti_contraenti
                     JOIN b_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente
                     JOIN b_operatori_economici ON b_operatori_economici.codice = b_contraenti.codice_operatore
                     JOIN b_utenti ON b_utenti.codice = b_operatori_economici.codice_utente
                     WHERE r_contratti_contraenti.codice_capogruppo = 0
                     AND r_contratti_contraenti.codice_contratto = :codice_contratto";


          $risultato = $pdo->bindAndExec($strsql,$bind);
          if ($risultato->rowCount()>0) {
            while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
              $indirizzi[] = $record["pec"];
            }
            $mailer = new Communicator();
            $mailer->oggetto = $oggetto;
            $mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
            $mailer->codice_pec = -1;
            $mailer->comunicazione = true;
            $mailer->coda = false;
            $mailer->sezione = "contratti";
            $mailer->codice_gara = $codice_contratto;
            $mailer->destinatari = $indirizzi;
            $esito = $mailer->send();

            if ($esito === true) {
              $confirm_msg .= "Una comunicazione è stata inviata alle parti.";
            } else {
              $confirm_msg .= "Non è stato possibile inviare una comunicazione alle parti.";
            }
          }
        }
        ?>
        alert('<?= html_entity_decode(htmlentities($confirm_msg, ENT_QUOTES), ENT_QUOTES, 'UTF-8') ?>');
        window.location.href = '<? echo $href ?>';
        <?
      } else {
        ?>jalert('Errore durante le procedure di salvataggio. Si prega di riprovare!');<?
      }
		} else {
			?>jalert('Errore durante le procedure di salvataggio. Si prega di riprovare!');<?
		}
	}
?>
