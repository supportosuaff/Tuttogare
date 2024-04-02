<?
if (isset($record) && isset($st_index)) {
	if ($rec["link"]=="/gare/nso/index.php") {
		$st_color = $st_index ["danger"];
		$bind = array();
		$bind[":codice"] = $record["codice"];
		$bind[":codice_gestore"] = $_SESSION["ente"]["codice"];
		$strsql = "SELECT codice, stato FROM b_nso
							 WHERE codice_gara = :codice AND codice_gestore = :codice_gestore";
		$ris_badge  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
		if ($ris_badge->rowCount()>0) {
			$st_color = $st_index ["warning"];
			if ($ris_badge->rowCount() == 1) {
				$orini = [$ris_badge->fetch(PDO::FETCH_ASSOC)];
			} else {
				$orini = $ris_badge->fetchAll(PDO::FETCH_ASSOC);
			}
			$totale = count($orini);
			$inviati = 0;
			foreach($orini AS $ordine) if ($ordine["stato"] == 20 || $ordine["stato"] == 40) $inviati++;
			if ($totale == $inviati) $st_color = $st_index["ok"];
		}
	}
}
