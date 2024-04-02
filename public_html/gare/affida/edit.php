<?
include_once("../../../config.php");
include_once($root."/layout/top.php");
$edit = false;
$lock = true;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
		if ($codice_fase!==false) {
			$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
	$bind[":codice"]=$codice;
	$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
	}
	$risultato = $pdo->bindAndExec($strsql,$bind);

	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);

		?><h1>AFFIDAMENTO</h1><?

		$bind = array();
		$bind[":codice"]=$record["codice"];

		$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice ";
		$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);

		if ($ris_r_partecipanti->rowCount()>0)
		{
			$art80 = (check_permessi("verifica-art-80", $_SESSION["codice_utente"])) ? true : false;
			if (!$lock) { ?>
				<form name="box" method="post" action="save.php" rel="validate">
					<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
					<div class="comandi">
						<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
					</div>
			<? } ?>

			<div class="box">
				<h2>Estremi affidamento</h2>
				<table width="100%">
					<tr>
						<td class="etichetta">
							Importo affidamento
						</td>
						<td>
							<input type="text" name="gara[importoAggiudicazione]" value="<?= $record["importoAggiudicazione"] ?>" title="Importo affidamento" rel="S;1;0;N;0;>">
						</td>
						<td class="etichetta">Numero atto</td><td><input type="text" name="gara[numero_atto_esito]" id="numero_atto_esito" value="<? echo $record["numero_atto_esito"] ?>" title="Numero atto" rel="N;1;50;A"></td>
						<td class="etichetta">Data atto</td><td><input type="text" class="datepick" name="gara[data_atto_esito]" id="data_atto_esito" value="<? echo mysql2date($record["data_atto_esito"]) ?>" title="Data atto" rel="N;10;10;D"></td>
					</tr>
				</table>
			</div>

			<table width="100%">
					<thead>
						<tr>
							<td></td>
							<td>Codice Fiscale Impresa</td>
							<td>Ragione Sociale</td>
							<td>Aggiudicatario</td>
						</tr>
					</thead>
					<tbody>
						<?
						while ($record_partecipante = $ris_r_partecipanti->fetch(PDO::FETCH_ASSOC))
						{
							include("tr_partecipante.php");
						}
						?>
					</tbody>
				</table>
				<? if (!$lock) { ?>
					<input type="submit" class="submit_big" value="Salva">
			</form>
			<? } else { ?>
			<script>
				$(":input").not('.espandi').prop("disabled", true);
			</script>
			<?
		}
		if (!empty($record["importoAggiudicazione"])) {
		?>
		<a class="submit_big btn-danger" href="report.php?codice=<?= $record["codice"] ?>" title="Report" target="_blank"><span class="fa fa-file"></span> Report</a>
		<?
		}
		} else {
			echo "<h1>ATTENZIONE</h1>";
			echo "<h3>Non sono presenti partecipanti</h3>";
		}
		include($root."/gare/ritorna.php");
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
} else {
	echo "<h1>Gara non trovata</h1>";
}
include_once($root."/layout/bottom.php");
?>
