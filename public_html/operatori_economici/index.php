<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("operatori_economici",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	echo "<h1>GESTIONE OPERATORI ECONOMICI</h1>";
		$bind =array();
		$strsql  = "SELECT b_utenti.*, b_gruppi.gruppo AS tipo, b_operatori_economici.ragione_sociale, b_operatori_economici.partita_iva, b_operatori_economici.codice_fiscale_impresa ";
		$strsql .= "FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice LEFT JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
		if (isset($_SESSION["ente"])) $strsql.="JOIN r_enti_operatori ON r_enti_operatori.cod_utente = b_utenti.codice ";
		$strsql.= "WHERE b_gruppi.gerarchia > 2 ";
		if (isset($_SESSION["ente"])) {
			 $bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
			 $strsql.=" AND r_enti_operatori.cod_ente = :codice_ente";
		}
		$strsql .= " GROUP BY b_operatori_economici.codice ORDER BY ragione_sociale,cognome,nome,dnascita" ;
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

		if ($_SESSION["amministratore"] && isset($_SESSION["ente"])) {
			?>
			<button class="submit_big" style="background-color: #FD8008; margin-bottom: 10px;" onclick="$('#importazione_massiva_oe').toggle('fast');">IMPORTAZIONE MASSIVA OPERATORI ECONOMICI</button>
			<div id="importazione_massiva_oe" class="box" style="display: none;">
				<h2>Inserimento massivo</h2>
				<script type="text/javascript" src="/js/resumable.js"></script>
				<script type="text/javascript" src="resumable-uploader-massive.js"></script>
				<form action="massive.php" method="post" rel="validate" target="_blank" enctype="multipart/form-data">
					<div class="box">
						<strong>Sorgente</strong><br>
						<input type="radio" name="source" value="csv" onClick="$('.source-type').hide(); $('#source-csv').show();"> CSV
						<input type="radio" name="source" value="jsonDPA" onClick="$('.source-type').hide(); $('#source-digitalPA').show();"> JSON DigitalPA - Formato ZIP
						<input type="hidden" class="filechunk" id="filechunk_digitalPA" name="filechunk" title="Allegato"><br><br>
						<div class=" source-type" style="display:none" id="source-csv">
							<img src="/img/xls.png" alt="Modello lotti" style="vertical-align:middle"/><a href="dl-modello.php">Modello CSV</a>
							<input type="file" name="utenti" id="file">
						</div>
						<div class="source-type" style="display:none" id="source-digitalPA">
							<div id="modulistica_digitalPA" rel="digitalPA" class="scegli_file"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
							<div id="progress_bar_digitalPA" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
						</div>
					</div>
					<script>
						tmp = (function($){
							return (new ResumableUploader($("#modulistica_digitalPA")));
						})(jQuery);
					</script>
					<div class="box">
						<strong>Elenco di riferimento</strong>
						<?
							$elenchi = array("albo"=>"Elenco fornitori");
							?>
							<select name="elenco" title="Elenco di riferimento">
								<option value="">Nessuno</option>
								<?
								$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
								foreach ($elenchi AS $elenco => $etichetta) {
									$sql_bando  = "SELECT b_enti.denominazione, b_bandi_{$elenco}.*, beneficiario.denominazione AS benef
																FROM b_bandi_{$elenco} JOIN b_enti ON b_bandi_{$elenco}.codice_gestore = b_enti.codice
																JOIN b_enti AS beneficiario ON b_bandi_{$elenco}.codice_ente = beneficiario.codice
																WHERE b_bandi_{$elenco}.codice_gestore = :codice_ente
																ORDER BY " ;
									if ($elenco == "albo") {
										$sql_bando .= "b_bandi_{$elenco}.manifestazione_interesse ASC, ";
									}
									$sql_bando .= "b_bandi_{$elenco}.oggetto, b_bandi_{$elenco}.codice DESC";
									$risultato_bando = $pdo->bindAndExec($sql_bando,$bind);
									if ($risultato_bando->rowCount()>0) {
										$change = false;
										?>
										<optgroup label="<?= $etichetta ?>">
										<?
										while($bando = $risultato_bando->fetch(PDO::FETCH_ASSOC)) {
											if (isset($bando["manifestazione_interesse"])) {
												if ($bando["manifestazione_interesse"] == "S") {
													$etichetta = "Indagine di mercato";
													if (!$change) {
														$change = true;
														?>
														</optgroup>
														<?
														?>
														<optgroup label="<?= $etichetta ?>">
														<?
													}
													
												} else {
													$etichetta = "Elenco fornitori";
												}
											}
											?>
											<option value="<?= $bando["codice"] ?>">
												<?= $bando["oggetto"] ?> - Scadenza: <?= mysql2date($bando["data_scadenza"]) ?>
												<?
													if ($bando["codice_gestore"] != $_SESSION["ente"]["codice"]) {
														echo " - Gestore: " . $bando["denominazione"];
													}
													if ($bando["codice_ente"] != $_SESSION["ente"]["codice"]) {
														echo " - Beneficiario: " . $bando["benef"];
													}
												?>
											</option>
											<?
										}
										?>
										</optgroup>
										<?
									}
								}
								?>
							</select>
						</td>
					</tr>
					<? if (!empty($_POST["oeManager"]["elenco"])) { ?>
					<script>
						$("#oeManager-elenco").val('<? echo $_POST["oeManager"]["elenco"] ?>');
					</script>
					<? } ?>
					</div>
					<div class="box">
						<strong>Oggetto:</strong>
						<input type="text" style="width: 100%;" name="oggetto" value="Pre-iscrizione operatore economico" rel="S;5;0;A">
					</div>
					<div class="box">
						<strong>Messaggio di comunicazione per l&#39;operatore economico:</strong>
						<textarea name="messaggio" id="messaggio" rel="N;0;0;A" class="ckeditor_full" title="Comunicazione">
							Spett.le operatore economico,<br><br>
							sulla piattaforma di e-procuremente di <b><?= $_SESSION["ente"]["denominazione"] ?></b> disponibile all'indirizzo url: <a href="https://<?= $_SERVER["SERVER_NAME"] ?>">https://<?= $_SERVER["SERVER_NAME"] ?></a> &egrave; stata creata un&#39;utenza in stato di <u>pre-iscrizione</u> relativa alla sua azienda.<br>
							Per procedere al completamento della sua registrazione e partecipare alle gare telematiche di <b><?= $_SESSION["ente"]["denominazione"] ?></b> la invitiamo a cliccare o copiare ed incolla nel browser il seguente link per scegliere una password:<br><br>[LINK-PASSWORD]<br><br>
							Una volta che avrà impostato la password di acceso potr&agrave; effettuare l&#39;accesso alla piattaforma tramite l&#39;url: <a href="https://<?= $_SERVER["SERVER_NAME"] ?>">https://<?= $_SERVER["SERVER_NAME"] ?></a><br><br>
							Le ricordiamo che <b><?= $_SESSION["ente"]["denominazione"] ?></b> ricorrer&agrave; alla metodologia di gara e affidamento telematici, pertanto al fine di poter esser invitati a partecipare alle procedure indette sar&agrave; necessario perfezionare l&#39;accreditamento unitamente all&#39;invio dell&#39;istanza per la registrazione agli elenchi fornitori dell&#39;ente (disponibili al link: <a href="https://<?= $_SERVER["SERVER_NAME"] ?>/archivio_albo/">https://<?= $_SERVER["SERVER_NAME"] ?>/archivio_albo/</a>).
						</textarea>
					</div>
					<button type="submit" id="upload_operatori" class="submit_big">UPLOAD OPERATORI</button>
				</form>
			</div>
			
			<?
		}
	if ($risultato->rowCount()>0) {
		?>
        <table style="text-align:center; width:100%; font-size:0.8em" id="utenti">
        <thead>
					<tr>
						<th width="5"></th>
						<th width="5"></th>
						<th>Ragione Sociale</th>
						<th>Nominativo</th>
						<th width="10">Tipo</th>
						<th width="100">Partita IVA</th>
						<th width="100">Codice Fiscale</th>
						<th>Timestamp</th>
						<? if (!isset($_SESSION["ente"])) { ?>
							<th>Enti</th>
						<? } ?>
						<th width="10">Modifica</th>
						<? if (isset($_SESSION["ente"])) { ?>
							<th width="10">Rigenera Password</th>
							<th width="10">Reinvia</th>
						<? } ?>
						<th width="10">Attiva Disattiva</th></tr>
        </thead>
        <tbody>
        </tbody>
				 </table>
				 <div style="text-align:right">
					<a href="export.php" target="_blank">Esporta CSV</a>
				</div>
				<script>
				function rigenera(id){
					$.ajax({
						type: "GET",
						url: "/user/rigenera.php",
						data: {id: id},
						success: function(e) {
							jalert(e);
						}
					});
				}
				function reinvia(id){
					$.ajax({
						type: "GET",
						url: "reinvia_conferma.php",
						data: {id: id},
						success: function(e) {
							jalert(e);
						}
					});
				}
				var elenco = $("#utenti").DataTable({
					"processing": true,
					"serverSide": true,
					"language": {
							"url": "/js/dataTables.Italian.json"
					},
					"ajax": {
						"url": "table.php",
						"method": "POST",
					},
					"drawCallback": function( settings ) {

					},
					"pageLength": 50,
					"lengthMenu": [
							[5, 10, 25, 50, 100, 200, -1],
							[5, 10, 25, 50, 100, 200, "Tutti"]
					]
				});
				</script>
         <div class="clear"></div>
          <?
	}		 else {
?><h1 style="text-align:center">
<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
}

	include_once($root."/layout/bottom.php");
	?>
