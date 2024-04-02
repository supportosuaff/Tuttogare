<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$progettazione = get_campi("b_esperienze_progettazione");
		$id = $_POST["id"];
	}
?>
<tr id="progettazione_<? echo $id ?>"><td>
<input type="hidden" name="progettazione[<? echo $id ?>][codice]"id="codice_progettazione_<? echo $id ?>" value="<? echo $progettazione["codice"] ?>">
<input type="hidden" name="progettazione[<? echo $id ?>][id]"id="codice_progettazione_<? echo $id ?>" value="<? echo $id ?>">
<table style="table-layout:fixed" width="100%">
	  <tr><td class="etichetta"><?= traduci("Categoria") ?>*</td><td colspan="3">

				<select rel="S;0;0;N" title="<?= traduci("Categoria") ?>" name="progettazione[<? echo $id ?>][codice_categoria]" id="codice_categoria_progettazione_<? echo $id ?>">
					<option value=""><?= traduci("selezione") ?>...</option>
					<?
		$sql_progettazione = "SELECT * FROM b_categorie_progettazione WHERE attivo = 'S' ORDER BY codice";
		$ris_elenco_progettazione = $pdo->query($sql_progettazione);
		if ($ris_elenco_progettazione->rowCount()>0) {
			while($oggetto_progettazione = $ris_elenco_progettazione->fetch(PDO::FETCH_ASSOC)) {
				?>
												<option value="<? echo $oggetto_progettazione["codice"] ?>"><strong><? echo $oggetto_progettazione["id"] . "</strong> - " . $oggetto_progettazione["destinazione"] . " - " . $oggetto_progettazione["descrizione"] ?></option>
												<?
			}
		}
	?>
				</select>
			<script>
				$("#codice_categoria_progettazione_<? echo $id ?>").val('<? echo $progettazione["codice_categoria"] ?>');
			</script>
		</td>
	</tr>
	<tr>
		<td class="etichetta"><?= traduci("importo") ?>*</td>
		<td>
			<input type="text" name="progettazione[<? echo $id ?>][importo]" id="importo_progettazione_<? echo $id ?>" title="Importo" value="<? echo $progettazione["importo"] ?>" rel="S;0;0;N">
		</td>
		<td class="etichetta"><?= traduci("Percentuale esecuzione") ?> *</td>
		<td width="15%">
			<input type="text" name="progettazione[<? echo $id ?>][percentuale]" id="percentuale_progettazione_<? echo $id ?>" title="<?= traduci("Percentuale") ?>" value="<? echo $progettazione["percentuale"] ?>" rel="S;0;0;N;100;<=">
		</td>
		</tr>
		<tr>
    <td class="etichetta"><?= traduci("inizio") ?>*</td><td><input type="text" name="progettazione[<? echo $id ?>][data_inizio]" class="datepick"  title="<?= traduci("inizio") ?>" rel="S;10;10;D" id="data_inizio_progettazione_<? echo $id ?>" value="<? echo mysql2date($progettazione["data_inizio"]) ?>"></td>
     <td class="etichetta"><?= traduci("fine") ?></td><td><input type="text" name="progettazione[<? echo $id ?>][data_fine]" class="datepick"  title="<?= traduci("fine") ?>" rel="N;10;10;D" id="data_fine_progettazione_<? echo $id ?>" value="<? echo mysql2date($progettazione["data_fine"]) ?>"></td></tr>
		<tr>
			<td class="etichetta"><?= traduci("Descrizione") ?>*</td>
			<td colspan="3">
				<textarea class="ckeditor_simple" name="progettazione[<? echo $id ?>][descrizione]" id="descrizione_progettazione_<? echo $id ?>" rel="S;0;0;A" title="Descrizione">
					<? echo $progettazione["descrizione"] ?>
				</textarea>
			</td>
		</tr>
</table>
</td><td width="10"><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','operatori_economici/progettazione');return false;"></td></tr>
