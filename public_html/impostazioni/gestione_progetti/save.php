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
				if (isset($_POST["opzione"]) && $_SESSION["gerarchia"] === "0") {
						$codici_opzione = array();
						foreach($_POST["opzione"] as $opzione) {
							$operazione_opzione = "UPDATE";
							if ($opzione["codice"] == "") {
								$opzione["codice"] = 0;
								$operazione_opzione = "INSERT";
							}
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_conf_gestione_progetti";
							$salva->operazione = $operazione_opzione;
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
