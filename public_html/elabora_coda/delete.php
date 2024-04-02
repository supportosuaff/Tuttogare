<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");

	if (isset($_POST["codice"]) && isset($_SESSION["codice_utente"]) && $_SESSION["gerarchia"]==="0") {
		if (is_numeric($_POST["codice"]) && $_POST["codice"] > 0) {
			$pdo->bindAndExec("DELETE FROM b_coda WHERE codice = :codice_riferimento",array(":codice_riferimento"=>$_POST["codice"]));
			?>
			$("#qu-<?= $_POST["codice"] ?>").slideUp();
			<?
		} else if (is_numeric($_POST["codice"]) && $_POST["codice"] == "-1") {
			$pdo->query("DELETE FROM b_coda WHERE codice_ente IN (SELECT codice FROM b_enti WHERE ambienteTest = 'S')");
			?>
			window.location.reload();
			<?
		} else {
			?>
			header(403);
			<?
		}
	}
	?>
