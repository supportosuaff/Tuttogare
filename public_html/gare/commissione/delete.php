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
				$bind = array();
				$bind[":codice"] = $codice;
				$strsql = "SELECT * FROM b_commissioni WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
				}
				$strsql = "DELETE FROM b_commissioni  WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("b_commissioni","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);

				log_gare($_SESSION["ente"]["codice"],$record["codice_gara"],"DELETE","Membro commissione - " . $record["titolo"] . "  " . $record["cognome"] . " " . $record["nome"]);

				$strsql = "DELETE FROM `b_confronto_coppie` WHERE `codice_commissario` = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("b_confronto_coppie","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
				
			}
			?>
			if ($("#partecipante_<? echo $codice ?>").length > 0) {
            	$("#partecipante_<? echo $codice ?>").remove();
            }
			<?
		}
	}

?>
