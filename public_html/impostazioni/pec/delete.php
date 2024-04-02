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
		if (isset($_POST["codice"]) && $_SESSION["gerarchia"]<=1) {
			$codice = $_POST["codice"];
			$bind = array(":codice"=>$codice);
			$strsql = "UPDATE b_pec SET eliminato = 'S', attivo = 'N' WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_pec","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			?>
			if ($("#<? echo $codice ?>").length > 0){
				$("#<? echo $codice ?>").slideUp().remove();
			}
			<?
		}
	}

?>
