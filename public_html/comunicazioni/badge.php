<?
	$span = true;
	if (!isset($pdo)) {
		session_start();
		include_once("../../config.php");
		include_once($root."/inc/funzioni.php");
		$span = false;
	}
	if (is_operatore()) {
		$bind=array();
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$sql_ope = "SELECT b_comunicazioni.codice FROM b_comunicazioni JOIN r_comunicazioni_utenti ON b_comunicazioni.codice = r_comunicazioni_utenti.codice_comunicazione
		 WHERE letto = 'N' AND b_comunicazioni.codice_ente = :codice_ente AND codice_utente = :codice_utente ";
		$ris_ope = $pdo->bindAndExec($sql_ope,$bind);
		if ($span) {
			echo "<span class=\"badge\"";
			if ($ris_ope->rowCount()===0) echo " style=\"display:none\"";
			echo ">";
		}
		if ($ris_ope->rowCount()>0) echo $ris_ope->rowCount();
		if ($span) echo "</span>";
	}
?>
