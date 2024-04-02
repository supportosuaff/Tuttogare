<?
if (isset($_POST["id"])) {
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (is_numeric($_POST["id"])) {
		$strsql = "SELECT * FROM b_paragrafi_new WHERE codice = :codice";
		$risultato = $pdo->bindAndExec($strsql,array(":codice"=>$_POST["id"]));
		if ($risultato->rowCount()>0) {
			$paragrafo = $risultato->fetch(PDO::FETCH_ASSOC);
			$id = $paragrafo["codice"];
			$colore = "#0C0";
			if ($paragrafo["attivo"] == "N") $colore = "#c00";
		}
	} else {
		$paragrafo = get_campi("b_paragrafi_new");
		$paragrafo["tipo"] = "paragrafo";
		$paragrafo["codice_opzione"] = 0;
		$paragrafo["modalita"] = 0;
		$paragrafo["vincoli_soa"] = 0;
		$paragrafo["ordinamento"] = 1000;
		$paragrafo["importo_minimo"] = 0;
		$paragrafo["importo_massimo"] = 0;
		$colore = "#0C0";
		$id = $_POST["id"];
	}
}
?>
<div id="paragrafo_<?= $id ?>">
	<input type="hidden" name="paragrafo[<? echo $id ?>][ordinamento]" id="paragrafo_<?= $id ?>_ordinamento" class="ordinamento" value="<? echo $paragrafo["ordinamento"]  ?>">
	<div class="box">
		<div style="float:left">
			<span class="btn-round handle" id="flag_paragrafo_<?= $id ?>" style="padding:5px; color:#FFF; background-color:<?= $colore ?>"><span class="fa fa-bars"></span></span>
			<span class="btn-round" style="padding:5px; background-color:<?= (empty($paragrafo["tag_esender"]) ? "#C00" : "#0C0") ?>">&nbsp;</span>
		</div>
		<div style="float:right;">
			<button class="btn-round btn-warning" onClick="$('#settings_<?= $id ?>').toggle();return false;" title="Dettagli"><span class="fa fa-search"></span></button>
			<button class="btn-round btn-default"  onClick="disabilita('<? echo $id ?>','impostazioni/compilatore/paragrafi');return false" title="Abilita/Disabilita"><span class="fa fa-refresh"></span></button>
			<button class="btn-round btn-danger" onClick="elimina('<? echo $id ?>','impostazioni/compilatore/paragrafi');return false" title="Elimina"><span class="fa fa-remove"></span></button>
		</div>
		<div class="clear"></div>
		<div style="min-height:300px;">

			<input type="hidden" id="paragrafo_<?= $id ?>_codice" name="paragrafo[<?= $id ?>][codice]" value="<? echo $paragrafo["codice"]; ?>">
			<input type="hidden" id="paragrafo_<?= $id ?>_id" name="paragrafo[<?= $id ?>][id]" value="<? echo $id ?>">
				<table width="100%" id="settings_<?= $id ?>" style="font-size:8px !important;">
					<tr>
						<th>Tipo</th>
						<td style="color:#000;">
							<select onChange="check_tipo('<?= $id ?>');" name="paragrafo[<?= $id ?>][tipo]" id="paragrafo_<?= $id ?>_tipo" title="Tipo" rel="S;0;0;A">
								<option value="avanzato">Avanzato</option>
								<option value="paragrafo">Paragrafo</option>
								<option value="ricorsivo">Ricorsivo</option>
							</select>
						</td>
						<th>Modalita</th><td>
							<select name="paragrafo[<?= $id ?>][modalita]" title="Modalita" rel="N;0;0;A" id="paragrafo_<?= $id ?>_modalita">
								<option value="0">Tutti</option>
								<?
								if (empty($ris_modalita)) {
									$strsql = "SELECT * FROM b_modalita WHERE eliminato = 'N' ORDER BY codice ";
									$ris_modalita = $pdo->query($strsql)->fetchAll(PDO::FETCH_ASSOC);
								}
								foreach ($ris_modalita AS $modalita) {
									?><option value="<? echo $modalita["codice"] ?>"><? echo $modalita["modalita"] ?></option>
									<? } ?>
								</select>
							</td>
							</tr>
							<tr>
								<th>Tag E-sender</th>
								<td colspan="3">
									<input type="text" style="width:98%" name="paragrafo[<?= $id ?>][tag_esender]" value="<? echo $paragrafo["tag_esender"] ?>" id="paragrafo_<?= $id ?>_tag_esender" title="E-sender" rel="N;0;0;A">
								</td>
							</tr>
							<tr>
								<th>Importo minimo</th>
								<td>
									<input type="text" style="width:98%" name="paragrafo[<?= $id ?>][importo_minimo]" value="<? echo $paragrafo["importo_minimo"] ?>" id="paragrafo_<?= $id ?>_importo_minimo" title="Importo minimo" rel="N;0;0;N">
								</td>
								<th>Importo massimo</th>
								<td>
									<input type="text" style="width:98%" name="paragrafo[<?= $id ?>][importo_massimo]" value="<? echo $paragrafo["importo_massimo"] ?>" id="paragrafo_<?= $id ?>_importo_massimo" title="Importo massimo" rel="N;0;0;N">
								</td>
							</tr>
							<tr>
								<th>Criteri</th>
								<td>
									<select name="paragrafo[<?= $id ?>][criteri][]" multiple title="Criteri" rel="N;0;0;A" id="paragrafo_<?= $id ?>_criteri">
										<option value="">Sempre</option>

										<?
										if (empty($ris_criteri)) {
											$strsql = "SELECT * FROM b_criteri WHERE eliminato = 'N' AND attivo = 'S' ORDER BY codice ";
											$ris_criteri = $pdo->query($strsql)->fetchAll(PDO::FETCH_ASSOC);
										}
										foreach ($ris_criteri AS $criterio) { ?>
											<option value="<? echo $criterio["codice"] ?>"><? echo $criterio["criterio"] ?></option>
										<? } ?>
									</select>
								</td>
								<th>Tipologie</th>
								<td>
									<select name="paragrafo[<?= $id ?>][tipologie][]" multiple title="Tipologie" rel="N;0;0;A" id="paragrafo_<?= $id ?>_tipologie">
										<option value="">Sempre</option>

										<?
										if (empty($ris_tipologie)) {
											$strsql = "SELECT * FROM b_tipologie WHERE eliminato = 'N' AND attivo = 'S' ORDER BY codice ";
											$ris_tipologie = $pdo->query($strsql)->fetchAll(PDO::FETCH_ASSOC);
										}
										foreach ($ris_tipologie AS $tipologia) { ?>
											<option value="<? echo $tipologia["codice"] ?>"><? echo $tipologia["tipologia"] ?></option>
										<? } ?>
									</select>
								</td>
							<tr>
								<td colspan="3">
									<select class="select_opzione" name="paragrafo[<?= $id ?>][codice_opzione][]" multiple title="Opzione" rel="N;0;0;A" id="paragrafo_<?= $id ?>_codice_opzione">
									<? include("list_opzioni.php") ?>
									</select>
								</td>
								<td>
									<button class="aggiungi" onClick="open_dialog();return false;"><img src="/img/add.png" alt="Aggiungi opzione">Aggiungi opzione</button>
								</td>
							</tr>
						</table>
						<table width="100%">
							<tr>
								<th colspan='14'>Vincoli SOA</th>
							</tr>
							<tr>
								<td>Nessuno</td>
								<td>S.I.O.S. > 15%</td>
								<td>S.I.O.S. Assenti</td>
								<td>S.I.O.S. che cambiano classe - 30%</td>
								<td>S.I.O.S. che non cambiano classe - 30%</td>
								<td>(T.A. - 70% S.I.O.S.) > 20.658.000 </td>
								<td>(T.A. - 70% S.I.O.S.) < 20.658.000 </td>
								<td>Scorporabili a Q.O. <> S.I.O.S.</td>
								<td>Scorporabili non a Q.O.</td>
								<td>OG2 - OS2-A - OS2-B - OS25</td>
								<td>Tutelate assenti</td>
								<td>OG11 o OS3 - OS28 - OS30</td>
								<td>Categorie Scorporabili Assenti</td>
								<td>Categorie Scorporabili Presenti</td>
							</tr>
							<tr>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="0" id="paragrafo_<?= $id ?>_vincoli_soa_0"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="1" id="paragrafo_<?= $id ?>_vincoli_soa_1"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="12" id="paragrafo_<?= $id ?>_vincoli_soa_12"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="2" id="paragrafo_<?= $id ?>_vincoli_soa_2"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="3" id="paragrafo_<?= $id ?>_vincoli_soa_3"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="4" id="paragrafo_<?= $id ?>_vincoli_soa_4"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="5" id="paragrafo_<?= $id ?>_vincoli_soa_5"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="6" id="paragrafo_<?= $id ?>_vincoli_soa_6"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="7" id="paragrafo_<?= $id ?>_vincoli_soa_7"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="8" id="paragrafo_<?= $id ?>_vincoli_soa_8"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="13" id="paragrafo_<?= $id ?>_vincoli_soa_13"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="9" id="paragrafo_<?= $id ?>_vincoli_soa_9"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="10" id="paragrafo_<?= $id ?>_vincoli_soa_10"></td>
								<td style="text-align:center"><input type="radio" name="paragrafo[<?= $id ?>][vincoli_soa]" value="11" id="paragrafo_<?= $id ?>_vincoli_soa_11"></td>
							</tr>
						</table>
						<script>
							$("#paragrafo_<?= $id ?>_vincoli_soa_<?= $paragrafo["vincoli_soa"] ?>").attr("checked","checked");
						</script>
			<table id="valori_tipi_<?= $id ?>" width="100%">
				<tr class="directory">
					<td>
						<select name="paragrafo[<?= $id ?>][directory]" title="Directory" rel="N;0;0;A" id="paragrafo_<?= $id ?>_directory">
							<option value="">Nessuno</option>
							<?
							$scripts = scandir($root."/gare/elaborazione/moduli_avanzati");
							foreach ($scripts AS $directory) {
								if ($directory != "." && $directory != "..") {
										?>
										<option><?= $directory ?></option>
										<?
									}
							}
							?>
						</select>
						<script>
							$("#paragrafo_<?= $id ?>_directory").val('<? echo $paragrafo["directory"] ?>');
						</script>
					</td>
				</tr>
				<tr class="ricorsivo">
					<td>
						<select name="paragrafo[<?= $id ?>][codice_ricorsivo]" title="Modello" rel="N;0;0;A" id="paragrafo_<?= $id ?>_codice_ricorsivo">
							<option value="">Nessuno</option>
							<?
							if (empty($ris_ricorsivi)) {
								$strsql = "SELECT codice,titolo FROM b_paragrafi_ricorsivi WHERE eliminato = 'N' ORDER BY titolo ";
								$ris_ricorsivi = $pdo->query($strsql)->fetchAll(PDO::FETCH_ASSOC);
							}
							foreach ($ris_ricorsivi AS $ricorsivo) {
								?><option value="<? echo $ricorsivo["codice"] ?>"><? echo $ricorsivo["titolo"] ?></option>
								<? } ?>
							?>
						</select>
						<script>
							$("#paragrafo_<?= $id ?>_codice_ricorsivo").val('<? echo $paragrafo["codice_ricorsivo"] ?>');
						</script>
					</td>
				</tr>
				<tr class="textarea">
					<td>
						<div>
							<textarea title="Contenuto" id="paragrafo_<? echo $id ?>_contenuto" rel="N;0;0;A" name="paragrafo[<? echo $id ?>][contenuto]" class="ckeditor_models"><? echo $paragrafo["contenuto"] ?></textarea>
						</div>
					</td>
				</tr>
				</table>
	      <script>
					$("#paragrafo_<?= $id ?>_tipo").val('<? echo $paragrafo["tipo"] ?>');
					codici_opzione = '<? echo $paragrafo["codice_opzione"] ?>';
					$("#paragrafo_<?= $id ?>_codice_opzione").val(codici_opzione.split(','));
					criteri = '<? echo $paragrafo["criteri"] ?>';
					$("#paragrafo_<?= $id ?>_criteri").val(criteri.split(','));
					tipologie = '<? echo $paragrafo["tipologie"] ?>';
					$("#paragrafo_<?= $id ?>_tipologie").val(tipologie.split(','));
					$("#paragrafo_<?= $id ?>_modalita").val('<?= $paragrafo["modalita"] ?>');
					check_tipo('<?= $id ?>');
				</script>
			</td>
			</div>
		</div>
		<button class="aggiungi" onClick="aggiungi('edit_paragrafo.php','#paragrafo_<?= $id ?>','',true); $.map($('#paragrafi').find('tr,div'), function(el) { $('input.ordinamento', el).val($(el).index());});return false;"><span class="fa fa-plus-circle"></span> Aggiungi paragrafo</button>
	</div>
