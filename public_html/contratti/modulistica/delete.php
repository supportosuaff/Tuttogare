<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	if (empty($_SESSION["codice_utente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
		die();
	} else {
		if (isset($_POST["codice"])) {
			$codice = $_POST["codice"];
			if(is_numeric($codice)) {
				$bind = array(":codice"=>$codice);
				$sql = "DELETE FROM b_modulistica_contratto WHERE codice = :codice";
				$ris = $pdo->bindAndExec($sql,$bind);
			}
			?>
			if ($("#modulo_<?= $codice ?>").length > 0) {
      	$("#modulo_<?= $codice ?>").slideUp().remove();
      }
      if ($(".modulo_<?= $codice ?>").length > 0) {
        $(".modulo_<?= $codice ?>").slideUp().remove();
      }
			<?
		}
	}
?>
