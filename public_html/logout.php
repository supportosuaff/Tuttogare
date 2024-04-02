<?
	session_start();
	if (!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"]) && ($_SESSION["gerarchia"] > 0) && ($_SESSION["gerarchia"] < 3)) {
		include("../config.php");
		$bind = array(":codice_utente"=>$_SESSION["codice_utente"]);
		$pdo->bindAndExec("DELETE FROM b_login_hash WHERE codice_utente = :codice_utente",$bind);
	}
	session_unset();
	session_destroy();
	echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
?>
