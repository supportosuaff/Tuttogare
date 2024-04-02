<?
	if (isset($risultato)) {
		if ($risultato->rowCount() > 0) {
	?>
			<table width="100%" id="gare" class="elenco">
    		<thead>
					<tr>
						<td></td>
						<td>ID</td>
						<td><?= traduci("Stato") ?></td>
						<td>CIG</td>
						<td><?= traduci("Tipologia") ?></td>
						<td><?= traduci("Criterio") ?></td>
						<td><?= traduci("Procedura") ?></td>
						<td><?= traduci("Oggetto") ?></td>
						<? if ($_SESSION["ente"]["tipo"] == "SUA") echo "<td>" . traduci("Ente") . "</td>"; ?>
						<td><?= traduci("Scadenza") ?></td>
          </tr>
        </thead>
				<tbody>
			    <?
					$sql_lotti = "SELECT cig FROM b_lotti WHERE codice_gara = :codice_gara AND annullata = 'N'";
					$ris_lotti = $pdo->prepare($sql_lotti);
					while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
						if (($record["stato"]==3) && (strtotime($record["data_scadenza"])<time())) {
							$record["colore"] = $config["colore_scaduta"];
							$record["fase"] = "Scaduta";
						}
						$ris_lotti->bindValue(":codice_gara",$record["codice"]);
						$ris_lotti->execute();
						if ($ris_lotti->rowCount() > 0) {
							$record["cig"] = array();
							while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) if (!empty($lotto["cig"])) $record["cig"][] = $lotto["cig"];
							$record["cig"] = implode("<br>",$record["cig"]);
						}
					?>
						<tr id="<? echo $record["codice"] ?>">
							<td width="1" style="background-color:#<? echo $record["colore"] ?>"></td>
							<td width="5%"><? echo $record["id"] ?></td>
							<td width="10%"><? echo traduci($record["fase"]) ?></td>
							<td><? echo $record["cig"]; ?></td>
							<td><? echo traduci($record["tipologia"]) ?></td>
							<td><? echo traduci($record["criterio"]) ?></td>
							<td><? echo traduci($record["procedura"]) ?></td>
							<td width="40%">
								<? if ($record["annullata"] == "S") {
									echo "<strong>" . traduci("Annullata") . " - " . $record["numero_annullamento"] . " - " . mysql2date($record["data_annullamento"]) . "</strong> - ";
								} ?>
								<a href="<?= $config["protocollo"] ?><?= $record["dominio"] ?>/gare/id<? echo $record["codice"] ?>-dettaglio" title="<?= traduci('dettagli') ?>"><? echo $record["oggetto"] ?></a></td>
								<? if ($_SESSION["ente"]["tipo"] == "SUA") echo "<td width='15%'>".$record["denominazione"]."</td>"; ?>
								<td width="15%">
									<span style="display:none"><? echo $record["data_scadenza"] ?></span>
									<? echo mysql2datetime($record["data_scadenza"]) ?>
								</td>
							</tr>
					<?
					}
					?>
				</tbody>
			</table>
		<? }
		}
	?>
