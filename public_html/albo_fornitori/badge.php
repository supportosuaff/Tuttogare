<?
	$span = true;
	if (!isset($pdo)) {
		session_start();
		include_once("../../config.php");
		include_once($root."/inc/funzioni.php");
		$span = false;
	}
	if (isset($_SESSION["codice_utente"]) && !is_operatore()) {
		$bind_badge = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
		$sql_ope = "SELECT r_partecipanti_albo.codice FROM r_partecipanti_albo
								JOIN b_bandi_albo ON r_partecipanti_albo.codice_bando = b_bandi_albo.codice
								WHERE r_partecipanti_albo.conferma = 'S' AND r_partecipanti_albo.valutato = 'N' AND (b_bandi_albo.data_scadenza > now() OR b_bandi_albo.data_scadenza = 0) AND b_bandi_albo.codice_gestore = :codice_ente ";
		if ($_SESSION["gerarchia"] > 0 && $_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"]) {
			$bind_badge[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$sql_ope .= " AND b_bandi_albo.codice_gestore = :codice_ente_utente";
		}
		$ris_ope = $pdo->bindAndExec($sql_ope,$bind_badge);
		if ($span) {
			echo "<span class=\"badge\"";
			if ($ris_ope->rowCount()==0) echo " style=\"display:none\"";
			echo ">";
		}
		if ($ris_ope->rowCount()>0) echo $ris_ope->rowCount();
		if ($span) echo "</span>";
	}
?>
