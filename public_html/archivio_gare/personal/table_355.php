<?
	if (isset($gara)) {
		$row["id"] = $gara["id"];
		$row["colore"] = '<div style="height: 100%; width:100%; position:absolute; top:0; left:0; right:0; bottom:0; background-color: #'.$gara["colore"].'"></div>';
		$row["cig"] = $gara["cig"];
		$row["stato"] = $gara["fase"];
		$row["tipo"] = $gara["tipologia"];
		$row["criterio"] = $gara["criterio"];
		$row["procedura"] = $gara["procedura"];
		ob_start();
		if ($gara["annullata"] == "S") { ?><strong><?= traduci('Annullata') ?> - <?= $gara["numero_annullamento"] ?> - <?= mysql2date($gara["data_annullamento"]) ?></strong> -<? } ?>
		<a href="<?= $config["protocollo"] ?><?= $gara["dominio"] ?>/gare/id<? echo $gara["codice"] ?>-dettaglio" title="<?= traduci('dettagli') ?>"><? echo $gara["oggetto"] ?></a><?
		$row["oggetto"] = ob_get_clean();
		$row["importo"] =  number_format($gara["prezzoBase"],2,",",".");
		if ($_SESSION["ente"]["tipo"] == "SUA") $row["denominazione_ente"] = $gara["denominazione"];
		$row["scadenza"] = mysql2datetime($gara["data_scadenza"]);
		$row["provincia"] = $gara["provincia"];
		$row["struttura"] = ($gara["codice_ente"]==620) ? $gara["denominazione"] : $gara["struttura_proponente"];
		$row["pubblicazione"] = mysql2date($gara["data_pubblicazione"]);
		$row["scadenza"] = mysql2date($gara["data_scadenza"]);
	}
	?>
