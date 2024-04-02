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
		include($root."/dgue/config.php");
		?>
		<h1>CONFIGURAZIONE DGUE</h1>
		<div style="text-align:right">
			<button type="button" onClick="$('.dgue-checkBox').prop('checked','checked').attr('checked','checked')">Seleziona Tutto</button>
			<button type="button" onClick="$('.dgue-checkBox').removeProp('checked').removeAttr('checked')">Deseleziona Tutto</button>
		</div>
		<?
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		$ris_form = getDGUECriteria($record["norma"]);
		if (!empty($ris_form)) {

			$gruppo = "";

			$bind = array();
			$bind[":codice_gara"] = $record["codice"];
			$percentuale = "50";
			$sql_check = "SELECT * FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'gare'";
			$ris_check = $pdo->bindAndExec($sql_check,$bind);
			if ($ris_check->rowCount() > 0) {
				if (!$lock) {
					$percentuale = "33";
					?>
					<div style="float:left; width:<?= $percentuale ?>%">
						<button onClick="elimina('<? echo $record["codice"] ?>','gare/dgue');" class="submit_big" style="background-color:#c00"><h4 style="text-align:center"><span class="fa fa-remove fa-2x"></span> Elimina DGUE</h4></button>
					</div>
					<?
				}
				?>
				<div style="float:left; width:<?= $percentuale ?>%">
					<a href="/dgue/getRequestPDF.php?codice_riferimento=<?= $record["codice"] ?>&sezione=gare" class="submit_big" style="background-color:#900"><h4 style="text-align:center"><span class="fa fa-file-pdf-o fa-2x"></span> Download PDF</h4></a>
				</div>
				<div style="float:left; width:<?= $percentuale ?>%">
					<a href="/dgue/getRequestXML.php?codice_riferimento=<?= $record["codice"] ?>&sezione=gare" class="submit_big" style="background-color:#066"><h4 style="text-align:center"><span class="fa fa-code fa-2x"></span> Download XML</h4></a>
				</div>
				<div class="clear"></div><br>
				<?
			}
			?>
			<form name="box" method="post" action="save.php" rel="validate">
				<input type="hidden" name="codice_gara" value="<?= $record["codice"]; ?>">
				<table width="100%">
					<?
				foreach($ris_form AS $form) {
					if ($form["livello2"] != "" && $form["obbligatorio"] == "N") {
						$form["selezionato"] = "";

						$bind = array();
						$bind[":codice_gara"] = $record["codice"];
						$bind[":codice_form"] = $form["codice"];
						$sql_selezionato = "SELECT * FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'gare' AND codice_form = :codice_form";
						$ris_selezionato = $pdo->bindAndExec($sql_selezionato,$bind);
						if ($ris_selezionato->rowCount() > 0) $form["selezionato"] = true;
						if ($gruppo != $form["livello1"]) {
							$gruppo = $form["livello1"];
							?>
							<tr>
								<th colspan="2"><h3><?= $dgue_translate_gruppi[$gruppo]['it'] ?></h3></th>
							</tr>
							<?
						}
						?>
						<tr>
							<td width="10"><input type="checkbox" class='dgue-checkBox' title="<?= $form["nome"] ?>" name="form[<?= $form["codice"] ?>]" value="<?= $form["codice"] ?>" <?= (!empty($form["selezionato"])) ? "checked='checked'" : "" ?>></td>
							<td><strong><?= $form["nome"] ?></strong><br>
								<?= $form["descrizione"] ?>
							</td>
						</tr>
						<?
					}
				}
				?>
				</table>
				<?
				if ($ris_check->rowCount() > 0) {
					if (!$lock) {
						?>
						<div style="float:left; width:<?= $percentuale ?>%">
							<button onClick="elimina('<? echo $record["codice"] ?>','gare/dgue');" type="button" class="submit_big" style="background-color:#c00"><h4 style="text-align:center"><span class="fa fa-remove fa-2x"></span> Elimina DGUE</h4></button>
						</div>
						<?
					}
					?>
					<div style="float:left; width:<?= $percentuale ?>%">
						<a href="/dgue/getRequestPDF.php?codice_riferimento=<?= $record["codice"] ?>&sezione=gare" type="button" class="submit_big" style="background-color:#900"><h4 style="text-align:center"><span class="fa fa-file-pdf-o fa-2x"></span> Download PDF</h4></a>
					</div>
					<div style="float:left; width:<?= $percentuale ?>%">
						<a href="/dgue/getRequestXML.php?codice_riferimento=<?= $record["codice"] ?>&sezione=gare" type="button" class="submit_big" style="background-color:#066"><h4 style="text-align:center"><span class="fa fa-code fa-2x"></span> Download XML</h4></a>
					</div>
					<div class="clear"></div><br>
					<?
				}
				?>
				<? if (!$lock) { ?>
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
                ?>

			    <? include($root."/gare/ritorna.php"); ?>

			</form>
			<?
		} else {
			?>
			<h2 class="errore">PRESET DGUE NON TROVATO - Contattare l'assistenza</h2>
			<?
		}
	} else {
		echo "<h1>Gara non trovata</h1>";
	}

	include_once($root."/layout/bottom.php");
	?>
