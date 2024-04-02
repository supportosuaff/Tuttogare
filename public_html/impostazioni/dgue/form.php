<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record = get_campi("b_dgue_settings");
		$id = $_POST["id"];
		$new = true;
	}
	$colore = "#3C0";
	if ($record["attivo"] == "N") { $colore = "#C00"; }
?>
<tr id="form_<? echo $id ?>">
	<td width="1" id="flag_<? echo $id ?>" style="background-color: <? echo $colore ?>"></td>
<td>
<input type="hidden" name="form[<? echo $id ?>][id]" id="id_form_<? echo $id ?>" value="<? echo $id ?>">
<input type="hidden" name="form[<? echo $id ?>][codice]"id="codice_form_<? echo $id ?>" value="<? echo $record["codice"] ?>">
<table width="100%">
<tr>
	<td colspan="2">
		<table width="100%">
			<tr>
				<td class="etichetta">Livello 1</td>
				<td class="etichetta">Livello 2</td>
				<td class="etichetta">Livello 3</td>
				<td class="etichetta">Livello 4</td>
				<td class="etichetta">Livello 5</td>
			</tr>
			<tr>
				<?
					for ($cont_livello=1;$cont_livello<6;$cont_livello++) {
				?>
				<td>
					<select name="form[<? echo $id ?>][livello<?= $cont_livello ?>]" title="Livello <?= $cont_livello ?>" rel="N;0;50;A" id="livello<?= $cont_livello ?>_form_<? echo $id ?>" onchange="check_altro('livello<?= $cont_livello ?>','<?= $id ?>');">
						<option value="">Vuoto</option>
						<?
							$sql = "SELECT livello$cont_livello FROM b_dgue_settings GROUP BY livello$cont_livello ORDER BY livello$cont_livello";
							$ris = $pdo->query($sql);
							if ($ris->rowCount()>0) {
								while($row = $ris->fetch(PDO::FETCH_ASSOC)) {
									?>
									<option><?=	$row["livello".$cont_livello] ?></option>
									<?
								}
							}
						?>
						<script>
							$("#livello<?= $cont_livello ?>_form_<? echo $id ?>").val('<?= $record["livello".$cont_livello] ?>');
						</script>
						<option value="-altro-">Altro...</option>
					</select>
					<input style="display:none; width:98%" type="text" name="form[<? echo $id ?>][livello<?= $cont_livello ?>_altro]" title="Livello <?= $cont_livello ?>" rel="N;0;50;A" id="livello<?= $cont_livello ?>_altro_form_<? echo $id ?>">
				</td>
				<? } ?>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2">
		<table width="100%">
			<tr>
				<td class="etichetta">Codifica Criterio</td>
				<td width="100">
					<input type="text" name="form[<? echo $id ?>][codifica_criterio]"  title="Codifica Criterio" rel="S;1;20;A" id="codifica_criterio_form_<? echo $id ?>" value="<? echo $record["codifica_criterio"] ?>">
				</td>
				<td class="etichetta">Denominazione</td>
				<td>
					<input style="width:98%" type="text" name="form[<? echo $id ?>][taxa]"  title="Denominazione" rel="S;1;255;A" id="taxa_form_<? echo $id ?>" value="<? echo $record["taxa"] ?>">
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="etichetta">UUID</td>
	<td>
		<input type="text" name="form[<? echo $id ?>][uuid]" style="width:100%" title="UUID" rel="S;1;50;A" id="uuid_form_<? echo $id ?>" value="<? echo $record["uuid"] ?>">
	</td>
</tr>
<tr><td class="etichetta">Nome</td><td>
<input type="text" class="titolo_edit" name="form[<? echo $id ?>][nome]"  title="Nome" rel="S;3;255;A" id="nome_form_<? echo $id ?>" value="<? echo $record["nome"] ?>"></td></tr>
<tr><td class="etichetta">Descrizione</td><td>
	<textarea class="ckeditor" name="form[<?= $id ?>][descrizione]" title="Descrizione" rel="N;0;0;A" id="descrizione_form_<? echo $id ?>">
		<?= $record["descrizione"] ?>
	</textarea>
</td></tr>
<tr><td class="etichetta">Tipologia</td><td>
	<select class="select_opzione" name="form[<?= $id ?>][tipologie][]" multiple title="Tipologie" rel="N;0;0;ARRAY" id="tipologie_form_<?= $id ?>">
		<option value="">Tutte</option>
		<?
		$sql = "SELECT * FROM b_tipologie WHERE eliminato = 'N' ORDER BY codice";
		$ris_tipologie = $pdo->query($sql);
		if ($ris_tipologie->rowCount()>0) {
			while($tipologie = $ris_tipologie->fetch(PDO::FETCH_ASSOC)) {
					?>
					<option value="<?= $tipologie["codice"] ?>"><?= $tipologie["tipologia"] ?></option>
					<?
				}
		}
		?>
	</select>
	<script>
		codici_tipologie = '<? echo $record["tipologie"] ?>';
		$("#tipologie_form_<?= $id ?>").val(codici_tipologie.split(','));
	</script>
</td></tr>
<tr><td class="etichetta">Template</td><td>
	<select name="form[<?= $id ?>][template]" title="Template" rel="N;0;0;A" id="template_form_<?= $id ?>">
		<option value="">Nessuno</option>
		<?
		$scripts = scandir($root."/dgue/templates");
		foreach ($scripts AS $directory) {
			if ($directory != "." && $directory != ".." && file_exists($root."/dgue/templates/".$directory."/form.php")) {
					?>
					<option><?= $directory ?></option>
					<?
				}
		}
		?>
	</select>
	</td></tr>
	<tr>
		<td class="etichetta">SUB UUID</td>
		<td>
			<input type="text" name="form[<? echo $id ?>][sub_uuid]" style="width:100%" title="SUB UUID" rel="N;1;50;A" id="sub_uuid_form_<? echo $id ?>" value="<? echo $record["sub_uuid"] ?>">
		</td>
	</tr>
<tr>
	<td class="etichetta">Obbligatorio</td>
	<td>
		<select name="form[<?= $id ?>][obbligatorio]" title="Obbligatorio" rel="S;1;1;A" id="obbligatorio_form_<?= $id ?>">
			<option value="S">Si</option>
			<option value="N">No</option>
		</select>
	</td>
</tr>
<tr>
	<td class="etichetta">Ripetizioni</td>
	<td>
		<select name="form[<?= $id ?>][ripeti]" title="Ripetizioni" rel="S;1;1;A" id="ripeti_form_<?= $id ?>">
			<option value="S">Si</option>
			<option value="N">No</option>
		</select>
	</td>
</tr>

</table>
<script>
	$("#template_form_<?= $id ?>").val('<?=$record["template"] ?>');
	$("#obbligatorio_form_<?= $id ?>").val('<?=$record["obbligatorio"] ?>');
	$("#ripeti_form_<?= $id ?>").val('<?=$record["ripeti"] ?>');
</script>
</td>
<td width="10"><input type="image" onClick="disabilita('<? echo $id ?>','impostazioni/dgue');return false;" src="/img/switch.png" title="Abilita/Disabilita"></td>
 <td width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/dgue');return false;" src="/img/del.png" title="Elimina"></td></tr>
