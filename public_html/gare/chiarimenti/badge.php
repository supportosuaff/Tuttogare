<?
	if (isset($record["codice"])) {
		$bind = array();
		$bind[":codice"] = $record["codice"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "SELECT b_quesiti.codice FROM b_quesiti LEFT JOIN b_risposte ON b_quesiti.codice = b_risposte.codice_quesito
						WHERE b_quesiti.codice_ente = :codice_ente AND b_quesiti.codice_gara = :codice AND (b_risposte.testo = '' OR b_risposte.testo IS NULL) AND b_quesiti.attivo = 'N'";
			$ris_badge  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

			if ($ris_badge->rowCount()>0) {
				echo "<span class=\"badge\">" . $ris_badge->rowCount() . "</span>";
			}
	}
