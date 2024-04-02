<?
include_once("../../config.php");
include_once($root."/layout/top.php");
$edit = false;
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
	$edit = check_permessi("dgue_ca",$_SESSION["codice_utente"]);
	if (!$edit) {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
} else {
	echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
	die();
}
if (isset($_GET["codice"]) ) {
	$codice = $_GET["codice"];
	if ($codice > 0) {
		$bind = array();
		$bind[":codice"]=$codice;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql = "SELECT * FROM b_dgue_free WHERE codice = :codice ";
		$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		if ($_SESSION["gerarchia"] > 0) {
			$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
			$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
		}
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0) {
			$record = $risultato->fetch(PDO::FETCH_ASSOC);
		}
	} else {
		$record = get_campi("b_dgue_free");
	}
	if (!empty($record)) {
		include($root."/dgue/config.php");
		?>
		<h1>CONFIGURAZIONE DGUE</h1> 		<div style="text-align:right"> 			<button type="button" onClick="$('.dgue-checkBox').prop('checked','checked').attr('checked','checked')">Seleziona Tutto</button> 			<button type="button" onClick="$('.dgue-checkBox').removeProp('checked').removeAttr('checked')">Deseleziona Tutto</button> 		</div>
		<?
		$ris_form = getDGUECriteria();
		if (!empty($ris_form)) {
			$gruppo = "";
			?>
			<form name="box" method="post" action="save.php" rel="validate">
				<input type="hidden" name="codice" value="<?= $record["codice"]; ?>">
				<h2>Dati Generali</h2>
				<table width="100%">
					<tr>
						<td class="etichetta">
							Denominazione committente:
						</td>
						<td>
							<input type="text" rel="S;3;0;A" title="Denominazione committente" name="denominazione" id="denominazione" value="<?= $record["denominazione"]?>" class="dgue_input">
						</td>
					</tr>
					<tr>
						<td class="etichetta">
							Oggetto dell'iniziativa:
						</td>
						<td>
							<input type="text" rel="S;3;0;A" title="Procedura"  name="procedura" id="procedura" value="<?= $record["procedura"]?>" class="dgue_input">
						</td>
					</tr>
					<tr>
						<td class="etichetta">
							Descrizione:
						</td>
						<td>
							<textarea rel="S;3;0;A" title="Descrizione" name="descrizione" id="descrizione"  class="dgue_input " rows="5"><?= $record["descrizione"]?></textarea>
						</td>
					</tr>
					<tr>
						<td class="etichetta">
							Numero di riferimento:
						</td>
						<td>
							<input type="text" rel="N;1;0;A" title="Numero di riferimento" name="identificativo" id="identificativo" value="<?= $record["identificativo"]?>" class="dgue_input">
						</td>
					</tr>
				</table><br>
				<h2>Richieste</h2>
				<table width="100%">
					<?
				foreach($ris_form AS $form) {
					if ($form["livello2"] != "" && $form["obbligatorio"] == "N") {
						$form["selezionato"] = "";

						$bind = array();
						$bind[":codice_gara"] = $record["codice"];
						$bind[":codice_form"] = $form["codice"];
						$sql_selezionato = "SELECT * FROM r_dgue_gare WHERE codice_gara = :codice_gara AND sezione = 'free' AND codice_form = :codice_form";
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

                <input type="submit" class="submit_big" value="Salva">
                </form>


			</form>
			<?
		} else {
			?>
			<h2 class="errore">PRESET DGUE NON TROVATO - Contattare l'assistenza</h2>
			<?
		}
	} else {
		echo "<h1>Nessun documento trovato</h1>";
	}
} else {
	echo "<h1>Nessun documento trovato</h1>";
}
	include_once($root."/layout/bottom.php");
	?>
