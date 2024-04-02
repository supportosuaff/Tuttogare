<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$soa = get_campi("b_certificazioni_soa");
		$id = $_POST["id"];
	}
?>
<tr id="soa_<? echo $id ?>"><td>
<input type="hidden" name="soa[<? echo $id ?>][codice]"id="codice_soa_<? echo $id ?>" value="<? echo $soa["codice"] ?>">
<input type="hidden" name="soa[<? echo $id ?>][id]"id="codice_soa_<? echo $id ?>" value="<? echo $id ?>">
<table width="100%">
	<tr><td class="etichetta"><?= traduci("Ente certificatore") ?>*</td><td colspan="3"><input type="text" name="soa[<? echo $id ?>][ente]" style="width:95%"  title="<?= traduci("Ente certificatore") ?>" rel="S;3;255;A" id="ente_soa_<? echo $id ?>" value="<? echo $soa["ente"] ?>"></td></tr>
    <tr><td class="etichetta"><?= traduci("Categoria") ?>*</td><td>
			<select rel="S;0;0;N" title="<?= traduci("Categoria") ?>" name="soa[<? echo $id ?>][codice_categoria]" id="codice_categoria_soa_<? echo $id ?>">
				<option value="">Seleziona...</option>
				<?
	$sql_soa = "SELECT * FROM b_categorie_soa WHERE attivo = 'S' AND id <> 'ALTRO' ORDER BY codice";
	$ris_elenco_soa = $pdo->query($sql_soa);
	if ($ris_elenco_soa->rowCount()>0) {
		while($oggetto_soa = $ris_elenco_soa->fetch(PDO::FETCH_ASSOC)) {
			?>
											<option value="<? echo $oggetto_soa["codice"] ?>"><strong><? echo $oggetto_soa["id"] . "</strong> - " . $oggetto_soa["descrizione"] ?></option>
											<?
		}
	}
?>
			</select>
			<script>
				$("#codice_categoria_soa_<? echo $id ?>").val('<? echo $soa["codice_categoria"] ?>');
			</script>
		</td>
		<td class="etichetta"><?= traduci("Classifica") ?>*</td><td>
			<select rel="S;0;0;N" title="<?= traduci("Classifica") ?> SOA" name="soa[<? echo $id ?>][codice_classifica]" id="codice_classifica_soa_<? echo $id ?>">
				<option value="">Seleziona...</option>
				<?
	$sql_soa = "SELECT * FROM b_classifiche_soa WHERE attivo = 'S' ORDER BY codice";
	$ris_elenco_soa = $pdo->query($sql_soa);
	if ($ris_elenco_soa->rowCount()>0) {
		while($oggetto_soa = $ris_elenco_soa->fetch(PDO::FETCH_ASSOC)) {
			?>
											<option value="<? echo $oggetto_soa["codice"] ?>"><strong><? echo $oggetto_soa["id"] . " - " . $oggetto_soa["minimo"] . " - " . $oggetto_soa["massimo"] ?></option>
											<?
		}
	}
?>
			</select>
			<script>
				$("#codice_classifica_soa_<? echo $id ?>").val('<? echo $soa["codice_classifica"] ?>');
			</script>
		</td></tr>
		<tr>
    <td class="etichetta"><?= traduci("Data") ?>*</td><td><input type="text" name="soa[<? echo $id ?>][data_rilascio]" class="datepick"  title="<?= traduci("Data") ?>" rel="S;10;10;D" id="data_rilascio_soa_<? echo $id ?>" value="<? echo mysql2date($soa["data_rilascio"]) ?>"></td>
     <td class="etichetta"><?= traduci("scadenza") ?></td><td><input type="text" name="soa[<? echo $id ?>][data_scadenza]" class="datepick"  title="<?= traduci("scadenza") ?>" rel="N;10;10;D" id="data_scadenza_soa_<? echo $id ?>" value="<? echo mysql2date($soa["data_scadenza"]) ?>"></td></tr>
<tr><td class="etichetta"><?= traduci("Certificato") ?></td><td colspan="3">
	<div id="upload_certificato_soa_<?= $id ?>" rel="soa_<?= $id ?>_certificato" class="scegli_file" style="float:left"><img src="/img/folder.png" height="30" style="vertical-align:middle"></div>
	<div id="nome_file_soa_<?= $id ?>_certificato" style="float:left;">
		<?
			$rel = "N;3;0;FP";
			if ($soa["certificato"] != "") {
				$rel = "N;3;0;FP";
				?>
				<a href="/documenti/operatori/<? echo $soa["codice_operatore"] ?>/<? echo $soa["certificato"] ?>" title="File allegato"><img src="/img/<? echo substr($soa["certificato"],-3)?>.png" alt="File <? echo substr($soa["certificato"],0,-3)?>" style="vertical-align:middle">Visualizza Allegato</a><br>
				<?
			}
		?>
	</div>
	<input type="hidden" id="soa_<?= $id ?>_certificato" name="soa_<?= $id ?>_certificato" title="<?= traduci("Certificato") ?>" rel="<? echo $rel ?>">
	<input type="hidden" class="terminato" id="terminato_soa_<?= $id ?>_certificato" title="Termine upload">
	<div class="clear"></div>
	<div id="progress_bar_soa_<?= $id ?>_certificato" class="big_progress_bar" style="display:none"><div class="progress_bar"></div></div>

	<script>
		tmp = (function($){
			return (new ResumableUploader($("#upload_certificato_soa_<?= $id ?>")));
		})(jQuery);
		uploader.push(tmp);
	</script>


    </td></tr>
</table>
</td><td width="10"><input type="image" src="/img/del.png" onClick="elimina('<? echo $id ?>','operatori_economici/soa');return false;"></td></tr>
