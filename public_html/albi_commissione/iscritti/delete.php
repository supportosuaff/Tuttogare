<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("albi_commissione",$_SESSION["codice_utente"]);
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
				$strsql = "SELECT * FROM b_commissari_albo WHERE codice = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$strsql = "UPDATE b_commissari_albo SET attivo = 'N' WHERE codice = :codice";
					$risultato = $pdo->bindAndExec($strsql,$bind);
				}
			}
			?>
			if ($("#iscritto_<? echo $codice ?>").length > 0){
				$("#iscritto_<? echo $codice ?>").remove();
			}
			<?
		}
	}

?>
