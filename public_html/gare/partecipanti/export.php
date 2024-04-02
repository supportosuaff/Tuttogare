<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/partecipanti/edit.php'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_GET["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			if ($edit) {
				$bind = array();
				$bind[":codice"] = $_GET["codice_gara"];
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$strsql .= " AND data_scadenza <= now() ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$bind=array();
					$bind[":codice"] = $_GET["codice_gara"];
					$bind[":lotto"] = $_GET["codice_lotto"];
					$table = "r_partecipanti";
					if (!empty($_GET["ifase"])) $table = "r_partecipanti_Ifase";
					$sql = "SELECT codice, partita_iva, ragione_sociale, pec, tipo FROM {$table} WHERE codice_gara = :codice AND codice_lotto = :lotto AND codice_capogruppo = 0 AND ({$table}.conferma = TRUE OR {$table}.conferma IS NULL)";
					$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
					if ($ris_partecipanti->rowCount() > 0) {
						$sql = "SELECT codice, partita_iva, ragione_sociale, pec, tipo FROM {$table} WHERE codice_capogruppo = :codice ";
						$ris_sub = $pdo->prepare($sql);
						$data = [["codice","partita_iva","ragione_sociale","pec","ruolo"]];
						while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
							$data[] = $partecipante;
							$ris_sub->bindValue(":codice",$partecipante["codice"]);
							$ris_sub->execute();
							if ($ris_sub->rowCount() > 0) {
								while ($partecipante = $ris_sub->fetch(PDO::FETCH_ASSOC)) {
									$partecipante["codice"] = "";
									$data[] = $partecipante;
								}
							}
						}
						$file = 'partecipanti.csv';
						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename='.basename($file));
						header('Expires: 0');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');
						foreach($data AS $record) {
							echo "\"";
							echo implode('";"',$record);
							echo "\"".PHP_EOL;
						}
					}
				}
	 		}
		}
	}

?>
