<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_cup_natura");
		$id = $_POST["id"];
	}
?>
<tr id="natura_<? echo $id ?>">
<td>
	<input type="hidden" name="natura[<? echo $id ?>][codice]"id="codice_<? echo $id ?>" value="<? echo $record["codice"] ?>">
	<input type="text" style="width:95%" name="natura[<? echo $id ?>][valore]"  title="Valore" rel="S;1;255;A" id="natura_valore_<? echo $id ?>" value="<? echo $record["valore"] ?>">
</td>
<td>
	<input type="text" style="width:95%" name="natura[<? echo $id ?>][etichetta]"  title="Etichetta" rel="S;3;255;A" id="natura_etichetta_<? echo $id ?>" value="<? echo $record["etichetta"] ?>">
</td>
<td width="10">
	<select name="natura[<? echo $id ?>][tipologia]"  title="Tipologia" rel="S;2;3;A" id="natura_tipologia_<? echo $id ?>">
		<option value="">Seleziona...</option>
		<option value="LV">Lavori</option>
		<option value="SR">Servizi</option>
		<option value="FR">Forniture</option>
		<option value="AL">Altro</option>
	</select>
</td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/cup/natura');return false" src="/img/del.png" title="Elimina"></td></tr>
<script>
	$("#natura_tipologia_<?= $id ?>").val("<?= $record["tipologia"] ?>");
</script>
