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

		$sql_ope  = "SELECT b_gare.codice, b_gare.data_scadenza, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_procedure.invito
								FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								JOIN b_criteri ON b_gare.criterio = b_criteri.codice
								JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice
								JOIN b_enti ON b_gare.codice_gestore = b_enti.codice
								WHERE b_gare.annullata = 'N' AND codice_gestore = :codice_ente
								AND pubblica > 0 ORDER BY id DESC, codice DESC";
		$ris_ope = $pdo->bindAndExec($sql_ope,$bind);
		$echo_span = 0;
		if ($ris_ope->rowCount() > 0) {
			$sql_bad_inviti = "SELECT * FROM r_inviti_gare WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara";
			$ris_bad_inviti = $pdo->prepare($sql_bad_inviti);

			$sql_bad_partecipanti = "SELECT codice FROM r_partecipanti WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara";
			$ris_bad_partecipanti = $pdo->prepare($sql_bad_partecipanti);

			$sql_bad_fase = "SELECT * FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto
								 WHERE r_partecipanti.codice_gara = :codice_gara AND data_fine > now() AND ammesso = 'S' AND escluso = 'N' AND codice_utente = :codice_utente";
			$ris_bad_fase = $pdo->prepare($sql_bad_fase);

			$sql_bad_asta = "SELECT * FROM r_partecipanti JOIN b_aste ON r_partecipanti.codice_gara = b_aste.codice_gara AND r_partecipanti.codice_lotto = b_aste.codice_lotto
								 WHERE r_partecipanti.codice_gara = :codice_gara AND data_fine > now() AND ammesso = 'S' AND escluso = 'N' AND codice_utente = :codice_utente";
			$ris_bad_asta = $pdo->prepare($sql_bad_asta);

			$sql_bad_integrazioni = "SELECT r_integrazioni.* FROM r_integrazioni JOIN b_integrazioni ON r_integrazioni.codice_integrazione = b_integrazioni.codice
								 WHERE b_integrazioni.codice_gara = :codice_gara AND b_integrazioni.data_scadenza > now() AND r_integrazioni.codice_utente = :codice_utente";
			$ris_bad_integrazioni = $pdo->prepare($sql_bad_integrazioni);
			while ($rec_ope = $ris_ope->fetch(PDO::FETCH_ASSOC)) {
				$echo = false;
				if (strtotime($rec_ope["data_scadenza"]) > time()) {
					if ($rec_ope["invito"]=="S") {
						$ris_bad_inviti->bindValue(":codice_gara",$rec_ope["codice"]);
						$ris_bad_inviti->bindValue(":codice_utente",$_SESSION["codice_utente"]);
						$ris_bad_inviti->execute();
						if ($ris_bad_inviti->rowCount()>0) $echo  = true;
					} else {
						$ris_bad_partecipanti->bindValue(":codice_gara",$rec_ope["codice"]);
						$ris_bad_partecipanti->bindValue(":codice_utente",$_SESSION["codice_utente"]);
						$ris_bad_partecipanti->execute();
						if ($ris_bad_partecipanti->rowCount()>0) $echo  = true;
					}
				} else {
					$ris_bad_fase->bindValue(":codice_gara",$rec_ope["codice"]);
					$ris_bad_fase->bindValue(":codice_utente",$_SESSION["codice_utente"]);
					$ris_bad_fase->execute();
					if ($ris_bad_fase->rowCount()>0) {
						$echo  = true;
					} else {
						$ris_bad_asta->bindValue(":codice_gara",$rec_ope["codice"]);
						$ris_bad_asta->bindValue(":codice_utente",$_SESSION["codice_utente"]);
						$ris_bad_asta->execute();
						if ($ris_bad_asta->rowCount()>0) {
							$echo  = true;
						} else {
							$ris_bad_integrazioni->bindValue(":codice_gara",$rec_ope["codice"]);
							$ris_bad_integrazioni->bindValue(":codice_utente",$_SESSION["codice_utente"]);
							$ris_bad_integrazioni->execute();
							if ($ris_bad_integrazioni->rowCount()>0) $echo  = true;
						}
					}
				}
				if ($echo) $echo_span++;
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
