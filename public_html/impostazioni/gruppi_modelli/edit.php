<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && ($_SESSION["gerarchia"] === "0" || $_SESSION["tipo_utente"]== "CON")) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
			$codice = $_GET["codice"];
			$bind = array(":codice"=>$codice);
			$strsql = "SELECT * FROM b_modelli WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($edit) {
				if ($risultato->rowCount() > 0) {
						$record = $risultato->fetch(PDO::FETCH_ASSOC);
						$operazione = "UPDATE";
				} else {
						$record = get_campi("b_modelli");
						$operazione = "INSERT";
				}
?>

<div class="clear"></div>
<div id="dialog"></div>
<form name="box" id="form" method="post" action="save.php" rel="validate">
          <input type="hidden" id="codice" name="codice" value="<? echo $record["codice"]; ?>">
          <input type="hidden" id="operazione" name="operazione" value="<? echo $operazione ?>">
					<input type="hidden" id="duplica" name="duplica" value="N">
          <div class="comandi">
						<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
						<button class="btn-round btn-warning" title="Visualizza" onClick="$('.settings').toggle();return false;"><span class="fa fa-search"></span></button>
						<button class="btn-round btn-default"  onclick="open_vocabolario(); return false;" title="Vocabolario"><span class="fa fa-book"></span></button>
					</div>
					<script>
					function check_tipo(id) {
						tipo = $("#paragrafo_"+id+"_tipo").val();
						$("tr","#valori_tipi_"+id).hide();
						if (tipo == "avanzato" || tipo == "paragrafo_avanzato") {
							$(".directory","#valori_tipi_"+id).show();
							$("#paragrafo_"+id+"_directory").attr("rel","S;3;0;A");
							$("#paragrafo_"+id+"_contenuto").attr("rel","N;0;0;A");
						} else {
							$(".textarea","#valori_tipi_"+id).show();
							$("#paragrafo_"+id+"_contenuto").attr("rel","S;3;0;A");
							$("#paragrafo_"+id+"_directory").attr("rel","N;0;0;A");
						}
					}
					function edit_paragrafo(id) {
							data = "id=" + id;
							$.ajax({
								type: "POST",
								url: "edit_paragrafo.php",
								dataType: "html",
								data: data,
								async:false,
								success: function(script) {
									ordinamento = $("#paragrafo_"+id+"_ordinamento").val();
									$("#paragrafo_"+id).replaceWith(script);
									$("#paragrafo_"+id+"_ordinamento").val(ordinamento);
								}
							});
							f_ready();
							etichette_testo();
					}
					function open_dialog() {
						$("#dialog").load("/impostazioni/opzioni/edit.php?codice=0",function(){
							$(this).dialog({
								modal:true,
								width: '800px',
								position: 'top',
							});
							f_ready();
						});
					}
					function open_vocabolario() {
						$("#vocabolario").dialog({
							modal:true,
							width: '800px',
							position: 'top',
						});
					}
					</script>
					<h1>Modello</h1>
					<input type="text" class="titolo_edit" value="<? echo $record["titolo"] ?>" name="titolo" id="titolo" title="Titolo" rel="N;0;0;A">
					<table width="100%">
						<tr>
							<td>Tipo di documento</td>
							<td>
								<select name="tipo" id="tipo" title="Tipo di documento" rel="S;0;0;A">
									<option>Bando</option>
									<option>Disciplinare</option>
									<option>Contratto</option>
								</select>
						</td>
							<td class="etichetta">Tipologia</td><td width="25%">
								<select name="tipologia" title="Tipologia" rel="S;0;0;A" id="tipologia">
									<?
									$strsql = "SELECT * FROM b_tipologie WHERE eliminato = 'N' ORDER BY codice ";
									$ris = $pdo->query($strsql);
									while ($tipologia = $ris->fetch(PDO::FETCH_ASSOC)) {
										?><option value="<? echo $tipologia["codice"] ?>"><? echo $tipologia["tipologia"] ?></option><? } ?>
									</select>
								</td>
							</tr>
							<tr class="no_contratto">
								<td class="etichetta">Procedura</td><td width="25%">
									<select name="procedura" title="Procedura" rel="N;0;0;A" id="procedura">
										<?
										$strsql = "SELECT * FROM b_procedure WHERE eliminato = 'N' ORDER BY ordinamento,codice  ";
										$ris = $pdo->query($strsql);
										while ($procedura = $ris->fetch(PDO::FETCH_ASSOC)) {
											?><option value="<? echo $procedura["codice"] ?>"><? echo $procedura["nome"] ?></option><? } ?>
										</select>
									</td>
								<td class="etichetta">Criterio</td><td width="25%">
									<select name="criterio" title="Criterio" rel="S;0;0;A" id="criterio">
										<?
										$strsql = "SELECT * FROM b_criteri WHERE eliminato = 'N' ORDER BY codice ";
										$ris = $pdo->query($strsql);
										while ($criterio = $ris->fetch(PDO::FETCH_ASSOC)) {
											?><option value="<? echo $criterio["codice"] ?>"><? echo $criterio["criterio"] ?></option><? } ?>
										</select>
									</td>
								</tr>
								<tr class="contratto">
									<td class="etichetta">Modalit&agrave; di stipula:</td>
									<td width="20%">
										<select id="modalita_stipula" name="modalita_stipula" rel="S;1;0;A" title="Modalit&agrave; di stipula">
											<option value="">Seleziona..</option>
											<?
											$ris = $pdo->query("SELECT * FROM b_conf_modalita_stipula WHERE eliminato = 'N' AND attivo = 'S' ORDER BY etichetta ASC");
											if($ris->rowCount() > 0) {
												while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
													?>
													<option value="<?= $rec["codice"] ?>"><?= $rec["etichetta"] ?></option>
													<?
												}
											}
											?>
										</select>
									</td>
									<td class="etichetta">Tipologia del contratto:</td>
									<td width="20%">
										<select id="tipologia_contratto" name="tipologia_contratto" rel="S;1;0;A" title="Tipologia">
											<option value="">Seleziona..</option>
											<?
											$ris = $pdo->query("SHOW COLUMNS FROM b_contratti WHERE Field = 'tipologia'");
											$tipologia = $ris->fetch(PDO::FETCH_ASSOC)["Type"];
											preg_match("/^enum\(\'(.*)\'\)$/", $tipologia, $matches);
											$enum = explode("','", $matches[1]);
											foreach ($enum as $value) {
												?><option value="<?= $value ?>"><?= ucfirst($value) ?></option><?
											}
											?>
										</select>
									</td>
								</tr>
								<tr >
									<td class="etichetta">Importo minimo</td>
									<td><input type="text" value="<? echo $record["importo_minimo"] ?>" name="importo_minimo" id="importo_minimo" rel="S;0;0;N" title="Importo minimo"></td>
									<td class="etichetta">Importo massimo</td>
									<td><input type="text" value="<? echo $record["importo_massimo"] ?>" name="importo_massimo" id="importo_massimo" rel="S;0;0;N" title="Importo massimo"></td>
								</tr>
							</table><br>
							<table width="100%">
								<tbody id="paragrafi" class="sortable">
									<?
										if ($record["codice"] != "") {
										$sql = "SELECT * FROM b_paragrafi WHERE codice_modello = :codice AND eliminato = 'N' ORDER BY ordinamento ";
										$ris_paragrafi = $pdo->bindAndExec($sql,array(":codice"=>$record["codice"]));
										if ($ris_paragrafi->rowCount()>0) {
											while($paragrafo = $ris_paragrafi->fetch(PDO::FETCH_ASSOC)) {
												include("paragrafo.php");
											}
										}
									}
									?>
								</tbody>
								<tfoot>
									<tr><td colspan="6">
										<button class="aggiungi" onClick="aggiungi('edit_paragrafo.php','#paragrafi');return false;"><img src="/img/add.png" alt="Aggiungi paragrafo">Aggiungi paragrafo</button></td></tr>
								</tfoot>
							</table>
							<input type="submit" class="submit_big" value="Salva">
							<? if ($operazione == "UPDATE") { ?>
								<input type="submit" class="submit_big" value="Duplica" onClick="if (confirm('Confermi la duplicazione?')) { $('#duplica').val('S'); } else { return false; }">
								<? } ?>
							<script>
								$('#tipo').on('change', function(event) {
									if($(this).val() == "Contratto") {
										$('.no_contratto').slideUp('fast').find(':input').each(function(index, el) {
											$(el).attr('disabled', 'disabled');
											$(el).prop('disabled', 'disabled');
											if($(el).is('select')) $(el).trigger('chosen:updated');
										});
										$('.contratto').slideDown('fast').find(':input').each(function(index, el) {
											$(el).removeAttr('disabled', 'disabled');
											$(el).removeProp('disabled', 'disabled');
											if($(el).is('select')) $(el).trigger('chosen:updated');
										});
									} else {
										$('.no_contratto').slideDown('fast').find(':input').each(function(index, el) {
											$(el).removeAttr('disabled', 'disabled');
											$(el).removeProp('disabled', 'disabled');
											if($(el).is('select')) $(el).trigger('chosen:updated');
										});
										$('.contratto').slideUp('fast').find(':input').each(function(index, el) {
											$(el).attr('disabled', 'disabled');
											$(el).prop('disabled', 'disabled');
											if($(el).is('select')) $(el).trigger('chosen:updated');
										});
									}
								});
								$("#tipo").val("<?= $record["tipo"] ?>").trigger('change');
								$("#criterio").val("<?= $record["criterio"] ?>");
								$("#tipologia").val("<?= $record["tipologia"] ?>");
								$("#procedura").val("<?= $record["procedura"] ?>");
								$('#modalita_stipula').val('<?= !empty($record["modalita_stipula"]) ? $record["modalita_stipula"] : null ?>');
								$('#tipologia_contratto').val('<?= !empty($record["tipologia_contratto"]) ? $record["tipologia_contratto"] : null ?>');
							</script>
						</form>
						<div id="vocabolario" style="display:none">
							<h2>Vocabolario</h2>
							<table width="100%" class="elenco">
								<thead>
									<tr>
										<td>Chiave</td>
										<td>Descrizione</td>
									</tr>
									<tbody>
									<?
										$vocabolario = json_decode(file_get_contents("vocabolario.json"),true);
										foreach ($vocabolario AS $key => $vocabolo) {
											?>
											<tr>
												<td>
													#<?= $key ?>#
												</td>
												<td><strong><?= $vocabolo ?></strong></td>
											</tr>
											<?
										}
									?>
								</tbody>
							</table>
							<div class="clear"></div>
						</div>
    <?
			} else {
						echo "<h1>Impossibile accedere!</h1>";
						echo '<meta http-equiv="refresh" content="0;URL=/enti/">';
						die();
				}
	?>


<?
	include_once($root."/layout/bottom.php");
	?>
