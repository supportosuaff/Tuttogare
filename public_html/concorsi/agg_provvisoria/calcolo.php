<?
if (isset($edit) && $edit) {

	$bind = array();
	$bind[":codice_gara"] = $_POST["codice_gara"];
	$bind[":codice_fase"] = $_POST["codice_fase"];
	$strsql = "UPDATE r_partecipanti_concorsi SET primo = 'N', secondo = 'N' WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL)";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$strsql = "SELECT r_partecipanti_concorsi.codice FROM r_partecipanti_concorsi ";
	$strsql.= " WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL) AND r_partecipanti_concorsi.ammesso = 'S'";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$numero_partecipanti = $risultato->rowCount();
	if ($numero_partecipanti>0) {
		$errore = false;
		$primi = 0;
		$secondi = 0;
		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		$bind[":codice_fase"] = $_POST["codice_fase"];
		// Aggiudicazione
		$sql = "SELECT r_partecipanti_concorsi.codice, r_partecipanti_concorsi.punteggio FROM r_partecipanti_concorsi
						WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase
						AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL) AND r_partecipanti_concorsi.ammesso = 'S' AND r_partecipanti_concorsi.escluso = 'N'
						ORDER BY punteggio DESC";
		$ris = $pdo->bindAndExec($sql,$bind);
		$i=0;
		$punteggio=0;
		$numero_aggiudicatari = 1;
		while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
			$bind = array();
			$bind[":codice"] = $rec["codice"];
			if ($punteggio != $rec["punteggio"]) {
				$punteggio = $rec["punteggio"];
				$i++;
			}
			if ($i<=$numero_aggiudicatari) {
				$sql = "UPDATE r_partecipanti_concorsi SET primo = 'S' WHERE codice = :codice";
				$ris_update = $pdo->bindAndExec($sql,$bind);
				$primi++;
			} else if ($i==2 && $numero_aggiudicatari == 1) {
				$sql = "UPDATE r_partecipanti_concorsi SET secondo = 'S' WHERE codice = :codice";
				$ris_update = $pdo->bindAndExec($sql,$bind);
				$secondi++;
			} else {
				break;
			}
		}
		if ($primi>$numero_aggiudicatari) $msg.= "Attenzione - Ex aequo su aggiudicazione\\n";
		if ($secondi>1) $msg.= "Attenzione - Ex aequo su secondi classificati\\n";
	} else {
		$errore = true;
		$msg .= "Verificare che vi siano partecipanti ammessi alla gara.\\n";
	}
} else {
	$errore = true;
	$msg .= "Impossibile proseguire.\\n";
}
?>
