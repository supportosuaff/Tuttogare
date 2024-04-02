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
				if ($risultato->rowCount()>0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
				?>
				<h1>SCADENZE</h1>
        <? if (!$lock) { ?>
					<form name="box" method="post" action="save.php" rel="validate">
						<input type="hidden" name="codice" value="<? echo $record["codice"]; ?>">
				<? } ?>
				<table width="100%" id="date">
					<tr>
						<td class="etichetta">Termine richieste chiarimenti</td>    <td><input type="text" inline="true" class="datetimepick" title="Termine richieste chiarimenti"  name="data_accesso" id="data_accesso" value="<? echo mysql2datetime($record["data_accesso"]); ?>" rel="S;16;16;DT">
						</td>
						<td class="etichetta">Termine ricevimento offerte</td>
						<td>
							<input type="text" class="datetimepick" title="Termine ricevimento offerte"  name="data_scadenza" id="data_scadenza" value="<? echo mysql2datetime($record["data_scadenza"]) ?>" rel="S;16;16;DT;data_accesso;>">
						</td>
						<td class="etichetta">Apertura offerte</td>
						<td>
							<input type="text" class="datetimepick" title="Apertura offerte"  name="data_apertura" id="data_apertura" value="<? echo mysql2datetime($record["data_apertura"]) ?>" rel="S;16;16;DT;data_scadenza;>">
						</td>
					</tr>
				</table>
<? if (!$lock) { ?>
	<input type="submit" class="submit_big" value="Salva">
    </form>

    <?
}
 if ($lock) { ?>
 <script>
			$("#date :input").not('.espandi').prop("disabled", true);
</script>
		<? }
			 include($root."/gare/ritorna.php"); ?>
<?
			} else {

				echo "<h1>Gara non trovata</h1>";

				}
			} else {

				echo "<h1>Gara non trovata</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
