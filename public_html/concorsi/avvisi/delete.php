<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("concorsi",$_SESSION["codice_utente"]);
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
			$strsql = "SELECT codice_gara FROM b_avvisi_concorsi WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$record = $risultato->fetch(PDO::FETCH_ASSOC);

			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "DELETE FROM b_avvisi_concorsi WHERE codice = :codice";
			$strsql.= " AND codice_ente = :codice_ente";

			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_avvisi_concorsi","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			log_concorso($_SESSION["ente"]["codice"],$record["codice_gara"],"DELETE","Avviso: " . $codice);
			?>
			if ($("#<? echo $codice ?>").length > 0){
            	$("#<? echo $codice ?>").slideUp();
            } else {
            	 window.history.back();
            }
			<?
		}
	}

?>
