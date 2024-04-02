<?
	if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
		$record_membro = get_campi("r_partecipanti");
		$id_membro = $_POST["id"];
	}
	if (is_operatore()) {
		$disabled = "";
		if (isset($partecipante) && $partecipante["conferma"] == true) $disabled = "disabled";
	?>
	<tr id="partecipante_<? echo $id_membro ?>">
		<td width="10">
          <input <?= $disabled ?> type="hidden" name="partecipante[<? echo $id_membro ?>][codice]" id="codice_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["codice"] ?>">
          <input <?= $disabled ?> type="text" class="partita_iva" dest="ragione_sociale_partecipante_<? echo $id_membro ?>" size="16" name="partecipante[<? echo $id_membro ?>][partita_iva]"  title="<?= traduci("Codice fiscale") ?> <?= traduci("azienda") ?>" rel="S;8;0;PICF" id="partita_iva_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["partita_iva"] ?>"></td>
  	<td><input <?= $disabled ?> type="text" style="width:99%" name="partecipante[<? echo $id_membro ?>][ragione_sociale]"  title="<?= traduci("Ragione Sociale") ?>" rel="S;3;255;A" id="ragione_sociale_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["ragione_sociale"] ?>"></td>
  	<td width="10"><input <?= $disabled ?> type="text" size="16" name="partecipante[<? echo $id_membro ?>][identificativoEstero]"  title="<?= traduci("Identificativo fiscale estero") ?>" rel="N;10;20;A" id="identificativoEstero_partecipante_<? echo $id_membro ?>" value="<? echo $record_membro["identificativoEstero"] ?>"></td>
		<td width="150"><select <?= $disabled ?> name="partecipante[<? echo $id_membro ?>][tipo]" title="<?= traduci("Ruolo") ?>" id="tipo_partecipante_<? echo $id_membro ?>" rel="N;2;250;A">
	                    <option>01-MANDANTE</option>
											<option>05-CONSORZIATA</option
										</select>
	  </td>
		<td width="10" style="text-align:center"><input type="image" <?= $disabled ?> onClick="$('#partecipante_<?= $id_membro ?>').slideUp().remove();return false;" src="/img/del.png" title="<?= traduci("Elimina") ?>"></td>
	</tr>
	<script>
		$("#tipo_partecipante_<? echo $id_membro ?>").val("<? echo $record_membro["tipo"] ?>");
	</script>
	<? } ?>
