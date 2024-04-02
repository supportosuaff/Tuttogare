<tr><td>
	<table style="table-layout:fixed" width="100%">
	  <tr><td class="etichetta">Categoria</td><td colspan="3"><? echo $progettazione["id"] . "</strong> - " . $progettazione["descrizione"] . " - " . $progettazione["descrizione_categoria"] ?></td></tr>
		<tr>
		<td class="etichetta">Importo</td>
		<td><? echo $progettazione["importo"] ?></td>
		<td class="etichetta">Percentuale esecuzione</td>
		<td width="15%"><? echo $progettazione["percentuale"] ?></td>
		</tr>
		<tr>
		<td class="etichetta">Data inizio</td>
		<td><? echo mysql2date($progettazione["data_inizio"]) ?></td>
		<td class="etichetta">Data fine</td>
		<td width="15%"><? echo mysql2date($progettazione["data_fine"]) ?></td>
		</tr>
		<tr>
			<td class="etichetta">Descrizione</td>
			<td colspan="3">
					<? echo $progettazione["descrizione"] ?>
			</td>
		</tr>
	</table>
</tr>
