<?
	if (isset($record["codice"])) {

		$bind = array();
		$bind[":codice_gara"] = $record["codice"];
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$strsql_badge  = "SELECT b_messaggi_concorsi.*
											FROM b_messaggi_concorsi
											WHERE b_messaggi_concorsi.codice_gara = :codice_gara AND b_messaggi_concorsi.utente_modifica <> :codice_utente AND b_messaggi_concorsi.codice NOT IN (
												SELECT codice_messaggio FROM r_read_concorsi WHERE utente_modifica = :codice_utente
											); ";

		$ris_badge  = $pdo->bindAndExec($strsql_badge,$bind); //invia la query contenuta in $strsql al database apero e connesso
		if ($ris_badge->rowCount()>0) {
			echo "<span class=\"badge\">" . $ris_badge->rowCount() . "</span>";
		}
	}
