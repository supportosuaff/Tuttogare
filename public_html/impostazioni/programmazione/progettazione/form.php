<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_programmazione_stato_progettazione");
		$id = $_POST["id"];
	}
?>
<tr id="progettazione_<? echo $id ?>">
<td>
	<input type="hidden" name="progettazione[<? echo $id ?>][codice]"id="codice_<? echo $id ?>" value="<? echo $record["codice"] ?>">
	<input type="text" style="width:95%" name="progettazione[<? echo $id ?>][valore]"  title="Valore" rel="S;1;255;A" id="progettazione_valore_<? echo $id ?>" value="<? echo $record["valore"] ?>">
</td>
<td>
	<input type="text" style="width:95%" name="progettazione[<? echo $id ?>][etichetta]"  title="Etichetta" rel="S;3;255;A" id="progettazione_etichetta_<? echo $id ?>" value="<? echo $record["etichetta"] ?>">
</td>
</td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/programmazione/progettazione');return false" src="/img/del.png" title="Elimina"></td></tr>
