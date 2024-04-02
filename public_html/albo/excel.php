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
			$keys = array_keys($alloperatori[0]);
			$keys[] = "cpv"; 
			$keys[] = "certificazioni_qualita"; 
			$keys[] = "certificazioni_ambientali"; 
			$keys[] = "altre_certificazioni"; 
			$keys[] = "brevetti"; 
			$keys[] = "soa"; 
			$keys[] = "fatturati"; 
			$fp = fopen('php://output', 'w');
			fputcsv($fp, $keys,";",'"');

			$sql = "SELECT GROUP_CONCAT(codice SEPARATOR '/') AS cpv FROM r_cpv_operatori WHERE codice_utente = :codice_utente GROUP BY codice_utente ";
			$cpv = $pdo->prepare($sql);
			
			$strsql = "SELECT codice FROM b_certificazioni_qualita WHERE codice_operatore = :codice_operatore";
			$qualita = $pdo->prepare($strsql);
		
			$strsql = "SELECT * FROM b_certificazioni_ambientali WHERE codice_operatore = :codice_operatore";
			$ambientali = $pdo->prepare($strsql);

			$strsql = "SELECT * FROM b_altre_certificazioni WHERE codice_operatore = :codice_operatore";
			$altre_certificazioni = $pdo->prepare($strsql);

			$strsql = "SELECT * FROM b_brevetti WHERE codice_operatore = :codice_operatore";
			$brevetti = $pdo->prepare($strsql);
			
			
			$strsql = "SELECT GROUP_CONCAT(CONCAT(b_categorie_soa.id,\"-\",b_classifiche_soa.id) SEPARATOR \"/\") AS soa
												FROM b_certificazioni_soa JOIN b_categorie_soa
								 ON b_certificazioni_soa.codice_categoria = b_categorie_soa.codice
								 JOIN b_classifiche_soa ON b_certificazioni_soa.codice_classifica = b_classifiche_soa.codice
								 WHERE b_certificazioni_soa.codice_operatore = :codice_operatore";
			$soa = $pdo->prepare($strsql);
			
			$strsql = "SELECT b_certificazioni_soa.*, b_categorie_soa.descrizione FROM b_certificazioni_soa JOIN b_categorie_soa
								 ON b_certificazioni_soa.codice_categoria = b_categorie_soa.codice
								 WHERE b_certificazioni_soa.codice_classifica = 0 AND b_certificazioni_soa.codice_operatore = :codice_operatore";
			$soa_fatturato = $pdo->prepare($strsql); 

			foreach($alloperatori AS $record) {
				$cpv->bindValue(":codice_utente",$record["codice_utente"]);
				$cpv->execute();
				$record[] = $cpv->fetch(PDO::FETCH_ASSOC)["cpv"];
				$qualita->bindValue(":codice_operatore",$record["codice_operatore"]);
				$qualita->execute();
				$record[] = ($qualita->rowCount() > 0) ? "S" : "N";
				$ambientali->bindValue(":codice_operatore",$record["codice_operatore"]);
				$ambientali->execute();
				$record[] = ($ambientali->rowCount() > 0) ? "S" : "N";
				$altre_certificazioni->bindValue(":codice_operatore",$record["codice_operatore"]);
				$altre_certificazioni->execute();
				$record[] = ($altre_certificazioni->rowCount() > 0) ? "S" : "N";
				$brevetti->bindValue(":codice_operatore",$record["codice_operatore"]);
				$brevetti->execute();
				$record[] = ($brevetti->rowCount() > 0) ? "S" : "N";
				$soa->bindValue(":codice_operatore",$record["codice_operatore"]);
				$soa->execute();
				$record[] = $soa->fetch(PDO::FETCH_ASSOC)["soa"];
				$soa_fatturato->bindValue(":codice_operatore",$record["codice_operatore"]);
				$soa_fatturato->execute();
				$tmp = "";
				if ($soa_fatturato->rowCount() > 0) {
					$tmp = [];
					while($soa_fatt = $soa_fatturato->fetch(PDO::FETCH_ASSOC)) {
						$text = $soa_fatt["descrizione"].":";
						$anno_corrente = (int)date("Y");
						for($i = ($anno_corrente-5);$i<$anno_corrente;$i++){
							$fatturato = 0;
							$sql = "SELECT * FROM b_fatturato_soa WHERE codice_attestazione = :codice_attestazione AND anno = :anno ";
							$ris_fatturato = $pdo->bindAndExec($sql,array(":codice_attestazione"=>$soa_fatt["codice"],":anno"=>$i));
							if ($ris_fatturato->rowCount() === 1) {
								$fatturato = $ris_fatturato->fetch(PDO::FETCH_ASSOC)["fatturato"];
							}
							$text .= "[" . $i . "-" . $fatturato."]";
						}
						$tmp[] = $text;
					}
					$tmp = implode("/",$tmp);
				}
				$record[] = $tmp;
				fputcsv($fp, $record,";",'"');
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
