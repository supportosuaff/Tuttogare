<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["codice"])) {
			$codice = $_POST["codice"];
			$bind = array(":codice"=>$codice);
			$sql = "UPDATE b_modulistica_albo SET attivo = 'N' WHERE codice = :codice";
			$ris = $pdo->bindAndExec($sql,$bind);
			?>
			if ($("#modulo_<? echo $codice ?>").length > 0){
      	$("#modulo_<? echo $codice ?>").slideUp().remove();
      }
			<?
		}
	}

?>
