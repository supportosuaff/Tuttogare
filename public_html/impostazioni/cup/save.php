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
			if (isset($_POST["strumenti"])) {
				foreach($_POST["strumenti"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_cup_strumenti_programmazione";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["finanziamenti"])) {
				foreach($_POST["finanziamenti"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_cup_finanziamenti";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["natura"])) {
				foreach($_POST["natura"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_cup_natura";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["tipologia"])) {
				foreach($_POST["tipologia"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_cup_tipologia";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["settore"])) {
				foreach($_POST["settore"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_cup_settore";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["sottosettore"])) {
				foreach($_POST["sottosettore"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_cup_sottosettore";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["categoria"])) {
				foreach($_POST["categoria"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_cup_categoria";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
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
