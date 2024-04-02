<?
$color = "";
$color_stato = "";
$posizione = "";
if ($record_partecipante["conferma"] == false) {
	$color_stato = "#FC0";
	$posizione = "Offerta non pervenuta a sistema";
}
if ($record_partecipante["secondo"] == "S") {
	$color_stato = "#33CCFF";
	$posizione = "Secondo";
}
if ($record_partecipante["primo"] == "S") {
	$color_stato = "#99FF66";
	$posizione = "Aggiudicatario";
}
$showArt80 = true;
?>
<tr style="background-color:<? echo $color ?>" id="<? echo $record_partecipante["codice"] ?>">
	<?
		$check = checkStatoArt80($record_partecipante["partita_iva"]);
		if ($check != false) {
			echo '<td width="10"><div class="status_indicator" style="background-color: ' .$check["color"]  .'"></div></td>';	
		} else {
			echo '<td></td>';
		}
	?>
	<td width="10">
	<input type="hidden" name="partecipante[<? echo $record_partecipante["codice"] ?>][codice]" id="codice_partecipante_<? echo $record_partecipante["codice"] ?>" value="<? echo $record_partecipante["codice"] ?>">
	<strong>
		<?if (!empty($art80) && !empty($record_partecipante["codice_operatore"]) && $showArt80) { ?><a href="#" onClick="sendArt80Request('<?= $record_partecipante["codice_operatore"] ?>')" title="Richiedi verifica art.80"><? } ?>
				<? echo $record_partecipante["partita_iva"] ?>
		<? if (!empty($art80) && !empty($record_partecipante["codice_operatore"])) { ?></a><? } ?>
	</strong>
	</td>
	<td><? if ($record_partecipante["tipo"] != "") echo "<strong>RAGGRUPPAMENTO</strong> - " ?><? echo $record_partecipante["ragione_sociale"] ?>
		<strong style="background-color:<? echo $color_stato ?>; padding:5px;"><? echo $posizione ?></strong>
	</td>
	<td width="10"><select class="ammesso" name="partecipante[<? echo $record_partecipante["codice"] ?>][primo]" id="primo_partecipante_<? echo $record_partecipante["codice"] ?>">
		<option value="S">Si</option>
		<option value="N">No</option>
	</select></td>
	</tr>
	<script>
		$("#primo_partecipante_<? echo $record_partecipante["codice"] ?>").val("<? echo $record_partecipante["primo"] ?>");
	</script>
