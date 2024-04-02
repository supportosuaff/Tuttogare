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
					if (isset($_POST["voce"]) && $_SESSION["gerarchia"] === "0") {
						$codici_voce = array();
						foreach($_POST["voce"] as $voce) {
								$operazione_voce = "UPDATE";
							if ($voce["codice"] == "") {
								$voce["codice"] = 0;
								$operazione_voce = "INSERT";
							}
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_conf_voci_certificato";
							$salva->operazione = $operazione_voce;
							$salva->oggetto = $voce;
							$codici_voce[] = $salva->save();

						}
					}
			?>
               alert('Modifica effettuata con successo');
		       window.location.href = window.location.href;
             <?
		}
	}



?>
