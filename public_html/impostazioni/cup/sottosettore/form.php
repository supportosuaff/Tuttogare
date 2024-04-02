<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_cup_sottosettore");
		$id = $_POST["id"];
	}
?>
<tr id="sottosettore_<? echo $id ?>">
<td>
	<input type="hidden" name="sottosettore[<? echo $id ?>][codice]"id="codice_<? echo $id ?>" value="<? echo $record["codice"] ?>">
	<input type="text" style="width:95%" name="sottosettore[<? echo $id ?>][valore]"  title="Valore" rel="S;1;255;A" id="sottosettore_valore_<? echo $id ?>" value="<? echo $record["valore"] ?>">
</td>
<td>
	<input type="text" style="width:95%" name="sottosettore[<? echo $id ?>][etichetta]"  title="Etichetta" rel="S;3;255;A" id="sottosettore_etichetta_<? echo $id ?>" value="<? echo $record["etichetta"] ?>">
</td>
<td width="10" style="max-width:400px;">
	<select name="sottosettore[<? echo $id ?>][codice_derivazione]"  title="Settore" rel="S;1;0;N" id="sottosettore_derivazione_<? echo $id ?>">
		<option value="">Seleziona...</option>
		<?
		$strsql = "SELECT * FROM b_conf_cup_settore WHERE attivo = 'S' ORDER BY etichetta";
		$risultato_natura = $pdo->query($strsql);
		if (isset($risultato_natura) && $risultato_natura->rowCount() > 0) {
			while ($record_natura = $risultato_natura->fetch(PDO::FETCH_ASSOC)) {
				?><option value="<?= $record_natura["codice"] ?>"><?= $record_natura["etichetta"] ?></option><?
			}
		}
		?>
	</select>
</td>
<td width="10" style="max-width:300px;">
	<select name="sottosettore[<? echo $id ?>][relazione_art_21]"  title="Art.21" rel="N;1;0;N" id="sottosettore_relazione_art_21_<? echo $id ?>">
		<option value="">Seleziona...</option>
		<?
		$strsql = "SELECT * FROM b_conf_programmazione_categorie WHERE attivo = 'S' ORDER BY etichetta";
		$risultato_art21 = $pdo->query($strsql);
		if (isset($risultato_art21) && $risultato_art21->rowCount() > 0) {
			while ($record_art21 = $risultato_art21->fetch(PDO::FETCH_ASSOC)) {
				?><option value="<?= $record_art21["codice"] ?>"><?= $record_art21["etichetta"] ?></option><?
			}
		}
		?>
	</select>
</td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/cup/sottosettore');return false" src="/img/del.png" title="Elimina"></td></tr>
<script>
	$("#sottosettore_derivazione_<?= $id ?>").val("<?= $record["codice_derivazione"] ?>");
	$("#sottosettore_relazione_art_21_<?= $id ?>").val("<?= $record["relazione_art_21"] ?>");
</script>
