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
			$strsql = "DELETE FROM b_moduli WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_moduli","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			?>
			if ($("#moduli_<? echo $codice ?>").length > 0) {
				$("#moduli_<? echo $codice ?>").slideUp().remove();
			}
			<?
		}
	}

?>
