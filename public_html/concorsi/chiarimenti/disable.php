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
			$strsql = "SELECT attivo FROM b_quesiti_concorsi WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$attivo = "S";
			$colore = "#3C0";
			if ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				$attivo = $record["attivo"];
				if ($attivo == "S") {
					$attivo = "N";
					$colore = "#C00";
				} else {
					$attivo = "S";
					$colore = "#3C0";
				}
			}
			$strsql = "SELECT codice_gara FROM b_quesiti_concorsi WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$record = $risultato->fetch(PDO::FETCH_ASSOC);
			$bind[":attivo"] = $attivo;
			$strsql = "UPDATE b_quesiti_concorsi SET attivo = :attivo WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_quesiti_concorsi","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			log_concorso($_SESSION["ente"]["codice"],$record["codice_gara"],"UPDATE","Quesito: " . $codice . " - Attivo: " . $attivo);
			?>
			if ($("#flag_<? echo $codice ?>").length > 0){
				$("#flag_<? echo $codice ?>").css('background-color',"<? echo $colore ?>");
			}
			<?
		}
	}

?>
