<?
	@session_start();
	include_once '../../config.php';
	include_once $root . '/inc/funzioni.php';
	$access = FALSE;
	if(!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"])) {
		$access = check_permessi("guue",$_SESSION["codice_utente"]);
		if (!$access) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	if($access && !empty($_GET["codice"]) && $_SESSION["gerarchia"] === "0") {
		$bind = array(':codice' => $_GET["codice"]);
		$sql = "UPDATE b_pubb_guue SET stato = 'BOZZA' WHERE codice = :codice";
		$ris = $pdo->bindAndExec($sql, $bind);
	} 
	echo '<meta http-equiv="refresh" content="0;URL=/guue/">';
?>