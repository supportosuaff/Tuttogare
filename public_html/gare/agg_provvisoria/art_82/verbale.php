<?
	$vocabolario["#calcolo_soglia#"] = "";
	$vocabolario["#esclusione_automatica#"] = "";
	if ($record_gara["soglia_anomalia"] > 0) {
		$vocabolario["#calcolo_soglia#"] = "Media offerte: " . $record_gara["media"] . " Scarto Medio: " . $record_gara["scarto_medio"] . " Soglia di anomalia: " . $record_gara["soglia_anomalia"];
	}
	$bind = array();
	$bind[":codice_gara"] = $record_gara["codice"];
	$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 59";
	$ris_clausola = $pdo->bindAndExec($sql,$bind);
	if ($ris_clausola->rowCount()>0 && $ris_partecipanti->rowCount() >= 10) {
		$vocabolario["#esclusione_automatica#"] = "<br><br>&Egrave; stato effettuata l'esclusione automatica delle offerte anomale";
	}
?>
