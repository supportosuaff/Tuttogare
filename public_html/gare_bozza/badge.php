<?
	/*
	$span = true;
	if (!isset($pdo)) {
		die();
		session_start();
		include_once("../../config.php");
		include_once($root."/inc/funzioni.php");
		$span = false;
	}
	if (is_operatore()) {
		$bind=array();
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$bind[":codice_utente"] = $_SESSION["codice_utente"];

		$sql_ope  = "SELECT b_gare.*, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura ";
		$sql_ope .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
		$sql_ope .= "JOIN b_criteri ON b_gare.criterio = b_criteri.codice ";
		$sql_ope .= "JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice ";
		$sql_ope .= "JOIN r_partecipanti ON b_gare.codice = r_partecipanti.codice_gara ";
		$sql_ope .= "WHERE b_gare.annullata = 'N' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		$sql_ope .= "AND r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.conferma = FALSE AND r_partecipanti.conferma IS NOT NULL ";
		$sql_ope .= "AND b_gare.pubblica > 0 ";
		$sql_ope .= "GROUP BY b_gare.codice ORDER BY cast(id as INT) DESC, codice DESC" ;
		$ris_ope = $pdo->bindAndExec($sql_ope,$bind);
		$echo_span = 0;
		while ($rec_ope = $ris_ope->fetch(PDO::FETCH_ASSOC)) {
			$bind=array();
			$bind[":codice_gara"] = $rec_ope["codice"];
			if (strtotime($rec_ope["data_scadenza"]) > time()) {
				$echo_span++;
			} else {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$sql_aste = "SELECT * FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto ";
				$sql_aste.= " WHERE r_partecipanti.codice_gara = :codice_gara AND data_fine > now() AND conferma = FALSE and conferma IS NOT NULL AND codice_utente = :codice_utente";
				$ris_aste = $pdo->bindAndExec($sql_aste,$bind);
				if ($ris_aste->rowCount() >0) $echo_span++;
			}
		}
		if ($span) {
			echo "<span class=\"badge disable-update\"";
			if ($echo_span===0) echo " style=\"display:none\"";
			echo ">";
		}
		if ($echo_span>0) echo $echo_span;
		if ($span) echo "</span>";
	}
	*/
?>
