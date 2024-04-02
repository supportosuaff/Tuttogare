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
					if (isset($_POST["fase"]) && $_SESSION["gerarchia"] === "0") {
						foreach($_POST["fase"] as $fase) {
								$operazione_fase = "UPDATE";
							if ($fase["codice"] == "") {
								$fase["codice"] = 0;
								$operazione_fase = "INSERT";
							}
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_conf_stati_progetti";
							$salva->operazione = $operazione_fase;
							$salva->oggetto = $fase;
							$codici_fase[] = $salva->save();
						}
					}
			?>
			alert('Modifica effettuata con successo');
			window.location.href = window.location.href;
             <?
		}
	}



?>
