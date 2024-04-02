<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST)) {
			if (isset($_POST["modalita"]) && $_SESSION["gerarchia"] === "0") {
				$codici_modalita = array();
				foreach($_POST["modalita"] as $modalita) {
					$operazione_modalita = "UPDATE";
					if (empty($modalita["codice"])) {
						$modalita["codice"] = 0;
						$operazione_modalita = "INSERT";
					}
					$salva = new salva();
					$salva->debug = FALSE;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_modalita_stipula";
					$salva->operazione = $operazione_modalita;
					$salva->oggetto = $modalita;
					$codici_tipologia[] = $salva->save();

				}
			}
			?>
			alert('Modifica effettuata con successo');
			window.location.href = window.location.href;
			<?
		}
	}



?>
