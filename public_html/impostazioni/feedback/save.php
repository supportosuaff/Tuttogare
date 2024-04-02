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
					if ($_SESSION["gerarchia"] === "0") {
						if (isset($_POST["campo"])) {
							$codici_campo = array();
							foreach($_POST["campo"] as $campo) {
								$operazione_campo = "UPDATE";
								if ($campo["codice"] == "") {
									$campo["codice"] = 0;
									$operazione_campo = "INSERT";
								}
								if (!empty($_SESSION["ente"]["codice"])) $campo["codice_ente"] = $_SESSION["ente"]["codice"];
								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $_SESSION["codice_utente"];
								$salva->nome_tabella = "b_set_feedback";
								$salva->operazione = $operazione_campo;
								$salva->oggetto = $campo;
								$codici_tipologia[] = $salva->save();
							}
						}
						if (!empty($_SESSION["ente"]["codice"])) {
							$ente = ["codice"=>$_SESSION["ente"]["codice"]];
							$ente["required_feedback"] = (!empty($_POST["required_feedback"]) && $_POST["required_feedback"] > 1) ? $_POST["required_feedback"] : 1;
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_enti";
							$salva->operazione = "UPDATE";
							$salva->oggetto = $ente;
							$salva->save();
						}
					}
			?>
			alert('Modifica effettuata con successo');
			window.location.href = window.location.href;
			<?
		}
	}



?>
