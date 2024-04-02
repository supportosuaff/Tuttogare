<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;

	if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
		if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
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
	$bind = array(":codice"=>$codice,":codice_ente"=>$_SESSION["ente"]["codice"]);
	$strsql = "SELECT * FROM b_bandi_albo WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_utente_ente OR codice_gestore = :codice_utente_ente) ";
	}
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		$string_cpv = "";
		$cpv = array();
		$bind=array(":codice_bando"=>$record["codice"]);
		$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_bandi_albo ON b_cpv.codice = r_cpv_bandi_albo.codice WHERE r_cpv_bandi_albo.codice_bando = :codice_bando ORDER BY codice";
		$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
		if ($risultato_cpv->rowCount()>0) {
			$risultato_cpv = $risultato_cpv->fetchAll(PDO::FETCH_ASSOC);
			foreach ($risultato_cpv as $rec_cpv) {
				$cpv[] = $rec_cpv["codice"];
			}
			$string_cpv = implode(";",$cpv);
		}
		$operazione = "UPDATE";
	} else {
		$lock = false;
		$record = get_campi("b_bandi_albo");
		$operazione = "INSERT";
		$string_cpv = "";
	}
	?>
	<h1>INSERIMENTO PRELIMINARE</h1>
	<form name="box" method="post" action="save.php" rel="validate">
		<input type="hidden" name="codice" value="<?= $codice; ?>">
		<input type="hidden" name="operazione" value="<?= $operazione ?>">

		<div class="comandi">
			<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			<? if ($_SESSION["gerarchia"] === "0") { ?><button type="button" onclick="$('#avanzate').slideToggle()" class="btn-round btn-warning" title="Impostazioni avanzate"><span class="fa fa-cogs"></span></button><? } ?>
		</div>
		<? if ($_SESSION["gerarchia"] === "0") { ?>
			<div id="avanzate" style="display:none">
				<div style="padding:10px; text-align:center; background-color:#A00; color: #FFF; font-size:2em">
					<strong>ATTENZIONE: MODIFICARE QUESTO CAMPO POTREBBE PORTARE AD ERRORI DURANTE LA PARTECIPAZIONE DEGLI OPERATORI ECONOMICI</strong>
				</div>
				<button type="button" onclick="$('#textAreaJSON').slideToggle()" class="submit-big btn-warning" title="Visualizza JSON">Visualizza JSON</button>
				<textarea style="font-weight:bold; width:100%; display:none" id="textAreaJSON" rows="50" name="bando[jsonQuestionario]" title="questionario"><?= $record["jsonQuestionario"] ?></textarea>
			</div>
		<? } ?>
		<div id="tabs">
			<ul>
				<li><a href="#generali">Dati generali</a></li>
				<li><a href="#descrizione">Descrizione</a></li>
				<li><a href="#categorie">Categorie merceologiche</a></li>
			</ul>

			<div id="generali">
				<table width="100%" style="table-layout: fixed;">
					<tr>
						<td class="etichetta">PEC invio comunicazioni</td>
						<td colspan="3">
							<?
							$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
							$sql_pec = "SELECT * FROM b_pec WHERE codice_ente = :codice_ente AND attivo = 'S'";
							$ris_pec = $pdo->bindAndExec($sql_pec,$bind);
							if ($ris_pec->rowCount() > 0) {
								?>
								<select name="bando[codice_pec]" id="codice_pec" rel="S;0;0;N" title="PEC">
									<option value="0"><?= $_SESSION["ente"]["pec"] ?> - Predefinito</option>
									<?
									while ($indirizzo_pec = $ris_pec->fetch(PDO::FETCH_ASSOC)) {
										?><option value="<?= $indirizzo_pec["codice"] ?>"><?= $indirizzo_pec["pec"] ?></option><?
									}
									?>
								</select>
								<?
							}
							?>
						</td>
					</tr>
					<tr>
						<td class="etichetta" width="25%">Provvedimento di indizione</td>
						<td width="25%"><input type="text" id="Provvedimento indizione" name="bando[numero_atto_indizione]" title="Provvedimento di indizione" value="<?= $record["numero_atto_indizione"] ?>" rel="N;1;255;A"></td>
						<td class="etichetta" width="25%">Data atto di indizione</td>
						<td width="25%"><input type="text" class="datepick_today" id="data_atto_indizione" name="bando[data_atto_indizione]" title="Data atto di indizione" value="<?= mysql2date($record["data_atto_indizione"]) ?>" rel="N;10;10;D"></td>
					</tr>
					<tr>
						<td class="etichetta">Data scadenza</td>
						<td><input type="text" class="datetimepick" id="data_scadenza" name="bando[data_scadenza]" title="Data scadenza" value="<?= mysql2datetime($record["data_scadenza"]) ?>" rel="N;16;16;DT"></td>
						<td class="etichetta">Revisione (mesi)</td>
						<td><input type="text" id="periodo_revisione" name="bando[periodo_revisione]" title="Revisione" value="<?= $record["periodo_revisione"] ?>" rel="S;0;2;N"></td>
					</tr>
					<tr>
						<td class="etichetta">Tipologia</td>
						<td>
							<select name="bando[tipologia]" id="tipologia" rel="S;1;1;A" title="Tipologia">
								<option value="">Seleziona...</option>
								<option value="F">Albo di fornitori</option>
								<option value="P">Albo di professionisti</option>
								<option value="M">Manifestazione d'interesse / Indagine di mercato</option>
							</select>
							<script>
								$('#tipologia').val('<?= $record["tipologia"] ?>');
							</script>
						</td>
						<td class="etichetta">Si intende richiedere un profilo completo agli OO.EE.?</td>
						<td>
							<select name="bando[dati_minimi_profilo]" id="dati_minimi_profilo" rel="S;1;1;A" title="Manifestazione d'interesse">
								<option value="">Seleziona...</option>
								<option value="S">Si</option>
								<option value="N">No</option>
							</select>
							<script>
								$('#dati_minimi_profilo').val('<?= $record["dati_minimi_profilo"] ?>');
							</script>
						</td>
					</tr>
					<tr>
						<td class="etichetta">Oggetto</td>
						<td colspan="3">

							<input type="hidden" name="bando[codice_gestore]" id="codice_gestore" rel="S;0;0;N" value="<?= $_SESSION["ente"]["codice"] ?>">
							<textarea name="bando[oggetto]" id="oggetto" title="Oggetto" rel="S;3;0;A" class="ckeditor_simple"><? echo $record["oggetto"] ?></textarea>
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
								<td class="etichetta">Ente committente</td>
								<td colspan="3">
									<select class="espandi" name="bando[codice_ente]" id="codice_ente" rel="S;0;0;N" title="Ente committente">
										<?
										while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
											?><option value="<?= $rec["codice"] ?>"<? if ($rec["codice"] == $record["codice_ente"]) echo "selected" ?>><?= $rec["denominazione"] ?></option><?
										}
										?>
									</select>
							</tr>
							<?
						} else {
							?>
							<input type="hidden" name="bando[codice_ente]" id="codice_ente" rel="S;0;0;N" title="Ente Beneficiario" value="<?= (empty($_SESSION["record_utente"]["codice_ente"])) ? $_SESSION["ente"]["codice"] : $_SESSION["record_utente"]["codice_ente"] ?>">
							<?
						}
					?>
					<tr>
						<td class="etichetta">Servizio</td>
						<td colspan="3"><input style="width:95%" type="text" id="struttura_proponente" name="bando[struttura_proponente]" title="Struttura proponente" value="<?= $record["struttura_proponente"] ?>" rel="S;3;255;A"></td>
					</tr>
					<tr>
						<td class="etichetta">R.U.P.</td>
						<td><input style="width:95%" type="text" id="rup" name="bando[rup]" title="RUP" value="<?= $record["rup"] ?>" rel="S;3;255;A"></td>
						<td class="etichetta">Responsabile del servizio</td>
						<td><input style="width:95%" type="text" id="responsabile_struttura" name="bando[responsabile_struttura]" title="Responsabile del servizio" value="<?= $record["responsabile_struttura"] ?>" rel="S;3;255;A"></td>
					</tr>
				</table>
				<div class="clear"></div>
				<a class="precedente" style="float:left" href="#">Step precedente</a>
				<a class="successivo" style="float:right" href="#">Step successivo</a>
				<div class="clear"></div>
			</div>
			<div id="descrizione">
				<textarea name="bando[descrizione]" id="descrizione" title="Descrizione" class="ckeditor_full" rel="S;3;0;A"><? echo $record["descrizione"] ?></textarea>
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
		<input type="submit" class="submit_big" value="Salva">
	</form>
	<script>
		$("#codice_pec").val("<?= $record["codice_pec"] ?>");
		$("#tabs").tabs();

		$(".precedente").each(function () {
			var id_parent = $("#tabs").children("div").index($(this).parent("div"));
			if (id_parent == 0) {
				$(this).remove();
			} else {
				var target = id_parent - 1;
				$(this).click(function () {
					$('#tabs').tabs('option', 'active', target)
				});
			}
		});

		$(".successivo").each(function () {
			var id_parent = $("#tabs").children("div").index($(this).parent("div"));
			if (id_parent == ($("#tabs").children("div").length - 1)) {
				$(this).remove();
			} else {
				var target = id_parent + 1;
				$(this).click(function () {
					$('#tabs').tabs('option', 'active', target)
				});
			}
		});
	</script>
	<?
	include_once($root."/layout/bottom.php");
?>
