<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
	if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
		if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			if ($_GET["codice"] == 0) {
				$edit = check_permessi("concorsi",$_SESSION["codice_utente"]);
			} else {
				$codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
				if ($codice_fase!==false) {
					$esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
					$edit = $esito["permesso"];
					$lock = $esito["lock"];
				}
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
		$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
		$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		if ($_SESSION["gerarchia"] > 0) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
		}
		$risultato = $pdo->bindAndExec($strsql,$bind);

		if ($risultato->rowCount() > 0) {
			$record = $risultato->fetch(PDO::FETCH_ASSOC);
			$bind = array();
			$bind[":codice"] = $record["codice"];

			$string_cpv = "";
			$cpv = array();
			$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_concorsi ON b_cpv.codice = r_cpv_concorsi.codice WHERE r_cpv_concorsi.codice_gara = :codice ORDER BY codice";
			$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
			if ($risultato_cpv->rowCount()>0) {
				$risultato_cpv = $risultato_cpv->fetchAll(PDO::FETCH_ASSOC);
				foreach ($risultato_cpv AS $rec_cpv) {
					$cpv[] = $rec_cpv["codice"];
				}
				$string_cpv = implode(";",$cpv);
			}
			$operazione = "UPDATE";
		} else {
			$lock = false;
			$record = get_campi("b_concorsi");
			$operazione = "INSERT";
			$string_cpv = "";
			$record["prezzoBase"] = 0;
		}
