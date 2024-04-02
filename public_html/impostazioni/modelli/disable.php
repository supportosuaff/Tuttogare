<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["codice"]) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
			$codice = $_POST["codice"];
			$bind = array();
			$bind[":codice"] = $codice;
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "SELECT * FROM b_modelli_enti WHERE codice = :codice AND codice_ente = :codice_ente";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$attivo = "S";
			$colore = "#3C0";
			if ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				$attivo = $record["attivo"];
				$modello = $record["codice_modello"];
				if ($attivo == "S") {
					$attivo = "N";
					$colore = "#C00";
				} else {
					$attivo = "S";
					$colore = "#3C0";
				}
			}
			$bind[":attivo"] = $attivo;
			$strsql = "UPDATE b_modelli_enti SET attivo = :attivo WHERE codice = :codice AND codice_ente = :codice_ente";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_modelli_enti","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			

			?>
			if ($("#flag_<? echo $modello ?>").length > 0){
            	$("#flag_<? echo $modello ?>").css('background-color',"<? echo $colore ?>");
            }
			<?
		}
	}

?>
