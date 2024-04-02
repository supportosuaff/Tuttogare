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
				$bind=array();
				$bind[":codice"] = $codice;
				$strsql = "SELECT * FROM b_fasi_concorsi WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()===1) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);

					$bind = array();
					$bind[":codice_gara"] = $record["codice_gara"];
					$strsql = "DELETE FROM r_step_valutazione_concorsi WHERE codice_criterio IN (SELECT codice FROM b_criteri_valutazione_concorsi WHERE codice_gara = :codice_gara)";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					$strsql = "DELETE FROM b_criteri_valutazione_concorsi WHERE codice_gara = :codice_gara";
					$risultato = $pdo->bindAndExec($strsql,$bind);

					$bind=array();
					$bind[":codice"] = $codice;

					$strsql = "DELETE FROM b_fasi_concorsi WHERE codice = :codice";
					$risultato = $pdo->bindAndExec($strsql,$bind);
				}
			}
			?>
			if ($("#fase_<? echo $codice ?>").length > 0){
				$("#fase_<? echo $codice ?>").remove();
			}
			<?
		}
	}

?>
