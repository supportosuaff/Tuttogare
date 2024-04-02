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
			$delete = true;
			if (is_numeric($codice)) {
				$delete = false;
				$bind=array();
				$bind[":codice"] = $codice;
				$strsql = "SELECT r_partecipanti.* FROM r_partecipanti JOIN b_gare ON r_partecipanti.codice_gara = b_gare.codice WHERE r_partecipanti.codice = :codice AND b_gare.modalita = 1";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$delete = true;
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$strsql = "DELETE FROM r_partecipanti WHERE codice = :codice";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					scrivilog("r_partecipanti","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);

					$strsql = "DELETE FROM r_partecipanti WHERE codice_capogruppo = :codice AND codice_capogruppo > 0";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					scrivilog("r_partecipanti","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
					
					log_gare($_SESSION["ente"]["codice"],$record["codice_gara"],"DELETE","Partecipante " . $record["ragione_sociale"] . " - " . $record["partita_iva"]);
				}
			}
			if ($delete) {
				?>
				if ($("#partecipante_<? echo $codice ?>").length > 0){
					$("#partecipante_<? echo $codice ?>").remove();
				}
				if ($("#partecipanti div").length==0) {
					$("#deserta").show();
				}
				<?
			} else {
				?>
				alert("Impossibile cancellare");
				<?
			}
		}
	}

?>
