<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("enti",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["codice"])) {
			$codice = $_POST["codice"];
			$bind = array();
			$bind[":codice"] = $codice;
			$strsql = "SELECT attivo FROM b_enti WHERE codice = :codice";
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
			$strsql = "UPDATE b_enti SET attivo = :attivo WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_enti","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			?>
			if ($("#flag_<? echo $codice ?>").length > 0){
				$("#flag_<? echo $codice ?>").css('background-color',"<? echo $colore ?>");
			} else {
				window.location.href="/enti/";
			}
			<?
		}
	}

?>
