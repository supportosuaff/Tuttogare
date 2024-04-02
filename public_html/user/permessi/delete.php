<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("user",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["codice"])) {
			$codice = $_POST["codice"];
			$bind = array(":codice"=>$codice);
			$strsql = "DELETE FROM r_moduli_utente WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("r_moduli_utente","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			?>
			if ($("#permesso_<? echo $codice ?>").length > 0){
				$("#permesso_<? echo $codice ?>").slideUp();
			} else {
				window.location.href="/user/";
			}
			<?
		}
	}

?>
