<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_cup_categoria");
		$id = $_POST["id"];
	}
?>
<tr id="categoria_<? echo $id ?>">
<td>
	<input type="hidden" name="categoria[<? echo $id ?>][codice]"id="codice_<? echo $id ?>" value="<? echo $record["codice"] ?>">
	<input type="text" style="width:95%" name="categoria[<? echo $id ?>][valore]"  title="Valore" rel="S;1;255;A" id="categoria_valore_<? echo $id ?>" value="<? echo $record["valore"] ?>">
</td>
<td>
	<input type="text" style="width:95%" name="categoria[<? echo $id ?>][etichetta]"  title="Etichetta" rel="S;3;255;A" id="categoria_etichetta_<? echo $id ?>" value="<? echo $record["etichetta"] ?>">
</td>
<td width="10">
	<select name="categoria[<? echo $id ?>][codice_derivazione]"  title="Tipologia" rel="S;1;0;N" id="categoria_derivazione_<? echo $id ?>">
		<option value="">Seleziona...</option>
		<?
		$strsql = "SELECT * FROM b_conf_cup_sottosettore WHERE attivo = 'S' ORDER BY etichetta";
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
	<select name="categoria[<? echo $id ?>][relazione_art_21]"  title="Art.21" rel="N;1;0;N" id="categoria_relazione_art_21_<? echo $id ?>">
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
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/cup/categoria');return false" src="/img/del.png" title="Elimina"></td></tr>
<script>
	$("#categoria_derivazione_<?= $id ?>").val("<?= $record["codice_derivazione"] ?>");
	$("#categoria_relazione_art_21_<?= $id ?>").val("<?= $record["relazione_art_21"] ?>");
</script>
