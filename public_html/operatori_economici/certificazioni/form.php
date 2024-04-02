<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$certificazioni = get_campi("b_altre_certificazioni");
		$id = $_POST["id"];
	}
?>
<tr id="certificazioni_<? echo $id ?>"><td>
<input type="hidden" name="certificazioni[<? echo $id ?>][codice]" id="codice_certificazioni_<? echo $id ?>" value="<? echo $certificazioni["codice"] ?>">
<table width="100%">
	<tr><td><?= traduci("Tipo") ?></td><td><select name="certificazioni[<? echo $id ?>][tipo]" id="tipo_certificazioni_<? echo $id ?>">
    	<option><?= traduci("Servizi") ?></option>
      <option><?= traduci("Forniture") ?></option>
			<option><?= traduci("Altro") ?></option>
    </select></td>
    <td class="etichetta"><?= traduci("ente certificatore") ?>*</td><td><input type="text" name="certificazioni[<? echo $id ?>][denominazione]" style="width:95%"  title="<?= traduci("ente certificatore") ?>" rel="S;3;255;A" id="denominazione_certificazioni_<? echo $id ?>" value="<? echo $certificazioni["denominazione"] ?>"></td>
		<td class="etichetta"><?= traduci("Certificazione") ?>*</td><td><input type="text" name="certificazioni[<? echo $id ?>][certificazione]" style="width:95%"  title="<?= traduci("Certificazione") ?>" rel="S;3;255;A" id="certificazione_certificazioni_<? echo $id ?>" value="<? echo $certificazioni["certificazione"] ?>"></td>
    </tr>
</table>
</td><td width="10"><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','operatori_economici/certificazioni');return false;"></td></tr><? if (!isset($_POST["id"])) { ?>
    <script>
		$("#tipo_certificazioni_<? echo $id ?>").val('<? echo $certificazioni["tipo"] ?>');
	</script>
		<? } ?>
