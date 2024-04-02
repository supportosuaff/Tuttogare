<?
	session_start();
	include_once("../../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}
		if ($edit && !$lock) {
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$bind[":codice_lotto"] = $_POST["codice_lotto"];
			$strsql = "SELECT r_partecipanti.codice, b_gare.nuovaOfferta
								 FROM r_partecipanti JOIN b_gare ON r_partecipanti.codice_gara = b_gare.codice
								 WHERE r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
								 AND r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)
								 AND r_partecipanti.ammesso = 'S'";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$numero_partecipanti = $risultato->rowCount();
			if ($numero_partecipanti>0) {
				$sum_punteggi = true;
				$sql_criteri = "SELECT b_valutazione_tecnica.*
									FROM b_valutazione_tecnica
									JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
									WHERE b_valutazione_tecnica.codice_gara = :codice_gara
									AND (b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)
									AND b_criteri_punteggi.economica = 'S' ";
				$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);
				if ($ris_criteri->rowCount() == 1) {
					$criterio = $ris_criteri->fetch(PDO::FETCH_ASSOC);
					if ($criterio["valutazione"] == "P" || ($criterio["valutazione"] == "E" && empty($criterio["options"]))) $sum_punteggi = false;
				}
				while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					if ($record["nuovaOfferta"] == "S") {
						include("new-importa.php");
					} else {
						include("old-importa.php");
					}
				}
		} else {
			?>
			 alert("Verificare che vi siano partecipanti ammessi alla gara.");
			<?
		}
	}
?>
