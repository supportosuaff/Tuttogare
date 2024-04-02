<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$lock = false;
if ((isset($_POST["codice"]) || isset($_POST["cod"]))&&(isset($_POST["codice_fase"]))) {
	$codice = $_POST["codice"];
	$codice_fase = $_POST["codice_fase"];
	$bind = array();
	$bind[":codice"] = $codice;
	$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
	}
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		$file=sys_get_temp_dir().DIRECTORY_SEPARATOR."aggiudicazione_provvisoria_".$codice."_".$codice_fase.".csv";
		ini_set('auto_detect_line_endings',TRUE);
		$fp=fopen($file,"w");

		$header = array("Codice","Identificativo","Ammesso","Motivazione","Punteggio");
		fputcsv($fp, $header,";");

		$bind = array();
		$bind[":codice_gara"] = $codice;
		$bind[":codice_fase"] = $codice_fase;

		$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL) ";
		$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);
		if ($ris_r_partecipanti->rowCount()>0){
			while($record_partecipante = $ris_r_partecipanti->fetch(PDO::FETCH_ASSOC)){
				$row = array($record_partecipante["codice"], html_entity_decode($record_partecipante["identificativo"], ENT_QUOTES), $record_partecipante["ammesso"], $record_partecipante["motivazione"],$record_partecipante["punteggio"]);
				fputcsv($fp, $row,";");
			}
		}
		fclose($fp);
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
		exit;
	}
}
?>
