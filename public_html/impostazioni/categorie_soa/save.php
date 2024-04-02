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
					if (isset($_POST["categoria"]) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
						foreach($_POST["categoria"] as $categoria) {
								$operazione = "UPDATE";
							if ($categoria["codice"] == "") {
								$categoria["codice"] = 0;
								$operazione = "INSERT";
							}
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_categorie_soa";
							$salva->operazione = $operazione;
							$salva->oggetto = $categoria;
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
