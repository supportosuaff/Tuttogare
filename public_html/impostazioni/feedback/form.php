<?
if (isset($_POST["id"])) {
	session_start();
	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
;
	$record = get_campi("b_set_feedback");
	$id = $_POST["id"];
}
?>

<tr id="campo_<? echo $id ?>">
	<td  width="30%">
		<input type="hidden" name="campo[<? echo $id ?>][codice]"id="codice_campo_<? echo $id ?>" value="<? echo $record["codice"] ?>">
		<input type="text" class="titolo_edit" name="campo[<? echo $id ?>][titolo]"  title="Titolo" rel="S;3;255;A" id="campo_titolo_<? echo $id ?>" value="<? echo $record["titolo"] ?>">
	</td>
	<td width="60%">
		<input type="text" name="campo[<? echo $id ?>][descrizione]"  title="descrizione" style="width:100%" rel="N;5;0;A" id="campo_descrizione_<? echo $id ?>" value="<? echo $record["descrizione"] ?>">
	</td>
	<td  width="5%">
		<input type="text" name="campo[<? echo $id ?>][ponderazione]"  title="ponderazione" rel="S;1;0;N;1;<=" id="campo_ponderazione_<? echo $id ?>" value="<? echo $record["ponderazione"] ?>">
	</td>
	<td width="10">
		<input type="image" onClick="elimina('<? echo $id ?>','impostazioni/feedback');return false;" src="/img/del.png" title="Elimina">
	</td>
</tr>
