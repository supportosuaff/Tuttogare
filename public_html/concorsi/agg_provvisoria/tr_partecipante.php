<?
if (isset($record_partecipante)) {
	$color = "";
	$color_stato = "";
	$posizione = "";
	if (($record_partecipante["ammesso"] == "N") || ($record_partecipante["escluso"] == "S")) $color = "#FF6600";
	if ($record_partecipante["secondo"] == "S") {
		$color_stato = "#33CCFF";
		$posizione = "Secondo";
	}
	if ($record_partecipante["primo"] == "S") {
		$color_stato = "#99FF66";
		$posizione = "Aggiudicatario";
	}

	?>
	<tr style="background-color:<? echo $color ?>" id="<? echo $record_partecipante["codice"] ?>">
		<td width="200">
		<? echo $record_partecipante["codice"] ?> del <? echo mysql2date($record_partecipante["timestamp"]); ?>
		<br>Assegnato dal sistema

	</td>
	<td>
		<input type="hidden" name="partecipante[<? echo $record_partecipante["codice"] ?>][codice]" id="codice_partecipante_<? echo $record_partecipante["codice"] ?>" value="<? echo $record_partecipante["codice"] ?>">
		<strong><? echo $record_partecipante["identificativo"] ?></strong>
		<?
			$sql_dec = "SELECT * FROM r_partecipanti_utenti_concorsi WHERE codice_partecipante = :codice_partecipante ";
			$ris_dec = $pdo->bindAndExec($sql_dec,array(":codice_partecipante"=>$record_partecipante["codice"]));
			if ($ris_dec->rowCount() === 1) {
				$rec_dec = $ris_dec->fetch(PDO::FETCH_ASSOC);
				if (is_numeric($rec_dec["codice_utente"])) {
					echo " - " . $rec_dec["partita_iva"] . " - " . $rec_dec["ragione_sociale"];
				}
			}
			?>
			<strong style="background-color:<? echo $color_stato ?>; padding:5px;"><? echo $posizione ?></strong>
			<?
			$rel = "N;3;0;A";
			$style = "display:none";
			if ($record_partecipante["ammesso"] == "N") {
				$rel = "S;3;0;A";
				$style = "";
			}
			?>
			<textarea style="width:98%; <? echo $style ?>" rows="5" name="partecipante[<? echo $record_partecipante["codice"] ?>][motivazione]" rel="<? echo $rel ?>" id="motivazione_partecipante_<? echo $record_partecipante["codice"] ?>" class="motivazione" title="Motivazione esclusione"><? echo $record_partecipante["motivazione"] ?></textarea>
		</td>
		<td width="10"><select class="ammesso" name="partecipante[<? echo $record_partecipante["codice"] ?>][ammesso]" id="ammesso_partecipante_<? echo $record_partecipante["codice"] ?>">
			<option value="S">Si</option>
			<option value="N">No</option>
		</select></td>
		<td width="10">
			<input type="text" size="5" name="partecipante[<? echo $record_partecipante["codice"] ?>][punteggio]"  title="Punteggio" id="punteggio_partecipante_<? echo $record_partecipante["codice"] ?>" value="<? echo $record_partecipante["punteggio"] ?>" rel="S;0;0;N;100;<=">
		</td>
		</tr>
		<script>
			$("#ammesso_partecipante_<? echo $record_partecipante["codice"] ?>").val("<? echo $record_partecipante["ammesso"] ?>");
		</script>
<? } ?>
