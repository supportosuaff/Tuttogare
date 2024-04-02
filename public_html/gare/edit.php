<?
include_once("../../config.php");
include_once($root . "/layout/top.php");
$edit = false;
$lock = true;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		if ($_GET["codice"] == 0) {
			$edit = check_permessi("gare/preliminari", $_SESSION["codice_utente"]);
		} else {
			$codice_fase = getFase($_SERVER['QUERY_STRING'], $_SERVER['REQUEST_URI']);
			if ($codice_fase !== false) {
				$esito = check_permessi_gara($codice_fase, $_GET["codice"], $_SESSION["codice_utente"]);
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
	$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
	}
	$risultato = $pdo->bindAndExec($strsql, $bind);

	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		if (!empty($record["id_suaff"])) $lock = true;
		$bind = array();
		$bind[":codice"] = $record["codice"];
		$sql = "SELECT b_importi_gara.*, b_tipologie_importi.titolo FROM b_importi_gara JOIN b_tipologie_importi ON b_importi_gara.codice_tipologia = b_tipologie_importi.codice ";
		$sql .= "WHERE codice_gara = :codice";
		$ris_importi = $pdo->bindAndExec($sql, $bind);
		$string_cpv = "";
		$cpv = array();
		$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice ORDER BY codice";
		$risultato_cpv = $pdo->bindAndExec($strsql, $bind);
		if ($risultato_cpv->rowCount() > 0) {
			$risultato_cpv = $risultato_cpv->fetchAll(PDO::FETCH_ASSOC);
			foreach ($risultato_cpv as $rec_cpv) {
				$cpv[] = $rec_cpv["codice"];
			}
			$string_cpv = implode(";", $cpv);
		}
		$operazione = "UPDATE";
	} else {
		/****************************
		 * LIMITE PROCEDURE DI GARA *
		 ****************************/
		if(! empty($_SESSION["ente"]["tipologia_limite"]) && $_SESSION["ente"]["limite_procedure"] > 0) {
			$procedure = $pdo->go("SELECT COUNT(codice) FROM b_gare WHERE codice_gestore = :codice_gestore", array(':codice_gestore' => ! empty($_SESSION["ente"]["sua"]) ? $_SESSION["ente"]["sua"] : $_SESSION["ente"]["codice"]))->fetch(PDO::FETCH_COLUMN, 0);
			if(($procedure + 1) > $_SESSION["ente"]["limite_procedure"] && $_SESSION["ente"]["tipologia_limite"] == "block") {
				echo '<meta http-equiv="refresh" content="0;URL=/gare/limite.php">';
				die();
			}
		}
		/*********************************
		 * FINE LIMITE PROCEDURE DI GARA *
		 *********************************/
		$lock = false;
		$record = get_campi("b_gare");
		$record["email_chiave"] = (empty($_SESSION["ente"]["email_chiavi"])) ? $_SESSION["record_utente"]["email"] : $_SESSION["ente"]["email_chiavi"];
		$operazione = "INSERT";
		$string_cpv = "";
		$record["modalita_lotti"] = 0;
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
							$ris_pec = $pdo->bindAndExec($sql_pec, $bind);
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
					<td class="etichetta">Normativa di riferimento</td>
					<td colspan="3">
						<select name="gara[norma]" id="norma" rel="S;0;0;A" class="espandi" title="Norma">
							<? foreach(riferimentiNormativi() AS $idRiferimento => $riferimentoNormativo) { ?>
								<option value="<? echo $idRiferimento ?>"><? echo $riferimentoNormativo ?></option>
							<? } ?>
						</select>
					</td>
				</tr>
				<? if (empty($_SESSION["ente"]["email_chiavi"])) { ?>
				<tr>
					<td class="etichetta">E-mail ricezione chiave privata</td>
					<td>
						<? if (empty($record["public_key"])) { ?>
						<input class="espandi" style="width:100%" type="text" id="email_chiave" name="gara[email_chiave]" title="Email chiave" value="<? echo $record["email_chiave"] ?>" rel="N;5;100;E">
						<? } else {
									echo $record["email_chiave"];
								} ?>
					</td>
					<td colspan="2">
						<? if (empty($record["public_key"])) { ?>
						Indicare <strong>SOLO</strong> indirizzi e-mail e <strong>NON</strong> PEC
						<? } ?>
					</td>
				</tr>
				<? } ?>
				<tr>
					<?
						$disabled = FALSE;
						if (!empty($record["codice"]) && !empty($record["cig"])) {
							$check_simog = $pdo->bindAndExec("SELECT b_lotti_simog.richiesto_simog FROM b_lotti_simog WHERE b_lotti_simog.codice_gara = :codice_gara AND b_lotti_simog.cig = :cig", array(":codice_gara" => $record["codice"], ":cig" => $record["cig"]));
							if ($check_simog->rowCount() == 1) {
								$disabled = $check_simog->fetch(PDO::FETCH_ASSOC)["richiesto_simog"] == "S" ? TRUE : FALSE;
							}
						}
						?>
					<td class="etichetta">CIG</td>
					<td><input class="titolo_edit" type="text" id="cig" name="gara[cig]" <?= $disabled ? 'disabled="disabled"' : null ?> title="CIG" value="<? echo $record["cig"] ?>" rel="N;10;10;A"></td>
					<td class="etichetta">CUP</td>
					<td><input class="titolo_edit" type="text" id="cup" name="gara[cup]" title="CUP" value="<? echo $record["cup"] ?>" rel="N;15;15;A"></td>
				</tr>
				<tr>
					<td class="etichetta">Codice NUTS</td>
					<td <? if ($_SESSION["ente"]["numerazione"] != "solare") echo "colspan='3'" ?>>
						<select id="nuts" name="gara[nuts]" title="Codice NUTS" rel="S;2;6;A">
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
						<script>
							$("#nuts").val("<? echo $record["nuts"] ?>");
						</script>
					</td>
					<? if ($_SESSION["ente"]["numerazione"] == "solare") { ?>
					<td class="etichetta">Anno di riferimento</td>
					<td><input type="text" id="anno" name="gara[anno]" title="Anno di riferimento" value="<? echo $record["anno"] ?>" rel="S;4;4;N"></td>
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
						<textarea name="gara[oggetto]" id="oggetto" title="Oggetto" rel="S;3;0;A" class="ckeditor_simple"><? echo $record["oggetto"] ?></textarea>
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
					$ris = $pdo->bindAndExec($sql, $bind);
					if ($ris->rowCount() > 1) {
						?>
				<tr>
					<td class="etichetta">Ente beneficiario</td>
					<td colspan="3">
						<select name="gara[codice_ente]" id="codice_ente" rel="S;0;0;N" title="Ente appaltatore" onchange="if ($(this).val() == '620') { $('#provinciaStruttura').show() } else { $('#provinciaStruttura').hide() } ">
							<?
									while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
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
					<td class="etichetta">Tipologia</td>
					<td>
						<select onChange="maschera_importi();aggiorna_procedure()" name="gara[tipologia]" id="tipologia" rel="S;0;0;N" title="Tipologia">
							<?
								$sql = "SELECT * FROM b_tipologie WHERE attivo = 'S' AND eliminato = 'N' ORDER BY codice";
								$ris = $pdo->query($sql);
								if ($ris->rowCount() > 0) {
									while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option value="<? echo $rec["codice"] ?>"><? echo $rec["tipologia"] ?></option><?
																																																						}
																																																					}
																																																					?>
						</select>
					</td>
					<td class="etichetta">Criterio di aggiudicazione</td>
					<td>
						<select name="gara[criterio]" onChange="aggiorna_procedure()" id="criterio" rel="S;0;0;N" title="Criterio di aggiudicazione">
							<?
								$sql = "SELECT * FROM b_criteri WHERE attivo = 'S' AND eliminato = 'N' ORDER BY codice";
								$ris = $pdo->query($sql);
								if ($ris->rowCount() > 0) {
									while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option value="<? echo $rec["codice"] ?>"><? echo $rec["criterio"] ?></option><?
																																																						}
																																																					}
																																																					?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="etichetta">Procedura</td>
					<td>
						<select name="gara[procedura]" id="procedura" rel="S;0;0;N" title="Procedura"></select>
					</td>
					<td class="etichetta">Modalit&agrave;</td>
					<td>
						<script>
							var modalita_online = new Array;
						</script>
						<select name="gara[modalita]" id="modalita" rel="S;0;0;A" title="Modalita">
							<? $sql = "SELECT * FROM b_modalita WHERE attivo = 'S' AND eliminato = 'N' ORDER BY codice";
								$ris = $pdo->query($sql);
								if ($ris->rowCount() > 0) {
									while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
										if ($rec["online"] == "S") { ?><script>
								modalita_online.push('<?= $rec["codice"] ?>');
							</script><? }
															?><option value="<? echo $rec["codice"] ?>"><? echo $rec["modalita"] ?></option><?
																																																						}
																																																					}
																																																					?>
						</select>
						<input type="hidden" id="modalita_online" rel="N;1;1;A" value="">
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<table width="100%">
							<thead>
								<tr>
									<td class="etichetta">Tipologia</td>
									<td class="etichetta">Importo base</td>
									<td class="etichetta">Costi della manodopera NON soggetti a ribasso</td>
									<td class="etichetta">Costi di sicurezza NON soggetti a ribasso</td>
								</tr>
							</thead>
							<tbody id="importi">
								<? if (isset($ris_importi) && ($ris_importi->rowCount() > 0)) {
										$importi = $ris_importi->fetchAll(PDO::FETCH_ASSOC);
										include("importi_preliminari/form.php");
									} ?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td class="etichetta">Somme a disposizione dell'Amministrazione</td>
					<td><input type="text" name="gara[somme_disponibili]" onChange="update_totale()" class="espandi" id="somme_disponibili" title="Somme disponibili" value="<? echo $record["somme_disponibili"] ?>" rel="N;0;0;N"></td>
					<td class="etichetta">Valore stimato appalto</td>
					<td><strong id="prezzoBase_label"> &euro; <? echo number_format($record["prezzoBase"], 2, ",", ".") ?></strong><input type="hidden" name="gara[prezzoBase]" id="prezzoBase" title="Totale appalto" value="<? echo $record["prezzoBase"] ?>" rel="S;0;0;N"></td>
				</tr>
				<tr>
					<td class="etichetta">Totale progetto</td>
					<td colspan="3" id="totale_appalto" style="font-weight:bold"> &euro; <? echo number_format($record["prezzoBase"] + $record["somme_disponibili"], 2, ",", ".") ?></td>
				</tr>
				<?
					$provincia_struttura = "";
					if ($record["codice_ente"] == 620) {
						$provincia_struttura = explode(" Provincia: ",$record["struttura_proponente"]);
						if (count($provincia_struttura) > 1) {
							$record["struttura_proponente"] = $provincia_struttura[0];
							$provincia_struttura = $provincia_struttura[1];
						} else {
							$provincia_struttura = "";
						}
					}
				?>
				<tr>
					<td class="etichetta">Struttura proponente</td>
					<td><input style="width:95%" type="text" class="espandi" id="struttura_proponente" name="gara[struttura_proponente]" title="Struttura proponente" value="<? echo $record["struttura_proponente"] ?>" rel="S;3;255;A"></td>
					<td class="etichetta">Responsabile della struttura</td>
					<td><input style="width:95%" type="text" class="espandi" id="responsabile_struttura" name="gara[responsabile_struttura]" title="Responsabile del servizio" value="<? echo $record["responsabile_struttura"] ?>" rel="S;3;255;A"></td>
				</tr>
				<tr>
					<td class="etichetta">Estremi del progetto o del CSA</td>
					<td colspan="3"><input style="width:95%" class="espandi" type="text" id="estremi_progetto" name="gara[estremi_progetto]" title="Estremi del progetto" value="<? echo $record["estremi_progetto"] ?>" rel="N;3;255;A"></td>
				</tr>
				<tr>
					<td class="etichetta">Data di validazione del progetto o del CSA</td>
					<td><input type="text" class="datepick_today espandi" id="data_validazione" name="gara[data_validazione]" title="Data di validazione" value="<? echo mysql2date($record["data_validazione"]) ?>" rel="N;10;10;D"></td>
					<td class="etichetta">Soggetto validatore</td>
					<td><input style="width:95%" type="text" class="espandi" id="validatore" name="gara[validatore]" title="Soggetto validatore" value="<? echo $record["validatore"] ?>" rel="N;3;255;A"></td>
				</tr>
			</table>

			<div class="clear"></div>
			<a class="precedente" style="float:left" href="#">Step precedente</a>
			<a class="successivo" style="float:right" href="#">Step successivo</a>
			<div class="clear"></div>
		</div>
		<div id="descrizione">
			<textarea name="gara[descrizione]" id="descrizione" title="Descrizione" class="ckeditor_full" rel="S;3;0;A">
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
<? if ($operazione == "UPDATE") {
		include($root . "/gare/ritorna.php");
	} ?>
