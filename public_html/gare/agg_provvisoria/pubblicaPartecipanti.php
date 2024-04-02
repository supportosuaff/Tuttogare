<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit && !$lock) {
		$bind = array();
		$bind[":codice"] = $_POST["codice"];
		$tabella = "b_gare";
		if (!empty($_POST["codice_lotto"])) {
			$tabella = "b_lotti";
			$bind[":codice"] = $_POST["codice_lotto"];
		}
		$check = $pdo->go("SELECT pubblica_partecipanti FROM {$tabella} WHERE codice = :codice ",$bind);
		if ($check->rowCount() === 1) {
			$check = $check->fetch(PDO::FETCH_COLUMN);
			if ($check === "S") {
				$value = "N";
			} else if ($check === "N") {
				$value = "S";
			}
		}
		if (!empty($value)) {
			$bind[":value"] = $value;
			$check = $pdo->go("UPDATE {$tabella} SET pubblica_partecipanti = :value WHERE codice = :codice ",$bind);
			if ($check->rowCount() === 1) {
				?>
				alert("Operazione effettuata con successo!");
				window.location.reload();
				<?
			} else {
				?>
				alert("Si è verificato un errore! Si prega di riprovare");
				<?
			}
		}
	}

?>
