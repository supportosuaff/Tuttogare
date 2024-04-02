<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase("","/gare/apribuste/edit.php");
		if ($codice_fase !== false && check_permessi("conference",$_SESSION["codice_utente"])) {
			$esito = check_permessi_gara($codice_fase,$_GET["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
		}
		if ($edit)
		{
			$strsql  = "SELECT b_emendamenti.* FROM b_emendamenti
						WHERE codice_gara = :codice_gara AND codice_partecipante = :codice_partecipante
						AND codice = :codice AND aperto = 'S'";
			$risultato = $pdo->bindAndExec($strsql,[":codice_gara"=>$_GET["codice_gara"],":codice_partecipante"=>$_GET["codice_partecipante"],":codice"=>$_GET["codice_emendamento"]]);
			if ($risultato->rowCount() > 0) {
				$emendamento = $risultato->fetch(PDO::FETCH_ASSOC);
				?>
				<strong>Descrizione della richiesta</strong>
				<div style="float:right">
					<a href="/allegati/download_allegato.php?codice=<? echo $emendamento["codice_allegato"] ?>" title="Scarica Allegato">
						<img src="/img/download.png" alt="Scarica Allegato" width="25">
					</a>
					<a href="/allegati/open_p7m.php?codice=<? echo $emendamento["codice_allegato"] ?>" title="Estrai Contenuto">
						<img src="/img/p7m.png" alt="Download Allegato" width="25">
					</a>
				</div>
				<div class="clear"></div>
				<? if ($emendamento["accettato"] == "S") { ?>
					<div class="box" style="background-color:#0C0">
						<div style="text-align:center"><strong>Accettato</strong></div>
					</div>
				<? } ?>
				<? if (!empty($emendamento["motivazione"])) { ?>
					<div class="box errore">
						<div style="text-align:center"><strong>Rifiutato</strong></div>
						<?= $emendamento["motivazione"] ?>
					</div>
				<? } ?>
				<div class="box">
					<?= $emendamento["descrizione"] ?>
				</div>
				
				<? if ($emendamento["accettato"] != "S") { ?>
					<div class="esito-emendamento" style="width:49%; float:left;">
						<form action="/gare/apribuste/valuta-emendamento.php" rel="validate">
							<input type="hidden" name="codice_gara" value="<?= $emendamento["codice_gara"] ?>">
							<input type="hidden" name="codice" value="<?= $emendamento["codice"] ?>">
							<input type="hidden" name="accettato" value="S">
							<button type="submit" class="submit_big" style="background-color:#0C0">Accetta</button>
						</form>
					</div>
				<? } ?>
				<? if ($emendamento["accettato"] != "N") { ?>
					<div class="esito-emendamento" style="width:49%; float:right;">
						<button type="button" onclick="$('.esito-emendamento').slideToggle();" class="submit_big" style="background-color:#C00">Rifiuta</button>
					</div>
					<div class="clear"></div>
					<div class="esito-emendamento" style="display:none">
						<button type="button" onclick="$('.esito-emendamento').slideToggle();" class="submit_big" style="background-color:#CCC">Annulla</button>
						<form action="/gare/apribuste/valuta-emendamento.php" rel="validate">
							<input type="hidden" name="codice_gara" value="<?= $emendamento["codice_gara"] ?>">
							<input type="hidden" name="codice" value="<?= $emendamento["codice"] ?>">
							<input type="hidden" name="accettato" value="N">
							<br><br>
							<strong>Motivazione rifiuto*</strong>
							<textarea title="motivazione" name="motivazione" rows="5" style="width:99%" rel="S;0;0;A"><?= $emendamento["motivazione"] ?></textarea>
							<button type="submit" class="submit_big" style="background-color:#C00">Rifiuta</button>
						</form>
					</div>
				<? } ?>
				<div class="clear"></div>
				<script>
					f_ready();
				</script>
				<?
				die();
			}
		}
	} 
	header("403 Forbidden");
	echo "<h1>Permesso negato</h1>"
?>
