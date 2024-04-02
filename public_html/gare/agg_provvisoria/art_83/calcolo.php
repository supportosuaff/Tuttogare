<?
if ((isset($edit) && $edit) || (isset($asta_telematica) && ($asta_telematica))) {

	$bind = array();
	$bind[":codice_gara"] = $_POST["codice_gara"];
	$bind[":codice_lotto"] = $_POST["codice_lotto"];
	$strsql = "UPDATE r_partecipanti SET primo = 'N', secondo = 'N' WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$strsql = "UPDATE r_partecipanti SET anomalia = 'N', motivazione_anomalia = '', escluso='N' WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND verifica = 'N' AND anomalia_facoltativa = 'N'";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$strsql = "SELECT r_partecipanti.codice FROM r_partecipanti ";
	$strsql.= " WHERE r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S'";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$numero_partecipanti = $risultato->rowCount();
	if ($numero_partecipanti>0) {

		$errore = false;
		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		// Codice Gruppo 42: Validità dell'aggiudicazione;
		$sql = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 42)";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount() > 0) {
			$rec = $ris->fetch(PDO::FETCH_ASSOC);
			if ($rec["opzione"] == "62" && $numero_partecipanti < 2) {
				$errore = true;
				$msg.= "Numero di offerte valide insufficiente\\n";
			} else if ($rec["opzione"] == "63" && $numero_partecipanti < 3) {
				$errore = true;
				$msg.= "Numero di offerte valide insufficiente\\n";
			}
			if (!$errore) {

					$primi = 0;
					$secondi = 0;
					$partecipanti_minimi = 5;
					if (strtotime($record_gara["data_pubblicazione"]) > strtotime('2016-04-20')) {
						$partecipanti_minimi = 1;
						if (strtotime($record_gara["data_pubblicazione"]) > strtotime('2019-04-19')) {
							$partecipanti_minimi = 3;
						}
					}
					if ($numero_partecipanti >= $partecipanti_minimi)
					{
						$bind = array();
						$bind[":codice_gara"] = $_POST["codice_gara"];
						$sql  = "SELECT SUM(b_valutazione_tecnica.punteggio) AS massimo, b_criteri_punteggi.economica
										 FROM b_valutazione_tecnica
										 JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
										 WHERE codice_gara = :codice_gara AND codice_padre = 0 ";
						if ($record_gara["nuovaOfferta"] == "S") {
							$bind[":codice_lotto"] = $_POST["codice_lotto"];
							$sql .= "AND (codice_lotto = :codice_lotto OR codice_lotto = 0)";
						}
						$sql .= " GROUP BY b_criteri_punteggi.economica";
						$ris = $pdo->bindAndExec($sql,$bind);
						$array_max=array();
						$array_max["tecnico"] = 0;
						$array_max["economico"] = 0;
						if ($ris->rowCount()>0) {
							while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
								if ($rec["economica"]=="N") {
									$array_max["tecnico"] += $rec["massimo"];
								} else {
									$array_max["economico"] += $rec["massimo"];
								}
							}
						}

						$bind = array();
						$bind[":codice_gara"] = $_POST["codice_gara"];
						$bind[":codice_lotto"] = $_POST["codice_lotto"];

						$sql = "SELECT r_partecipanti.codice, SUM(DISTINCT r_punteggi_gare.punteggio) as totale_tecnico,  SUM(DISTINCT r_punteggi_economica.punteggio) as totale_economico FROM ";
						$sql .= "r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = r_punteggi_gare.codice_partecipante ";
						$sql .= "JOIN r_punteggi_gare as r_punteggi_economica ON r_partecipanti.codice = r_punteggi_economica.codice_partecipante ";
						$sql .= "JOIN b_criteri_punteggi ON r_punteggi_gare.codice_punteggio = b_criteri_punteggi.codice ";
						$sql .= "JOIN b_criteri_punteggi AS b_criteri_economici ON r_punteggi_economica.codice_punteggio = b_criteri_economici.codice ";
						$sql .= "WHERE r_punteggi_gare.codice_gara = :codice_gara AND r_punteggi_gare.codice_lotto = :codice_lotto ";
						$sql .= "AND r_punteggi_economica.codice_gara = :codice_gara AND r_punteggi_economica.codice_lotto = :codice_lotto ";
						$sql .= "AND r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S' ";
						$sql .= "AND b_criteri_punteggi.economica = 'N' AND b_criteri_economici.economica = 'S' ";
						$sql .= "GROUP BY r_partecipanti.codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {
							while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
									$soglia_tecnica = 0.8 * $array_max["tecnico"];
									$soglia_tecnica = number_format($soglia_tecnica,3);
									$soglia_economica = 0.8 * $array_max["economico"];
									$soglia_economica = number_format($soglia_economica,3);
									if (($rec["totale_tecnico"] >= $soglia_tecnica) && ($rec["totale_economico"] >= $soglia_economica)) {
										/* echo $rec["totale_tecnico"] . "/" . $soglia_tecnica . " : ";
										echo $rec["totale_economico"] . "/ " . $soglia_economica . "/"; */
										$bind = array();
										$bind[":codice"] = $rec["codice"];
										$sql = "UPDATE r_partecipanti SET anomalia = 'S', motivazione_anomalia = 'Calcolo automatico delle offerte anomale' ";
										$sql.= " WHERE codice = :codice AND verifica = 'N' AND anomalia_facoltativa = 'N'";
										$ris_update = $pdo->bindAndExec($sql,$bind);
									}
							}
						}
					}

					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];

					//Verifico opzione 128 - Riparametrazione Assoluta
					$sql_128 = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 128";
					$ris_128 = $pdo->bindAndExec($sql_128,$bind);
					if($ris_128->rowCount() > 0)
					{
						$bind = array();
						$bind[":codice_gara"] = $_POST["codice_gara"];
						$bind[":codice_lotto"] = $_POST["codice_lotto"];

						$tmp_punteggi = array();
						$sql_punteggi = "SELECT r_punteggi_gare.* FROM r_punteggi_gare JOIN b_criteri_punteggi ON r_punteggi_gare.codice_punteggio = b_criteri_punteggi.codice WHERE r_punteggi_gare.codice_gara = :codice_gara AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' AND r_punteggi_gare.codice_lotto = :codice_lotto";
						$ris_punteggi = $pdo->bindAndExec($sql_punteggi,$bind);
						if ($ris_punteggi->rowCount() > 0)
						{
							while ($rec_punteggi = $ris_punteggi->fetch(PDO::FETCH_ASSOC))
							{
								$tmp_punteggi["partecipanti"][$rec_punteggi["codice_partecipante"]] =  $rec_punteggi["punteggio"];
								$tmp_punteggi["codice_univoco"][$rec_punteggi["codice_partecipante"]] = $rec_punteggi["codice"];
							}
							$bind = array();
							$bind[":codice_gara"] = $_POST["codice_gara"];
							$bind[":codice_lotto"] = $_POST["codice_lotto"];
							$sql_tot_peso = "SELECT SUM(b_valutazione_tecnica.punteggio) as tot FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_valutazione_tecnica.codice_padre = 0 AND b_valutazione_tecnica.codice_gara = :codice_gara AND (b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0) AND b_criteri_punteggi.economica = 'N' AND  b_criteri_punteggi.temporale = 'N' AND  b_criteri_punteggi.temporale = 'N'";
							$ris_tot_peso = $pdo->bindAndExec($sql_tot_peso,$bind);
							if ($ris_tot_peso->rowCount() > 0)
							{
								$rec_tot_peso = $ris_tot_peso->fetch(PDO::FETCH_ASSOC);
								$tmp_punteggi["partecipanti"] = riparametrazione_assoluta($tmp_punteggi["partecipanti"],$rec_tot_peso["tot"]);
								foreach ($tmp_punteggi["partecipanti"] as $codice_partecipante => $punteggio)
								{
									$bind = array();
									$bind[":punteggio"] = $punteggio;
									$bind[":codice_partecipante"] = $codice_partecipante;
									$bind[":codice_univoco"] = $tmp_punteggi["codice_univoco"][$codice_partecipante];

									$sql_update_punteggi = "UPDATE r_punteggi_gare SET punteggio = :punteggio WHERE codice_partecipante = :codice_partecipante AND codice = :codice_univoco";
									$ris_update_punteggi = $pdo->bindAndExec($sql_update_punteggi,$bind);
								}
							}
						}
					}


					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$bind[":codice_lotto"] = $_POST["codice_lotto"];
					// Aggiudicazione
					$sql = "SELECT r_partecipanti.codice, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
					$sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara AND r_punteggi_gare.codice_lotto = :codice_lotto
									 AND r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N' ";
					$sql.= " GROUP BY r_punteggi_gare.codice_partecipante, r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ORDER BY totale_punteggio DESC";
					$ris = $pdo->bindAndExec($sql,$bind);
					$i=0;
					$punteggio=0;
					$numero_aggiudicatari = 1;
					if (isset($_POST["numero_aggiudicatari"]) && is_numeric($_POST["numero_aggiudicatari"])) $numero_aggiudicatari = $_POST["numero_aggiudicatari"];
					while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) {

						$bind = array();
						$bind[":codice"] = $rec["codice"];

						if ($punteggio != $rec["totale_punteggio"]) {
							$punteggio = $rec["totale_punteggio"];
							$i++;
						}
						if ($i<=$numero_aggiudicatari) {
							$sql = "UPDATE r_partecipanti SET primo = 'S' WHERE codice = :codice";
							$ris_update = $pdo->bindAndExec($sql,$bind);
							$primi++;
						} else if ($i==2 && $numero_aggiudicatari == 1) {
							$sql = "UPDATE r_partecipanti SET secondo = 'S' WHERE codice = :codice";
							$ris_update = $pdo->bindAndExec($sql,$bind);
							$secondi++;
						} else {
							break;
						}
					}
					if ($primi>$numero_aggiudicatari) $msg.= "Attenzione - Ex aequo su aggiudicazione\\n";
					if ($secondi>1) $msg.= "Attenzione - Ex aequo su secondi classificati\\n";
			}
		} else {
			$errore = true;
			$msg .= "Impossibile procedere al calcolo.\\n";
		}
	} else {
		$errore = true;
		$msg .= "Verificare che vi siano partecipanti ammessi alla gara.\\n";
	}
}
?>
