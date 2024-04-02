<?
include_once("../../../config.php");
include_once($root . "/layout/top.php");
$edit = false;
$lock = true;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase($_SERVER['QUERY_STRING'], $_SERVER['REQUEST_URI']);
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase, $_GET["codice"], $_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	$codice = $_GET["codice"];
	$bind = array();
	$bind[":codice"] = $codice;
	$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
	}
	// if (strtotime($record["data_pubblicazione"]) < strtotime('2023-07-01 00:00:00') && $ente["ambienteTest"] == "N") {
	// 	$strsql .= " AND data_scadenza <= now() ";
	// }
	$risultato = $pdo->bindAndExec($strsql, $bind);
	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);

		$scelta_anomalia = false;

		$bind = array();
		$bind[":criterio"] = $record["criterio"];
		$sql = "SELECT * FROM b_criteri WHERE codice = :criterio AND directory = 'art_82'";
		$ris_scelta = $pdo->bindAndExec($sql, $bind);
		if ($ris_scelta->rowCount() > 0 && (strtotime($record["data_pubblicazione"]) > strtotime('2016-04-20'))) {
			$formSelezione = __DIR__."/201650/form.php";
			$scelta_anomalia = true;
		}
		if ($ris_scelta->rowCount() > 0 && $record["tipologia"] != 3 && $record["norma"] == "2023-36") {
			
			$formSelezione = __DIR__."/202336/form.php";
			$scelta_anomalia = true;
		}

		if ($scelta_anomalia) {

			?><h1>SCELTA MODALIT&Agrave; CALCOLO ANOMALIA</h1><?

			$bind = array();
			$bind[":codice"] = $record["codice"];

			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti, $bind);
			$print_form = false;
			if ($ris_lotti->rowCount() > 0) {
				if (isset($_GET["lotto"])) {
					$codice_lotto = $_GET["lotto"];

					$bind = array();
					$bind[":codice"] = $codice_lotto;

					$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice ORDER BY codice";
					$ris_lotti = $pdo->bindAndExec($sql_lotti, $bind);
					if ($ris_lotti->rowCount() > 0) {
						$print_form = true;
						$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
						echo "<h2>" . $lotto["oggetto"] . "</h2>";

						$sequenza_anomalia = $lotto["sequenza_anomalia"];
						$algoritmo_anomalia = $lotto["algoritmo_anomalia"];
						$coef_e = $lotto["coef_e"];
						$sequenza_coef = $lotto["sequenza_coef"];
					}
				} else {
					while ($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {

						$bind = array();
						$bind[":codice"] = $record["codice"];
						$bind[":codice_lotto"] = $lotto["codice"];

						$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND primo = 'S'";
						$ris_partecipanti = $pdo->bindAndExec($sql, $bind);
						$style = "";
						$primo = "";
						if ($ris_partecipanti->rowCount() > 0) {
							$primo = $ris_partecipanti->fetch(PDO::FETCH_ASSOC);
							$primo = "<br>" . $primo["partita_iva"] . " - " . $primo["ragione_sociale"];
							$style = "style=\"background-color:#0C0\"";
						}
																?>
						<a class="submit_big" <?= $style ?> href="edit.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
							<? echo $lotto["oggetto"] . $primo ?>
						</a>
					<?
					}
				}
			} else {
				$print_form = true;
				$codice_lotto = 0;
				$sequenza_anomalia = $record["sequenza_anomalia"];
				$algoritmo_anomalia = $record["algoritmo_anomalia"];
				$coef_e = $record["coef_e"];
				$sequenza_coef = $record["sequenza_coef"];
			}

			if ($print_form  && !empty($formSelezione)) {
				if (empty($sequenza_anomalia)) {
					include $formSelezione;
				} else { ?>
				<h2 class="ui-state-error" style="text-align:center">
					Metodo gi&agrave; scelto:<br>
					<?
						echo '<span style="text-transform:uppercase">' . $algoritmo_anomalia . '</span>';
						if (!empty($coef_e)) {
							echo "<br>Coefficiente: " . $coef_e;
						}
					?>
					</h2>
			<? 	}
			} else {
				if (($ris_lotti->rowCount() > 0 && isset($_GET["lotto"])) || $ris_lotti->rowCount() == 0) {
					?>
						<h1>Impossibile procedere</h1>
					<?
				}
			}
			include($root . "/gare/ritorna.php");
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
} else {
	echo "<h1>Gara non trovata</h1>";
}
include_once($root . "/layout/bottom.php");
?>