<?
if (isset($record) && isset($st_index) && $rec["link"]=="/gare/apribuste/edit.php") {
	$st_color = $st_index ["ok"];
	$bind = array();
	$bind[":codice"] = $record["codice"];
	$strsql = "SELECT b_buste.codice FROM b_buste JOIN r_partecipanti ON b_buste.codice_partecipante = r_partecipanti.codice
							WHERE r_partecipanti.codice_gara = :codice AND r_partecipanti.ammesso = 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND b_buste.aperto = 'N' ";
	$ris_badge  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
	if ($ris_badge->rowCount()>0) {
		$st_color = $st_index ["warning"];
		$strsql = "SELECT b_buste.codice FROM b_buste JOIN r_partecipanti ON b_buste.codice_partecipante = r_partecipanti.codice
								WHERE r_partecipanti.codice_gara = :codice AND r_partecipanti.ammesso = 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND b_buste.aperto = 'S' ";
		$ris_badge  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
		if ($ris_badge->rowCount()==0) {
			$st_color = $st_index ["danger"];
		}
	} else {
		$strsql = "SELECT b_emendamenti.codice FROM b_emendamenti JOIN r_partecipanti ON b_emendamenti.codice_partecipante = r_partecipanti.codice
					WHERE r_partecipanti.codice_gara = :codice AND r_partecipanti.ammesso = 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND b_emendamenti.accettato = 'W' ";
		$ris_badge  = $pdo->bindAndExec($strsql,$bind);
		if ($ris_badge->rowCount()>0) {
			$st_color = $st_index ["warning"];
		}
	}
	
}
