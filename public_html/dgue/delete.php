<?
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");

	if (isset($_POST["codice"]) && is_operatore()) {
		$codice_riferimento = $_POST["codice"];
		$bind = array();
		$bind[":codice_riferimento"] = $codice_riferimento;
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql = "SELECT codice FROM b_dgue_compilati WHERE codice = :codice_riferimento AND
							codice_utente = :codice_utente";
		$ris_old = $pdo->bindAndExec($sql,$bind);
		if ($ris_old->rowCount() === 1) {
			$sql = "DELETE FROM b_dgue_compilati WHERE codice = :codice_riferimento AND codice_utente = :codice_utente";
			$pdo->bindAndExec($sql,$bind);
			scrivilog("b_dgue_compilati","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);

			?>
			window.location.reload();
			<?
		}
	}
	?>
