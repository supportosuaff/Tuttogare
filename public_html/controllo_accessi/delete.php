<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_POST["codice"]) && $_SESSION["gerarchia"] === "0") {
			$codice = $_POST["codice"];
			$bind = array();
			$bind[":codice"] = $codice;
			$sql = "DELETE FROM b_login_hash WHERE codice = :codice ";
			$ris = $pdo->bindAndExec($sql,$bind);
			?>
			window.location.href = window.location.href;
			<?
	}

?>
