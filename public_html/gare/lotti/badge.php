<?
	if (isset($record["codice"])) {
		$bind = array();
		$bind[":codice"] = $record["codice"];
		$strsql  = "SELECT b_lotti.* ";
		$strsql .= "FROM b_lotti ";
		$strsql .= "WHERE codice_gara = :codice";
		$ris_badge  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

		if ($ris_badge->rowCount()>0) {
			echo "<span class=\"badge\">" . $ris_badge->rowCount() . "</span>";
		}
	}
