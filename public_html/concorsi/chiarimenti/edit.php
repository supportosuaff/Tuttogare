<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_concorso($codice_fase,$_GET["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}

	if (isset($_GET["codice"]) && isset($_GET["codice_gara"])) {

				$codice = $_GET["codice"];

				$bind = array();
				$bind[":codice"] = $codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$bind[":codice_gara"] = $_GET["codice_gara"];

				$strsql = "SELECT * FROM b_quesiti_concorsi WHERE codice = :codice ";
				$strsql .= " AND codice_ente = :codice_ente";
				$strsql .= " AND codice_gara = :codice_gara";

				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record_quesito = $risultato->fetch(PDO::FETCH_ASSOC);
					$bind = array();
					$bind[":codice"] = $codice;
					$bind[":codice_gara"] = $_GET["codice_gara"];
					$strsql = "SELECT * FROM b_risposte_concorsi WHERE codice_quesito = :codice AND codice_gara = :codice_gara";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$record_risposta = $risultato->fetch(PDO::FETCH_ASSOC);
						$operazione = "UPDATE";
					} else {
						$record_risposta = get_campi("b_risposte_concorsi");
						$record_risposta["quesito"] = $record_quesito["testo"];
						$operazione = "INSERT";
					}
?>
		<div class="clear"></div>
		<script>
			function confirmPubblication() {
				continua = false;
				valida($('#quesito'));
				if ($("#quesito").val() == "") {
					continua = confirm('La revisione quesito è vuota. Vuoi continuare?');
				} else if ($('#quesito_originale').html().trim() == $('#quesito').val()) {
					continua = confirm('Il quesito non è stato revisionato. Vuoi continuare?');
				} else {
					continua = true;
				}
				if (continua) {
					if ($('#pubblica_all').val()=="S") {
						return confirm('La risposta sarà pubblicata e trasmessa tramite pec agli OE interessati. Vuoi continuare?');
					} else {
						return true;
					}
				} else {
					return false;
				}
			}
		</script>
		<form name="box" method="post" action="save.php" rel="validate">
			<input type="hidden" name="codice" value="<? echo $record_risposta["codice"]; ?>">
			<input type="hidden" name="operazione" value="<? echo $operazione ?>">
			<input type="hidden" name="codice_gara" value="<? echo $_GET["codice_gara"] ?>">
			<input type="hidden" name="codice_quesito" value="<? echo $record_quesito["codice"] ?>">
			<input type="hidden" name="pubblica_all" id="pubblica_all" value="N">
			<div class="comandi">
			<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
			</div>
			<h1>RICHIESTA CHIARIMENTI</h1>
			<div class="box">
				<h2><b><u>Quesito Originale:</u></b></h2>
				<div style="padding:5px" id="quesito_originale"><?= $record_quesito["testo"] ?></div>
				<?
				if(! empty($record_quesito["cod_allegati"])) {
					$cod_allegati = implode(",", explode(";", $record_quesito["cod_allegati"]));
					$allegati = $pdo->bindAndExec("SELECT * FROM b_allegati WHERE codice IN ({$cod_allegati})")->fetchAll(PDO::FETCH_ASSOC);
					if(count($allegati) > 0) {
						?>
						<div class="padding"></div>
						<h4><u>Allegati alla richiesta:</u></h4>
						<table style="width: 100%; table-layout: fixed">
							<?
							foreach ($allegati as $allegato) {
								?>
								<tr>
									<td width="20"><img src="/img/<?= substr($allegato["nome_file"],-3) ?>.png"></td>
									<td><a href="/documenti/allegati/<?= $_GET["codice_gara"] ?>/<?= $allegato["nome_file"] ?>" target="_blank"><?= $allegato["titolo"] ?></a></td>
								</tr>
								<?
							}
							?>
						</table>
						<?
					}
				}
				?>
			</div>
			<div style="text-align:right"><strong><?= mysql2datetime($record_quesito["timestamp"]) ?></strong>
			<h2>Revisione Quesito</h2>
			<button class="submit_big btn-warning" onClick="$('#quesito').val($('#quesito_originale').html()); return false;">Importa testo quesito</button>
			<textarea rows='10' class="ckeditor_full" name="quesito" cols='80' id="quesito" title="Quesito" rel="N;3;0;A"><? echo (!empty($record_risposta["quesito"])) ? $record_risposta["quesito"] : ""; ?></textarea><br>
			<h2>Risposta Quesito</h2>
			<textarea rows='10' class="ckeditor_full" name="testo" cols='80' id="testo" title="Testo" rel="S;3;0;A"><? echo $record_risposta["testo"]; ?></textarea>
			<input type="submit" class="submit_big" onclick="$('#pubblica_all').val('N'); return confirmPubblication();" value="Salva e rispondi al richiedente">
			<input type="submit" class="submit_big" style="background-color:#0C0" onclick="$('#pubblica_all').val('S'); return confirmPubblication();" value="Salva e pubblica">
    </form>
    <div class="clear"></div>
    <?

			} else {

				echo "<h1>Quesito non trovato</h1>";

				}
			} else {

				echo "<h1>Quesito non trovato</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
