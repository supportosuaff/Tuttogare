<?

	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("dialogo_competitivo",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	if (isset($_GET["codice_bando"]) && isset($_GET["key"])) {
		$chiavi = [
			"nuove_istanze",
			"aggiornamenti",
			"ammessi",
			"respinti"
		];
		if (in_array($_GET["key"], $chiavi)) {
			$key = $_GET["key"];

			ini_set('max_execution_time', 600);
			ini_set('memory_limit', '-1');

			$codice = $_GET["codice_bando"];
			$bind = array();
			$bind[":codice_bando"] = $codice;
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql = "SELECT * FROM b_bandi_dialogo WHERE codice = :codice_bando ";
			$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			if ($_SESSION["gerarchia"] > 0) {
				$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
				$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$bando = $risultato->fetch(PDO::FETCH_ASSOC);
				if (!empty($_SESSION["exportElencoPartecipanti"]["dialogo"][$bando["codice"]][$key])) {
					$data = $_SESSION["exportElencoPartecipanti"]["dialogo"][$bando["codice"]][$key];
					header("Content-type: text/csv");
					header("Content-Disposition: attachment; filename=file.csv");
					header("Pragma: no-cache");
					header("Expires: 0");
					echo "\"Codice Fiscale Impresa\";\"Ragione sociale\";\"Data richiesta\"";
					if ($key != "nuove_istanze") {
						echo ";\"Data Aggiornamento\";\"Data abilitazione\"";
						if (!empty($bando["periodo_revisione"])) echo ";\"Data scadenza\";\"GG Scadenza\"";
					}
					echo "\n";
					foreach ($data AS $record_partecipante) {
						echo "\""; echo (!empty($record_partecipante["partita_iva"])) ? $record_partecipante["partita_iva"] : $record_partecipante["codice_fiscale_impresa"]; echo "\";";
						echo "\""; echo strtoupper($record_partecipante["ragione_sociale"]); echo "\";";
						echo "\""; echo mysql2datetime($record_partecipante["ricezione"]); echo "\"";
						if ($key != "nuove_istanze") {
							echo ";\""; echo mysql2datetime($record_partecipante["aggiornamento"]); echo "\";";
							echo "\""; echo mysql2datetime($record_partecipante["abilitazione"]); echo "\";";
							if (!empty($bando["periodo_revisione"])) {
								echo "\""; echo $record_partecipante["data_scadenza"]; echo "\";";
								echo "\""; echo $record_partecipante["gg_scadenza"]; echo "\"";
							}
						}
						echo "\n";
					}
				}
		} else {
			?><h1 style="text-align:center">
			<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
		}
	} else {
		?><h1 style="text-align:center">
		<span class="fa fa-exclamation-circle fa-3x"></span><br>Errore</h1>	<?
	}
} else {
	?><h1 style="text-align:center">
	<span class="fa fa-exclamation-circle fa-3x"></span><br>Errore</h1>	<?
}
	?>
