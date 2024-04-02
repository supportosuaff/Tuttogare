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
			if (isset($_POST["form"]) && $_SESSION["gerarchia"] === "0") {
				$errore = false;
				foreach($_POST["form"] as $form) {
					$operazione = "UPDATE";
					$form["version"] = $_POST["version"];
					if ($form["codice"] == "") {
						$form["codice"] = 0;
						$operazione = "INSERT";
					}
					if (isset($form["tipologie"])) {
						$form["tipologie"] = implode(",",$form["tipologie"]);
					}
					for ($cont_livello=1;$cont_livello<6;$cont_livello++) {
						if ($form["livello".$cont_livello] == "-altro-") $form["livello".$cont_livello] = $form["livello".$cont_livello."_altro"];
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_dgue_settings";
					$salva->operazione = $operazione;
					$salva->oggetto = $form;
					$codice_form = $salva->save();
					if ($codice_form == 0) $errore = true;

				}
				if ($errore) {
					?>
					alert("Errore nel salvataggio.");
					<?
				} else {
					?>
					alert('Modifica effettuata con successo');
					window.location.href = window.location.href;
					<?
				}
			}
		}
	}
?>
