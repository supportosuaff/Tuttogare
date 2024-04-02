<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_stati_concorsi");
		$id = $_POST["id"];
	}
?>

<tr id="fase_<? echo $id ?>">
<td width="1" id="flag_<? echo $id ?>" style="background: #<? echo $record["colore"] ?>" rel="colore_fase_<? echo $id ?>" object="" property="" class="color_selector"></td>
<td width="10">
<input type="text" size="3" name="fase[<? echo $id ?>][fase]"  title="fase" rel="S;1;3;N" id="fase_fase_<? echo $id ?>" value="<? echo $record["fase"] ?>"></td>
<td><input type="hidden" name="fase[<? echo $id ?>][codice]"id="codice_fase_<? echo $id ?>" value="<? echo $record["codice"] ?>">
<input type="hidden" name="fase[<? echo $id ?>][colore]" id="colore_fase_<? echo $id ?>" value="<? echo $record["colore"] ?>">
<input type="text" class="titolo_edit" name="fase[<? echo $id ?>][titolo]"  title="titolo" rel="S;3;255;A" id="titolo_fase_<? echo $id ?>" value="<? echo $record["titolo"] ?>">
</td>
 <td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/fasi');return false;" src="/img/del.png" title="Elimina"></td></tr>
