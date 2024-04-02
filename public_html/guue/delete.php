<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$access = FALSE;
	if(!empty($_SESSION["codice_utente"]) && !empty($_SESSION["ente"])) {
		$access = check_permessi("guue",$_SESSION["codice_utente"]);
		if (!$access) {
			die();
		}
	} else {
		die();
	}

	if($access && !empty($_POST["codice"]) && $_SESSION["gerarchia"] === "0") {
		$bind = array(':codice' => $_POST["codice"]);
		$sql = "UPDATE b_pubb_guue SET soft_delete = TRUE WHERE codice = :codice";
		$ris = $pdo->bindAndExec($sql, $bind);
		?>
			if ($("#guue_<?= $_POST["codice"] ?>").length > 0) {
				$("#guue_<?= $_POST["codice"] ?>").slideUp();
      } else {
      	window.location.reload();
      }
		<?
	} 
?>
