<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
				$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);

					  $bind = array();
					  $bind[":codice_gara"] = $record["codice"];
					$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara AND attiva = 'S' ORDER BY codice DESC LIMIT 0,1";
				  $ris = $pdo->bindAndExec($sql,$bind);
				  if ($ris->rowCount() > 0) {
				    $fase = $ris->fetch(PDO::FETCH_ASSOC);
				?>
				<h1>SCADENZE<br><small>Fase: <?= $fase["oggetto"] ?></small></h1>
        <? if (!$lock) { ?>
					<form name="box" method="post" action="save.php" rel="validate">
						<input type="hidden" name="codice" value="<? echo $fase["codice"]; ?>">
						<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
				<? } ?>
				<table width="100%" id="date">
				<tr>
					<td class="etichetta">Termine richieste chiarimenti</td>    <td><input type="text" inline="true" class="datetimepick" title="Termine richieste chiarimenti"  name="chiarimenti" id="chiarimenti" value="<? echo mysql2datetime($fase["chiarimenti"]); ?>" rel="S;16;16;DT">
					</td>
					<td class="etichetta">Termine ricevimento offerte</td>
					<td>
						<input type="text" class="datetimepick" title="Termine ricevimento offerte"  name="scadenza" id="scadenza" value="<? echo mysql2datetime($fase["scadenza"]) ?>" rel="S;16;16;DT;chiarimenti;>">
					</td>
					<td class="etichetta">Apertura offerte</td>
					<td>
						<input type="text" class="datetimepick" title="Apertura offerte"  name="apertura" id="apertura" value="<? echo mysql2datetime($fase["apertura"]) ?>" rel="S;16;16;DT;scadenza;>">
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
			 include($root."/concorsi/ritorna.php"); ?>
<?
			} else {

				echo "<h1>Fase non trovata</h1>";

				}
			} else {

				echo "<h1>Concorso non trovato</h1>";

				}
			} else {

				echo "<h1>Concorso non trovato</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
