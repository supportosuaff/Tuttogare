<?

	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("sda",$_SESSION["codice_utente"]);
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
			$strsql = "SELECT * FROM b_bandi_sda WHERE codice = :codice_bando ";
			$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			if ($_SESSION["gerarchia"] > 0) {
				$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
				$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$bando = $risultato->fetch(PDO::FETCH_ASSOC);
				if (!empty($_SESSION["exportElencoPartecipanti"]["sda"][$bando["codice"]][$key])) {
					$data = $_SESSION["exportElencoPartecipanti"]["sda"][$bando["codice"]][$key];
					ob_start();
					?>
					<html>
						<style>

							body { font-size:10px; } table.elenco td, th { padding:5px; border: 1px solid #ccc}
							th { background-color: #CCC }
							tr.odd { background-color: #DDD; }
							table { width:100% }
						</style>
						<body>
							<table width="100%">
								<tr>
									<td width="150">
										<img src="<?= $config["link_sito"] ?>/documenti/enti/<?= $_SESSION["ente"]["logo"] ?>" width="150">
									</td>
									<td>
										<h1><?= $_SESSION["ente"]["denominazione"] ?></h1>
										<strong><?= $_SESSION["ente"]["indirizzo"] ?> - <?= $_SESSION["ente"]["citta"] ?> (<?= $_SESSION["ente"]["provincia"] ?>)</strong><br>
										<h2 style="margin:5px;padding:0px"><?= $bando["oggetto"] ?></h2>
										<h3><?= ucfirst(str_replace('_', ' ', $key)) ?></h3>
									</td>
								</tr>
							</table>
							<table width="100%" class="elenco">
								<thead>
									<tr>
										<th width="100">Codice Fiscale Impresa</th>
										<th>Ragione sociale</th>
										<th width="100">Data richiesta</th>
										<? if ($key != "nuove_istanze") { ?>
											<th width="100">Data Aggiornamento</th>
											<th width="100">Data abilitazione</th>
											<? if (!empty($bando["periodo_revisione"])) { ?>
												<th width="100">Data Scadenza</th>
											<? } ?>
										<? } ?>
									</tr>
								</thead>
								<tbody>
									<?
										foreach ($data AS $record_partecipante) {
									?>
										<tr <? if ($record_partecipante["visto"]=="N") echo "style='font-weight:bold'"; ?>>
											<td><?= (!empty($record_partecipante["partita_iva"])) ? $record_partecipante["partita_iva"] : $record_partecipante["codice_fiscale_impresa"] ?></td>
											<td><? echo strtoupper($record_partecipante["ragione_sociale"]) ?></td>
											<td><? echo mysql2datetime($record_partecipante["ricezione"]) ?></td>
											<? if ($key != "nuove_istanze") { ?>
												<td><? echo mysql2datetime($record_partecipante["aggiornamento"]) ?></td>
												<td><? echo mysql2datetime($record_partecipante["abilitazione"]) ?></td>
												<? if (!empty($bando["periodo_revisione"])) { ?>
													<td style="text-align:center"><?= $record_partecipante["data_scadenza"] ?>
														<? if ($key != "respinti") { ?><br><? echo $record_partecipante["gg_scadenza"] ?> gg<? } ?></td>
												<? } ?>
											<? } ?>
										</tr>
									<?
										}
									?>
								</tbody>
							</table>
						</body>
					</html>
					<?
					$html = ob_get_clean();
					require_once("{$root}/dompdf/autoload.inc.php");
					$options = new Dompdf\Options();
					$options->set('defaultFont', 'Helvetica');
					$options->set('isRemoteEnabled', true);
					$dompdf = new Dompdf\Dompdf($options);
					$dompdf->loadHtml($html);
					$dompdf->setPaper('A3', 'landscape');
					$dompdf->render();
					$dompdf->stream('Elenco.pdf',["Attachment"=>0]);
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
