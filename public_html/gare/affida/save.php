<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}
		if ($edit && !$lock) {
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$strsql = "SELECT * FROM b_gare WHERE codice = :codice_gara";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$_POST["gara"]["codice"] = $_POST["codice_gara"];
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_gare";
				$salva->operazione = "UPDATE";
				$salva->oggetto = $_POST["gara"];
				$codice = $salva->save();

				$aggiudicata = false;
				foreach($_POST["partecipante"] as $partecipante) {
					if ($partecipante["primo"]=="S") {
						$partecipante["conferma"] = true;
						$aggiudicata = true;
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "r_partecipanti";
					$salva->operazione = "UPDATE";
					$salva->oggetto = $partecipante;
					$codice = $salva->save();
					if ($codice == false) {
						$errore = true;
					}
				}
				if (!isset($errore) || (isset($errore) && !$errore)) {
					if ($aggiudicata) {
						$bind = array();
						$bind[":codice_gara"] = $_POST["codice_gara"];
						$bind[":data_scadenza"] = date2mysql($_POST["gara"]["data_atto_esito"]);
						$pubblica = ($_SESSION["ente"]["codice"] == "138") ? 1 : 2;

						$strsql = "UPDATE b_gare SET pubblica = {$pubblica}, stato = 7, data_scadenza = :data_scadenza, pubblica_partecipanti = 'S' WHERE codice = :codice_gara";
						$risultato = $pdo->bindAndExec($strsql,$bind);
					}

					$href = "/gare/pannello.php?codice=".$_POST["codice_gara"];
					if (class_exists("syncERP")) {
						$syncERP = new syncERP();
						if (method_exists($syncERP,"sendUpdateRequest")) {
							$syncERP->sendUpdateRequest($_POST["codice_gara"]);
						}
					}
					
					if (!empty($msg)) { ?>
						alert('<? echo trim($msg); ?>');
					<? } else {	?>
						alert('Elaborazione effettuata con successo');
					<? }  ?>
						window.location.href = window.location.href;
					<?
						log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Modifica graduatoria provvisoria");
					} else {
					?>
						alert('Si è verificato un errore');
					<?
				}
			} else {
				?>
					alert('Si è verificato un errore durante il salvataggio. Riprovare');
				<?
			}
	}

?>
