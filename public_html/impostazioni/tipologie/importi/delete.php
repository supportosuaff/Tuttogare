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
			$bind = array(":codice"=>$codice);
			$strsql = "DELETE FROM b_tipologie_importi WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_tipologie_importi","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			?>
			if ($("#importo_<? echo $codice ?>").length > 0){
				$("#importo_<? echo $codice ?>").slideUp().remove();
			}
			<?
		}
	}

?>
