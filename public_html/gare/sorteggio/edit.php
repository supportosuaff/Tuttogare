<?
include_once("../../../config.php");
include_once($root . "/layout/top.php");
$edit = false;
$lock = true;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase($_SERVER['QUERY_STRING'], $_SERVER['REQUEST_URI']);
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
						echo "<h2>" . $lotto["oggetto"] . "</h2>";
					}
				} else {
					$sorteggi = false;
					while ($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
						$bind = array();
						$bind[":codice"] = $record["codice"];
						$bind[":codice_lotto"] = $lotto["codice"];
						$sql = "SELECT * FROM r_partecipanti WHERE primo = 'S' AND codice_gara = :codice AND codice_lotto = :codice_lotto";
						$ris = $pdo->bindAndExec($sql, $bind);
						if ($ris->rowCount() > 1 || $lotto["numero_sorteggio"] != "") {
							$sorteggi = true;
							?>
							<a class="submit_big" href="edit.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
							<? echo $lotto["oggetto"] ?>
							</a>
							<?
						}
					}
					if (!$sorteggi) { ?>
						<h1>Sorteggio non necessario</h1>
						<?
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
				$bind = array();
				$bind[":codice"] = $record["codice"];
				$bind[":codice_lotto"] = $codice_lotto;
				$risultato_sorteggio = $pdo->go("SELECT * FROM b_sorteggi WHERE codice_gara = :codice AND codice_lotto = :codice_lotto",$bind);
				if ($ris->rowCount() > 1 || $risultato_sorteggio->rowCount() > 0 || $numero_sorteggio != "") {
					$editor_tipo = "avviso_exaequo";

					$bind = array(
						":codice" => $record["codice"],
						":codice_lotto" => $codice_lotto,
						":tipo" => $editor_tipo,
					);
					$strsql = "SELECT * FROM b_documentale WHERE tipo= :tipo AND attivo = 'S' AND sezione = 'gara' AND codice_gara = :codice AND codice_lotto = :codice_lotto";
					$risultato = $pdo->bindAndExec($strsql, $bind);
					$style="";
					if ($risultato->rowCount()>0) $style="background-color:#0C0";
					?>
					<a class="submit_big" style="<?= $style ?>" href="comunicazione.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $codice_lotto ?>">
						Avviso di esecuzione sorteggio
					</a>
					<?
						$bind = array();
						$bind[":codice"] = $record["codice"];
						$bind[":codice_lotto"] = $codice_lotto;
						$style="";
						$risultato = $pdo->go("SELECT * FROM b_sorteggi WHERE codice_gara = :codice AND codice_lotto = :codice_lotto",$bind);
						if ($risultato->rowCount()>0) $style="background-color:#0C0";
					?>
					<a class="submit_big" style="<?= $style ?>"  href="estrai.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $codice_lotto ?>">
						Sorteggio
					</a>
					<?
						$style="";
						if (!empty($numero_sorteggio)) $style="background-color:#0C0";
					?>
					<a class="submit_big" style="<?= $style ?>"  href="result.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $codice_lotto ?>">
						Risultati
					</a>
					<?
				} else {
					?>
					<h1>Sorteggio non necessario</h1>
				<?
				}
			}
		} else { ?>
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