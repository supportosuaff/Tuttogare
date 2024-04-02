<?
include_once("../../../config.php");
include_once($root . "/layout/top.php");
$edit = false;
$lock = true;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase($_SERVER['QUERY_STRING'], "/gare/sorteggio/edit.php");
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
	$risultato = $pdo->bindAndExec($strsql, $bind);

	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		if ($record["procedura"] != 7) {
			$numero_sorteggio = $record["numero_sorteggio"];
			$data_sorteggio = $record["data_sorteggio"];
			?>
			<h1>RISULTATI SORTEGGIO</h1>
			<?
				$bind = array();
				$bind[":codice"] = $record["codice"];

				$sql_lotti = "SELECT b_lotti.* FROM b_lotti WHERE b_lotti.codice_gara = :codice";
				$sql_lotti .= " ORDER BY codice";
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
							$numero_sorteggio = $lotto["numero_sorteggio"];
							$data_sorteggio = $lotto["data_sorteggio"];
							echo "	<h2>" . $lotto["oggetto"] . "</h2>";
						}
					} 
				} else {
					$print_form = true;
					$codice_lotto = 0;
				}
				if ($print_form) {
					$bind = array();
					$bind[":codice"] = $record["codice"];
					$bind[":codice_lotto"] = $codice_lotto;
					$sql = "SELECT * FROM r_partecipanti WHERE primo = 'S' AND codice_gara = :codice AND codice_lotto = :codice_lotto";
					$ris = $pdo->bindAndExec($sql, $bind);
					$ris_sorteggio = $pdo->go("SELECT * FROM b_sorteggi WHERE codice_gara = :codice AND codice_lotto = :codice_lotto",$bind);
					if ($ris->rowCount() > 1 || $ris_sorteggio->rowCount() > 0 || $numero_sorteggio != "") { ?>
						<? if (!$lock) { ?>
							<form name="box" method="post" action="save.php" rel="validate">
								<input type="hidden" name="codice" value="<? echo $record["codice"]; ?>">
								<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
						<? } ?>
						<table width="100%" id="date">
							<tr>
								<td class="etichetta">Atto di approvazione</td>
								<td>
									<input type="text" title="Atto" style="width:99%;" name="numero_sorteggio" id="numero_sorteggio" value="<? echo $numero_sorteggio ?>" rel="S;3;255;A">
								</td>
								<td class="etichetta">Data atto</td>
								<td>
									<input type="text" class="datepick" title="Data sorteggio" name="data_sorteggio" id="data_sorteggio" value="<? echo mysql2date($data_sorteggio) ?>" rel="S;10;10;D">
								</td>
							</tr>
						</table>
						<? if ($ris->rowCount() > 1) { ?>
							<table width="100%" title="Partecipanti" class="valida" rel="S;0;0;checked;group_validate">
								<thead>
									<tr>
										<td>Protocollo</td>
										<td>Partita IVA</td>
										<td>Ragione Sociale</td>
										<td>Primo</td>
										<td>Secondo</td>
									</tr>
								</thead>
								<tbody>
									<? while ($record_partecipante = $ris->fetch(PDO::FETCH_ASSOC)) { ?>
										<tr title="<? echo $record_partecipante["ragione_sociale"] ?>" id="<? echo $record_partecipante["codice"] ?>" class="valida" rel="N;0;0;checked;group_one">
											<td width="200">
												<strong> <? echo $record_partecipante["numero_protocollo"] ?></strong> del <? echo mysql2date($record_partecipante["data_protocollo"]) ?></td>
											<td width="10">
												<strong> <? echo $record_partecipante["partita_iva"] ?></strong>
											</td>
											<td> <? if ($record_partecipante["tipo"] != "") echo "<strong>RAGGRUPPAMENTO</strong> - " ?> <? echo $record_partecipante["ragione_sociale"] ?></td>
											<td width="10" align="center">
												<input type="radio" name="primo" value="<? echo $record_partecipante["codice"] ?>" id="primo_partecipante_<? echo $record_partecipante["codice"] ?>"></td>
											<td width="10" align="center">
												<input type="radio" name="secondo" value="<? echo $record_partecipante["codice"] ?>" va id="secondo_partecipante_<? echo $record_partecipante["codice"] ?>"></td>
										</tr>
									<? } ?>
								</tbody>
							</table>
						<? } else if ($ris_sorteggio->rowCount()>0) {
						$codice_gara = $record["codice"];
						include("report.php");
						}
						if (!$lock) { ?>
						<input type="submit" class="submit_big" value="Salva">
					</form>
					<? }
				} else {?>
					<h1>Sorteggio non necessario</h1>
					<?
				}
				if ($lock) { ?>
				<script>
					$("#date :input").not('.espandi').prop("disabled", true);
				</script>
				<? }
				} else {
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
			} else {
				?>
				<h1>Sorteggio non necessario</h1>
				<?
			}
		include($root . "/gare/ritorna.php"); ?>
<?
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
} else {

	echo "<h1>Gara non trovata</h1>";
}

?>


<?
include_once($root . "/layout/bottom.php");
?>