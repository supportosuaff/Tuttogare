<?
include_once("../../../config.php");
include_once($root."/layout/top.php");
$edit = false;
$lock = true;
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
	if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
		if ($codice_fase!==false) {
			$esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
	$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
	}
	$risultato = $pdo->bindAndExec($strsql,$bind);

	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		$codice_gara = $record["codice"];
		$operazione = "UPDATE";
		$estrazione = false;
		?>
		<h1>COMMISSIONE</h1>
		<? if (!$lock) { ?>
				<script>
					var uploader = new Array();
				</script>
				<script type="text/javascript" src="/js/resumable.js"></script>
				<script type="text/javascript" src="resumable-uploader.js"></script>
				<div id="estrai_commissione" class="box" style="display:none">
					<h2>Estrazione</h2>
					<?
					$bind=array();
					$bind[":codice_gara"]=$codice_gara;
					$sql_estrazione = "SELECT * FROM b_estrazioni_commissioni_concorsi WHERE codice_gara = :codice_gara";
					$ris_estrazione = $pdo->bindAndExec($sql_estrazione,$bind);
					if ($ris_estrazione->rowCount() > 0) {
						include("report.php");
					} else {
						$sql = "SELECT b_albi_commissione.* FROM b_albi_commissione JOIN b_commissari_albo ON b_albi_commissione.codice = b_commissari_albo.codice_albo
										WHERE b_albi_commissione.codice_gestore = :codice_ente AND b_commissari_albo.attivo = 'S' AND codice_gara IS NULL GROUP BY b_albi_commissione.codice ORDER BY b_albi_commissione.codice DESC";
						$ris_albi = $pdo->bindAndExec($sql,array(":codice_ente"=>$_SESSION["ente"]["codice"]));
						$albi_cpv = array();
						$albi_other = array();
						if ($ris_albi->rowCount() > 0) {
							while($albo = $ris_albi->fetch(PDO::FETCH_ASSOC)) {
								$sql_cpv = "SELECT r_cpv_concorsi.* FROM r_cpv_concorsi
														WHERE r_cpv_concorsi.codice_gara = :codice_gara AND r_cpv_concorsi.codice IN (SELECT r_cpv_albi_commissione.codice FROM r_cpv_albi_commissione WHERE r_cpv_albi_commissione.codice_bando = :codice_albo) ";
								$check_cpv = $pdo->bindAndExec($sql_cpv,array(":codice_gara"=>$record["codice"],":codice_albo"=>$albo["codice"]));
								if($check_cpv->rowCount() > 0) {
									$albi_cpv[] = $albo;
								} else {
									$albi_other[] = $albo;
								}
							}
						}
						?>
						<form name="box" method="post" action="estrai.php" rel="validate">
							<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
							<table width="100%">
								<tr>
									<td class="etichetta">Albo di riferimento</td>
									<td colspan="3">
										<div style="width:700">
											<select name="codice_albo" id="codice_albo" title="Albo di riferimento" rel="S;0;0;N" onChange="if($(this).val()==-1) { $('#import_list').slideDown(); } else { $('#import_list').slideUp(); }">
												<option value="">Seleziona...</option>
												<? if (count($albi_cpv) > 0) { ?>
													<optgroup label="Albi compatibili">
														<? foreach($albi_cpv AS $albo) { ?>
															<option value="<?= $albo["codice"] ?>"><?= $albo["oggetto"] ?></option>
														<? } ?>
													</optgroup>
												<? }
												if (count($albi_other) > 0) { ?>
													<optgroup label="Altri albi">
														<? foreach($albi_other AS $albo) { ?>
															<option value="<?= $albo["codice"] ?>"><?= $albo["oggetto"] ?></option>
														<? } ?>
													</optgroup>
												<? } ?>
												<optgroup label="Crea nuovo">
													<option value="-1">Importa elenco</option>
												</optgroup>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td class="etichetta">Componenti</td>
									<td>
										<select name="componenti" id="componenti" title="Componenti" rel="S;1;1;N">
											<option value="">Seleziona</option>
											<option>3</option>
											<option>5</option>
										</select>
									</td>
									<td class="etichetta">Interni</td>
									<td width="25%">
										<input type="text" name="interni" id="interni" title="Componenti interni" rel="S;1;1;N;componenti;<">
									</td>
								</tr>
								<tr id="import_list" style="display:none">
									<td colspan="4">
										<table class="dettaglio" width="100%">
											<tbody>
											<tr>
												<td width="20%">
													<img src="../../img/xls.png" alt="Modello iscritti"/><a href="/albi_commissione/iscritti/modello.php" target="_blank" download style="vertical-align:super">Modello CSV</a>
												</td>
												<td width="10%">
													<div id="albo_import" rel="import" class="scegli_file"><span class="fa fa-folder-open" style="vertical-align:middle"></div>
												</td>
												<td>
													<input type="hidden" class="filechunk" id="filechunk_import" name="import_filechunk" title="Allegato">
													<input type="hidden" class="terminato" id="terminato_import" title="Termine upload">

													<div class="clear"></div>
													<div id="progress_bar_import" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

													 <script>
														tmp = (function($){
															return (new ResumableUploader($("#albo_import")));
														})(jQuery);
														uploader.push(tmp);
													</script>
												</td>
											</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4">
										<input type="submit" class="submit_big" value="Estrai">
									</td>
								</tr>
							</table>
						</form>
						<?
					}
				?>
				</div>
				<button class="submit_big btn-warning" onClick="$('#estrai_commissione').slideToggle(); return false;">
					Estrazione commissione
				</button>
		<form name="box" method="post" action="save.php" rel="validate">
			<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
			<div class="comandi">
				<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			</div>
			<? } ?>

			<div style="text-align:right" class="box">
				<h2 style="text-align:right">Atto di costituzione</h2>
				<table style="float:right">
					<tr>
						<td class="etichetta">Numero</td>
						<td>
							<input type="text" name="numero_atto_commissione" id="numero_atto_commissione" value="<? echo $record["numero_atto_commissione"] ?>" title="Numero atto" rel="S;1;50;A">
						</td>
						<td rowspan="2" class="etichetta">Allegato</td>
						<td rowspan="2">
							<input type="hidden" name="existing_atto" value="<?= $record["allegato_atto_commissione"] ?>" title="Allegato">

							<input type="hidden" class="filechunk" id="filechunk_atto" name="atto_filechunk" title="Allegato">
							<input type="hidden" class="terminato" id="terminato_atto" title="Termine upload">
							<div id="nome_file_atto" style="text-align:center">
								<? if (!empty($record["allegato_atto_commissione"])) {
									$sql = "SELECT * FROM b_allegati WHERE codice = :codice_allegato";
									$ris_allegato = $pdo->bindAndExec($sql,array(":codice_allegato"=>$record["allegato_atto_commissione"]));
									if ($ris_allegato->rowCount() > 0) {
										$allegato = $ris_allegato->fetch(PDO::FETCH_ASSOC);
										$percorso_html = "/documenti/allegati/concorsi/". $allegato["codice_gara"] . "/" . $allegato["nome_file"];
										$percorso_fisico = $config["pub_doc_folder"] . "/allegati/concorsi/". $allegato["codice_gara"] . "/" . $allegato["riferimento"];
										if (file_exists($percorso_fisico)) {
											$estensione = explode(".",$allegato["nome_file"]);
											$estensione = end($estensione);
											?>
											<a href="<?= $percorso_html ?>" target="_blank" title="Allegato">
											<?
												if (file_exists($root."/img/".$estensione.".png")) { ?>
													<img src="/img/<? echo $estensione ?>.png" alt="File <? echo $estensione ?>" style="vertical-align:middle">
												<? } else {
													echo $allegato["nome_file"];
												 }
											?>
											</a>
											<?
										}
									}
								}
								?>
							</div>
							<div id="modulistica_atto" rel="atto" class="scegli_file"><span class="fa fa-folder-open" style="vertical-align:middle"></div>
							<div class="clear"></div>
							<div id="progress_bar_atto" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

							 <script>
								tmp = (function($){
									return (new ResumableUploader($("#modulistica_atto")));
								})(jQuery);
								uploader.push(tmp);
							</script>
						</td>
					</tr>
					<tr>
						<td class="etichetta">Data</td>
						<td>
							<input type="text" class="datepick" name="data_atto_commissione" id="data_atto_commissione" value="<? echo mysql2date($record["data_atto_commissione"]) ?>" title="Data atto" rel="S;10;10;D">
						</td>
					</tr>
				</table>
				<div class="clear"></div>
			</div>
			<table width="100%">
				<thead>
				<tr><td>Cognome</td><td>Nome</td><td>Ruolo</td><td>E-mail</td><td>Valutatore</td><td>CV</td><td></td></tr>
				</thead>
				<tbody  id="commissione">
					<?
					$bind = array();
					$bind[":codice"]=$record["codice"];
					$sql = "SELECT * FROM b_commissioni_concorsi WHERE codice_gara = :codice";
					$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
					if ($ris_partecipanti->rowCount()>0) {
						while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
							$id = $record_partecipante["codice"];
							$send = false;
							include("tr_commissione.php");
						}
					} else {
						if ($estrazione===false) {
							$send = false;
							$record_partecipante = get_campi("b_commissioni_concorsi");
							$id = "i_".rand();
							include("tr_commissione.php");
						} else {
							$sql = "SELECT b_commissari_albo.*, r_estrazioni_commissioni_concorsi.presidente FROM
											b_commissari_albo JOIN r_estrazioni_commissioni_concorsi ON b_commissari_albo.codice =  r_estrazioni_commissioni_concorsi.codice_commissario
											WHERE r_estrazioni_commissioni_concorsi.codice_estrazione = :codice_estrazione AND selezionato = 'S' ORDER BY presidente DESC ";
							$ris_commissari = $pdo->bindAndExec($sql,array(":codice_estrazione"=>$estrazione["codice"]));
							if ($ris_commissari->rowCount() > 0) {
								$alert_salvataggio = true;
								while($commissario = $ris_commissari->fetch(PDO::FETCH_ASSOC)) {
									$send = false;
									$record_partecipante = get_campi("b_commissioni_concorsi");
									$id = "i_".rand();
									$record_partecipante["cognome"] = $commissario["cognome"];
									$record_partecipante["nome"] = $commissario["nome"];
									$record_partecipante["pec"] = $commissario["email"];
									if ($commissario["presidente"]=="S") $record_partecipante["ruolo"] = "PRESIDENTE";
									include("tr_commissione.php");
								}
							}
						}
					}
					?>
				</tbody></table>
					<? if ($estrazione===false) { ?>
						<div>
						<button class="aggiungi" onClick="aggiungi('tr_commissione.php?codice_gara=<? echo $record["codice"] ?>','#commissione');return false;"><span class="fa fa-plus-circle fa-2x" style="color:#0c0; vertical-align:middle" title="Aggiungi partecipante"></span> Aggiungi partecipante</button></div>
					<? } ?>
					<? if (!$lock) {
						if (isset($alert_salvataggio)) {?>
							<h3 class="ui-state-error" style="text-align:center">
								<strong>ATTENZIONE:</strong> E' necessario procedere al salvataggio dei dati.
							</h3>
						<? } ?>
					<input type="submit" class="submit_big" value="Salva">
				</form>

				<?
			} else {
				?>
				<script>
					$("#contenuto_top :input").not('.espandi').prop("disabled", true);
				</script>
				<?
			}
			include($root."/concorsi/ritorna.php");
			if (isset($alert_salvataggio)) { ?>
				<script>
					modifica = true;
				</script>
			<? }
		} else {

			echo "<h1>Gara non trovata</h1>";

		}
	} else {

		echo "<h1>Gara non trovata</h1>";

	}

	?>
	<script type="text/javascript">
		function rigenera(codice)
		{
			event.preventDefault();
			msg = "Stai per rigenerare le credenziali per questo valutatore. Confermi?";
			function conferma_rigenera() {
				$("#wait").show();
				$.ajax({
					type: "POST",
					url: "/concorsi/commissione/rigenera.php",
					data: "codice="+codice,
					dataType: "script"
				}).done(function(script) {
                	$("#wait").fadeOut('fast');
                	script;
              	}).error(function() {
              		$("#wait").fadeOut('fast');
              		alert("Errore, si prega di riprovare...");
              	});
			}
			jconfirm(msg,conferma_rigenera);
			return false;
		}
	</script>
	<?
	include_once($root."/layout/bottom.php");
	?>
