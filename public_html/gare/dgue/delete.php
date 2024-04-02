<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("gare/elaborazione",$_SESSION["codice_utente"]);
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

			$bind = array();
			$bind[":codice"] = $codice;
			$strsql = "DELETE FROM r_dgue_gare WHERE codice_gara = :codice AND sezione = 'gare'";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("r_dgue_gare","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			log_gare($_SESSION["ente"]["codice"],$codice,"DELETE","Richiesta DGUE");
			?>
			window.location.href = '/gare/pannello.php?codice=<?= $codice ?>';
			<?
		}
	}

?>
