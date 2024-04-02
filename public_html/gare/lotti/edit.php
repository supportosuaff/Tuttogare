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
	if ($risultato->rowCount()>0) {
		$gara = $record = $risultato->fetch(PDO::FETCH_ASSOC);
		if (!empty($gara["id_suaff"])) $lock = true;
		$_SESSION["gara"] = $record;
		$bind = array();
		$bind[":codice"] = $record["codice"];
		$sql = "SELECT SUM(importo_base) AS importo_base, SUM(importo_oneri_ribasso) AS importo_oneri_ribasso, SUM(importo_oneri_no_ribasso) AS importo_oneri_no_ribasso, SUM(importo_personale) AS importo_personale FROM b_importi_gara ";
		$sql .= "WHERE codice_gara = :codice GROUP BY codice_gara ";
		$ris_importi = $pdo->bindAndExec($sql,$bind);
	?>
	<h1>LOTTI</h1>

	<? if (!$lock) { ?>
					<div class="box">
						<table width="100%">
							<tbody>
								<tr>
									<td style="text-align: center;vertical-align: middle;"><a href="#" onClick="$('#massive').slideToggle()">Caricamento massivo dei lotti</strong></td>
								</tr>
							</tbody>
						</table>
						<form id="massive" action="edit.php?codice=<? echo $codice ?>" method="post" enctype="multipart/form-data" style="display:none">
							<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
							<table class="dettaglio" width="100%">
								<tbody>
								<tr>
									<td width="25%">
										<img src="../../img/xls.png" alt="Modello lotti"/><a href="lotti.php" name="lotti_csv" download style="vertical-align:super">Modello CSV</a>
									</td>
									<td width="50%">
											<input type="file" name="lotti" id="file">
									</td>
									<td width="5%">
											<input type="submit" name="submit" value="Upload">
									</td>
								</tr>
								</tbody>
							</table>
							<h2 style="text-align:center">Guida alla compilazione del CSV</h2>
							Il file da caricare dovrà essere generato includendo ogni campo in doppi apici <strong>(")</strong> ed utilizzando il separatore punto e virgola <strong>(;)</strong>
							<table>
								<tr><td><strong>CIG</strong></td><td></td></tr>
								<tr><td><strong>OGGETTO*</strong></td><td><strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>DESCRIZIONE*</strong></td><td><strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>ULTERIORI_INFORMAZIONI</strong></td><td></td></tr>
								<tr><td><strong>IMPORTO_BASE*</strong></td><td>Inserire solo separatore decimale <strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>IMPORTO_ONERI_RIBASSO*</strong></td><td>Inserire solo separatore decimale <strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>IMPORTO_ONERI_NO_RIBASSO*</strong></td><td>Inserire solo separatore decimale <strong>Campo obbligatorio</strong><td></td></tr>
								<tr><td><strong>IMPORTO_PERSONALE*</strong></td><td>Inserire solo separatore decimale <strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>DURATA*</strong></td><td>Numerico intero <strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>UNITA_DURATA*</strong></td><td>GG: Giorni / MM: Mesi <strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>ANAC-SOMMA_URGENZA</strong></td><td>S / N</td></tr>
								<tr><td><strong>ANAC-IMPORTO_LOTTO</strong></td><td>Inserire se differente da precedente</td></tr>
								<tr><td><strong>ANAC-IMPORTO_ATTUAZIONE_SICUREZZA</strong></td><td>Inserire se differente da precedente</td></tr>
								<tr><td><strong>ANAC-TIPOAPPALTOTYPE*</strong></td><td>
									<?
									  $listeSimog = getListeSIMOG();
										foreach ($listeSimog["TipoAppaltoType"] as $value => $description) {
											?><strong><?= $value ?></strong>: <?= $description ?><br><?
										}
									?>
									<br>
									Inserire solo numero intero <strong>Campo obbligatorio</strong>
								</td></tr>
								<tr><td><strong>ANAC-FLAG_ESCLUSO *</strong></td><td>Contratto escluso in tutto o in parte dall'applicazione del codice: S / N <strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>ANAC-ARTESCLUSIONETYPE</strong></td><td>
									<?
										foreach ($listeSimog["ArtEsclusioneType"] as $value => $description) {
											?><strong><?= $value ?></strong>: <?= $description ?><br><?
										}
									?>
									<br>Inserire solo numero intero <strong>Se S precedente Campo obbligatorio</strong>
								</td></tr>
								<tr><td><strong>ANAC-TRIENNIO_ANNO_INIZIO</strong></td><td>Dati programma triennale</td></tr>
								<tr><td><strong>ANAC-TRIENNIO_ANNO_FINE</strong></td><td>Dati programma triennale</td></tr>
								<tr><td><strong>ANAC-TRIENNIO_PROGRESSIVO</strong></td><td>Dati programma triennale</td></tr>
								<tr><td><strong>ANAC-ANNUALE_CUI_MININF</strong></td><td>Codice CUI</td></tr>
								<tr><td><strong>ANAC-FLAG_PREVEDE_RIP *</strong></td><td>L'appalto prevede ripetizioni S / N <strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>ANAC-FLAG_RIPETIZIONE *</strong></td><td>L'appalto è ripetizione di un precedente appalto <strong>Campo obbligatorio</strong></td></tr>
								<tr><td><strong>ANAC-CIG_ORIGINE_RIP</strong></td><td>CIG di origine in caso di ripetizioni</td></tr>
							</table>
						</form>
					</div>
					<?
					if (isset($_POST["submit"])) {
						include("massive.php");
					}
					?>
					<br/>
		<form name="box" method="post" action="save.php" rel="validate">
			<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
			<div class="comandi">
				<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			</div>
			<?
			}
			?>
			<div id="tabs">
			<script>
				function aggiungi_lotto() {
					if ($(".edit_lotto").length < 10) {
						aggiungi('form.php','#body_lotti');
$('#lotti_partecipa').slideDown();
					} else {
						alert("Troppi lotti in modifica, procedere al salvataggio e riprovare");
					}
					return false;
				}
				function edit_lotto(id) {
					if ($(".edit_lotto").length < 10) {
							data = "codice=" + id;
							$.ajax({
								type: "GET",
								url: "form.php",
								dataType: "html",
								data: data,
								async:false,
								success: function(script) {
									$("#lotti_"+id).replaceWith(script);
								}
							});
							f_ready();
							etichette_testo();
					} else {
						alert("Troppi lotti in modifica, procedere al salvataggio e riprovare");
					}
					return false;
				}
				function check_importi() {
					$(".totale_importi").each(function() {
						importo = $(this).attr('id');
						totale = 0;
						$("."+importo).each(function() {
							if ($(this).val() !== "") {
								totale = totale + parseFloat($(this).val());
							} else {
								totale = totale + parseFloat($(this).html());
							}
						})
						$(this).val(totale);
					})
				}
			</script>
							<?
							if ($record["codice"]=="") $record["codice"] = 0;
							$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = " . $record["codice"] . " ORDER BY codice";
							$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
							?>
							<div id="lotti_partecipa" <? if ($ris_lotti->rowCount()==0) echo "style=\"display:none\"" ?>>
								<table width="100%">
									<tr>
										<td class="etichetta"><strong>Indicazioni di partecipazione</strong></td>
										<td>
											<select title="Partecipazione lotti" name="gara[modalita_lotti]" id="modalita_lotti" rel="S;0;0;N">
												<option value="0">Libera</option>
												<option value="1">Lotto singolo</option>
												<option value="2">Tutti i lotti</option>
											</select>
										</td>
									</tr>
								</table>
					     <script>
					       $("#modalita_lotti").val('<?= $record["modalita_lotti"] ?>');
					     </script>
							</div>
							<?
							$_SESSION["numero_lotto"] = 1;
							?>
							<h3 id="controlla_importi" style="display:none" class="ui-state-error">Controllare importi</h3>
							<div id="body_lotti">
								<?
								$importo_base = 0;
								$importo_oneri_ribasso = 0;
								$importo_oneri_no_ribasso = 0;
								$importo_personale = 0;
								if ($ris_lotti->rowCount() > 0) {
									while ($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
										$id = $lotto["codice"];
										include("view.php");
									}
								} else {
									?>
									<h2>Compilare solo in caso di lotti multipli</h2>
									<?
								}
								?>
							</div>
							<button class="aggiungi" onClick="aggiungi_lotto();return false;"><img src="/img/add.png" alt="Aggiungi lotto">Aggiungi lotto</button>
							<div class="clear"></div>
							<?
							if ($ris_importi->rowCount()>0) {
								$importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
								$diff = 0;
								$diff += truncate($importi["importo_base"],2) - truncate($importo_base,2);
								$diff += truncate($importi["importo_oneri_ribasso"],2) - truncate($importo_oneri_ribasso,2);
								$diff += truncate($importi["importo_oneri_no_ribasso"],2) - truncate($importo_oneri_no_ribasso,2);
								$diff += truncate($importi["importo_personale"],2) - truncate($importo_personale,2);

								if ($diff != 0) {
									?>
									<h3 class="ui-state-error">Controllare importi</h3>
									<script>
										$("#controlla_importi").slideDown();
									</script>
									<?
								}
							}
							?>
						</div>
					<? if (!$lock) { ?>
						<input type="submit" class="submit_big" value="Salva">
					</form>
					<script>
					//	check_importi();
					</script>
					<? } ?>

					<? include($root."/gare/ritorna.php"); ?>
					<script>
						<? if ($lock) { ?>
							$(":input","#tabs").not('.espandi').prop("disabled", true);
						<? } ?>
					</script>
						<?
} else {
	echo "<h1>Gara non trovata</h1>";
}
					} else {

						echo "<h1>Gara non trovata</h1>";

					}

					?>


					<?
					include_once($root."/layout/bottom.php");
					?>
