<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_tipologie_ente");
		$id = $_POST["id"];
	}
	$colore = "#3C0";
	if ($record["attivo"] == "N") { $colore = "#C00"; }
?>

<tr id="tipologia_<? echo $id ?>">
<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
<td><input type="hidden" name="tipologia[<? echo $id ?>][codice]"id="codice_tipologia_<? echo $id ?>" value="<? echo $record["codice"] ?>">
<input type="text" class="titolo_edit" name="tipologia[<? echo $id ?>][titolo]"  title="Tipologia" rel="S;3;255;A" id="tipologia_titolo_<? echo $id ?>" value="<? echo $record["titolo"] ?>">
</td>
<td><input type="text" class="titolo_edit" name="tipologia[<? echo $id ?>][esender]"  title="Esender" rel="S;3;255;A" id="tipologia_esender_<? echo $id ?>" value="<? echo $record["esender"] ?>">
</td>
 <td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/tipologie_ente');return false;" src="/img/switch.png" title="Abilita/Disabilita"></td>
 <td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/tipologie_ente');return false;" src="/img/del.png" title="Elimina"></td></tr>
