<?
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("enti",$_SESSION["codice_utente"]);
		if ($edit) {
			
			$bind = array();
			$strsql = "";
			$strsql  = "SELECT b_enti.codice, b_enti.attivo, b_enti.timestamp, b_enti.denominazione, b_enti.dominio, b_sua.dominio AS dominio_sua, b_enti.cf, b_enti.indirizzo, b_enti.citta, b_enti.provincia, b_enti.cap, b_enti.pec,
									b_sua.codice AS codice_sua, b_sua.cf AS cf_sua, b_sua.denominazione AS denominazione_sua
									FROM b_enti
									LEFT JOIN b_enti AS b_sua ON b_enti.sua = b_sua.codice 
									WHERE b_enti.ambienteTest = 'N' AND b_enti.ufficio = 'N' ";
			if (isset($_SESSION["ente"])) {
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql .= "AND b_enti.codice = :codice_ente OR b_enti.sua = :codice_ente ";
			}
			$strsql .= " ORDER BY b_enti.denominazione ASC ";
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount()>0) {
				header('Content-Type: application/excel');
				header('Content-Disposition: attachment; filename="export.csv"');
				$risultato = $risultato->fetchAll(PDO::FETCH_ASSOC);
				$first = reset($risultato);
				$keys = array_keys($first);
				$fp = fopen('php://output', 'w');
				fputcsv($fp, $keys,";",'"');
				foreach($risultato AS $ente) {
					fputcsv($fp, $ente,";",'"');
				}
				fclose($fp);
			} else {
				?>
				<h1>Nessun record disponibile</h1>
				<?
			}
		}
	}
	?>
