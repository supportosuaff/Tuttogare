<?
	session_start();
	include("../../config.php");
	include_once($root."/inc/funzioni.php");
	include_once($root."/inc/oeManager.class.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albo",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	if (isset($_POST["filters"])) {
		$oeManager = new oeManager();
		parse_str($_POST["filters"],$filtri);
		foreach($filtri["oeManager"] AS $key => $value) {
			if (property_exists("oeManager",$key)) $oeManager->$key = $value;
			if ($key == "classifica_only_selected") $oeManager->$key = false;
			if ($key == "elenco" && !empty($value)) {
				$value = explode("-",$value);
				$oeManager->tipo_elenco = $value[0];
				$oeManager->codice_elenco = $value[1];
			}
		}
		$alloperatori = $oeManager->getList();
		if (count($alloperatori) > 0 && $alloperatori !== false) {
			header('Content-Type: application/excel');
			header('Content-Disposition: attachment; filename="export.csv"');
			$keys = ["categoria","scadenza"];
			$chiavi_operatore = ["ragione_sociale","codice_fiscale_impresa","partita_iva","indirizzo_legale","citta_legale","provincia_legale","pec","telefono"];
			$keys = array_merge($keys,$chiavi_operatore);
			$keys[] = "certificazioni_qualita"; 
			$keys[] = "certificazioni_ambientali"; 
			$keys[] = "altre_certificazioni"; 
			$keys[] = "soa"; 
			$fp = fopen('php://output', 'w');
			fputcsv($fp, $keys,";",'"');

			$strsql = "SELECT codice FROM b_certificazioni_qualita WHERE codice_operatore = :codice_operatore";
			$qualita = $pdo->prepare($strsql);
		
			$strsql = "SELECT * FROM b_certificazioni_ambientali WHERE codice_operatore = :codice_operatore";
			$ambientali = $pdo->prepare($strsql);

			$strsql = "SELECT * FROM b_altre_certificazioni WHERE codice_operatore = :codice_operatore";
			$altre_certificazioni = $pdo->prepare($strsql);			
			
			$strsql = "SELECT GROUP_CONCAT(CONCAT(b_categorie_soa.id,\"-\",b_classifiche_soa.id) SEPARATOR \"/\") AS soa
												FROM b_certificazioni_soa JOIN b_categorie_soa
								 ON b_certificazioni_soa.codice_categoria = b_categorie_soa.codice
								 JOIN b_classifiche_soa ON b_certificazioni_soa.codice_classifica = b_classifiche_soa.codice
								 WHERE b_certificazioni_soa.codice_operatore = :codice_operatore";
			$soa = $pdo->prepare($strsql);
			$sql = "SELECT r_partecipanti_albo.timestamp_abilitazione AS abilitazione, b_bandi_albo.oggetto
							FROM b_bandi_albo JOIN r_partecipanti_albo ON b_bandi_albo.codice = r_partecipanti_albo.codice_bando 
							AND b_bandi_albo.codice_gestore = :codice_ente AND r_partecipanti_albo.ammesso = 'S' 
							AND r_partecipanti_albo.codice_operatore = :codice_operatore AND b_bandi_albo.manifestazione_interesse = 'N'";
			$albi = $pdo->prepare($sql);
			$albi->bindValue(":codice_ente",$_SESSION["ente"]["codice"]);
			foreach($alloperatori AS $record) {
				$tmp = [];
				foreach($chiavi_operatore AS $chiave) $tmp[] = $record[$chiave];
				$qualita->bindValue(":codice_operatore",$record["codice_operatore"]);
				$qualita->execute();
				$tmp[] = ($qualita->rowCount() > 0) ? "S" : "N";
				$ambientali->bindValue(":codice_operatore",$record["codice_operatore"]);
				$ambientali->execute();
				$tmp[] = ($ambientali->rowCount() > 0) ? "S" : "N";
				$altre_certificazioni->bindValue(":codice_operatore",$record["codice_operatore"]);
				$altre_certificazioni->execute();
				$tmp[] = ($altre_certificazioni->rowCount() > 0) ? "S" : "N";
				$soa->bindValue(":codice_operatore",$record["codice_operatore"]);
				$soa->execute();
				$tmp[] = $soa->fetch(PDO::FETCH_ASSOC)["soa"];
				$albi->bindValue(":codice_operatore",$record["codice_operatore"]);
				$albi->execute();
				if ($albi->rowCount() > 0) {
					$iscrizioni = $albi->fetchAll(PDO::FETCH_ASSOC);
				} else {
					$iscrizioni = [["oggetto"=>""]];
				}
				foreach($iscrizioni AS $albo) {
					$variable = ["",""];
					if (!empty($albo["oggetto"])) {
						$data_scadenza = "";
						$re = '/20[0-9]{2}/m';
						preg_match_all($re, $albo["oggetto"], $anno, PREG_SET_ORDER, 0);
						if (!empty($anno)) {
							$anno = $anno[0][0];
							$data_scadenza = "31/12/{$anno}";
						}
						$variable = [$albo["oggetto"],$data_scadenza];
					}
					$put = array_merge($variable,$tmp);
					fputcsv($fp, $put,";",'"');
				}
			}
			fclose($fp);
		} else {
			?><h1 style="text-align:center">
			<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
		}
	} else {
		?><h1 style="text-align:center">
		<span class="fa fa-exclamation-circle fa-3x"></span><br>Errore</h1>	<?
	}
	?>
