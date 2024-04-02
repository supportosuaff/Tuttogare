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
		if (isset($_POST) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {

						$tabella = "b_modelli_standard";
						$operazione = $_POST["operazione"];
						$codice = $_POST["codice"];
						if (isset($_SESSION["ente"])) {
							$operazione = "INSERT";
							$_POST["codice_modello"] = $codice;
							$_POST["codice_ente"] = $_SESSION["ente"]["codice"];
							$bind = array();
							$bind[":codice"] = $codice;
							$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
							$sql = "SELECT * FROM b_modelli_enti WHERE codice = :codice AND codice_ente = :codice_ente";
							$ris = $pdo->bindAndExec($sql,$bind);
							$tabella = "b_modelli_enti";
							$codice = 0;
							if ($ris->rowCount()>0) {
								$modello = $ris->fetch(PDO::FETCH_ASSOC);
								$_POST["codice"] =  $modello["codice"];
								$operazione = "UPDATE";
							}
						}
						$chiavi_ignora = array("x","y","operazione","codice",);

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = $tabella;
						$salva->operazione = $operazione;
						$salva->oggetto = $_POST;
						$codice = $salva->save();


			if ($_POST["operazione"]=="UPDATE") {
				$href = "/impostazioni/modelli/";
				$href = str_replace('"',"",$href);
				$href = str_replace(' ',"-",$href);
				?>
				alert('Modifica effettuata con successo');
    	        window.location.href = '<? echo $href ?>';
        	    <?
			} elseif ($_POST["operazione"]=="INSERT") {
				$href = "/impostazioni/modelli/";
				$href = str_replace('"',"",$href);
				$href = str_replace(' ',"-",$href);
				?>
				alert('Inserimento effettuato con successo');
    	        window.location.href = '<? echo $href ?>';
        	    <?
			}
		}
	}



?>
