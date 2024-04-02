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
			<h1>SORTEGGIO</h1>
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
					$bind[":codice"] = $codice;
					$bind[":codice_lotto"] = $codice_lotto;
					$risultato = $pdo->go("SELECT * FROM b_sorteggi WHERE codice_gara = :codice AND codice_lotto = :codice_lotto",$bind);
					if ($risultato->rowCount()>0) {
						$codice_gara = $record["codice"];
						include("report.php");
					} else {
						$bind = array();
						$bind[":codice"] = $record["codice"];
						$bind[":codice_lotto"] = $codice_lotto;
						$sql = "SELECT * FROM r_partecipanti WHERE primo = 'S' AND codice_gara = :codice AND codice_lotto = :codice_lotto";
						$ris = $pdo->bindAndExec($sql, $bind);
						if ($ris->rowCount() > 1) { ?>
							<? if (!$lock) { ?>
								<form name="box" method="post" action="sorteggia.php" rel="validate">
									<input type="hidden" name="codice" value="<? echo $record["codice"]; ?>">
									<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
							<? }
								if ($ris->rowCount() > 1) { ?>
								<table width="100%" title="Partecipanti">
									<thead>
										<tr>
											<td>Protocollo</td>
											<td>Partita IVA</td>
											<td>Ragione Sociale</td>
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
											</tr>
										<? } ?>
									</tbody>
								</table>
							<? } ?>
						<? if (!$lock) { ?>
							<input type="submit" class="submit_big" value="Sorteggia">
						</form>
						<? }
					} else {?>
						<h1>Sorteggio non necessario</h1>
						<?
					}
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