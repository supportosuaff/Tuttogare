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
		?>
		<h1>FEEDBACK</h1>
		<?
		if ($risultato->rowCount() > 0) {
			$bind = array();
			$bind[":codice"]=$codice;
			$sql_lotti = "SELECT b_lotti.* FROM b_lotti WHERE b_lotti.codice_gara = :codice";
			$sql_lotti.= " GROUP BY b_lotti.codice ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
			$print_form = false;

			if ($ris_lotti->rowCount()>0) {
				if (isset($_GET["lotto"])) {
					$codice_lotto = $_GET["lotto"];
					$bind = array();
					$bind[":codice_lotto"]=$codice_lotto;
					$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice_lotto ORDER BY codice";
					$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
					if ($ris_lotti->rowCount()>0) {
						$print_form = true;
						$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
						$record_gara["ribasso"] = $lotto["ribasso"];
						echo "<h2>" . $lotto["oggetto"] . "</h2>";
					}
				} else {
					while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
						?>
						<a class="submit_big" href="index.php?codice=<? echo $_GET["codice"] ?>&lotto=
						<? echo $lotto["codice"] ?>">
						<? echo $lotto["oggetto"]; ?>
						</a>
						<?
					}
				}
			} else {
				$print_form = true;
				$codice_lotto = 0;
			}

			if ($print_form) {
				$bind = array();
				$bind[":codice"]=$codice;
				$bind[":codice_lotto"]=$codice_lotto;
				$sql = "SELECT r_partecipanti.codice AS codice_partecipante, r_partecipanti.*, b_operatori_economici.*
								FROM r_partecipanti JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice
								WHERE r_partecipanti.primo = 'S' AND r_partecipanti.codice_gara = :codice AND r_partecipanti.codice_lotto = :codice_lotto GROUP BY r_partecipanti.codice ";
				$ris_aggiudicatario = $pdo->bindAndExec($sql,$bind);
				if ($ris_aggiudicatario->rowCount()>0) {
					$feedback_codice_riferimento = $codice;
					$feedback_dettaglio_riferimento = $codice_lotto;
					$feedback_soggetti = $ris_aggiudicatario->fetchAll(PDO::FETCH_ASSOC);
					$feedback_tipologia = "G";
					?>
					<form action="salva.php" rel="validate">
						<input type="hidden" name="codice_gara" value="<? echo $codice; ?>">
						<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
						
						<?
						include_once($root."/moduli/formFeedback.php");
						?>
						<input type="submit" class="submit_big" value="Salva">
					</form>
					<?
					include($root."/gare/ritorna.php");
				}
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0;URL=/gare/pannello.php?codice='.$codice_gara.'">';
			die();
		}
	}
	else
	{
		echo '<meta http-equiv="refresh" content="0;URL=/gare/pannello.php?codice='.$codice_gara.'">';
		die();
	}
	include_once($root."/layout/bottom.php");
?>
