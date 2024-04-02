<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_conf_voci_certificato");
		$id = $_POST["id"];
	}
	$colore = "#3C0";
	if ($record["attivo"] == "N") { $colore = "#C00"; }
?>

<tr id="voce_<? echo $id ?>">
<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
<td>
	<input type="hidden" name="voce[<? echo $id ?>][codice]"id="codice_voce_<? echo $id ?>" value="<? echo $record["codice"] ?>">
	<input type="text" class="titolo_edit" name="voce[<? echo $id ?>][descrizione]"  title="Descrizione" rel="S;2;0;A" id="voce_descrizione_<? echo $id ?>" value="<? echo $record["descrizione"] ?>">
</td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/voci_certificati_pagamento');return false;" src="/img/switch.png" title="Abilita/Disabilita"></td>
<td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/voci_certificati_pagamento');return false;" src="/img/del.png" title="Elimina"></td></tr>
