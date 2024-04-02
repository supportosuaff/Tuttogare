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
					if (isset($_POST["tipo"]) && $_SESSION["gerarchia"] === "0") {
						$codici_tipo = array();
						foreach($_POST["tipo"] as $tipo) {
								$operazione_tipo = "UPDATE";
							if ($tipo["codice"] == "") {
								$tipo["codice"] = 0;
								$operazione_tipo = "INSERT";
							}
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_conf_tipo_varianti";
							$salva->operazione = $operazione_tipo;
							$salva->oggetto = $tipo;
							$codici_tipo[] = $salva->save();

						}
					}
			?>
               alert('Modifica effettuata con successo');
		       window.location.href = window.location.href;
             <?
		}
	}



?>
