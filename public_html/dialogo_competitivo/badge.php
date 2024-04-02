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
		$sql_ope = "SELECT * FROM r_partecipanti_dialogo
								JOIN b_bandi_dialogo ON r_partecipanti_dialogo.codice_bando = b_bandi_dialogo.codice
								WHERE r_partecipanti_dialogo.valutato = 'N' AND (b_bandi_dialogo.data_scadenza > now() OR b_bandi_dialogo.data_scadenza = 0) AND b_bandi_dialogo.codice_gestore = :codice_ente ";

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
