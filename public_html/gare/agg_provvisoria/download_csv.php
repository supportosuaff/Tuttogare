<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$lock = false;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))&&(isset($_GET["codice_lotto"]))) {
	$codice = $_GET["codice"];
	$codice_lotto = $_GET["codice_lotto"];
	$bind = array();
	$bind[":codice"] = $codice;
	$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
	}
	$strsql .= " AND data_apertura <= now() ";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		// $file=sys_get_temp_dir().DIRECTORY_SEPARATOR."aggiudicazione_provvisoria_".$codice."_".$codice_lotto.".csv";
		// ini_set('auto_detect_line_endings',TRUE);
		// $fp=fopen($file,"w");
		$bind = array();
		$bind[":codice_criterio"] = $record["criterio"];
		$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio=:codice_criterio ORDER BY ordinamento ";
		$ris_punteggi = $pdo->bindAndExec($sql,$bind);
		
		$header = array("Codice","Ragione sociale","Ammesso","Motivazione","Anomalia","Motivazione anomalia");
		if ($ris_punteggi->rowCount()>0) {
			$ris_punteggi = $ris_punteggi->fetchAll(PDO::FETCH_ASSOC);
			foreach($ris_punteggi AS $punteggio) {
				$header[]= $punteggio["nome"];
			}
		}
		// fputcsv($fp, $header,";");
		ob_start();
		echo "\"";
		echo implode("\";\"",$header);
		echo "\"" . PHP_EOL;
		$bind = array();
		$bind[":codice_gara"] = $codice;
		$bind[":codice_lotto"] = $codice_lotto;

		$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";
		$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);
		if ($ris_r_partecipanti->rowCount()>0){
			while($record_partecipante = $ris_r_partecipanti->fetch(PDO::FETCH_ASSOC)){
				$row = array($record_partecipante["codice"], html_entity_decode($record_partecipante["ragione_sociale"], ENT_QUOTES), $record_partecipante["ammesso"], $record_partecipante["motivazione"], $record_partecipante["anomalia"],$record_partecipante["motivazione_anomalia"]);
				if (count($ris_punteggi)>0) {
					foreach($ris_punteggi AS $punteggio) {
						$punti = 0;
						if (is_numeric($record_partecipante["codice"])) {
							$bind = array();
							$bind[":codice_partecipante"] = $record_partecipante["codice"];
							$bind[":codice_gara"] = $record["codice"];
							$bind[":codice_lotto"] = $record_partecipante["codice_lotto"];
							$bind[":codice_punteggio"] = $punteggio["codice"];

							$sql_punteggi  = "SELECT * FROM r_punteggi_gare WHERE codice_partecipante = :codice_partecipante";
							$sql_punteggi .= " AND codice_gara = :codice_gara ";
							$sql_punteggi .= " AND codice_lotto = :codice_lotto ";
							$sql_punteggi .= " AND codice_punteggio = :codice_punteggio";

							$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
							if ($ris_punteggio->rowCount()>0) {
								$arr_punti = $ris_punteggio->fetch(PDO::FETCH_ASSOC);
								$punti = $arr_punti["punteggio"];
							}
						}
						$row[]=$punti;
					}
				}
				echo "\"";
				echo implode("\";\"",$row);
				echo "\"" . PHP_EOL;
			}
		}
		// fclose($fp);
		$csv = ob_get_clean();
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename("aggiudicazione_provvisoria_".$codice."_".$codice_lotto.".csv"));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . strlen($csv));
		echo $csv;
		exit;
	}
}
?>
