<?php
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$ambientali = get_campi("b_certificazioni_ambientali");
		$id = $_POST["id"];
	}
?>
<tr id="ambientali_<?php echo $id ?>"><td>
<input type="hidden" name="ambientali[<?php echo $id ?>][codice]" id="codice_ambientali_<?php echo $id ?>" value="<?php echo $ambientali["codice"] ?>">
<input type="hidden" name="ambientali[<?php echo $id ?>][id]" id="id_ambientali_<?php echo $id ?>" value="<?php echo $id ?>">
<table width="100%">
	<tr><td class="etichetta"><?= traduci("Ente certificatore") ?>*</td><td colspan="3"><input type="text" name="ambientali[<?php echo $id ?>][ente]" style="width:95%"  title="<?= traduci("Ente certificatore") ?>" rel="S;3;255;A" id="ente_ambientali_<?php echo $id ?>" value="<?php echo $ambientali["ente"] ?>"></td></tr>
    <tr><td class="etichetta"><?= traduci("Settore") ?>*</td><td><input type="text" name="ambientali[<?php echo $id ?>][settore]" style="width:95%"  title="<?= traduci("Settore") ?>" rel="S;3;255;A" id="settore_ambientali_<?php echo $id ?>" value="<?php echo $ambientali["settore"] ?>"></td>
    <td class="etichetta"><?= traduci("Norma") ?>*</td><td><input type="text" name="ambientali[<?php echo $id ?>][norma]" style="width:95%"  title="<?= traduci("Norma") ?>" rel="S;3;255;A" id="norma_ambientali_<?php echo $id ?>" value="<?php echo $ambientali["norma"] ?>"></td></tr><tr>
    <td class="etichetta"><?= traduci("Data") ?>*</td><td><input type="text" name="ambientali[<?php echo $id ?>][data_rilascio]" class="datepick"  title="<?= traduci("Data") ?>" rel="S;10;10;D" id="data_rilascio_ambientali_<?php echo $id ?>" value="<?php echo mysql2date($ambientali["data_rilascio"]) ?>"></td>
     <td class="etichetta"><?= traduci("scadenza") ?></td><td><input type="text" name="ambientali[<?php echo $id ?>][data_scadenza]" class="datepick"  title="<?= traduci("scadenza") ?>" rel="N;10;10;D" id="data_scadenza_ambientali_<?php echo $id ?>" value="<?php echo mysql2date($ambientali["data_scadenza"]) ?>"></td></tr>

<tr><td class="etichetta"><?= traduci("Certificato") ?></td><td colspan="3">
	<div id="upload_certificato_ambientale_<?= $id ?>" rel="ambientali_<?= $id ?>_certificato" class="scegli_file" style="float:left"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
	<div id="nome_file_ambientali_<?= $id ?>_certificato" style="float:left;">
		<?php
			$rel = "N;3;0;FP";
			if ($ambientali["certificato"] != "") {
				$rel = "N;3;0;FP";
				?>
				<a href="/documenti/operatori/<? echo $ambientali["codice_operatore"] ?>/<?php echo $ambientali["certificato"] ?>" title="File allegato"><img src="/img/<?php echo substr($ambientali["certificato"],-3)?>.png" alt="File <?php echo substr($ambientali["certificato"],0,-3)?>" style="vertical-align:middle"><?= traduci("Download") ?></a><br>
				<?php
			}
		?>
	</div>
	<input type="hidden" id="ambientali_<?= $id ?>_certificato" name="ambientali_<?= $id ?>_certificato" title="<?= traduci("Certificato") ?>" rel="<?php echo $rel ?>">
	<input type="hidden" class="terminato" id="terminato_ambientali_<?= $id ?>_certificato" title="Termine upload">
	<div class="clear"></div>
	<div id="progress_bar_ambientali_<?= $id ?>_certificato" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>
    </td></tr>
</table>
</td><td width="10"><input type="image" src="/img/del.png" onClick="elimina('<?php echo $id ?>','operatori_economici/ambientali');return false;"></td></tr>
<script>
	tmp = (function($){
		return (new ResumableUploader($("#upload_certificato_ambientale_<?= $id ?>")));
	})(jQuery);
	uploader.push(tmp);
</script>
