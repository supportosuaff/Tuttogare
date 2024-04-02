<?
	session_start();
	include_once("../../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("gare/elaborazione",$_SESSION["codice_utente"]);
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
			$strsql = "SELECT codice_gara FROM b_documentale WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$record = $risultato->fetch(PDO::FETCH_ASSOC);
			$strsql = "UPDATE b_documentale SET attivo = 'N' WHERE codice = :codice";
			if (isset($_SESSION["ente"])) {
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql.= " AND codice_ente = :codice_ente";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("b_documentale","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			
			log_gare($_SESSION["ente"]["codice"],$record["codice_gara"],"DELETE","Modello verbale");
			?>
				alert("Rielaborazione effettuata con successo");
				window.location.href = '/gare/verbali/verbale_aggiudicazione.php?codice=<? echo $record["codice_gara"] ?>';
		   <?
		}
	}

?>
