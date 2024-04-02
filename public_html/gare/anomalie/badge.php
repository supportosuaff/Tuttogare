<?
	if (isset($record["codice"])) {
		$bind=array();
		$bind[":codice"] = $record["codice"];
		$strsql  = "SELECT r_partecipanti.* ";
		$strsql .= "FROM r_partecipanti ";
		$strsql .= "WHERE codice_gara = :codice AND anomalia = 'S' AND escluso = 'N' AND verifica = 'N' ";
		$ris_badge  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
		if ($ris_badge->rowCount()>0) {
			echo "<span class=\"badge\">" . $ris_badge->rowCount() . "</span>";
		}
	}
