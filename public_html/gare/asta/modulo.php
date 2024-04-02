<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_GET["cod"]) && is_operatore()) {
		$codice = $_GET["cod"];
		$bind = array();
		$bind[":codice"] = $codice;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_gare.*,  b_procedure.fasi FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice ";
		$strsql .= "JOIN b_procedure ON b_gare.procedura = b_procedure.codice ";
		$strsql .= "WHERE b_gare.codice = :codice ";
		$strsql .= "AND b_gare.annullata = 'N' AND b_modalita.online = 'S' ";
		$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		$accedi = false;
		if ($risultato->rowCount() > 0) {
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			$bind = array();
			$bind[":codice"] = $codice;
			$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice";
			$ris_inviti = $pdo->bindAndExec($strsql,$bind);
			if ($ris_inviti->rowCount()>0) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice AND r_inviti_gare.codice_utente = :codice_utente";
				$ris_invitato = $pdo->bindAndExec($strsql,$bind);
				if ($ris_invitato->rowCount()>0) $accedi = true;
			} else {
				$accedi = true;
			}
		}
		if ($accedi) {
			$bind = array();
			$bind[":codice"] = $record_gara["codice"];
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$sql_fase = "SELECT r_partecipanti.* FROM r_partecipanti JOIN b_2fase ON r_partecipanti.codice_gara = b_2fase.codice_gara AND r_partecipanti.codice_lotto = b_2fase.codice_lotto
									WHERE r_partecipanti.codice_utente = :codice_utente AND r_partecipanti.codice_gara = :codice
									AND ammesso = 'S' AND escluso = 'N' AND b_2fase.data_inizio <= now() AND b_2fase.data_fine > now()";
			$ris_fase = $pdo->bindAndExec($sql_fase,$bind);
			if (strtotime($record_gara["data_scadenza"]) > time() || ($ris_fase->rowCount()>0)) {
				header("Location: /gare/telematica2.0/modulo.php?cod=".$record_gara["codice"]);
			} else {
				$strsql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND ammesso = 'S' AND escluso = 'N' AND codice_utente = :codice_utente";
				$ris = $pdo->bindAndExec($strsql,$bind);
				if ($ris->rowCount()>0) {
						header("Location: /gare/asta/asta.php?cod=".$record_gara["codice"]);
				} else {
					echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
				}
			}
		} else {
			echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
		}
	} else {
		echo "<h1>Gara inesistente</h1>";
	}
	?>
