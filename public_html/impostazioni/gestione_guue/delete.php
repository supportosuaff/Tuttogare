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
			$strsql = "DELETE FROM b_gestione_guue WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql, array(':codice' => $_POST["codice"]));
			scrivilog("b_gestione_guue ","DELETE",$strsql,$_SESSION["codice_utente"]);
			
			?>
				if ($("#opzione_<? echo $codice ?>").length > 0){
	      	$("#opzione_<? echo $codice ?>").slideUp();
	      }
			<?
		}
	}
?>