<script>
	function check_modalita() {
		var pos = $.inArray($("#modalita").val(), modalita_online);
		if (pos === -1) {
			if (confirm('ATTENZIONE!!!\nLa modalità selezionata non è telematica.\nVuoi continuare comunque?')) {
				return true;
			} else {
				return false;
			}
		} else {
			return true
		}
	}
	$("#codice_pec").val("<? echo $record["codice_pec"] ?>");
	$("#norma").val("<? echo $record["norma"] ?>");
	$("#modalita").val("<? echo $record["modalita"] ?>");
	$("#criterio").val("<? echo $record["criterio"] ?>");
	var values = "<? echo $record["tipologia"] ?>";
	$("#tipologia").val(values.split(";"));

	function aggiorna_procedure() {
		var selezione = '<? echo $record["procedura"] ?>';
		if ($("#procedura").val() != null) selezione = $("#procedura").val();
		$("#procedura").html('');
		if ($("#tipologia").val() != null && $("#criterio").val() != null) {
			tipologie = $("#tipologia").val();
			criterio = $("#criterio").val();
			$.ajax({
				type: "POST",
				url: "aggiorna_procedure.php",
				dataType: "html",
				data: "codici_tipologia=" + tipologie + "&criterio=" + criterio,
				async: false,
				success: function(script) {
					$("#procedura").html(script);
					$("#procedura").trigger("chosen:updated");
					$("#procedura").val(selezione);
				}
			});
		}
	}

	aggiorna_procedure();

	function update_totale() {
		var sum = 0;
		$('.importi').each(function() {
			$(this).val($(this).val().replace(",", "."));
			if (isNumeric($(this).val())) {
				sum += +($(this).val());
			}
		});
		$("#prezzoBase_label").html("&euro; " + number_format(sum, 2, ",", "."));
		$("#prezzoBase").val(sum);
		totale_appalto = sum;
		if (valida($("#somme_disponibili")) == "") {
			totale_appalto += +($("#somme_disponibili").val());
		}
		$("#totale_appalto").html("&euro; " + number_format(totale_appalto, 2, ",", "."));
	}

	function maschera_importi() {
		tipologie = $("#tipologia").val();
		$(".tr_importo").each(function() {
			codice = $(this).attr('id').replace("importo_", "");
			if ($.inArray(codice, tipologie) === -1) {
				$(this).remove();
			}
		});
		if (tipologie != null) {
			for (i = 0; i < tipologie.length; i++) {
				if ($("#importo_" + tipologie[i]).length == 0) {
					$.ajax({
						type: "POST",
						url: "importi_preliminari/form.php",
						dataType: "html",
						data: "codice_tipologia=" + tipologie[i],
						async: false,
						success: function(script) {
							$("#importi").append(script);
						}
					});
				}
			}
		}
		f_ready();
		etichette_testo();
		update_totale();
	}

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
			$(this).click(function() {
				$('#tabs').tabs('option', 'active', target)
			});
		}
	});

	$(".successivo").each(function() {
		var id_parent = $("#tabs").children("div").index($(this).parent("div"));
		if (id_parent == ($("#tabs").children("div").length - 1)) {
			$(this).remove();
		} else {
			var target = id_parent + 1;
			$(this).click(function() {
				$('#tabs').tabs('option', 'active', target)
			});
		}
	});
</script>
<?

} else {

	echo "<h1>Gara non trovata</h1>";
}

?>


<?
include_once($root . "/layout/bottom.php");
?>