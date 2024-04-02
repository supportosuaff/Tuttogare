<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

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
          ?>jalert('<b>Attenzione!</b><br>Non &egrave; possibile modificare le informazioni relative all&#39;ufficiale rogante nello stato attuale!');<? die();
          // $ris = $pdo->bindAndExec("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $_POST["codice"]));
          // if($ris->rowCount() > 0) {}
        }
      }
    }
    $_POST["codice_gestore"] = $_SESSION["ente"]["codice"];
    if(!empty($_POST["ruolo_altro"]) && $_POST["ruolo"] == "altro") {
      $_POST["ruolo"] = $_POST["ruolo_altro"];
    }
    if(!empty($_POST["titolo_altro"]) && $_POST["titolo"] == "altro") {
      $_POST["titolo"] = $_POST["titolo_altro"];
    }
    $operazione = "INSERT";
    if(!empty($_POST["codice_ufficiale"])) {
      $operazione = "UPDATE";
      $_POST["codice"] = $_POST["codice_ufficiale"];
    }
		$salva = new salva();
		$salva->debug = FALSE;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_ufficiale_rogante";
		$salva->operazione = $operazione;
		$salva->oggetto = $_POST;
		$codice_ufficiale = $salva->save();
    if(is_numeric($codice_ufficiale)) {
      $href = "/contratti/pannello.php?codice=" . $codice_contratto;
      $pdo->bindAndExec('REPLACE INTO r_contratti_ufficiale_rogante (codice_contratto, codice_ufficiale, utente_modifica, codice_ente) VALUES (:codice_contratto, :codice_ufficiale, :utente_modifica, :codice_ente)',
                        array(':codice_contratto' => $codice_contratto,
                              ':codice_ufficiale' => $codice_ufficiale,
                              ':utente_modifica' => $_SESSION["codice_utente"],
                              ':codice_ente' => $_SESSION["ente"]["codice"]
                        )
                      );
			if ($salva->operazione == "UPDATE") {
				?>
				alert('Modifica effettuata con successo');
        <?
			} elseif ($salva->operazione == "INSERT") {
				?>
				alert('Inserimento effettuato con successo');
				<?
			}
		  ?>window.location.href = '<? echo $href ?>';<?
    } else {
      ?>
      jalert("Errore, si prega di riprovare...");
      <?
    }
	}
?>
