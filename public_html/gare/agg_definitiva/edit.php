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

				$strsql = "SELECT b_gare.*, b_criteri.directory FROM b_gare JOIN b_criteri ON b_gare.criterio = b_criteri.codice
									 WHERE b_gare.codice = :codice AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= " AND (b_gare.codice_ente = :codice_ente_utente OR b_gare.codice_gestore = :codice_ente_utente) ";
				}

				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					if ($record["allegati_esito"] != "" && preg_match("/^[0-9\;]+$/",$record["allegati_esito"])) {
								$allegati = explode(";",$record["allegati_esito"]);
								$str_allegati = ltrim(implode(",",$allegati),",");
								$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ")";
								$ris_allegati = $pdo->query($sql);
					}
					$operazione = "UPDATE";

					$bind = array();
					$bind[":codice"]=$codice;
					$sql = "SELECT r_partecipanti.codice FROM r_partecipanti
									WHERE r_partecipanti.primo = 'S' AND r_partecipanti.codice_gara = :codice ";
					$ris_aggiudicatario = $pdo->bindAndExec($sql,$bind);
					if ($ris_aggiudicatario->rowCount()>0) {
						$aggiudicatario = $ris_aggiudicatario->fetch(PDO::FETCH_ASSOC);
						$bind = array();
						$bind[":codice_gara"] = $codice;
						$sql_tipo = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 40)";
						$ris_tipo = $pdo->bindAndExec($sql_tipo,$bind);
						$rialzo = false;
						if ($ris_tipo->rowCount() > 0) {
							$opzione = $ris_tipo->fetch(PDO::FETCH_ASSOC);
							if ($opzione["opzione"] == "270") $rialzo = true;
						}
?>
				<h1>AGGIUDICAZIONE DEFINITIVA</h1>

				<?
				if (!$lock) {
					?>
					<form name="box" method="post" action="save.php" rel="validate">
						<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
						<div class="comandi">
							<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
						</div>
					<?
				}
				$bind = array();
				$bind[":codice"] = $record["codice"];
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice AND deserta = 'N' ORDER BY codice";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				if ($ris_lotti->rowCount()>0) {
					while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
						if (empty($lotto["ribasso"]) && empty($lotto["importoAggiudicazione"])) {
							$ribasso = getImportoAggiudicazione($record["codice"],$lotto["codice"]);
							if (!empty($ribasso)) {
								$lotto["ribasso"] = $ribasso["percentuale"];
								$lotto["importoAggiudicazione"] = $ribasso["importo"];
							}
						}
						?>
						<table width="100%">
							<tr>
								<td colspan="4" class="etichetta">Estremi dell'atto - <b><?= $lotto["oggetto"] ?></b></td></tr>
							<tr>
							<tr>
								<td class="etichetta">Numero</td>
								<td><input type="text" name="lotto[<?= $lotto["codice"] ?>][numero_atto_esito]" id="numero_atto_esito_<?= $lotto["codice"] ?>" value="<?= $lotto["numero_atto_esito"] ?>" title="Numero atto" rel="N;1;50;A"></td>
								<td class="etichetta">Data</td>
								<td><input type="text" class="datepick" name="lotto[<?= $lotto["codice"] ?>][data_atto_esito]" id="data_atto_esito_<?= $lotto["codice"] ?>" value="<?= mysql2date($lotto["data_atto_esito"]) ?>" title="Data atto" rel="N;10;10;D"></td>
							</tr>
							<tr>
								<td class="etichetta"><?= ($rialzo) ? "Rialzo":"Ribasso" ?> finale:</td>
								<td ><input type="text" name="lotto[<?= $lotto["codice"] ?>][ribasso]" id="ribasso_<?= $lotto["codice"] ?>" value="<? echo $lotto["ribasso"] ?>" title="<?= ($rialzo) ? "Rialzo":"Ribasso" ?> Finale" rel="N;1;0;N;100;<="></td>
								<td class="etichetta">Importo aggiudicazione:</td>
								<td ><input type="text" name="lotto[<?= $lotto["codice"] ?>][importoAggiudicazione]" id="ribasso_<?= $lotto["codice"] ?>" value="<? echo $lotto["importoAggiudicazione"] ?>" title="Importo aggiudicazione" rel="N;1;0;N;"></td>
							</tr>
						</table>
						<br>
						<?
					}
				} else {
					if (empty($record["ribasso"]) && empty($record["importoAggiudicazione"])) {
						$ribasso = getImportoAggiudicazione($record["codice"]);
						if (!empty($ribasso)) {
							$record["ribasso"] = $ribasso["percentuale"];
							$record["importoAggiudicazione"] = $ribasso["importo"];
						}
					}

					?>
					<table width="100%">
						<tr>
							<td colspan="4"><strong>Estremi dell'atto</strong></td></tr>
						<tr>
							<td class="etichetta">Numero</td><td><input type="text" name="numero_atto_esito" id="numero_atto_esito" value="<? echo $record["numero_atto_esito"] ?>" title="Numero atto" rel="N;1;50;A"></td>
							<td class="etichetta">Data</td><td><input type="text" class="datepick" name="data_atto_esito" id="data_atto_esito" value="<? echo mysql2date($record["data_atto_esito"]) ?>" title="Data atto" rel="N;10;10;D"></td>
						</tr>
						<tr>
							<td class="etichetta"><?= ($rialzo) ? "Rialzo":"Ribasso" ?> finale</td>
							<td><input type="text" name="ribasso" id="ribasso" value="<? echo $record["ribasso"] ?>" title="<?= ($rialzo) ? "Rialzo":"Ribasso" ?> Finale" rel="N;1;0;N;100;<="></td>
							<td class="etichetta">Importo aggiudicazione:</td>
							<td ><input type="text" name="importoAggiudicazione" id="importoAggiudicazione" value="<? echo $record["importoAggiudicazione"] ?>" title="Importo aggiudicazione" rel="N;1;0;N"></td>
						</tr>
					</table>
					<br>
					<?
				}
				?>
				<table width="100%">
					<tr>
						<td class="etichetta">Pubblica Avviso</td><td><input type="checkbox" name="avviso"></td>
						<td class="etichetta">Invia PEC</td><td><input type="checkbox" name="pec"></td>
					</tr>
				</table>
				<div id="allegati">
					<?	$cod_allegati = $record["allegati_esito"]; ?>
					<input type="hidden" value="<? echo $cod_allegati ?>" name="allegati_esito" title="Allegati" id="cod_allegati" rel="N;0;0;A">
					<table width="100%" id="tab_allegati">
						<? if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
							while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
								include($root."/allegati/tr_allegati.php");
							}
						} ?>
					</table>
				</div>
				<button onClick="open_allegati();return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
					<img src="/allegati/icon.png" alt="Allega" width="15" style="vertical-align:middle"> Allega file
				</button>
				<?
				if (!$lock) {
					?>
						<input type="submit" class="submit_big" value="Salva">
					</form>
					<?
					$form_upload["codice_gara"] = $_GET["codice"];
					$form_upload["online"] = 'S';
					include($root."/allegati/form_allegati.php");
				} else {
					?>
					<script>$(":input").not('.espandi').prop("disabled", true);</script>
					<?
				}
			} else {
				echo "<h1>Aggiudicazione provvisoria non effettuata</h1>";
			}
			include($root."/gare/ritorna.php");
		} else {
			echo "<h0>Gara non trovata</h1>";
		}

	} else {
		echo "<h1>Gara non trovata</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