?>
		<h1>INSERIMENTO PRELIMINARE</h1>
		<form name="box" method="post" action="save.php" rel="validate">
			<input type="hidden" name="codice" value="<? echo $codice; ?>">
			<input type="hidden" name="operazione" value="<? echo $operazione ?>">
			<div class="comandi">
				<button class='btn-round btn-primary' title="Salva" onclick="return check_modalita();"><span class="fa fa-floppy-o"></span></button>
			</div>
			<div id="tabs">
				<ul>
					<li><a href="#generali">Dati generali</a></li>
					<li><a href="#descrizione">Descrizione</a></li>
					<li><a href="#categorie">Categorie merceologiche</a></li>
				</ul>
				<div id="generali">
					<table width="100%">
						<tr>
							<td class="etichetta">PEC invio comunicazioni</td>
							<td colspan="3">
								<?
									$bind = array();
									$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
									$sql_pec = "SELECT * FROM b_pec WHERE codice_ente = :codice_ente AND attivo = 'S'";
									$ris_pec = $pdo->bindAndExec($sql_pec,$bind);
									if ($ris_pec->rowCount() > 0) { ?>
									<select name="gara[codice_pec]" id="codice_pec" rel="S;0;0;N" class="espandi" title="PEC">
										<option value="0"><? echo $_SESSION["ente"]["pec"] ?> - Predefinito</option>
										<? while ($indirizzo_pec = $ris_pec->fetch(PDO::FETCH_ASSOC)) { ?>
											<option value="<? echo $indirizzo_pec["codice"] ?>"><? echo $indirizzo_pec["pec"] ?></option>
										<? } ?>
									</select>
								<? } ?>
							</td>
						</tr>
						<tr>
							<td class="etichetta">CIG</td><td><input class="titolo_edit" type="text" id="cig" name="gara[cig]" title="CIG" value="<? echo $record["cig"] ?>" rel="N;10;10;A"></td>
							<td class="etichetta">CUP</td><td><input class="titolo_edit" type="text" id="cup" name="gara[cup]" title="CUP" value="<? echo $record["cup"] ?>" rel="N;15;15;A"></td>
						</tr>
						<tr>
							<td class="etichetta">Codice NUTS</td>
							<td <?	if ($_SESSION["ente"]["numerazione"] != "solare") echo "colspan='3'" ?>>
								<select class="espandi" id="nuts" name="gara[nuts]" title="Codice NUTS" rel="S;3;6;A">
									<option value="">Seleziona...</option>
									<?
									$sql_nuts = "SELECT * FROM b_nuts ORDER BY descrizione";
									$ris_nuts = $pdo->query($sql_nuts);
									if ($ris_nuts->rowCount() > 0) {
										while ($nuts = $ris_nuts->fetch(PDO::FETCH_ASSOC)) {
											?><option value="<?= $nuts["nuts"] ?>"><?= $nuts["nuts"] ?> - <?= $nuts["descrizione"] ?> <? if (!empty($nuts["data_fine_validita"])) { ?> - scadenza: <?= mysql2date($nuts["data_fine_validita"]) ?><? } ?></option><?
										}
									}
									?>
								</select>
								<script> $("#nuts").val("<? echo $record["nuts"] ?>");</script>
							</td>
							<?	if ($_SESSION["ente"]["numerazione"] == "solare") { ?>
								<td class="etichetta">Anno di riferimento</td>
								<td><input class="espandi" type="text" id="anno" name="gara[anno]" title="Anno di riferimento" value="<? echo $record["anno"] ?>" rel="S;4;4;N"></td>
							<? } ?>
						</tr>
						<tr>
							<td class="etichetta">Provvedimento di indizione</td>
							<td><input type="text" class="espandi" id="Provvedimento indizione" name="gara[numero_atto_indizione]" title="Provvedimento di indizione" value="<? echo $record["numero_atto_indizione"] ?>" rel="N;1;255;A"></td>
							<td class="etichetta">Data atto di indizione</td>
							<td><input type="text" class="espandi datepick" id="data_atto_indizione" name="gara[data_atto_indizione]" title="Data atto di indizione" value="<? echo mysql2date($record["data_atto_indizione"]) ?>" rel="N;10;10;D"></td>
						</tr>
						<tr>
							<td class="etichetta">Oggetto</td>
							<td colspan="3">
								<textarea name="gara[oggetto]" id="oggetto" title="Oggetto" rel="S;3;0;A" class="ckeditor_simple espandi"><? echo $record["oggetto"] ?></textarea>
							</td>
						</tr>
						<?
						$bind = array();
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql  = "SELECT * FROM b_enti WHERE ((codice = :codice_ente) ";
						$sql .= " OR (sua = :codice_ente)) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql .= " AND ((codice = :codice_ente_utente) ";
							$sql .= " OR (sua = :codice_ente_utente))";
						}
						$sql .= "ORDER BY denominazione ";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>1 ) {
						?>
							<tr>
								<td class="etichetta">Ente beneficiario</td>
								<td colspan="3">
								<select class="espandi" name="gara[codice_ente]" id="codice_ente" rel="S;0;0;N" title="Ente appaltatore">
									<?
									while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option value="<? echo $rec["codice"] ?>"><? echo $rec["denominazione"] ?></option><?
									}
									?>
								</select>
								<script>
									$("#codice_ente").val("<? echo $record["codice_ente"] ?>");
								</script>
							</tr>
						<?
						} else {
							?>
							<input type="hidden" name="gara[codice_ente]" id="codice_ente" rel="S;0;0;N" title="Ente Beneficiario" value="<?= (empty($_SESSION["record_utente"]["codice_ente"])) ? $_SESSION["ente"]["codice"] : $_SESSION["record_utente"]["codice_ente"] ?>">
							<?
						}
						?>
						<input type="hidden" name="gara[codice_gestore]" id="codice_gestore" rel="S;0;0;N" title="Stazione appaltante" value="<? echo $_SESSION["ente"]["codice"] ?>">
						<tr>
							<td class="etichetta">Premio</td>
							<td><input type="text" name="gara[premio]" id="premio" title="Premio" value="<? echo $record["premio"] ?>" rel="S;0;0;N"></td>
						</tr>
						<tr>
							<td class="etichetta">Struttura proponente</td>
							<td><input style="width:95%" type="text" class="espandi" id="struttura_proponente" name="gara[struttura_proponente]" title="Struttura proponente" value="<? echo $record["struttura_proponente"] ?>" rel="S;3;255;A"></td>
							<td class="etichetta">Responsabile della struttura</td>
							<td><input style="width:95%" type="text" class="espandi" id="responsabile_struttura" name="gara[responsabile_struttura]" title="Responsabile del servizio" value="<? echo $record["responsabile_struttura"] ?>" rel="S;3;255;A"></td>
						</tr>
					</table>

					<div class="clear"></div>
					<a class="precedente" style="float:left" href="#">Step precedente</a>
					<a class="successivo" style="float:right" href="#">Step successivo</a>
					<div class="clear"></div>
				</div>
				<div id="descrizione">
					<textarea name="gara[descrizione]" id="descrizione" title="Descrizione" class="ckeditor_full espandi" rel="S;3;0;A">
						<? echo $record["descrizione"] ?>
					</textarea>
					<div class="clear"></div>
					<a class="precedente" style="float:left" href="#">Step precedente</a>
					<a class="successivo" style="float:right" href="#">Step successivo</a>
					<div class="clear"></div>
				</div>
				<div id="categorie">
					<? include("categorie/form.php"); ?>
					<div class="clear"></div>

					<a class="precedente" style="float:left" href="#">Step precedente</a>
					<a class="successivo" style="float:right" href="#">Step successivo</a>
					<div class="clear"></div>

				</div>
			</div>
			<div class="clear"></div>
			<input type="submit" class="submit_big espandi" value="Salva" onclick="return check_modalita();">
		</form>
		<? if ($operazione == "UPDATE") { include($root."/concorsi/ritorna.php"); } ?>
		<script>

					$("#codice_pec").val("<? echo $record["codice_pec"] ?>");

					$("#tabs").tabs();

					<? if ($lock) { ?>
					$("#tabs :input").not('.espandi').prop("disabled", true);
					<? } ?>
					$(".precedente").each(function() {
					var id_parent = $("#tabs").children("div").index($(this).parent("div"));
					if (id_parent == 0) {
					$(this).remove();
					} else {
					var target = id_parent - 1;
					$(this).click(function() { $('#tabs').tabs('option','active',target) });
					}
					});

					$(".successivo").each(function() {
					var id_parent = $("#tabs").children("div").index($(this).parent("div"));
					if (id_parent == ($("#tabs").children("div").length - 1)) {
					$(this).remove();
					} else {
					var target = id_parent + 1;
					$(this).click(function() { $('#tabs').tabs('option','active',target) });
					}
					});
					</script>
					<?

					} else {

					echo "<h1>Concorso non trovata</h1>";

					}

					?>


<?
	include_once($root."/layout/bottom.php");
	?>
