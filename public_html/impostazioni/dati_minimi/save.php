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
					if (isset($_POST["campo"]) && $_SESSION["gerarchia"] === "0" && !empty($_SESSION["ente"]["codice"])) {
						$codici_campo = array();
						foreach($_POST["campo"] as $campo) {
							$operazione_campo = "UPDATE";
							if ($campo["codice"] == "") {
								$campo["codice"] = 0;
								$operazione_campo = "INSERT";
							}
							$campo["codice_gestore"] = $_SESSION["ente"]["codice"];
							if (isset($campo["tipologie"])) $campo["tipologie"] = implode(";",$campo["tipologie"]);
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_impostazioni_dati_minimi";
							$salva->operazione = $operazione_campo;
							$salva->oggetto = $campo;
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
