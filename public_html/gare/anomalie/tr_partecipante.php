<?
	$color = "#FF6600";
	if ($record_partecipante["verifica"] == "S") $color = "#99FF66";
?>
<tr id="<? echo $record_partecipante["codice"] ?>">
	<td width="1" style="background-color:<? echo $color ?>"></td>
	<td width="200"><strong><? echo $record_partecipante["numero_protocollo"] ?></strong> del <? echo mysql2date($record_partecipante["data_protocollo"]) ?></td>
	<td width="10">
		<input type="hidden" name="partecipante[<? echo $record_partecipante["codice"] ?>][codice]" id="codice_partecipante_<? echo $record_partecipante["codice"] ?>" value="<? echo $record_partecipante["codice"] ?>">
		<strong><? echo $record_partecipante["partita_iva"] ?></strong>
	</td>
	<td><? if ($record_partecipante["tipo"] != "") echo "<strong>RAGGRUPPAMENTO</strong> - " ?><? echo $record_partecipante["ragione_sociale"] ?>
    <?
		$rel = "S;3;255;A";
		$style = "";
		if ($record_partecipante["escluso"] == "N") {
			$rel = "N;3;255;A";
			$style = "display:none";
		}
		?>
		<input type="text" style="width:98%; <? echo $style ?>" name="partecipante[<? echo $record_partecipante["codice"] ?>][motivazione]" rel="<? echo $rel ?>" id="motivazione_partecipante_<? echo $record_partecipante["codice"] ?>" class="motivazione" title="Motivazione esclusione" value="<? echo $record_partecipante["motivazione"] ?>">
	</td>
	<td width="10">
		<select name="partecipante[<? echo $record_partecipante["codice"] ?>][verifica]" id="verifica_partecipante_<? echo $record_partecipante["codice"] ?>">
			<option value="N">No</option>
			<option value="S">Si</option>
		</select>
	</td>
	<td width="10">
		<select class="escluso" name="partecipante[<? echo $record_partecipante["codice"] ?>][escluso]" id="escluso_partecipante_<? echo $record_partecipante["codice"] ?>">
			<option value="S">Si</option>
			<option value="N">No</option>
		</select>
	</td>
</tr>
<script>
	$("#verifica_partecipante_<? echo $record_partecipante["codice"] ?>").val("<? echo $record_partecipante["verifica"] ?>");
	$("#anomalia_partecipante_<? echo $record_partecipante["codice"] ?>").val("<? echo $record_partecipante["anomalia"] ?>");
	$("#escluso_partecipante_<? echo $record_partecipante["codice"] ?>").val("<? echo $record_partecipante["escluso"] ?>");
</script>
