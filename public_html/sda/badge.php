<?
	$span = true;
	if (!isset($pdo)) {
		session_start();
		include_once("../../config.php");
		include_once($root."/inc/funzioni.php");
		$span = false;
	}
	if (isset($_SESSION["codice_utente"]) && !is_operatore()) {
		$sql_ope = "SELECT * FROM r_partecipanti_sda
								JOIN b_bandi_sda ON r_partecipanti_sda.codice_bando = b_bandi_sda.codice
								WHERE r_partecipanti_sda.valutato = 'N' AND b_bandi_sda.data_scadenza >= now() AND b_bandi_sda.codice_gestore = :codice_ente ";
		$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
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
