<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$importo = get_campi("b_tipologie_importi");
		$id = $_POST["id"];
		$importo["codice_tipologia"] = $_GET["id_tipologia"];
	}
?>
<div id="importo_<? echo $id ?>">
	<table width="100%">
		<tr>
			<td><input type="hidden" name="importo[<?= $importo["codice_tipologia"] ?>][<? echo $id ?>][codice]"id="codice_importo_<? echo $id ?>" value="<? echo $importo["codice"] ?>">
				<input type="hidden" name="importo[<?= $importo["codice_tipologia"] ?>][<? echo $id ?>][codice_tipologia]"id="codice_tipologia_importo_<? echo $id ?>" value="<? echo $importo["codice_tipologia"] ?>">
<input type="text" class="titolo_edit" name="importo[<?= $importo["codice_tipologia"] ?>][<? echo $id ?>][titolo]"  title="Nome importo" rel="S;3;255;A" id="titolo_importo_<? echo $id ?>" value="<? echo $importo["titolo"] ?>">
</td>
 <td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/tipologie/importi');return false;" src="/img/del.png" title="Elimina"></td></tr>
</table>
</div>
