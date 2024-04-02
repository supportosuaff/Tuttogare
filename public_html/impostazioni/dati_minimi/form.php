<?
if (isset($_POST["id"])) {
	session_start();
	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
;
	$record = get_campi("b_impostazioni_dati_minimi");
	$id = $_POST["id"];
}
$colore = "#3C0";
if ($record["attivo"] == "N") { $colore = "#C00"; }
?>

<tr id="campo_<? echo $id ?>">
	<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
	<td width="40%">
		<input type="hidden" name="campo[<? echo $id ?>][codice]"id="codice_campo_<? echo $id ?>" value="<? echo $record["codice"] ?>">
		<input type="text" class="titolo_edit" name="campo[<? echo $id ?>][titolo]"  title="Titolo" rel="S;3;255;A" id="campo_titolo_<? echo $id ?>" value="<? echo $record["titolo"] ?>">
	</td>
	<td>
		<select name="campo[<? echo $id ?>][tipo]"  title="tipo" rel="S;3;255;A" id="campo_tipo_<? echo $id ?>">
			<option value="">Seleziona...</option>
			<option value="input">Testo Semplice</option>
			<option value="text">Textarea</option>
			<option value="attach">Allegato</option>
		</select>
		<script>
			$("#campo_tipo_<?= $id ?>").val('<?= $record["tipo"] ?>');
		</script>
	</td>
	<td width="20%">
		<select name="campo[<? echo $id ?>][tipologie][]" multiple id="tipologie_campo_<? echo $id ?>" rel="S;0;0;ARRAY" title="Tipologia">
			<? $sql = "SELECT * FROM b_tipologie WHERE eliminato = 'N' ORDER BY codice";
			$ris = $pdo->query($sql);
			if ($ris->rowCount()>0) {
				while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
					?><option value="<? echo $rec["codice"] ?>"><? echo $rec["tipologia"] ?></option><?
				}
			}
			?>
		</select>
		<script>
			var values="<? echo $record["tipologie"] ?>";
			$("#tipologie_campo_<? echo $id ?>").val(values.split(";"));
		</script>
	</td>
	<td>
		<input type="text" name="campo[<? echo $id ?>][tag]"  title="tag" rel="N;1;255;A" id="campo_tag_<? echo $id ?>" value="<? echo $record["tag"] ?>">
	</td>
	<td>
		<select name="campo[<? echo $id ?>][obbligatorio]"  title="obbligatorio" rel="S;1;1;A" id="campo_obbligatorio_<? echo $id ?>">
			<option value="">Seleziona...</option>
			<option value="S">Si</option>
			<option value="N">No</option>
		</select>
		<script>
			$("#campo_obbligatorio_<?= $id ?>").val('<?= $record["obbligatorio"] ?>');
		</script>
	</td>
	<td width="10">
		<input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/dati_minimi');return false;" src="/img/switch.png" title="Abilita/Disabilita">
	</td>
	<td width="10">
		<input type="image" onClick="elimina('<? echo $id ?>','impostazioni/dati_minimi');return false;" src="/img/del.png" title="Elimina">
	</td>
</tr>
