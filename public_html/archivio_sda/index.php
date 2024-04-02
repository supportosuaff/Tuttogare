<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (isset($_SESSION["ente"])) {
		$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
		if (!isset($_SESSION["codice_utente"])) {
			$strsql  = "SELECT * ";
			$strsql .= "FROM b_bandi_sda ";
			$strsql .= "WHERE pubblica = '2' AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			if (isset($_GET["scadute"])) {
				if ($_GET["scadute"]) {
					$strsql .= " AND b_bandi_sda.data_scadenza < NOW() AND b_bandi_sda.data_scadenza > 0  ";
				} else {
					$strsql .= " AND (b_bandi_sda.data_scadenza >= NOW() OR b_bandi_sda.data_scadenza = 0) ";
				}
			}
			$strsql .= "ORDER BY id DESC, codice DESC" ;
		} else {
			$strsql  = "SELECT * ";
			$strsql .= "FROM b_bandi_sda ";
			$strsql .= "WHERE (pubblica = '2' OR pubblica = '1') AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			if (isset($_GET["scadute"])) {
				if ($_GET["scadute"]) {
					$strsql .= " AND b_bandi_sda.data_scadenza < NOW() AND b_bandi_sda.data_scadenza > 0  ";
				} else {
					$strsql .= " AND (b_bandi_sda.data_scadenza >= NOW() OR b_bandi_sda.data_scadenza = 0) ";
				}
			}
			$strsql .= "ORDER BY id DESC, codice DESC" ;
		}
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso

	?>
	<h1><?= traduci("S.D.A.") ?> <? if (isset($_GET["scadute"])) { echo ($_GET["scadute"]) ? traduci("Scaduti") : traduci("Attivi"); } ?></h1>
	<a href="/archivio_sda/index.php"><?= traduci("Tutti") ?></a> | <a href="/archivio_sda/index.php?scadute=0"><?= traduci("Attivi") ?></a> | <a href="/archivio_sda/index.php?scadute=1"><?= traduci("Scaduti") ?></a><br><br>
	<?
	if ($risultato->rowCount() > 0) {
	?>
    <table width="100%" id="bandi" class="elenco">
    	<thead>
    		<tr><td>ID</td><td><?= traduci("Oggetto") ?></td><td><?= traduci("Scadenza") ?></td>
            </tr>
            </thead>
            <tbody>
    <?
			while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {

	?>
    <tr id="<? echo $record["codice"] ?>">
		<td width="5%"><? echo $record["id"] ?></td>
		<td width="75%">
        <? if ($record["annullata"] == "S") {
			echo "<strong>" . traduci("Annullata") ." - " . $record["numero_annullamento"] . " \ " . mysql2date($record["data_annullamento"]) . "</strong> - ";
		} ?>
        <a href="/sda/id<? echo $record["codice"] ?>-dettaglio" title="Dettagli bando"><? echo $record["oggetto"] ?></a><br>
				<?= $record["descrizione"] ?></td>
        <td><? if ($record["data_scadenza"] > 0) {
					?><span style="display:none"><? echo $record["data_scadenza"] ?></span><?
					echo mysql2datetime($record["data_scadenza"]);
				} ?></td>
     </tr>
        <?
		}

	?>
    	</tbody>
    </table>
    <div class="clear"></div>

<?php
		} else { ?>
			<h2 style="text-align:center"><?= traduci("nessun risultato") ?></h2>
		<? }
	}
	include_once($root."/layout/bottom.php");
	?>
