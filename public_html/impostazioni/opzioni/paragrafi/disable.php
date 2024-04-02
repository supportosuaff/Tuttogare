<?
	session_start();
	include_once("../../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["codice"]) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
			$codice = $_POST["codice"];
			if (strpos($codice,"i_") === false) {
			$bind = array(":codice"=>$codice);
			$strsql = "SELECT attivo FROM b_paragrafi WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$attivo = "S";
			$colore = "#3C0";
			if ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				$attivo = $record["attivo"];
				if ($attivo == "S") {
					$attivo = "N";
					$colore = "#C00";
				} else {
					$attivo = "S";
					$colore = "#3C0";
				}
			}
			$bind[":attivo"] = $attivo;
			$strsql = "UPDATE b_paragrafi SET attivo = :attivo WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_paragrafi","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			}
			?>
			if ($("#flag_paragrafo_<? echo $codice ?>").length > 0){
				$("#flag_paragrafo_<? echo $codice ?>").css('background-color',"<? echo $colore ?>");
			}
			<?
		}
	}

?>
