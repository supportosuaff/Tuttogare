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
		if (isset($_POST["codice"]) && $_SESSION["gerarchia"]==0) {
			$codice = $_POST["codice"];
			$bind = array(":codice"=>$codice);
			$strsql = "DELETE FROM b_gestione_progetti WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_gestione_progetti","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			?>
			if ($("#opzione_<? echo $codice ?>").length > 0){
				$("#opzione_<? echo $codice ?>").remove();
			}
			<?
		}
	}

?>
