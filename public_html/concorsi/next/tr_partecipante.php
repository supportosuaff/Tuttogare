<?
if (isset($record_partecipante)) {
	$style = "";
	if (($record_partecipante["ammesso"] == "N") || ($record_partecipante["escluso"] == "S")) $style = "background-color:#FF6600";
?>
<tr style="<?= $style ?>" id="<? echo $record_partecipante["codice"] ?>">
	<td>
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
	<td><?=($record_partecipante["ammesso"] == "S") ? "Ammesso" : "Non ammesso" ?>
</tr>
<? } ?>
