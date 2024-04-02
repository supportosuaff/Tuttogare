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
		$strsql  = "SELECT b_integrazioni_concorsi.*, r_integrazioni_concorsi.timestamp_trasmissione,r_integrazioni_concorsi.codice AS codice_integrazione, r_integrazioni_concorsi.nome_file, b_fasi_concorsi.oggetto AS fase FROM
								r_integrazioni_concorsi JOIN b_integrazioni_concorsi ON r_integrazioni_concorsi.codice_integrazione = b_integrazioni_concorsi.codice
								JOIN b_concorsi ON b_integrazioni_concorsi.codice_gara = b_concorsi.codice
								JOIN b_fasi_concorsi ON b_integrazioni_concorsi.codice_fase = b_fasi_concorsi.codice
								WHERE b_integrazioni_concorsi.codice_gara = :codice_gara AND r_integrazioni_concorsi.codice_utente = :codice_utente
								AND b_concorsi.annullata = 'N'
								AND (b_concorsi.codice_ente = :codice_ente OR b_concorsi.codice_gestore = :codice_ente)
								AND (b_concorsi.pubblica = '2' OR b_concorsi.pubblica = '1') ORDER BY b_integrazioni_concorsi.codice_fase, b_integrazioni_concorsi.timestamp DESC";

		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
		?>
			<h1>INTEGRAZIONI GARA</h1>
			<table width="100%">
				<thead>
				<tr>
					<td>Tipologia</td>
					<td>Oggetto</td>
					<td>Termini</td>
					<td>Risposta</td>
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
							<?= ($integrazione["fase"] != "") ? "<br>".$integrazione["fase"]:""; ?>
						</td>
						<td width="150">
							Scadenza: <strong><?= mysql2datetime($integrazione["data_scadenza"]) ?></strong>
							<? if ($integrazione["data_apertura"] > 0) { ?><br>Apertura:<br><strong><?= mysql2datetime($integrazione["data_apertura"]) ?></strong><? } ?>
						</td>
						<td width="150">
							<? if ($integrazione["timestamp_trasmissione"] > 0) { ?>
								<?= mysql2datetime($integrazione["timestamp_trasmissione"]) ?>
							<? } ?>
						</td>
						<td width="10">
							<a href="/concorsi/integrazioni/modulo.php?cod=<? echo $integrazione["codice_integrazione"] ?>" title="Dettagli" class="btn-round btn-warning"><span class="fa fa-search"></span></span></a>
						</td>
					</tr>

			<?
			}
			?>
			</table>
			<?
		} else {
			echo "<h1>Nessuna integrazione richiesta</h1>";
		}
	} else {
		echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
	}
include_once($root."/layout/bottom.php");
?>
