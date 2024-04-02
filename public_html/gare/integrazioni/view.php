<?
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$public = true;
	if ((isset($_GET["cod"]) || isset($_POST["cod"]))&& is_operatore()) {
		if (isset($_POST["cod"])) $_GET["cod"] = $_POST["cod"];
		$codice = $_GET["cod"];
		$bind = array();
		$bind[":codice_gara"] = $codice;
		$bind[":codice_utente"] = $_SESSION["codice_utente"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_integrazioni.*, r_integrazioni.timestamp_trasmissione,r_integrazioni.codice AS codice_integrazione, r_integrazioni.nome_file, b_lotti.oggetto AS lotto FROM
								r_integrazioni JOIN b_integrazioni ON r_integrazioni.codice_integrazione = b_integrazioni.codice
								JOIN b_gare ON b_integrazioni.codice_gara = b_gare.codice
								LEFT JOIN b_lotti ON b_integrazioni.codice_lotto = b_lotti.codice
								WHERE b_integrazioni.codice_gara = :codice_gara AND r_integrazioni.codice_utente = :codice_utente
								AND b_gare.annullata = 'N'
								AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
								AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ORDER BY b_integrazioni.codice_lotto, b_integrazioni.timestamp DESC";

		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
		?>
			<h1><?= traduci("INTEGRAZIONI") ?></h1>
			<table width="100%">
				<thead>
				<tr>
					<td><?=traduci("Tipologia")?></td>
					<td><?=traduci("Oggetto")?></td>
					<td><?=traduci("scadenza")?></td>
					<td><?=traduci("Risposta")?></td>
					<td></td>
				</tr>
				</thead>
			<?
			while($integrazione = $risultato->fetch(PDO::FETCH_ASSOC)) {
			?>

					<tr>
						<td width="100">
							<strong><? switch($integrazione["soccorso_istruttorio"]) {
								case "N": echo "Integrazione"; break;
								case "S": echo "Soccorso Istruttorio"; break;
								case "A": echo "Verifica Anomalie"; break;
							} ?></strong>
						</td>
						<td>
							<strong><?= $integrazione["titolo"] ?></strong>
							<?= ($integrazione["lotto"] != "") ? "<br>".$integrazione["lotto"]:""; ?>
						</td>
						<td width="150">
							<?= traduci("Scadenza") ?>: <strong><?= mysql2datetime($integrazione["data_scadenza"]) ?></strong>
							<? if ($integrazione["data_apertura"] > 0) { ?><br><?= traduci("Apertura") ?>:<br><strong><?= mysql2datetime($integrazione["data_apertura"]) ?></strong><? } ?>
						</td>
						<td width="150">
							<? if ($integrazione["timestamp_trasmissione"] > 0) { ?>
								<?= mysql2datetime($integrazione["timestamp_trasmissione"]) ?>
							<? } ?>
						</td>
						<td width="10">
							<a href="/gare/integrazioni/modulo.php?cod=<? echo $integrazione["codice_integrazione"] ?>" title="<?= traduci("Dettagli") ?>"><span class="btn-round btn-warning"><span class="fa fa-search"></span></span></a>
						</td>
					</tr>

			<?
			}
			?>
			</table>
			<?
		} else {
			echo "<h1>".traduci('nessun risultato')."</h1>";
		}
	} else {
		echo "<h1>". traduci('impossibile accedere') . " - ERROR 0</h1>";
	}
include_once($root."/layout/bottom.php");
?>
