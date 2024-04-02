<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$errore = FALSE;
	if (empty($_SESSION["codice_utente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
		die();
	} else if (!empty($_POST["codice_contratto"])) {
    $codice_contratto = $_POST["codice_contratto"];
    if(!empty($codice_contratto)) {
      $oe = $ore = 0;
      $oe = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "oe" GROUP BY b_contraenti.codice', array(':codice_contratto' => $codice_contratto))->rowCount();
      $ore = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "ore" GROUP BY b_contraenti.codice', array(':codice_contratto' => $codice_contratto))->rowCount();
      if($oe > 0 && $ore == 1) {
        $bind = array(':codice' => $codice_contratto, ':tipo' => 'contratto', ':sezione' => 'contratti');
        $ris = $pdo->bindAndExec("SELECT b_documentale.codice FROM b_documentale WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0", $bind);
        if($ris->rowCount() > 0) {
          ?>jalert('<b>Attenzione!</b><br>Non &egrave; possibile modificare le informazioni relative alle parti nello stato attuale!');<? die();
          // $ris = $pdo->bindAndExec("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $_POST["codice"]));
          // if($ris->rowCount() > 0) {}
        }
      }
    }
		$pdo->bindAndExec('DELETE FROM `r_contratti_contraenti` WHERE codice_contratto = :codice_contratto', array(':codice_contratto' => $codice_contratto));

		if(!empty($_POST["ore"])) {
			$ore = $_POST["ore"];
			$ore["tipologia"] = "ore";
			$ore["codice"] = $ore["codice_organo"];
			$ore["codice_gestore"] = $_SESSION["ente"]["codice"];
			if(!empty($ore["titolo_altro"]) && $ore["titolo"] == "altro") $ore["titolo"] = $ore["titolo_altro"];

			$salva = new salva();
			$salva->debug = FALSE;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_contraenti";
			$salva->operazione = !empty($ore["codice"]) ? 'UPDATE' : 'INSERT';
			$salva->oggetto = $ore;
			$codice_contraente = $salva->save();
			if(is_numeric($codice_contraente)) {
				$pdo->bindAndExec('INSERT INTO r_contratti_contraenti(codice_contratto, codice_contraente, utente_modifica) VALUES (:codice_contratto, :codice_contraente, :utente_modifica)', array( ':codice_contratto' => $codice_contratto, ':codice_contraente' => $codice_contraente, ':utente_modifica' => $_SESSION["codice_utente"]));
			} else {
				$errore = TRUE;
			}
		}

		$id_partecipanti_codici = array();
		$sth = $pdo->prepare("SELECT b_operatori_economici.* FROM b_operatori_economici WHERE b_operatori_economici.codice_fiscale_impresa = :partita_iva");
		if(!empty($_POST["partecipante"])) {
			$salva = new salva();
			$salva->debug = FALSE;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_contraenti";
			foreach ($_POST["partecipante"] as $partecipante) {
				$salva->operazione = (!empty($partecipante["codice"]) && is_numeric($partecipante["codice"])) ? 'UPDATE' : 'INSERT';
				$partecipante["codice_gestore"] = $_SESSION["ente"]["codice"];
				if(!empty($partecipante["titolo_altro"]) && $partecipante["titolo"] == "altro") $partecipante["titolo"] = $partecipante["titolo_altro"];
				$sth->bindValue(':partita_iva', $partecipante["partita_iva"]);
				$sth->execute();
				if($sth->rowCount() == 1) {
					$rec_dati_operatore = $sth->fetch(PDO::FETCH_ASSOC);
					$partecipante["codice_operatore"] = $rec_dati_operatore["codice"];
					$partecipante["codice_utente"] = $rec_dati_operatore["codice_utente"];
				} else {
					$partecipante["codice_operatore"] = $partecipante["codice_utente"] = null;
				}
				// $partecipante["ruolo"] = "legale rappresentante";
				$codice_capogruppo = 0;
				$salva->oggetto = $partecipante;
				$codice_contraente = $salva->save();
				if(is_numeric($codice_contraente)) {
					$id_partecipanti_codici[$partecipante["id"]] = $codice_contraente;
					if(!empty($partecipante["codice_capogruppo"]) && $partecipante["tipo"] == "01-MANDANTE") {
						$codice_capogruppo = $partecipante["codice_capogruppo"];
						if(!is_numeric($partecipante["codice_capogruppo"])) $codice_capogruppo = $id_partecipanti_codici[$partecipante["codice_capogruppo"]];
					}
					$pdo->bindAndExec('INSERT INTO r_contratti_contraenti(codice_contratto, codice_contraente, codice_capogruppo, utente_modifica) VALUES (:codice_contratto, :codice_contraente, :codice_capogruppo, :utente_modifica)', array( ':codice_contratto' => $codice_contratto, ':codice_contraente' => $codice_contraente, ':utente_modifica' => $_SESSION["codice_utente"], ':codice_capogruppo' => $codice_capogruppo));
				} else {
					$errore = TRUE;
					break;
				}
			}
		}
	}
	if(!$errore) {
		?>
		alert('Modifica effettuata con successo');
		window.location.href="/contratti/pannello.php?codice=<?= $codice_contratto ?>";
		<?
	} else {
		?>
		jalert("Errore, si prega di riprovare...");
		<?
	}
?>
