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
		if (isset($_POST["codice"]) && $_SESSION["gerarchia"]<=2) {
			$codice = $_POST["codice"];
			if (is_numeric($codice)) {
				$bind=array();
				$bind[":codice"] = $codice;
				$strsql = "SELECT * FROM b_lotti WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$strsql = "DELETE FROM b_lotti  WHERE codice = :codice";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					scrivilog("b_lotti","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
					
					log_gare($_SESSION["ente"]["codice"],$record["codice_gara"],"DELETE","Lotto - " . $record["oggetto"]);
				}
			}
			?>
			if ($("#lotti_<? echo $codice ?>").length > 0) {
            	$("#lotti_<? echo $codice ?>").remove();
            }
			<?
			if (isset($record["codice_gara"])) {
				$bind=array();
				$bind[":codice"] = $record["codice_gara"];
				$sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount() == 0) { ?>
					 $("#modalita_lotti").val("0");
					 $("#lotti_partecipa").slideUp();
				<? }
			}
		}
	}

?>
