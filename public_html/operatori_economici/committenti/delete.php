<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_POST["codice"])) {
		$codice = $_POST["codice"];
		if (strpos($codice,"i")!==0) {
		$bind = array(":codice"=>$codice);
		$strsql = "DELETE FROM b_committenti WHERE codice = :codice ";
		if (isset($_SESSION["gerarchia"]) && ($_SESSION["gerarchia"] >0)) {
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$strsql .= " AND codice_utente = :codice_utente";
		}
		$risultato = $pdo->bindAndExec($strsql,$bind);
		scrivilog("b_committenti","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
		
	}
			?>
			if ($("#committenti_<? echo $codice ?>").length > 0){
            	$("#committenti_<? echo $codice ?>").slideUp();
				$("#committenti_<? echo $codice ?>").remove();
            }
			<?
	}
?>
