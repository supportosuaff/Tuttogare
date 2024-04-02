<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$committenti = get_campi("b_committenti");
		$id = $_POST["id"];
	}
?>
<tr id="committenti_<? echo $id ?>"><td>
<input type="hidden" name="committenti[<? echo $id ?>][codice]"id="codice_committenti_<? echo $id ?>" value="<? echo $committenti["codice"] ?>">
<table width="100%">
	<tr><td class="etichetta"><?= traduci("Denominazione") ?>*</td><td colspan="3"><input type="text" name="committenti[<? echo $id ?>][denominazione]" style="width:95%"  title="<?= traduci("Denominazione") ?>" rel="S;3;255;A" id="denominazione_committenti_<? echo $id ?>" value="<? echo $committenti["denominazione"] ?>"></td></tr>
    <td class="etichetta"><?= traduci("Atto") ?>*</td><td><input type="text" name="committenti[<? echo $id ?>][atto]"  title="<?= traduci("Atto") ?>" rel="S;1;255;A" id="atto_committenti_<? echo $id ?>" value="<? echo $committenti["atto"] ?>"></td>
    <td class="etichetta"><?= traduci("Importo") ?>*</td><td><input type="text" name="committenti[<? echo $id ?>][importo]"  title="<?= traduci("Importo") ?>" rel="S;3;255;N" id="importo_committenti_<? echo $id ?>" value="<? echo $committenti["importo"] ?>"></td></tr><tr>
    <td class="etichetta"><?= traduci("inizio") ?></td><td><input type="text" name="committenti[<? echo $id ?>][dal]" class="datepick"  title="<?= traduci("inizio") ?>" rel="N;10;10;D" id="dal_committenti_<? echo $id ?>" value="<? echo mysql2date($committenti["dal"]) ?>"></td>
    <td class="etichetta"><?= traduci("fine") ?></td><td><input type="text" name="committenti[<? echo $id ?>][al]" class="datepick" title="<?= traduci("fine") ?>" rel="N;10;10;D" id="al_committenti_<? echo $id ?>" value="<? echo mysql2date($committenti["al"]) ?>"></td>
  <tr><td colspan="4" class="etichetta"><?= traduci("Oggetto") ?>*</td></tr>
  <tr><td colspan="4"><textarea name="committenti[<? echo $id ?>][oggetto]" class="ckeditor_simple" id="oggetto_committenti_<? echo $id ?>" title="<?= traduci("Oggetto") ?>" rel="S;3;0;A"><? echo $committenti["oggetto"] ?></textarea>
    </tr>
</table>
</td><td width="10"><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','operatori_economici/committenti');return false;"></td></tr>
