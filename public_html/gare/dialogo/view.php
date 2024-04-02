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
		$strsql  = "SELECT b_dialogo.*, r_dialogo.timestamp_trasmissione,r_dialogo.codice AS codice_dialogo, r_dialogo.nome_file FROM
								r_dialogo JOIN b_dialogo ON r_dialogo.codice_dialogo = b_dialogo.codice
								JOIN b_gare ON b_dialogo.codice_gara = b_gare.codice
								WHERE b_dialogo.codice_gara = :codice_gara AND r_dialogo.codice_utente = :codice_utente
								AND b_gare.annullata = 'N'
								AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente)
								AND (b_gare.pubblica = '2' OR b_gare.pubblica = '1') ORDER BY b_dialogo.timestamp DESC";

		$risultato = $pdo->bindAndExec($strsql,$bind);

		if ($risultato->rowCount() > 0) {
		?>
			<h1>DIALOGO COMPETITIVO</h1>
			<table width="100%">
				<thead>
				<tr>
					<td>Oggetto</td>
					<td>Termini</td>
					<td>Risposta</td>
					<td></td>
				</tr>
				</thead>
			<?
			while($dialogo = $risultato->fetch(PDO::FETCH_ASSOC)) {
			?>

					<tr>
						<td>
							<strong><?= $dialogo["titolo"] ?></strong>
						</td>
						<td width="150">
							Scadenza: <strong><?= mysql2datetime($dialogo["data_scadenza"]) ?></strong>
							<? if ($dialogo["data_apertura"] > 0) { ?><br>Apertura:<br><strong><?= mysql2datetime($dialogo["data_apertura"]) ?></strong><? } ?>
						</td>
						<td width="150">
							<? if ($dialogo["timestamp_trasmissione"] > 0) { ?>
								<?= mysql2datetime($dialogo["timestamp_trasmissione"]) ?>
							<? } ?>
						</td>
						<td width="10">
							<a href="/gare/dialogo/modulo.php?cod=<? echo $dialogo["codice_dialogo"] ?>" title="Dettagli"><span class="btn-round btn-warning" ><span class="fa fa-search"></span></span></a>
						</td>
					</tr>

			<?
			}
			?>
			</table>
			<?
		} else {
			echo "<h1>Nessuna richiesta</h1>";
		}
	} else {
		echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
	}
include_once($root."/layout/bottom.php");
?>
