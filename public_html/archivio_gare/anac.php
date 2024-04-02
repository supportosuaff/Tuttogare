<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (isset($_SESSION["ente"])) {
		$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
		
		$strsql  = "SELECT b_simog.codice, b_simog.oggetto, b_enti.denominazione, GROUP_CONCAT(b_lotti_simog.cig SEPARATOR '<br>') AS lotti FROM b_simog 
								JOIN b_enti ON b_simog.codice_ente = b_enti.codice 
								JOIN b_enti AS b_ente_gestore ON b_simog.codice_gestore = b_ente_gestore.codice 
								JOIN b_lotti_simog ON b_simog.codice = b_lotti_simog.codice_simog
								WHERE b_lotti_simog.codice_gara IS NULL AND b_lotti_simog.190importoAggiudicato > 0 
								AND (b_simog.codice_gestore = :codice_ente OR b_simog.codice_ente = :codice_ente) AND b_lotti_simog.eliminato = 'N' ";
		if (isset($_GET["codice_ente"])) {
			$bind[":codice_ente_filtro"]=$_GET["codice_ente"];
			$strsql .= " AND b_simog.codice_ente = :codice_ente_filtro ";
		}
		$strsql .= "GROUP BY b_simog.codice ORDER BY b_simog.codice DESC" ;
		
		$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso


?><h1><?= traduci("Altre procedure non gestite tramite il portale") ?></h1>
<a href="/archivio_gare/index.php">Tutte</a> |
<a href="/archivio_gare/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>scadute=0"><?= traduci("Attive") ?></a> |
<a href="/archivio_gare/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>scadute=1"><?= traduci("Scadute") ?></a> |
<a href="/archivio_gare/index.php?<?= (!empty($_GET["codice_ente"])) ? "codice_ente=".$_GET["codice_ente"]."&" : "" ?>esiti=1"><?= traduci("Esiti di gara") ?></a> | 
<a href="/archivio_gare/anac.php"><?= traduci("Altre procedure non gestite tramite il portale") ?></a>
<br><br>
<?
	if ($risultato->rowCount() > 0) {
		?>
		<table width="100%"  class="elenco">
			<thead>
				<tr>
					<td>CIG</td>
					<td>Oggetto</td>
					<td>Ente</td>
				</tr>
			</thead>
			<tbody>
			<? 
				while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					?>
					<tr>
						<td>
							<?= $record["lotti"] ?>
						</td>
						<td>
							<a href="dettaglio_anac.php?codice=<?= $record["codice"] ?>" title="Accedi ai dettagli" ?><?= $record["oggetto"] ?></a>
						</td>
						<td>
							<?= $record["denominazione"] ?>
						</td>
					</tr>
					<?
				}
			?>
			</tbody>
		</table>
		<?
	?>
	<div class="clear"></div>
<?php
		} else { ?>
		<h1 style="text-align:center">Nessuna gara disponibile</h1>
		<? }
	}
	include_once($root."/layout/bottom.php");
	?>
