<?
if (isset($_POST["id"])) {
	session_start();
	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
;
	$record = get_campi("b_tipo_attivita");
	$id = $_POST["id"];
}
$colore = "#3C0";
if ($record["attivo"] == "N") { $colore = "#C00"; }
?>

<tr id="attivita_<? echo $id ?>">
	<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
	<td>
		<input type="hidden" name="attivita[<? echo $id ?>][codice]"id="codice_attivita_<? echo $id ?>" value="<? echo $record["codice"] ?>">
		<input type="text" class="titolo_edit" name="attivita[<? echo $id ?>][value]"  title="attivita" rel="S;3;255;A" id="attivita_value_<? echo $id ?>" value="<? echo $record["value"] ?>">
	</td>
	<td>
		<input type="text" class="titolo_edit" name="attivita[<? echo $id ?>][tag]"  title="tag" rel="S;3;255;A" id="attivita_tag_<? echo $id ?>" value="<? echo $record["tag"] ?>">
	</td>
	<td width="10">
		<input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/tipo_attivita');return false;" src="/img/switch.png" title="Abilita/Disabilita">
	</td>
	<td width="10">
		<input type="image" onClick="elimina('<? echo $id ?>','impostazioni/tipo_attivita');return false;" src="/img/del.png" title="Elimina">
	</td>
</tr>
