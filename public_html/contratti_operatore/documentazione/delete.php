<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	if (!is_operatore()) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
		die();
	} else {
		if (isset($_POST["codice"])) {
			$codice = $_POST["codice"];
			if(is_numeric($codice)) {
				$bind = array(":codice"=>$codice);
				$sql = "DELETE FROM b_allegati_contratto WHERE codice = :codice";
				$ris = $pdo->bindAndExec($sql,$bind);
			}
			?>
			if ($("#modulo_<?= $codice ?>").length > 0){
      	$("#modulo_<?= $codice ?>").slideUp().remove();
      }
			<?
		}
	}
?>
