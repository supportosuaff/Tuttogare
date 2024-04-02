<?
	session_start();
	include_once "../../../config.php";
	include_once $root . "/inc/funzioni.php";

	if (!is_operatore()) {
		?><h3 class="ui-state-error">Permessi insufficienti per il download del file. #1</h3><?
	} else {
	  $ris_operatore = $pdo->bindAndExec("SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente", array(':codice_utente' => $_SESSION["codice_utente"]));
	  $operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);
	  if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && isset($_GET["codice"])) {
			$ris = $pdo->bindAndExec("SELECT * FROM b_allegati_contratto WHERE codice = :codice AND codice_operatore = :codice_operatore", array(':codice' => $_GET["codice"], ':codice_operatore' => $operatore["codice"]));
			if($ris->rowCount() > 0) {
				$rec_allegato = $ris->fetch(PDO::FETCH_ASSOC);
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
