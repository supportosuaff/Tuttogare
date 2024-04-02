<?
$span = true;
if (!isset($pdo)) {
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	$span = false;
}
if (isset($_SESSION["codice_utente"]) && !is_operatore()) {
		$bind = array(":codice_ente"=>(empty($_SESSION["record_utente"]["codice_ente"])) ? $_SESSION["ente"]["codice"] : $_SESSION["record_utente"]["codice_ente"]);
		$strsql_badge  = "SELECT b_quesiti.codice
											FROM b_quesiti LEFT JOIN b_risposte ON b_quesiti.codice = b_risposte.codice_quesito
											JOIN b_gare ON b_quesiti.codice_gara = b_gare.codice ";
		if ($_SESSION["gerarchia"] > 1) {
			$strsql_badge  .= "	JOIN b_permessi ON b_gare.codice = b_permessi.codice_gara ";
		}
		$strsql_badge  .= " WHERE (b_gare.codice_gestore = :codice_ente OR b_gare.codice_ente = :codice_ente) AND b_gare.stato < 4 AND b_quesiti.attivo = 'N' AND (b_risposte.testo = '' OR b_risposte.testo IS NULL)";
		if ($_SESSION["gerarchia"] > 1) {
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$strsql_badge  .= "	AND b_permessi.codice_utente = :codice_utente ";
		}
		$ris_badge  = $pdo->bindAndExec($strsql_badge,$bind); //invia la query contenuta in $strsql al database apero e connesso
		$badge = 0;
		$badge += $ris_badge->rowCount();
		$strsql_badge  = "SELECT b_sopralluoghi.codice
											FROM b_sopralluoghi
											JOIN b_gare ON b_sopralluoghi.codice_gara = b_gare.codice ";
		if ($_SESSION["gerarchia"] > 1) {
			$strsql_badge  .= "	JOIN b_permessi ON b_gare.codice = b_permessi.codice_gara ";
		}
		$strsql_badge  .= " WHERE (b_gare.codice_gestore = :codice_ente OR b_gare.codice_ente = :codice_ente) AND b_gare.stato < 4 AND b_sopralluoghi.appuntamento IS NULL ";
		if ($_SESSION["gerarchia"] > 1) {
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$strsql_badge  .= "	AND b_permessi.codice_utente = :codice_utente ";
		}
		$ris_badge  = $pdo->bindAndExec($strsql_badge,$bind); //invia la query contenuta in $strsql al database apero e connesso
		$badge += $ris_badge->rowCount();
		if ($span) {
			echo "<span class=\"badge\"";
			if ($badge==0) echo " style=\"display:none\"";
			echo ">";
		}
		if ($badge>0) echo $badge;
		if ($span) echo "</span>";
	}
	?>
