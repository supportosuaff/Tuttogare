<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_cup_settore");
		$id = $_POST["id"];
	}
?>
<tr id="settore_<? echo $id ?>">
<td>
	<input type="hidden" name="settore[<? echo $id ?>][codice]"id="codice_<? echo $id ?>" value="<? echo $record["codice"] ?>">
	<input type="text" style="width:95%" name="settore[<? echo $id ?>][valore]"  title="Valore" rel="S;1;255;A" id="settore_valore_<? echo $id ?>" value="<? echo $record["valore"] ?>">
</td>
<td>
	<input type="text" style="width:95%" name="settore[<? echo $id ?>][etichetta]"  title="Etichetta" rel="S;3;255;A" id="settore_etichetta_<? echo $id ?>" value="<? echo $record["etichetta"] ?>">
</td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/cup/settore');return false" src="/img/del.png" title="Elimina"></td></tr>
