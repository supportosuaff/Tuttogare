<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
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

			$operazione = "UPDATE";
			if (isset($_POST["ente"]["usa_ssl"])) {
				$_POST["ente"]["usa_ssl"] = 1;
			} else {
				$_POST["ente"]["usa_ssl"] = 0;
			}
			$_POST["ente"]["password"] = simple_encrypt($_POST["ente"]["password"],$_SESSION["ente"]["cf"]);
			$_POST["ente"]["codice"] = $_SESSION["ente"]["codice"];
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_enti";
			$salva->operazione = $operazione;
			$salva->oggetto = $_POST["ente"];
			$codice = $salva->save();
			if (isset($_POST["pec"]) && is_array($_POST["pec"])) {
				foreach($_POST["pec"] as $pec) {
					$pec["codice_ente"] = $_SESSION["ente"]["codice"];
					$operazione = "INSERT";
					if (isset($pec["usa_ssl"])) {
						$pec["usa_ssl"] = 1;
					} else {
						$pec["usa_ssl"] = 0;
					}
					if (is_numeric($pec["codice"])) {
						$operazione = "UPDATE";
					}
					$pec["password"] = simple_encrypt($pec["password"],$_SESSION["ente"]["cf"]);

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_pec";
					$salva->operazione = $operazione;
					$salva->oggetto = $pec;
					$codice = $salva->save();

				}
			}
			?>
               alert('Modifica effettuata con successo');
		       window.location.href = window.location.href;
             <?
		}
	}



?>
