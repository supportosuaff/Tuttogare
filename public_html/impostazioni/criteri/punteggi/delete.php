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
		if (isset($_POST["codice"]) && $_SESSION["gerarchia"]==0) {
			$codice = $_POST["codice"];
			$bind = array();
			$bind[":codice"] = $codice;
			$strsql = "UPDATE b_criteri_punteggi SET eliminato = 'S' WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_criteri_punteggi","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			

			?>
			if ($("#punteggio_<? echo $codice ?>").length > 0){
            	$("#punteggio_<? echo $codice ?>").remove();
            }
			<?
		}
	}

?>
