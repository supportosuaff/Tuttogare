<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	if (empty($_SESSION["codice_utente"]) || !check_permessi("contratti",$_SESSION["codice_utente"])) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
		die();
	} else {
		if(!empty($_POST["codice"])) {
			$codice = $_POST["codice"];
			?>
			if ($("#partecipante_<? echo $codice ?>").length > 0){
      	$("#partecipante_<? echo $codice ?>").slideUp().remove();
      }
			<?
		}
	}
?>
