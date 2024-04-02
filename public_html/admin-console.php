<?
	include("../config.php");
	$pagina_login = true;
	include($root."/layout/top.php");
	if (!isset($_SESSION["codice_utente"]) && !isset($_SESSION["ente"])) {
		$_SESSION["id_sessione-admin"] = sha1(session_id());
		echo '<meta http-equiv="refresh" content="0;URL=/accesso.php">';
	} else {
		echo "<h1>Accesso negato</h1>";
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	include($root."/layout/bottom.php");
?>
