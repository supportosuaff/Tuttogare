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
					if (isset($_POST["tipologia"]) && $_SESSION["gerarchia"] === "0") {
						$codici_tipologia = array();
						foreach($_POST["tipologia"] as $tipologia) {
								$operazione_tipologia = "UPDATE";
							if ($tipologia["codice"] == "") {
								$tipologia["codice"] = 0;
								$operazione_tipologia = "INSERT";
							}
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_tipologie_ente";
							$salva->operazione = $operazione_tipologia;
							$salva->oggetto = $tipologia;
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
