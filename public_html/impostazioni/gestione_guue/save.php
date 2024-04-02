<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			header('Location: /');
			die();
		}
	} else {
		header('Location: /');
		die();
	}

	if (!$edit) {
		die();
	} else {
		$codici_opzione = array();
		if (!empty($_POST)) {
			if (!empty($_POST["opzione"]) && $_SESSION["gerarchia"] === "0") {
				foreach ($_POST["opzione"] as $opzione) {
					if(!isset($opzione["ordinamento"]) || $opzione["ordinamento"] == "") $opzione["ordinamento"] = 1000;
					if (empty($opzione["modalita"]) && (count($opzione["modalita"]) > 0)) {
						$opzione["modalita"] = implode(",",$opzione["modalita"]);
					} else {
						$opzione["modalita"] = "0";
					}

					$salva = new salva();
					$salva->debug = FALSE;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_gestione_guue";
					$salva->operazione = (!empty($opzione["codice"]) && $opzione["codice"] > 0) ? "UPDATE" : "INSERT";
					$salva->oggetto = $opzione;
					$codici_opzione[] = $salva->save();
				}
			}
			?>
			alert('Modifica effettuata con successo');
			window.location.href = window.location.href;
			<?
		}
	}
?>
