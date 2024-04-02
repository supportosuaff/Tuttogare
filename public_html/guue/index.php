<?
	@session_start();
	include_once "../../config.php";
	include_once $root . "/layout/top.php";

	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("guue",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	if($edit) {
		echo "<h1>GESTIONE PUBBLICAZIONI GUUE</h1>";
		include_once 'check_stato.php';
		include_once 'table.php';
	}
	include_once $root . "/layout/bottom.php";
	?>
