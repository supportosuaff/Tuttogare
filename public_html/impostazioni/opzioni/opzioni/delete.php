<?
	session_start();
	include_once("../../../../config.php");
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
			$strsql = "UPDATE b_opzioni SET eliminato = 'S', attivo = 'N' WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_opzioni","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			?>
			if ($("#opzione_<? echo $codice ?>").length > 0){
				$("#opzione_<? echo $codice ?>").slideUp().remove();
      }
			<?
		}
	}

?>
