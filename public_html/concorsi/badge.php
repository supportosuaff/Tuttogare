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
		$strsql_badge  = "SELECT b_quesiti_concorsi.*
											FROM b_quesiti_concorsi LEFT JOIN b_risposte_concorsi ON b_quesiti_concorsi.codice = b_risposte_concorsi.codice_quesito
											JOIN b_concorsi ON b_quesiti_concorsi.codice_gara = b_concorsi.codice ";
		if ($_SESSION["gerarchia"] > 1) {
			$strsql_badge  .= "	JOIN b_permessi ON b_concorsi.codice = b_permessi.codice_gara ";
		}
		$strsql_badge  .= " WHERE (b_concorsi.codice_gestore = :codice_ente OR b_concorsi.codice_ente = :codice_ente) AND b_quesiti_concorsi.attivo = 'N' AND (b_risposte_concorsi.quesito = '' OR b_risposte_concorsi.quesito IS NULL)";
		if ($_SESSION["gerarchia"] > 1) {
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$strsql_badge  .= "	AND b_permessi.codice_utente = :codice_utente ";
		}
		$ris_badge  = $pdo->bindAndExec($strsql_badge,$bind); //invia la query contenuta in $strsql al database apero e connesso
		if ($span) {
			echo "<span class=\"badge\"";
			if ($ris_badge->rowCount()==0) echo " style=\"display:none\"";
			echo ">";
		}
		if ($ris_badge->rowCount()>0) echo $ris_badge->rowCount();
		if ($span) echo "</span>";
	}
	?>
