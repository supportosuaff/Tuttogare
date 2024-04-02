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
		$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
		if ($record_gara["procedura"] != 7) {
			$numero_sorteggio = $record_gara["numero_sorteggio"];
			$data_sorteggio = $record_gara["data_sorteggio"];
			?>
			<h1>SORTEGGIO</h1>
			<?
				$bind = array();
				$bind[":codice"] = $record_gara["codice"];

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
					$bind[":codice"]=$codice;
					$bind[":codice_lotto"]=$codice_lotto;
					$sql = "SELECT r_partecipanti.* FROM r_partecipanti WHERE r_partecipanti.codice_gara = :codice AND r_partecipanti.codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";
					$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
					$bind = array();
					$bind[":codice"]=$codice;
					$bind[":codice_lotto"]=$codice_lotto;
					$sql = "SELECT r_partecipanti.* 
					FROM r_partecipanti 
					WHERE r_partecipanti.primo = 'S' AND r_partecipanti.codice_gara = :codice AND r_partecipanti.codice_lotto = :codice_lotto GROUP BY r_partecipanti.codice ";
					$ris_aggiudicatario = $pdo->bindAndExec($sql,$bind);
					if ($ris_aggiudicatario->rowCount()>0) {
						if (isset($lotto)) {
							$record_gara["oggetto"] .= " - Lotto: " . $lotto["oggetto"];
							$record_gara["soglia_anomalia"] = $lotto["soglia_anomalia"];
							$record_gara["ribasso"] = $lotto["ribasso"];
						}

						$editor_tipo = "avviso_exaequo";

						$bind = array(
							":codice" => $record_gara["codice"],
							":codice_lotto" => $codice_lotto,
							":tipo" => $editor_tipo,
						);

						$strsql = "SELECT * FROM b_documentale WHERE tipo= :tipo AND attivo = 'S' AND sezione = 'gara' AND codice_gara = :codice AND codice_lotto = :codice_lotto";
						$risultato = $pdo->bindAndExec($strsql, $bind);

						if ($risultato->rowCount()>0) {
							$elemento = $risultato->fetch(PDO::FETCH_ASSOC);
							$html = $elemento["corpo"];
							$operazione = "UPDATE";
							$codice_elemento = $elemento["codice"];
						} else {
							$operazione = "INSERT";
							$codice_elemento = 0;
							$bind = array();
							$bind[":codice"] = $record_gara["criterio"];

							$bind = array();
							$bind[":codice_ente"] = $record_gara["codice_ente"];
							$sql = "SELECT * FROM b_enti WHERE codice = :codice_ente";
							$ris_ente = $pdo->bindAndExec($sql,$bind);
							if ($ris_ente->rowCount()>0) $record_appaltatore = $ris_ente->fetch(PDO::FETCH_ASSOC);

							$bind = array();
							$bind[":codice_gestore"] = $record_gara["codice_gestore"];
							$sql = "SELECT * FROM b_enti WHERE codice = :codice_gestore";
							$ris_gestore = $pdo->bindAndExec($sql,$bind);
							if ($ris_gestore->rowCount()>0) $record_gestore = $ris_gestore->fetch(PDO::FETCH_ASSOC);

							$chiavi = array_keys($record_appaltatore);
							foreach($chiavi as $chiave) {
								$vocabolario["#record_appaltatore-".$chiave."#"] = $record_appaltatore[$chiave];
							}
							$vocabolario["#record_appaltatore-logo-path#"] = ! empty($record_appaltatore["logo"]) ? "{$config["link_sito"]}/documenti/enti/{$record_appaltatore["logo"]}" : "{$config["link_sito"]}/img/no_logo.gif";

							$chiavi = array_keys($record_gestore);
							foreach($chiavi as $chiave) {
								$vocabolario["#record_gestore-".$chiave."#"] = $record_gestore[$chiave];
							}
							
							$vocabolario["#record_gestore-logo-path#"] = ! empty($record_gestore["logo"]) ? "{$config["link_sito"]}/documenti/enti/{$record_gestore["logo"]}" : "{$config["link_sito"]}/img/no_logo.gif";

							$record_gara["prezzoBase"] = number_format($record_gara["prezzoBase"],2,",",".");
							$record_gara["data_pubblicazione"] = mysql2date($record_gara["data_pubblicazione"]);

							$chiavi = array_keys($record_gara);
							foreach($chiavi as $chiave) {
								$vocabolario["#record_gara-".$chiave."#"] = $record_gara[$chiave];
							}

							$vincitore = "";
							while($record_agg = $ris_aggiudicatario->fetch(PDO::FETCH_ASSOC)) {
								$vincitore .= "<strong>" .  $record_agg["partita_iva"] . " - " . $record_agg["ragione_sociale"] . "</strong><br>";
							}
							$vocabolario["#vincitore#"] = $vincitore;
							$sql_modello = "SELECT * FROM b_modelli_standard WHERE codice = 28"; // Codice 28 - Comunicazione ex aequo
							$ris_modello = $pdo->query($sql_modello);
							if ($ris_modello->rowCount()>0) {
								$modello = $ris_modello->fetch(PDO::FETCH_ASSOC);
								$bind = array();
								$bind[":codice_modello"] = $modello["codice"];
								$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
								$sql = "SELECT * FROM b_modelli_enti WHERE attivo = 'S' AND codice_modello = :codice_modello AND codice_ente = :codice_ente ";
								$ris = $pdo->bindAndExec($sql,$bind);
								if ($ris->rowCount()>0) {
									$modello = $ris->fetch(PDO::FETCH_ASSOC);
								}
								$html = strtr($modello["corpo"],$vocabolario);
							} else {
								echo "<h1>Modello mancante</h1>";
							}

						}

						?>
						<form name="box" method="post" action="invia.php" rel="validate">
							<input type="hidden" name="operazione" value="<? echo $operazione ?>">
							<input type="hidden" name="codice" value="<? echo $codice_elemento; ?>">
							<input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"]; ?>">
							<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
							<input type="hidden" name="allega" id="allega" value="N">
							<input type="hidden" name="invia" id="invia" value="N">
							<input type="hidden" name="bozza" id="bozza" value="N">
							<div style="float:left; width:65%">
								<?
								$file_title = $editor_tipo;
								include($root."/moduli/editor.php");
								if ($codice_elemento > 0 && $elemento["bozza"] == "N") { ?>
									<input type="button" style="background-color:#C00" class="submit_big" onClick="elimina('<? echo $codice_elemento ?>','gare/sorteggio');" src="/img/del.png" value="Rielabora Comunicazione">
									<?
								} else { ?>
									<? if ($codice_elemento > 0 && $elemento["bozza"] == "S") { ?><input type="button" style="background-color:#C00" class="submit_big" onClick="elimina('<? echo $codice_elemento ?>','gare/avvisoStipula');" src="/img/del.png" value="Rielabora Comunicazione"><? } ?>
									<input class="submit_big" type="submit" onClick="$('#bozza').val('S');" value="Salva una bozza">
									<input class="submit_big" type="submit" onClick="$('#bozza').val('N');$('#invia').val('N')" value="Pubblica avviso">
									<input class="submit_big" type="submit" onClick="$('#bozza').val('N');$('#invia').val('S')" value="Pubblica avviso e invia comunicazione">
								<? } ?>
							</div>
							<div style="float:right; width:34%;">
								<h2>Destinatari</h2>
								<?
								if (count($ris_partecipanti)>0) {
									$messaggio = false;
									foreach($ris_partecipanti AS $partecipante) {
										$class = "";
										$alert = "";
										$tipo = "";
										if ($partecipante["tipo"] != "") $tipo = " - <strong>CAPOGRUPPO</strong>";
										if ($partecipante["codice_utente"] == 0) {
											$messaggio = true;
											$class = " errore";
											$alert = "<img src=\"/img/alert.png\" alt=\"Alert\" style=\"vertical-align:middle\"> ";
										}
										?>
										<div class="box<? echo $class ?>">
										<? echo $alert .  $partecipante["ragione_sociale"] . $tipo ?>
										</div>
										<?
									}
									if ($messaggio) echo "<br><strong><img src=\"/img/alert.png\" alt=\"Alert\" style=\"vertical-align:middle\"> Impossibile inviare la comunicazione a tutti i partecipanti</strong>";
								}
								?>
							</div>
						</form>
						<div class="clear"></div>
						<? if (!$lock) { ?>
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