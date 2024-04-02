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
				if (isset($_POST["procedura"]) && $_SESSION["gerarchia"] === "0") {
						$codici_procedura = array();
						foreach($_POST["procedura"] as $procedura) {
								if (isset($procedura["avcp"])) $procedura["avcp"] = implode(";",$procedura["avcp"]);
								if (isset($procedura["tipologie"])) $procedura["tipologie"] = implode(";",$procedura["tipologie"]);
								if (isset($procedura["criteri"])) $procedura["criteri"] = implode(";",$procedura["criteri"]);
								$operazione_procedura = "UPDATE";
							if ($procedura["codice"] == "") {
								$procedura["codice"] = 0;
								$operazione_procedura = "INSERT";
							}
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_procedure";
							$salva->operazione = $operazione_procedura;
							$salva->oggetto = $procedura;
							$codici_procedura[] = $salva->save();
						}
					}
			?>
               alert('Modifica effettuata con successo');
		       window.location.href = window.location.href;
             <?
		}
	}



?>
