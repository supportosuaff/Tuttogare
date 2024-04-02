<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_categorie_soa");
		$record["sios"] = "N";
		$record["obbligo_qualificazione"] = "N";
		$id = $_POST["id"];
	}
?>
<tr id="categoria_<? echo $id ?>"><td width="10%"><input type="hidden" name="categoria[<? echo $id ?>][codice]"id="codice_<? echo $id ?>" value="<? echo $record["codice"] ?>">
<input type="text" class="titolo_edit" name="categoria[<? echo $id ?>][id]"  title="ID" rel="S;3;10;A" id="id_<? echo $id ?>" value="<? echo $record["id"] ?>">
</td>
<td><input type="text" style="width:95%" name="categoria[<? echo $id ?>][descrizione]"  title="Descrizione" rel="S;3;255;A" id="descrizione_<? echo $id ?>" value="<? echo $record["descrizione"] ?>"></td>
<td width="10">
	<select name="categoria[<? echo $id ?>][sios]"  title="SIOS" rel="S;1;1;A" id="sios_<? echo $id ?>">
		<option value="S">Si</option>
		<option value="N">No</option>
	</select>
</td>
<td width="10">
	<select name="categoria[<? echo $id ?>][obbligo_qualificazione]"  title="Qualificazione obbligatoria" rel="S;1;1;A" id="obbligo_qualificazione_<? echo $id ?>">
		<option value="S">Si</option>
		<option value="N">No</option>
	</select>
</td>
<td width="10">
	<select name="categoria[<? echo $id ?>][tutelate]"  title="Tutelate" rel="S;1;1;A" id="tutelate_<? echo $id ?>">
		<option value="S">Si</option>
		<option value="N">No</option>
	</select>
</td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/categorie_soa');return false" src="/img/del.png" title="Elimina"></td></tr>
<script>
	$("#sios_<?= $id ?>").val("<?= $record["sios"] ?>");
	$("#obbligo_qualificazione_<?= $id ?>").val("<?= $record["obbligo_qualificazione"] ?>");
	$("#tutelate_<?= $id ?>").val("<?= $record["tutelate"] ?>");
</script>
