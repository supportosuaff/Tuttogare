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
			if (is_numeric($codice)) {
				$bind = array();
				$bind[":codice"] = $codice;
				$strsql = "SELECT * FROM b_pubblicita_legale_concorsi WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$strsql = "DELETE FROM b_pubblicita_legale_concorsi  WHERE codice = :codice";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					scrivilog("b_pubblicita_legale_concorsi","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
					
					log_concorso($_SESSION["ente"]["codice"],$record["codice_gara"],"DELETE","Pubblicità legale - " . $record["descrizione"]);
				}
			}
			?>
				if ($("#pubblicita_<? echo $codice ?>").length > 0){
					$("#pubblicita_<? echo $codice ?>").remove();
				}
			<?
		}
	}
?>
