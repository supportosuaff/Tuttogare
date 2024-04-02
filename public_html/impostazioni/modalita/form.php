<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_modalita");
		$id = $_POST["id"];
		$new = true;
	}
	
	$colore = "#3C0";
	if ($record["attivo"] == "N") { $colore = "#C00"; }
?>
<tr id="modalita_<? echo $id ?>">
<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
<td>
<input type="hidden" name="modalita[<? echo $id ?>][id]" id="id_modalita_<? echo $id ?>" value="<? echo $id ?>">
<input type="hidden" name="modalita[<? echo $id ?>][codice]"id="codice_modalita_<? echo $id ?>" value="<? echo $record["codice"] ?>">
<table width="100%">
<tr><td class="etichetta">Titolo</td><td colspan="3">
<input type="text" class="titolo_edit" name="modalita[<? echo $id ?>][modalita]"  title="Modalita" rel="S;3;255;A" id="modalita_modalita_<? echo $id ?>" value="<? echo $record["modalita"] ?>"></td></tr>
<tr><td class="etichetta">On-line</td><td><select name="modalita[<? echo $id ?>][online]" id="online_modalita_<? echo $id ?>" rel="S;1;1;A" title="On-line">
<option value="S">Si</option>
<option value="N">No</option>
</select></td><td class="etichetta">Directory</td><td>
<input type="text" name="modalita[<? echo $id ?>][directory]"  title="Directory elaborazione" rel="S;3;255;A" id="directory_modalita_<? echo $id ?>" value="<? echo $record["directory"] ?>">
</td></tr></table>
</td>

 <td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/modalita');return false;" src="/img/switch.png" title="Abilita/Disabilita"></td>
 <td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/modalita');return false;" src="/img/del.png" title="Elimina"></td></tr>
<? if (!isset($new)) {
	?>
    <script>
		$("#online_modalita_<? echo $id ?>").val("<? echo $record["online"] ?>");
	</script>
    <?
}
?>