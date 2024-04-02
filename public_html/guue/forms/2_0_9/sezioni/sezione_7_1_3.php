<tr>
	<td class="etichetta" colspan="4">
		<label><b>VII.1.3) Luogo di esecuzione:</b></label>
	</td>
</tr>
<tr>
	<td>Codice NUTS:</td>
	<td>
		<select name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][NUTS][ATTRIBUTE][CODE]" rel="S;1;0;A" title="Codice Nuts">
			<option value="">Seleziona...</option>
			<?
			$sql_nuts = "SELECT * FROM b_nuts ORDER BY descrizione";
			$ris_nuts = $pdo->query($sql_nuts);
			if ($ris_nuts->rowCount() > 0)
			{
				while ($nuts = $ris_nuts->fetch(PDO::FETCH_ASSOC)) {
					?>
					<option value="<?= $nuts["nuts"] ?>" <?= (!empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["NUTS"]["ATTRIBUTE"]["CODE"]) && $guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["NUTS"]["ATTRIBUTE"]["CODE"] == $nuts["nuts"]) ? 'selected="selected"' : null  ?> ><?= $nuts["nuts"] ?> - <?= $nuts["descrizione"] ?> <? if (!empty($nuts["data_fine_validita"])) { ?> - scadenza: <?= mysql2date($nuts["data_fine_validita"]) ?><? } ?></option>
					<?
				}
			}
			?>
		</select>
	</td>
	<td>Luogo principale di esecuzione:</td>
	<td>
		<input type="text" title="Luogo principale di esecuzione" name="guue[MODIFICATIONS_CONTRACT][DESCRIPTION_PROCUREMENT][MAIN_SITE]" value="<?= !empty($guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["MAIN_SITE"]) ? $guue["MODIFICATIONS_CONTRACT"]["DESCRIPTION_PROCUREMENT"]["MAIN_SITE"] : null ?>" rel="N;3;0;A">
	</td>
</tr>
