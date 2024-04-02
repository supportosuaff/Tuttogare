<?
session_start();
include_once '../../../config.php';
include_once ($root."/inc/funzioni.php");

if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albi_commissione",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	$codice = $_GET["codice"];
	$bind = array(":codice"=>$codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
	$strsql = "SELECT * FROM b_albi_commissione WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_utente_ente OR codice_gestore = :codice_utente_ente) ";
	}
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		$bind=array();
		$bind[":codice"] = $record["codice"];
		$sql = "SELECT * FROM b_commissari_albo WHERE attivo = 'S' AND codice_albo = :codice";
		$ris_iscritto = $pdo->bindAndExec($sql,$bind);
		$found =false;
		if ($ris_iscritto->rowCount() > 0) {
			ob_start();
			?>CODICE FISCALE;COGNOME;NOME;INTERNO S/N;TELEFONO;EMAIL;FAX;INDIRIZZO;COMUNE;CAP<?
			echo "\n";
			while($record_iscritto = $ris_iscritto->fetch(PDO::FETCH_ASSOC)) {
				echo $record_iscritto["codice_fiscale"].";";
				echo $record_iscritto["cognome"].";";
				echo $record_iscritto["nome"].";";
				echo $record_iscritto["interno"].";";
				echo $record_iscritto["telefono"].";";
				echo $record_iscritto["email"].";";
				echo $record_iscritto["fax"].";";
				echo $record_iscritto["indirizzo"].";";
				echo $record_iscritto["comune"].";";
				echo $record_iscritto["cap"]."\n";
			}
			$csv = ob_get_clean();
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename('export_albo_commissione.csv'));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . strlen($csv));
			echo $csv;
			exit;
		} else {
			echo "<h1>Albo non trovato</h1>";
		}
	} else {
		echo "<h1>Albo non trovato</h1>";
	}
} else {
	echo "<h1>Albo non trovato</h1>";
}

	?>
