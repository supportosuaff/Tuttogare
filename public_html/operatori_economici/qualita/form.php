<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$qualita = get_campi("b_certificazioni_qualita");
		$id = $_POST["id"];
	}
?>
<tr id="qualita_<? echo $id ?>"><td>
<input type="hidden" name="qualita[<? echo $id ?>][codice]"id="codice_qualita_<? echo $id ?>" value="<? echo $qualita["codice"] ?>">
<input type="hidden" name="qualita[<? echo $id ?>][id]"id="codice_qualita_<? echo $id ?>" value="<? echo $id ?>">
<table width="100%">
	<tr><td class="etichetta"><?= traduci("Ente certificatore") ?>*</td><td colspan="3"><input type="text" name="qualita[<? echo $id ?>][ente]" style="width:95%"  title="<?= traduci("Ente certificatore") ?>" rel="S;3;255;A" id="ente_qualita_<? echo $id ?>" value="<? echo $qualita["ente"] ?>"></td></tr>
    <tr><td class="etichetta"><?= traduci("Settore") ?>*</td><td><input type="text" name="qualita[<? echo $id ?>][settore]" style="width:95%"  title="<?= traduci("Settore") ?>" rel="S;3;255;A" id="settore_qualita_<? echo $id ?>" value="<? echo $qualita["settore"] ?>"></td>
    <td class="etichetta"><?= traduci("Norma") ?>*</td><td><input type="text" name="qualita[<? echo $id ?>][norma]" style="width:95%"  title="<?= traduci("Norma") ?>" rel="S;3;255;A" id="norma_qualita_<? echo $id ?>" value="<? echo $qualita["norma"] ?>"></td></tr><tr>
    <td class="etichetta"><?= traduci("Data") ?>*</td><td><input type="text" name="qualita[<? echo $id ?>][data_rilascio]" class="datepick"  title="<?= traduci("Data") ?>" rel="S;10;10;D" id="data_rilascio_qualita_<? echo $id ?>" value="<? echo mysql2date($qualita["data_rilascio"]) ?>"></td>
     <td class="etichetta"><?= traduci("scadenza") ?></td><td><input type="text" name="qualita[<? echo $id ?>][data_scadenza]" class="datepick"  title="<?= traduci("scadenza") ?>" rel="N;10;10;D" id="data_scadenza_qualita_<? echo $id ?>" value="<? echo mysql2date($qualita["data_scadenza"]) ?>"></td></tr>
<tr><td class="etichetta"><?= traduci("Certificato") ?></td><td colspan="3">
	<div id="upload_certificato_qualita_<?= $id ?>" rel="qualita_<?= $id ?>_certificato" class="scegli_file" style="float:left"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
	<div id="nome_file_qualita_<?= $id ?>_certificato" style="float:left;">
		<?
			$rel = "N;3;0;FP";
			if ($qualita["certificato"] != "") {
				$rel = "N;3;0;FP";
				?>
				<a href="/documenti/operatori/<? echo $qualita["codice_operatore"] ?>/<? echo $qualita["certificato"] ?>" title="File allegato"><img src="/img/<? echo substr($qualita["certificato"],-3)?>.png" alt="File <? echo substr($qualita["certificato"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
				<?
			}
		?>
	</div>
	<input type="hidden" id="qualita_<?= $id ?>_certificato" name="qualita_<?= $id ?>_certificato" title="<?= traduci("Certificato") ?>" rel="<? echo $rel ?>">
	<input type="hidden" class="terminato" id="terminato_qualita_<?= $id ?>_certificato" title="Termine upload">
	<div class="clear"></div>
	<div id="progress_bar_qualita_<?= $id ?>_certificato" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

	<script>
		tmp = (function($){
			return (new ResumableUploader($("#upload_certificato_qualita_<?= $id ?>")));
		})(jQuery);
		uploader.push(tmp);
	</script>

    </td></tr>
</table>
</td><td width="10"><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','operatori_economici/qualita');return false;"></td></tr>
