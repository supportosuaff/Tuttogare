<?
	session_start();
	include_once('../../config.php');
	include_once($root.'/inc/funzioni.php');

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("documentale",$_SESSION["codice_utente"]);
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
		if ( isset( $_GET['start'] ) && $_GET['length'] != '-1' && is_numeric( $_GET['start'] ) && is_numeric( $_GET['length'] ))
		{
			$sLimit = "LIMIT ".$_GET['start'].", ".$_GET['length'];
		}
		$sql = "SELECT b_allegati.*, b_enti.denominazione FROM b_allegati JOIN b_enti ON b_enti.codice = b_allegati.codice_ente WHERE sezione = 'documentale' ";
		if (isset($_SESSION["ente"])) {
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$sql.= " AND b_enti.codice = :codice_ente ";
		}
	 	$sql.= "ORDER BY timestamp DESC " . $sLimit;
		$ris  = $pdo->bindAndExec($sql,$bind);

		$bind = array();

		/* Data set length after filtering */
		$sql = "SELECT b_allegati.*, b_enti.denominazione FROM b_allegati JOIN b_enti ON b_enti.codice = b_allegati.codice_ente WHERE sezione = 'documentale' ";
		if (isset($_SESSION["ente"])) {
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$sql.= " AND b_enti.codice = :codice_ente ";
		}
		$rResultNoFilterTotal = $pdo->bindAndExec($sql,$bind);
		$iTotal = $rResultNoFilterTotal->rowCount();

		$iFilteredTotal = $ris->rowCount();


	 /*
		* Output
		*/

		$output = array(
			"sEcho" => intval($_GET['draw']),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iTotal,
			"aaData" => array()
		);


		while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
      	$columns = array();
				$dir = "/allegati/documentale/".$rec["codice_ente"]."/";
				$size = "";
				if (file_exists($config["pub_doc_folder"].$dir.$rec["riferimento"]) && !is_dir($config["pub_doc_folder"].$dir.$rec["riferimento"])) {
					$size = human_filesize(filesize($config["pub_doc_folder"].$dir.$rec["riferimento"]));
				}
				$ext = explode(".",$rec["nome_file"]);
				$ext = end($ext);
        $columns[] = "<img src=\"/img/" . $ext . ".png\" alt=\"File " . $ext . "\" style=\"vertical-align:middle\">";
				if (!empty($size)) {
					$columns[] = '<a href="/documenti'.$dir.$rec["nome_file"].'">'.$rec["titolo"].'</a>';
				} else {
					$columns[] = $rec["titolo"];
				}
        if (!isset($_SESSION["ente"])) $columns[]= $rec["denominazione"];
        $columns[] = $size;
        if (check_permessi("manage_documentale",$_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
          $columns[] = "<button class='submit' style=\"width:32px !important; height:32px !important; background-color:#c00; cursor:pointer; border-radius: 16px;\" onClick=\"elimina('".$rec["codice"]."','documentale');\" title=\"Elimina\">
    			<span class=\"fa fa-remove\"></span></button>";
        } else {
          $columns[] = '';
        }

			$output["aaData"][] = $columns;

		}
		echo json_encode( $output );
	}

?>
