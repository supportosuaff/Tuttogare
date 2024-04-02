<?
if (isset($_POST["id"])) {
	session_start();
	include("../../../config.php");
	include_once($root."/inc/funzioni.php");
;
	$opzione = get_campi("b_opzioni");
	$id = $_POST["id"];
}
?>
<tr id="opzione_<?= $id ?>">
	<td width="70%">
		<input type="hidden" id="opzione_<? echo $id ?>_codice" rel="N;0;0;A" name="opzioni[<? echo $id ?>][codice]" value="<? echo $opzione["codice"] ?>">
		<input type="text" style="width:99%" id="opzione_<? echo $id ?>_titolo" title="titolo" rel="S;0;0;A" name="opzioni[<? echo $id ?>][titolo]" value="<? echo $opzione["titolo"] ?>">
	</td>
	<td width="23%">
		<input type="text" style="width:99%" id="opzione_<? echo $id ?>_guue" title="GUUE" rel="N;0;0;A" name="opzioni[<? echo $id ?>][guue]" value="<? echo $opzione["guue"] ?>">
	</td>
		<td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/opzioni/opzioni');return false" src="/img/del.png" title="Elimina"></td>

</tr>
