<?
if (isset($_POST["id"])) {
	if (!isset($pdo)) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
	}
	$lotto = get_campi("b_lotti");
	$id = $_POST["id"];
	$lotto["numero_lotto"] = $_SESSION["numero_lotto"];
} else if (isset($_GET["codice"])) {
	if (!isset($pdo)) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
	}
	$bind = array();
	$bind[":codice"] = $_GET["codice"];
	$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice ORDER BY codice";
	$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
	if ($ris_lotti->rowCount() > 0) {
		$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
		$id = $lotto["codice"];
	}
}
if (isset($lotto)) {
	$edit_cig = true;
	if (is_numeric($lotto["codice"])) {
		$ris = $pdo->bindAndExec("SELECT * FROM b_lotti_simog WHERE richiesto_simog = 'S' AND codice_lotto = :lotto AND cig <> ''",array(":lotto"=>$lotto["codice"]));
		if ($ris->rowCount() > 0) $edit_cig = false;
	}
?>
<div class="box" id="lotti_<? echo $id ?>">
	<table width="100%">
		<tr>
			<td width="10"><input type="hidden" class="edit_lotto" name="lotti[<? echo $id ?>][codice]" value="<? echo $id ?>">
				<input <? if (!$edit_cig) echo "readonly"; ?> size="12" type="text" id="lotti_cig_<? echo $id ?>" name="lotti[<? echo $id ?>][cig]" title="CIG" value="<? echo $lotto["cig"] ?>" rel="N;10;10;A">
			</td>
			<td>
				<input type="text" title="Oggetto" class="titolo_edit" style="width:98%" id="oggetto_<? echo $id ?>" rel="S;0;0;A" name="lotti[<? echo $id ?>][oggetto]" value="<? echo strip_tags($lotto["oggetto"]) ?>"></td>
				<td width="10"><button class="espandi btn-round btn-warning" onClick="$(this).parents('div').children('table.dettaglio').toggle(); return false;" title="Visualizza"><span class="fa fa-search"></span></button></td>
				<td width="10"><button class="btn-round btn-danger" onClick="elimina('<? echo $id ?>','gare/lotti');return false" title="Elimina"><span class="fa fa-remove"></span></button></td></tr>
			</table>
			<table width="100%" class="dettaglio">
				<tr>
					<td class="etichetta" colspan="4">
						<strong>Breve descrizione</strong></td></tr>
						<tr><td colspan="4">
							<textarea title="Breve descrizione" id="lotti_descrizione_<? echo $id ?>" rel="S;0;0;A" name="lotti[<? echo $id ?>][descrizione]" class="ckeditor_full"><? echo $lotto["descrizione"] ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="etichetta"><strong>Importo base</strong></td>
						<!-- <td class="etichetta"><strong>Costi di sicurezza soggetti a ribasso</strong></td> -->
						<td class="etichetta"><strong>Costi di sicurezza NON soggetti a ribasso</strong></td>
						<!-- <td class="etichetta"><strong>Costo della manodopera</strong></td> -->
					</tr>
					<tr>
							<td>
								<input name="lotti[<? echo $id ?>][importo_base]" id="lotti_importo_base_<?= $id ?>" class="importo_base" onchange="check_importi()" title="Importo base" value="<? echo $lotto["importo_base"] ?>" rel="S;1;0;N"><br>
								di cui Costo della manodopera compresi nell'importo soggetto a ribasso<br>
								<input name="lotti[<? echo $id ?>][importo_personale]" id="lotti_importo_personale_<?= $id ?>" title="Costo della manodopera" onchange="check_importi()" class="importo_personale" value="<? echo $lotto["importo_personale"] ?>" rel="S;1;0;N">
							</td>
							<!-- <td><input name="lotti[<? echo $id ?>][importo_oneri_ribasso]" id="lotti_importo_oneri_ribasso_<?= $id ?>" class="importo_oneri_ribasso" onchange="check_importi()" title="Oneri di sicurezza" value="<? echo $lotto["importo_oneri_ribasso"] ?>" rel="S;1;0;N"></td> -->
							<td><input name="lotti[<? echo $id ?>][importo_oneri_no_ribasso]" id="lotti_importo_oneri_no_ribasso_<?= $id ?>" class="importo_oneri_no_ribasso" onchange="check_importi()" title="Costi di sicurezza" value="<? echo $lotto["importo_oneri_no_ribasso"] ?>" rel="S;1;0;N"></td>
							<!-- <td><input name="lotti[<? echo $id ?>][importo_personale]" id="lotti_importo_personale_<?= $id ?>" title="Costo della manodopera" onchange="check_importi()" class="importo_personale" value="<? echo $lotto["importo_personale"] ?>" rel="S;1;0;N"></td> -->
						</tr>
						<tr><td class="etichetta" colspan="2"><strong>Categoria Merceologica</strong></td></tr>
						<tr><td colspan="2">
							<select title="Categoria CPV Lotto" class="cpv_lotti" id="lotti_cpv_<? echo $id ?>" name="lotti[<? echo $id ?>][cpv]" rel="S;0;0;A">
								<option value="">Seleziona...</option>
								<?
									$bind[":codice"] = $_SESSION["gara"]["codice"];
									$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice ORDER BY codice";
									$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
									if ($risultato_cpv->rowCount()>0) {
										while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
											echo "<option value='" . $rec_cpv["codice"] . "'>" . $rec_cpv["descrizione"] . "</option>";
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr><td class="etichetta" colspan="2"><strong>Durata contrattuale</strong></td></tr>
					<tr><td colspan="2">
						<input size="3" type="text" title="Durata"  name="lotti[<? echo $id ?>][durata]" id="lotti_durata_<? echo $id ?>" value="<? echo $lotto["durata"]; ?>" rel="S;1;3;N;0;>">
						<div style="width:100px; float:left;">
							<select style="width:100px !important;" name="lotti[<? echo $id ?>][unita_durata]" id="lotti_unita_durata_<? echo $id ?>" title="Unità durata" rel="S;2;2;A">
								<option value="">Seleziona...</option>
								<option value="gg">Giorni</option>
								<option value="mm">Mesi</option>
							</select>
						</div>
						<div class="clear"></div>
					</td>
				</tr>
				<script>
					$("#lotti_unita_durata_<? echo $id ?>").val("<? echo $lotto["unita_durata"] ?>");
				</script>
				<tr><td class="etichetta" colspan="4">
					<strong>Ulteriori informazioni</strong></td></tr>
					<tr><td colspan="4">
						<textarea title="Ulteriori informazioni" id="lotti_ulteriori_informazioni_<? echo $id ?>" rel="N;0;0;A" name="lotti[<? echo $id ?>][ulteriori_informazioni]" class="ckeditor_full"><? echo $lotto["ulteriori_informazioni"] ?></textarea>
					</td>
				</tr>
			</table></div>
			<script>
				$("#lotti_cpv_<? echo $id ?>").val('<?= $lotto["cpv"] ?>');
			</script>
<? } ?>
