<?
	if (isset($record["codice"])) {
		$bind = array();
		$bind[":codice"] = $record["codice"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "SELECT b_quesiti_concorsi.codice FROM b_quesiti_concorsi LEFT JOIN b_risposte_concorsi ON b_quesiti_concorsi.codice = b_risposte_concorsi.codice_quesito
						WHERE b_quesiti_concorsi.codice_ente = :codice_ente AND b_quesiti_concorsi.codice_gara = :codice AND (b_risposte_concorsi.quesito = '' OR b_risposte_concorsi.quesito IS NULL) AND b_quesiti_concorsi.attivo = 'N'";
			$ris_badge  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

			if ($ris_badge->rowCount()>0) {
				echo "<span class=\"badge\">" . $ris_badge->rowCount() . "</span>";
			}
	}
