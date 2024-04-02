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
				if (isset($_POST["moduli"]) && $_SESSION["gerarchia"] === "0") {
						foreach($_POST["moduli"] as $modulo) {
							$operazione_modulo = "UPDATE";
							if ($modulo["codice"] == "") {
								$modulo["codice"] = 0;
								$operazione_modulo = "INSERT";
							}
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_moduli";
							$salva->operazione = $operazione_modulo;
							$salva->oggetto = $modulo;
							$codici_modulo[] = $salva->save();
						}
					}
			?>
			alert('Modifica effettuata con successo');
			window.location.href = window.location.href;
			<?
		}
	}



?>
