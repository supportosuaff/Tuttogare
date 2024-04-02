<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi($_POST["modulo"],$_SESSION["codice_utente"]);
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
			$strsql = "DELETE FROM b_pagina WHERE codice = :codice";
			if (isset($_SESSION["ente"])) {
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql.= " AND codice_ente = :codice_ente";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_pagina","DELETE",$strsql,$_SESSION["codice_utente"]);
			
			?>
			if ($("#<? echo $codice ?>").length > 0){
            	$("#<? echo $codice ?>").slideUp();
            } else {
            	window.location.href="/pagine/";
            }
			<?
		}
	}

?>
