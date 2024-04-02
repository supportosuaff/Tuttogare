<?
	session_start();
	include("../../config.php");
	include_once($root."/inc/funzioni.php");
;
	if (is_operatore()) {
		$bind=array();
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql = "UPDATE r_comunicazioni_utenti SET letto = 'S' WHERE codice_ente = :codice_ente AND codice_utente = :codice_utente ";
		$ris = $pdo->bindAndExec($sql,$bind);
	}
?>
