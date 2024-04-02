<table id="partecipanti" class="no_border" <? if ($valutazione) echo 'style="display: none"' ?> width="100%">
	<thead>
    	<tr class="macro">
    		<td width="10%" style="text-align:center">#</td>
    		<td width="20%">Codice Fiscale Impresa</td>
    		<td width="60%">Ragione sociale</td>
    		<td width="10%" align="center">Pnt.</td>
    	</tr>
    </thead>
    <tbody>
    <?
    $ch = "A";
    $i = 0;
	while ($rec_partecipanti = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
		// $partecipanti[$rec_partecipanti["codice"]] = $rec_partecipanti["ragione_sociale"];
		$partecipanti[$i] = [$rec_partecipanti["codice"], $ch,  $rec_partecipanti["ragione_sociale"]];

		$punteggi[$rec_partecipanti["codice"]] = 0;
		$bind = array();
		$bind[":codice_partecipante"] = $rec_partecipanti["codice"];
		$bind[":codice_gara"] = $codice_gara;
		$bind[":codice_lotto"] = $codice_lotto;
		$bind[":codice_commissario"] = $_SESSION["codice_commissario"];
		$sql = "SELECT SUM(`b_confronto_coppie`.`punteggio_partecipante_1`) AS parziale ";
		$sql .= "FROM `b_confronto_coppie` ";
		$sql .= "WHERE `codice_partecipante_1` = :codice_partecipante ";
		$sql .= "AND `codice_gara` = :codice_gara ";
		$sql .= "AND `codice_lotto` = :codice_lotto  ";
		$sql .= "AND `codice_commissario` = :codice_commissario ";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount() > 0)
		{
			$rec = $ris->fetch(PDO::FETCH_ASSOC);
			$punteggi[$rec_partecipanti["codice"]] += intval($rec["parziale"]);
		}
		$bind = array();
		$bind[":codice_partecipante"] = $rec_partecipanti["codice"];
		$bind[":codice_gara"] = $codice_gara;
		$bind[":codice_lotto"] = $codice_lotto;
		$bind[":codice_commissario"] = $_SESSION["codice_commissario"];
		$sql = "SELECT SUM(`b_confronto_coppie`.`punteggio_partecipante_2`) AS parziale ";
		$sql .= "FROM `b_confronto_coppie` ";
		$sql .= "WHERE `codice_partecipante_2` = :codice_partecipante ";
		$sql .= "AND `codice_gara` = :codice_gara ";
		$sql .= "AND `codice_lotto` = :codice_lotto  ";
		$sql .= "AND `codice_commissario` = :codice_commissario ";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount() > 0)
		{
			$rec = $ris->fetch(PDO::FETCH_ASSOC);
			$punteggi[$rec_partecipanti["codice"]] += intval($rec["parziale"]);
		}
		?>
		<tr>
			<td width="10%" style="text-align:center"><?= $ch ?></td>
			<td width="20%"><?= $rec_partecipanti["partita_iva"] ?></td>
			<td width="60%"><?= $rec_partecipanti["ragione_sociale"] ?></td>
			<td width="10%" align="center"><?= $punteggi[$rec_partecipanti["codice"]] ?></td>
		</tr>
		<?
		$ch++;
		$i++;
	}
	?>
    </tbody>
</table>
