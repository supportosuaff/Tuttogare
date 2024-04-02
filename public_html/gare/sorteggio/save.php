<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFase($_SERVER['QUERY_STRING'], "/gare/sorteggio/edit.php");
			if ($codice_fase !== false) {
	  		$esito = check_permessi_gara($codice_fase,$_POST["codice"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}
		if ($edit && !$lock)
	 {


			$tabella = "b_gare";
			$codice_gara = $_POST["codice"];

			if ($_POST["codice_lotto"] != 0) {
				$tabella= "b_lotti";
				$codice_lotto = $_POST["codice_lotto"];
			}
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = $tabella;
			$salva->operazione = "UPDATE";
			$salva->oggetto = $_POST;
			$codice_gara = $salva->save();

			if ($codice_gara !== false) {

				$bind=array();
				$bind[":codice"] = $codice_gara;

				$sql = "UPDATE b_gare SET stato = 6 WHERE codice = :codice";
				$update_stato = $pdo->bindAndExec($sql,$bind);

				$bind=array();
				$bind[":codice"] = $codice_gara;
				$bind[":codice_lotto"] = $_POST["codice_lotto"];
				if (isset($_POST["primo"])) {
					$strsql = "UPDATE r_partecipanti SET primo = 'N', secondo = 'N' WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($_POST["primo"] != "") {
						$bind=array();
						$bind[":codice"] = $_POST["primo"];
						$bind[":codice_lotto"] = $_POST["codice_lotto"];
						$strsql = "UPDATE r_partecipanti SET primo = 'S' WHERE codice = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0";
						$risultato = $pdo->bindAndExec($strsql,$bind);
					}

					if ($_POST["secondo"] != "") {
						$bind[":codice"] = $_POST["secondo"];
						$bind[":codice_lotto"] = $_POST["codice_lotto"];
						$strsql = "UPDATE r_partecipanti SET secondo = 'S' WHERE codice = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0";
						$risultato = $pdo->bindAndExec($strsql,$bind);
					}
				}
				log_gare($_SESSION["ente"]["codice"],$_POST["codice"],"UPDATE","Sorteggio",false);
				$href = "/gare/pannello.php?codice=" . $_POST["codice"];
				$href = str_replace('"',"",$href);
				$href = str_replace(' ',"-",$href);
				if (class_exists("syncERP")) {
					$syncERP = new syncERP();
					if (method_exists($syncERP,"sendUpdateRequest")) {
						$syncERP->sendUpdateRequest($_POST["codice"]);
					}
				}
				
				?>
				alert('Modifica effettuato con successo');
				window.location.href = '<? echo $href ?>';
				<?
			} else {
				?>
				alert('Errore durante il salvtaggio. Riprovare.');
				<?
			}
	}



?>
