<?
include_once '../../../config.php';
include_once "{$root}/layout/top.php";

if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albi_commissione",$_SESSION["codice_utente"]);
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
	$strsql = "SELECT * FROM b_albi_commissione WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_utente_ente OR codice_gestore = :codice_utente_ente) ";
	}
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		$operazione = "UPDATE";
		?>

				<script>
					function aggiungi_iscritto() {
						if ($(".edit-box").length < 50) {
							aggiungi('scheda.php','#iscritti');
						} else {
							alert("Troppi iscritti in modifica, procedere al salvataggio e riprovare");
						}
						return false;
					}

					function edit_iscritto(id) {
						if ($(".edit").length < 50) {
							data = "codice=" + id + "&codice_albo=<?= $record["codice"] ?>";
							$.ajax({
								type: "GET",
								url: "scheda.php",
								dataType: "html",
								data: data,
								async:false,
								success: function(script) {
									$("#iscritto_"+id).replaceWith(script);
								}
							});
							f_ready();
							etichette_testo();
						} else {
							alert("Troppi iscritti in modifica, procedere al salvataggio e riprovare");
						}
						return false;
					}
					function confirm_massive() {
						msg = "";
						if ($("#tipo").val()=="A") {
							msg = "aggiungerà gli iscritti del file csv all'elenco esistente";
						} else if ($("#tipo").val()=="R") {
							msg = "sostituirà l'elenco esistente con gli iscritti del file csv";
						}
						if (msg != "") {
							return confirm("L'operazione " + msg + ". Vuoi continuare?");
						} else {
							return false;
						}
					}
				</script>
				<h1>Iscritti</h1>
				<div class="box">
					<?
					if (isset($_POST["import_filechunk"])) {
						include("utility.php");
						$msg = importoCSV2Albo($_POST["import_filechunk"],$record["codice"],$_POST["tipo"]);
						if(strcmp($msg,'')!=0) echo '<script type="text/javascript">jalert("' . $msg . '"); </script>';
					} ?>
					<table width="100%">
						<tbody>
							<tr>
								<td style="text-align: center;vertical-align: middle;"><strong>Caricamento massivo </strong></td>
							</tr>
						</tbody>
					</table>
					<form action="index.php?codice=<?= $record["codice"] ?>" method="post" target="_self">
					<input type="hidden" name="codice_albo" value="<? echo $record["codice"]; ?>">
					<table class="dettaglio" width="100%">
						<tbody>
						<tr>
							<td width="20%">
								<img src="../../img/xls.png" alt="Modello iscritti"/><a href="modello.php" target="_blank" download style="vertical-align:super">Modello CSV</a>
							</td>
							<td width="10%">
								<div id="albo_import" rel="import" class="scegli_file"><span class="fa fa-folder-open" style="vertical-align:middle"></div>
							</td>
							<td width="50%">
								<input type="hidden" class="filechunk" id="filechunk_import" name="import_filechunk" title="Allegato">
								<input type="hidden" class="terminato" id="terminato_import" title="Termine upload">

								<div class="clear"></div>
								<div id="progress_bar_import" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

									<script>
										var uploader = new Array();
									</script>
								 <script type="text/javascript" src="/js/resumable.js"></script>
								 <script type="text/javascript" src="resumable-uploader.js"></script>
								 <script>
									tmp = (function($){
										return (new ResumableUploader($("#albo_import")));
									})(jQuery);
									uploader.push(tmp);
								</script>
							</td>
							<td style="15%">
								<select name="tipo" id="tipo" title="Tipo caricamento" rel="S;1;1;A">
									<option value="">Seleziona...</option>
									<option value="A">Aggiungi ad elenco</option>
									<option value="R">Sostituisci elenco</option>
								</select>
							</td>
							<td style="5%">
									<input type="submit" name="submit" value="Upload" onClick="return confirm_massive();">
							</td>
						</tr>
						</tbody>
					</table>
								</form>
				</div>
				<form name="box" method="post" action="save.php" rel="validate">
				 <input type="hidden" name="codice_albo" value="<? echo $record["codice"]; ?>">
				<div id="iscritti">
				<?
					$bind=array();
					$bind[":codice"] = $record["codice"];
					$sql = "SELECT * FROM b_commissari_albo WHERE attivo = 'S' AND codice_albo = :codice";
					$ris_iscritto = $pdo->bindAndExec($sql,$bind);
					$found =false;
					if ($ris_iscritto->rowCount() > 0) {
						?>
						<script>
							function filtra_albo(target) {
								if (typeof target !== undefined) {
									if ($("#button_"+target).hasClass('button-action')) {
										$("#button_"+target).removeClass('button-action');
										$("#button_"+target).addClass('btn-danger');
									} else {
										$("#button_"+target).removeClass('btn-danger');
										$("#button_"+target).addClass('button-action');
									}

									var interno;
									var esterno;

									if ($("#button_interno").hasClass('button-action')) interno = "S";
									if ($("#button_esterno").hasClass('button-action')) esterno = "S";

									$(".iscritto_view").show();

									if (interno!="S") $(".interno").hide();
									if (esterno!="S") $(".esterno").hide();

								}
							}
						</script>
						<table width="100%">
							<tr>
								<td colspan="4" class="etichetta">Filtri</td>
							</tr>
							<tr>
								<td width="50%">
									<button id="button_interno" data-target="interno" onClick="filtra_albo('interno'); return false;" class="filter_button submit_big button-action">Interni</button>
								</td>
								<td width="50%">
									<button id="button_esterno" data-target="esterno" onClick="filtra_albo('esterno'); return false;" class="filter_button submit_big button-action">Esterni</button>
								</td>
							</tr>
						</table>
						<?
						$found = true;
						while($record_iscritto = $ris_iscritto->fetch(PDO::FETCH_ASSOC)) {
							$id = $record_iscritto["codice"];
							include("view.php");
						}
					}
					if (!$found) {
						$record_iscritto = get_campi("b_commissari_albo");
						$id = "i_".rand();
						$new_line = true;
						include("scheda.php");
					}
				?>
				</div>
				<div>
					<button class="aggiungi" onClick="aggiungi_iscritto();return false;"><span class="fa fa-plus-circle fa-3x"></span><br>Aggiungi iscritto</button>
				</div>
				<input type="submit" class="submit_big" value="Salva">
				<a href="export.php?codice=<?= $record["codice"] ?>" target="_blank" class="submit_big btn-warning">Esporta CSV</a>
			</form>
							<?

			 include($root."/albi_commissione/ritorna.php");
		} else {
			echo "<h1>Albo non trovato</h1>";
		}
	} else {
		echo "<h1>Albo non trovato</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
