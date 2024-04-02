<?
	session_start();
	include_once('../../config.php');
	include_once($root.'/inc/funzioni.php');

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("controllo_accessi",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit) {
		$bind = array();
		$sLimit = "";
		if ( isset( $_POST['start'] ) && $_POST['length'] != '-1' && is_numeric( $_POST['start'] ) && is_numeric( $_POST['length'] ))
		{
			$sLimit = "LIMIT ".$_POST['start'].", ".$_POST['length'];
		}

		switch ($_POST["operazione"]) {
			case 'accessi':
				$sql = "SELECT b_log_accessi.timestamp AS times, concat(b_utenti.cognome,' ',b_utenti.nome) AS username, b_log_accessi.ip, b_enti.denominazione FROM b_log_accessi JOIN b_utenti ON b_log_accessi.utente_modifica = b_utenti.codice
								LEFT JOIN b_enti ON b_enti.codice = b_log_accessi.codice_ente WHERE 1 ";
				if (isset($_SESSION["ente"])) {
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$sql.= " AND b_log_accessi.codice_ente = :codice_ente ";
				}
				break;
			case 'tentativi':
				$sql = "SELECT b_log_tentativi.timestamp AS times,  b_log_tentativi.username, b_log_tentativi.ip, b_enti.denominazione FROM b_log_tentativi LEFT JOIN b_enti ON b_enti.codice = b_log_tentativi.codice_ente WHERE 1 ";
				if (isset($_SESSION["ente"])) {
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$sql.= " AND b_log_tentativi.codice_ente = :codice_ente ";
				}
				break;
		}
		if (!empty($sql)) {

			$rResultNoFilterTotal = $pdo->bindAndExec($sql,$bind);
			$iTotal = $rResultNoFilterTotal->rowCount();

			$sql.= "ORDER BY times DESC " . $sLimit;
			$ris  = $pdo->bindAndExec($sql,$bind);

			$iFilteredTotal = $iTotal;

			$output = array(
				"sEcho" => intval($_POST['draw']),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);

			while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {

				$columns = array();
					if ($rec["denominazione"] == "") $rec["denominazione"] = "AMMINISTRAZIONE";
					$columns[]= $rec["denominazione"];
					$columns[]= $rec["username"];
					$columns[]= $rec["ip"];
					$columns[]= mysql2datetime($rec["times"]);


				$output["aaData"][] = $columns;

			}
			echo json_encode( $output );
		}
	}

?>
