<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("enti",$_SESSION["codice_utente"]);
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
			$strsql = "DELETE FROM b_enti WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_enti","DELETE",$strsql,$_SESSION["codice_utente"]);
			
			?>
			if ($("#<? echo $codice ?>").length > 0){
            	$("#<? echo $codice ?>").slideUp();
            } else {
            	window.location.href="/enti/";
            }
			<?
		}
	}

?>
