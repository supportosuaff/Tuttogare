<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
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
			$strsql = "DELETE FROM r_dgue_gare WHERE codice_gara = :codice AND sezione = 'albo'";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("r_dgue_gare","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			?>
			window.location.href = window.location.href;
			<?
		}
	}

?>
