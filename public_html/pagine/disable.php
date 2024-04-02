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
			$strsql = "SELECT attivo FROM b_pagina WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$attivo = "S";
			$colore = "#3C0";
			if ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				if ($record["attivo"] == "S") {
					$attivo = "N";
					$colore = "#C00";
				}
			}
			$bind[":attivo"] = $attivo;
			$strsql = "UPDATE b_pagina SET attivo = :attivo WHERE codice = :codice";
			if (isset($_SESSION["ente"])) {
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql.= " AND codice_ente = :codice_ente";
			}

			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_pagina","UPDATE",$strsql,$_SESSION["codice_utente"]);
			
			?>
			if ($("#flag_<? echo $codice ?>").length > 0){
      	$("#flag_<? echo $codice ?>").css('background-color',"<? echo $colore ?>");
      } else {
      	window.location.href="/pagine/";
      }
			<?
		}
	}

?>
