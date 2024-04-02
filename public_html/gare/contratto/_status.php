<?
if (isset($record) && isset($st_index)) {
	if ($rec["link"]=="/gare/contratto/index.php") {
		$st_color = $st_index ["danger"];
		$bind = array();
		$bind[":codice"] = $record["codice"];
		$bind[":codice_gestore"] = $_SESSION["ente"]["codice"];
		$strsql = "SELECT codice FROM b_contratti
							 WHERE codice_gara = :codice AND codice_gestore = :codice_gestore";
		$ris_badge  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
		if ($ris_badge->rowCount()>0) {
			$st_color = $st_index ["warning"];
			if ($ris_badge->rowCount() == 1) {
				$contratti = [$ris_badge->fetch(PDO::FETCH_ASSOC)];
			} else {
				$contratti = $ris_badge->fetchAll(PDO::FETCH_ASSOC);
			}
			$totale = count($contratti);
			$firmati = 0;
			foreach($contratti AS $contratto) {
				$check_firmato = $pdo->bindAndExec("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'", array(':codice_contratto' => $contratto["codice"]));
        if($check_firmato->rowCount() > 0) $firmati++;
			}
			if ($totale == $firmati) $st_color = $st_index["ok"];
		}
	}
}
