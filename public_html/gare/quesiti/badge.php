<?
	if (isset($record["codice"])) {

		$bind = array();
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$bind[":codice"] = $record["codice"];

		$strsql_badge  = "SELECT b_conversazioni.*
											FROM b_conversazioni JOIN b_utenti ON b_conversazioni.utente_modifica = b_utenti.codice
											JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
											JOIN b_consulenze ON b_conversazioni.codice_quesito = b_consulenze.codice
											JOIN b_enti ON b_consulenze.codice_ente = b_enti.codice ";
		if ($_SESSION["tipo_utente"] == "SAD" || $_SESSION["tipo_utente"] == "CON") {
			$strsql_badge .= "WHERE b_conversazioni.letto = 'N' AND b_gruppi.id <> 'SAD' AND b_gruppi.id <> 'CON'";
		} else {
			$strsql_badge .= "WHERE b_conversazioni.letto = 'N' AND (b_gruppi.id = 'SAD' || b_gruppi.id = 'CON')";
		}
		$strsql_badge .= " AND b_consulenze.codice_ente = :codice_ente AND b_consulenze.codice_gara = :codice";
		if ($_SESSION["tipo_utente"] == "CON") {
			$bind[":gruppo_consulenza"] = $_SESSION["record_utente"]["gruppo_consulenza"];
			$strsql_badge .= " AND b_enti.gruppo_consulenza = :gruppo_consulenza ";
		}
		$ris_badge  = $pdo->bindAndExec($strsql_badge,$bind); //invia la query contenuta in $strsql al database apero e connesso
		if ($ris_badge->rowCount()>0) {
			echo "<span class=\"badge\">" . $ris_badge->rowCount() . "</span>";
		}
	}
