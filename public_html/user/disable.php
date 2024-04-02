<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("user",$_SESSION["codice_utente"]);
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
			$bind = array(":codice"=>$codice);
			$strsql = "SELECT attivo FROM b_utenti WHERE codice = :codice";
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
			$bind = array(":attivo"=>$attivo,":codice"=>$codice);
			$strsql = "UPDATE b_utenti SET attivo = :attivo, scaduto = 'N' WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_utenti","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			?>
			if ($("#flag_<? echo $codice ?>").length > 0){
            	$("#flag_<? echo $codice ?>").css('background-color',"<? echo $colore ?>");
							$("#flag_scaduto_<? echo $codice ?>").css('background-color',"#3C0");
            } else {
            	window.location.href="/user/";
            }
			<?
		}
	}

?>
