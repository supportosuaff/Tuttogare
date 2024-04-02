<?
if (isset($record_partecipante)) {
	$style = "";
	$stato = "Ammesso";
	if ($record_partecipante["ammesso"] == "N") $stato = "Non Ammesso";
	if (($record_partecipante["ammesso"] == "N") || ($record_partecipante["escluso"] == "S")) $style = "background-color:#FF6600";
	if ($record_partecipante["secondo"] == "S") {
		$style = "background-color:#33CCFF";
		$stato = "Secondo";
	}
	if ($record_partecipante["primo"] == "S") {
		$style = "background-color:#99FF66";
		$stato = "Aggiudicatario";
	}

	$sql_punteggi = "SELECT b_criteri_valutazione_concorsi.descrizione, b_criteri_valutazione_concorsi.punteggio AS max, b_punteggi_criteri_concorsi.punteggio
									 FROM b_punteggi_criteri_concorsi JOIN b_criteri_valutazione_concorsi ON b_punteggi_criteri_concorsi.codice_criterio = b_criteri_valutazione_concorsi.codice
									 WHERE b_punteggi_criteri_concorsi.codice_partecipante = :codice_partecipante ";
	$ris_punteggi = $pdo->bindAndExec($sql_punteggi,array(":codice_partecipante" => $record_partecipante["codice"]));
	$punteggi = $ris_punteggi->rowCount();
?>
<tr style="<?= $style ?>" id="<? echo $record_partecipante["codice"] ?>">
	<td rowspan="<?= $punteggi + 1 ?>">
		<strong><? echo $record_partecipante["identificativo"] ?></strong>
	</td>
	<?
		if (!is_numeric($record_partecipante["codice_utente"])) {
			$criptato = true;
			$record_partecipante["partita_iva"] = "Criptato";
			$record_partecipante["ragione_sociale"] = "Criptato";
			$record_partecipante["pec"] = "Criptato";
			$record_partecipante["identificativoEstero"] = "Criptato";
		}
	?>
	<td><?= $record_partecipante["partita_iva"] ?></td>
	<td><?= $record_partecipante["ragione_sociale"] ?></td>
	<td><?= $record_partecipante["pec"] ?></td>
	<td><?= $record_partecipante["identificativoEstero"] ?></td>
	<td><strong><?= $record_partecipante["punteggio"] ?></strong></td>
	<td rowspan="<?= $punteggi + 1 ?>"><?= $stato ?></td>
</tr>
<? if ($punteggi > 0) {
	while($punteggio = $ris_punteggi->fetch(PDO::FETCH_ASSOC)) {?>
		<tr>
			<td colspan="4"><?= $punteggio["descrizione"] . " - <strong>Max. " . $punteggio["max"] . "</strong>"; ?></td>
			<td><?= $punteggio["punteggio"] ?></td>
		</tr>
<? }
	}
} ?>
