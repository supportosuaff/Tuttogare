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
		$strsql  = "SELECT b_rdo_ad.*, r_rdo_ad.timestamp_trasmissione,r_rdo_ad.codice AS codice_rdo, r_rdo_ad.nome_file FROM
								r_rdo_ad JOIN b_rdo_ad ON r_rdo_ad.codice_rdo = b_rdo_ad.codice
								JOIN b_gare ON b_rdo_ad.codice_gara = b_gare.codice
								WHERE b_rdo_ad.codice_gara = :codice_gara AND r_rdo_ad.codice_utente = :codice_utente
								AND b_gare.annullata = 'N'
								AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
								AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ORDER BY b_rdo_ad.timestamp DESC";

		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
		?>
			<h1><?= traduci("RICHIESTA OFFERTA") ?></h1>
			<table width="100%">
				<thead>
				<tr>
					<td><?= traduci("Oggetto") ?></td>
					<td><?= traduci("scadenza") ?></td>
					<td><?= traduci("Risposta") ?></td>
					<td></td>
				</tr>
				</thead>
			<?
			while($rdo = $risultato->fetch(PDO::FETCH_ASSOC)) {
			?>
				<tr>
					<td>
						<strong><?= $rdo["titolo"] ?></strong>
					</td>
					<td width="150">
						<?=traduci("Scadenza")?>: <strong><?= mysql2datetime($rdo["data_scadenza"]) ?></strong>
						<? if ($rdo["data_apertura"] > 0) { ?><br><?=traduci("Apertura")?>:<br><strong><?= mysql2datetime($rdo["data_apertura"]) ?></strong><? } ?>
					</td>
					<td width="150">
						<? if ($rdo["timestamp_trasmissione"] > 0) { ?>
							<?= mysql2datetime($rdo["timestamp_trasmissione"]) ?>
						<? } ?>
					</td>
					<td width="10">
						<a href="/gare/rdo_ad/modulo.php?cod=<? echo $rdo["codice_rdo"] ?>" title="<?= traduci("Dettagli") ?>" class="btn-round btn-warning" ><span class="fa fa-search"></span></a>
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
