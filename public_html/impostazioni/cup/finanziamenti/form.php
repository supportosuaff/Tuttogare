<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_cup_finanziamenti");
		$id = $_POST["id"];
	}
?>
<tr id="finanziamenti_<? echo $id ?>">
<td>
	<input type="hidden" name="finanziamenti[<? echo $id ?>][codice]"id="codice_<? echo $id ?>" value="<? echo $record["codice"] ?>">
	<input type="text" style="width:95%" name="finanziamenti[<? echo $id ?>][valore]"  title="Valore" rel="S;1;255;A" id="finanziamenti_valore_<? echo $id ?>" value="<? echo $record["valore"] ?>">
</td>
<td>
	<input type="text" style="width:95%" name="finanziamenti[<? echo $id ?>][etichetta]"  title="Etichetta" rel="S;3;255;A" id="finanziamenti_etichetta_<? echo $id ?>" value="<? echo $record["etichetta"] ?>">
</td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/cup/finanziamenti');return false" src="/img/del.png" title="Elimina"></td></tr>
