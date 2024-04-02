<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_voci_qe");
		$id = $_POST["id"];
	}
	$colore = "#3C0";
	if ($record["attivo"] == "N") { $colore = "#C00"; }
?>

<tr id="voce_<? echo $id ?>">
<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
<td width="30">
	<input type="hidden" name="voce[<? echo $id ?>][codice]"id="codice_voce_<? echo $id ?>" value="<? echo $record["codice"] ?>">
	<input type="text" class="titolo_edit" name="voce[<? echo $id ?>][sezione]"  title="Sezione" rel="S;1;1;A" id="voce_sezione_<? echo $id ?>" value="<? echo $record["sezione"] ?>">
</td>
<td width="30">
	<input type="text" class="titolo_edit" name="voce[<? echo $id ?>][voce]"  title="Voce" rel="N;1;2;N" id="voce_voce_<? echo $id ?>" value="<? echo $record["voce"] ?>">
</td>
<td width="30">
	<input type="text" class="titolo_edit" name="voce[<? echo $id ?>][dettaglio]"  title="Dettaglio" rel="N;1;2;N" id="voce_dettaglio_<? echo $id ?>" value="<? echo $record["dettaglio"] ?>">
</td>
<td width="30">
	<input type="text" class="titolo_edit" name="voce[<? echo $id ?>][sub]"  title="Sub" rel="N;1;2;N" id="voce_sub_<? echo $id ?>" value="<? echo $record["sub"] ?>">
</td>
<td>
	<input type="text" class="titolo_edit" name="voce[<? echo $id ?>][descrizione]"  title="Descrizione" rel="S;2;0;A" id="voce_descrizione_<? echo $id ?>" value="<? echo $record["descrizione"] ?>">
</td>
<td width="30">
	<input type="text" class="titolo_edit" name="voce[<? echo $id ?>][codice_bdap]"  title="BDAP" rel="N;0;0;N" id="voce_codice_bdap_<? echo $id ?>" value="<? echo $record["codice_bdap"] ?>">
</td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/quadro_economico');return false;" src="/img/switch.png" title="Abilita/Disabilita"></td>
<td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/quadro_economico');return false;" src="/img/del.png" title="Elimina"></td></tr>
