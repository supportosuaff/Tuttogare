<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$brevetti = get_campi("b_brevetti");
		$id = $_POST["id"];
	}
?>
<tr id="brevetti_<? echo $id ?>"><td>
<input type="hidden" name="brevetti[<? echo $id ?>][codice]"id="codice_brevetti_<? echo $id ?>" value="<? echo $brevetti["codice"] ?>">
<table width="100%"><tr>
    <td class="etichetta"><?= traduci("Numero") ?>*</td><td><input type="text" name="brevetti[<? echo $id ?>][numero]"  title="<?= traduci("Numero") ?>" rel="S;1;255;A" id="numero_brevetti_<? echo $id ?>" value="<? echo $brevetti["numero"] ?>"></td>
		<td class="etichetta"><?= traduci("Data") ?></td><td><input type="text" name="brevetti[<? echo $id ?>][data]" class="datepick"  title="<?= traduci("Data") ?>" rel="N;10;10;D" id="data_brevetti_<? echo $id ?>" value="<? echo mysql2date($brevetti["data"]) ?>"></td></tr>
        <tr><td colspan="4" class="etichetta"><?= traduci("Descrizione") ?>*</td></tr>
        <tr><td colspan="4"><textarea name="brevetti[<? echo $id ?>][descrizione]" class="ckeditor_simple" id="descrizione_brevetti_<? echo $id ?>" title="<?= traduci("Descrizione") ?>" rel="S;3;0;A"><? echo $brevetti["descrizione"] ?></textarea>
        </tr>
</table>
</td><td width="10"><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','operatori_economici/brevetti');return false;"></td></tr>
