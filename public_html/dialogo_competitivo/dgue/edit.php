<?
include_once("../../../config.php");
include_once($root."/layout/top.php");
$edit = false;
$lock = true;
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
	$edit = check_permessi("dialogo_competitivo",$_SESSION["codice_utente"]);
	if (!$edit) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
} else {
	echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
	die();
}
if (isset($_GET["codice"])) {
	$codice = $_GET["codice"];
	$bind = array();
	$bind[":codice"]=$codice;
	$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$strsql = "SELECT * FROM b_bandi_dialogo WHERE codice = :codice ";
	$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
	if ($_SESSION["gerarchia"] > 0) {
		$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
		$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
	}
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		include($root."/dgue/config.php");
		?>
		<h1>CONFIGURAZIONE DGUE</h1> 		<div style="text-align:right"> 			<button type="button" onClick="$('.dgue-checkBox').prop('checked','checked').attr('checked','checked')">Seleziona Tutto</button> 			<button type="button" onClick="$('.dgue-checkBox').removeProp('checked').removeAttr('checked')">Deseleziona Tutto</button> 		</div>
		<?
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		?>
		<h3><?= $record["oggetto"] ?></h3><br>
		<?
		$ris_form = getDGUECriteria(findDGUEVersion($record["data_pubblicazione"]));
		if (!empty($ris_form)) {
			$gruppo = "";
			$bind = array();
			$bind[":codice_gara"] = $record["codice"];

			$percentuale = "33";
			$sql_check = "SELECT * FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'dialogo'";
			$ris_check = $pdo->bindAndExec($sql_check,$bind);
			if ($ris_check->rowCount() > 0) {
				?>
				<div style="float:left; width:<?= $percentuale ?>%">
					<button onClick="elimina('<? echo $record["codice"] ?>','dialogo_competitivo/dgue');" class="submit_big" style="background-color:#c00"><h4 style="text-align:center"><span class="fa fa-remove fa-2x"></span> Elimina DGUE</h4></button>
				</div>
				<div style="float:left; width:<?= $percentuale ?>%">
					<a href="/dgue/getRequestPDF.php?codice_riferimento=<?= $record["codice"] ?>&sezione=dialogo" class="submit_big" style="background-color:#900"><h4 style="text-align:center"><span class="fa fa-file-pdf-o fa-2x"></span> Download PDF</h4></a>
				</div>
				<div style="float:left; width:<?= $percentuale ?>%">
					<a href="/dgue/getRequestXML.php?codice_riferimento=<?= $record["codice"] ?>&sezione=dialogo" class="submit_big" style="background-color:#066"><h4 style="text-align:center"><span class="fa fa-code fa-2x"></span> Download XML</h4></a>
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
						$sql_selezionato = "SELECT * FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'dialogo' AND codice_form = :codice_form";
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
							<td width="10"><input type="checkbox" name="form[<?= $form["codice"] ?>]" value="<?= $form["codice"] ?>" class='dgue-checkBox'  <?= (!empty($form["selezionato"])) ? "checked='checked'" : "" ?>></td>
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
	?>
	<div style="float:left; width:<?= $percentuale ?>%">
		<button onClick="elimina('<? echo $record["codice"] ?>','dialogo_competitivo/dgue');" class="submit_big" style="background-color:#c00"><h4 style="text-align:center"><span class="fa fa-remove fa-2x"></span> Elimina DGUE</h4></button>
	</div>
	<div style="float:left; width:<?= $percentuale ?>%">
		<a href="/dgue/getRequestPDF.php?codice_riferimento=<?= $record["codice"] ?>&sezione=dialogo" class="submit_big" style="background-color:#900"><h4 style="text-align:center"><span class="fa fa-file-pdf-o fa-2x"></span> Download PDF</h4></a>
	</div>
	<div style="float:left; width:<?= $percentuale ?>%">
		<a href="/dgue/getRequestXML.php?codice_riferimento=<?= $record["codice"] ?>&sezione=dialogo" class="submit_big" style="background-color:#066"><h4 style="text-align:center"><span class="fa fa-code fa-2x"></span> Download XML</h4></a>
	</div>
	<div class="clear"></div><br>
	<?
}
?>
                <input type="submit" class="submit_big" value="Salva">
                </form>

			    <? include($root."/dialogo_competitivo/ritorna.php"); ?>

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
}
	include_once($root."/layout/bottom.php");
	?>
