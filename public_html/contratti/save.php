<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");

	if (empty($_SESSION["codice_utente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400); die();
	} else {
		if (isset($_POST["operazione"])) {
      $_POST["codice_gestore"] = $_SESSION["ente"]["codice"];
      if(empty($_POST["codice_ente"]) && empty($_POST["codice"])) {
        $_POST["codice_ente"] = empty($_SESSION["record_utente"]["codice_ente"]) ? $_SESSION["ente"]["codice"] : $_SESSION["record_utente"]["codice_ente"];
      }
      if(!empty($_POST["codice"])) {
        $oe = $ore = 0;
        $oe = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "oe" GROUP BY b_contraenti.codice', array(':codice_contratto' => $_POST["codice"]))->rowCount();
        $ore = $pdo->bindAndExec('SELECT b_contraenti.codice FROM b_contraenti JOIN r_contratti_contraenti ON b_contraenti.codice = r_contratti_contraenti.codice_contraente WHERE r_contratti_contraenti.codice_contratto = :codice_contratto AND b_contraenti.tipologia  = "ore" GROUP BY b_contraenti.codice', array(':codice_contratto' => $_POST["codice"]))->rowCount();
        if($oe > 0 && $ore == 1) {
          $bind = array(':codice' => $_POST["codice"], ':tipo' => 'contratto', ':sezione' => 'contratti');
          $ris = $pdo->bindAndExec("SELECT b_documentale.codice FROM b_documentale WHERE b_documentale.tipo = :tipo AND b_documentale.sezione = :sezione AND b_documentale.codice_gara = :codice AND codice_allegato > 0", $bind);
          if($ris->rowCount() > 0) {
            ?>jalert('<b>Attenzione!</b><br>Non &egrave; possibile modificare i dati preliminati del contratto nello stato attuale!');<? die();
            // $ris = $pdo->bindAndExec("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $_POST["codice"]));
            // if($ris->rowCount() > 0) {}
          }
        }
      }

			$salva = new salva();
			$salva->debug = FALSE;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_contratti";
			$salva->operazione = empty($_POST["codice"]) ? "INSERT" : "UPDATE";
			$salva->oggetto = $_POST;
			$codice_contratto = $salva->save();
      if(is_numeric($codice_contratto)) {
				if ($salva->operazione=="INSERT" && $_SESSION["gerarchia"] == 2) {
					$utente = array();
					$utente["codice_contratto"] = $codice_contratto;
					$utente["codice_ente"] = $_SESSION["ente"]["codice"];
					$utente["codice_utente"] = $_SESSION["codice_utente"];

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_permessi_contratti";
					$salva->operazione = "INSERT";
					$salva->oggetto = $utente;
					$permesso = $salva->save();

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_permessi_esecuzione_contratti";
					$salva->operazione = "INSERT";
					$salva->oggetto = $utente;
					$permesso = $salva->save();
				}
        $href = "/contratti/pannello.php?codice=" . $codice_contratto;
  			if ($salva->operazione == "UPDATE") {
  				?>alert('Modifica effettuata con successo');<?
  			} elseif ($salva->operazione == "INSERT") {
  				?>alert('Inserimento effettuato con successo');<?
  			}
			  ?>window.location.href = '<? echo $href ?>';<?
      } else {
        ?>jalert("Errore, si prega di riprovare...");<?
      }
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400); die();
		}
	}
?>
