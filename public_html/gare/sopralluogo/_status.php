<?
if (isset($record) && isset($st_index)) {
		$st_color = $st_index ["ok"];
		$bind = array();
		$bind[":codice"] = $record["codice"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql = "SELECT codice FROM b_sopralluoghi WHERE codice_gara = :codice AND codice_ente = :codice_ente AND appuntamento IS NULL ";
		$ris_badge  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
		if ($ris_badge->rowCount()>0) $st_color = $st_index ["warning"];
	}
