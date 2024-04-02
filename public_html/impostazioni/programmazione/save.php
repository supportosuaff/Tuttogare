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
					$salva->nome_tabella = "b_conf_programmazione_tipologia";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["categorie"])) {
				foreach($_POST["categorie"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_programmazione_categorie";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["privato"])) {
				foreach($_POST["privato"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_programmazione_capitale_privato";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["progettazione"])) {
				foreach($_POST["progettazione"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_programmazione_stato_progettazione";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["finalita"])) {
				foreach($_POST["finalita"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_programmazione_finalita";
					$salva->operazione = $operazione;
					$salva->oggetto = $elemento;
					$salva->save();
				}
			}
			if (isset($_POST["finanziarie"])) {
				foreach($_POST["finanziarie"] as $elemento) {
						$operazione = "UPDATE";
					if ($elemento["codice"] == "") {
						$elemento["codice"] = 0;
						$operazione = "INSERT";
					}
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_conf_programmazione_risorse_finanziarie";
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
