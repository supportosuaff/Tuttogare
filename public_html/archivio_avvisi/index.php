<?
include_once("../../config.php");
include_once($root . "/layout/top.php");
if (isset($_SESSION["ente"])) {
	$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
	if (!isset($_SESSION["codice_utente"])) {
		$strsql  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id, b_gare.cig, b_enti.dominio ";
		$strsql .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
		$strsql .= "JOIN b_enti ON b_gare.codice_gestore = b_enti.codice ";
		$strsql .= "WHERE b_gare.pubblica = '2' AND (b_gare.codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
		$strsql .= " AND b_avvisi.data <= now() ";
		if (isset($_GET["scadute"])) {
			if ($_GET["scadute"]) {
				$strsql .= " AND b_gare.data_scadenza < NOW() ";
			} else {
				$strsql .= " AND b_gare.data_scadenza >= NOW() ";
			}
		}
	} else {
		if (is_operatore()) {
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$strsql  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id, b_gare.cig, b_enti.dominio, b_gare.codice_ente ";
			$strsql .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
			$strsql .= "LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara ";
			$strsql .= "JOIN b_procedure ON b_procedure.codice = b_gare.procedura ";
			$strsql .= "JOIN b_enti ON b_gare.codice_gestore = b_enti.codice ";
			$strsql .= "WHERE (b_gare.codice_ente  = :codice_ente OR codice_gestore = :codice_ente) ";
			$strsql .= "AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) ";
			$strsql .= " AND b_avvisi.data <= now() ";
			if (isset($_GET["scadute"])) {
				if ($_GET["scadute"]) {
					$strsql .= " AND b_gare.data_scadenza < NOW() ";
				} else {
					$strsql .= " AND b_gare.data_scadenza >= NOW() ";
				}
			}
		} else {
			$strsql  = "SELECT b_avvisi.*, b_gare.oggetto, b_gare.id, b_gare.cig, b_enti.dominio, b_gare.codice_ente ";
			$strsql .= "FROM b_avvisi JOIN b_gare ON b_avvisi.codice_gara =  b_gare.codice ";
			$strsql .= "JOIN b_enti ON b_gare.codice_gestore = b_enti.codice ";
			$strsql .= "WHERE (pubblica > 0) AND (b_gare.codice_ente  = :codice_ente OR codice_gestore = :codice_ente) ";
			if (isset($_GET["scadute"])) {
				if ($_GET["scadute"]) {
					$strsql .= " AND b_gare.data_scadenza < NOW() ";
				} else {
					$strsql .= " AND b_gare.data_scadenza >= NOW() ";
				}
			}
		}
	}
	if (!empty($_GET["codice_ente"])) {
		$bind[":codice_beneficiario"] = $_GET["codice_ente"];
		$strsql .= " AND b_gare.codice_ente = :codice_beneficiario ";
	}
	$strsql .= "ORDER BY data DESC, codice DESC";
	$risultato  = $pdo->bindAndExec($strsql, $bind); //invia la query contenuta in $strsql al database apero e connesso
	?><h1><?= traduci("Avvisi di gara") ?> <? if (isset($_GET["scadute"])) {
																						echo ($_GET["scadute"]) ? traduci("Scaduti") : traduci("Attivi");
																					} ?></h1>
	<a href="/archivio_avvisi/index.php"><?= traduci("Tutti") ?></a> | <a href="/archivio_avvisi/index.php?scadute=0"><?= traduci("Attivi") ?></a> | <a href="/archivio_avvisi/index.php?scadute=1"><?= traduci("Scaduti") ?></a>
	<? 
		$bind = array();
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$sql  = "SELECT codice,denominazione FROM b_enti WHERE ((codice = :codice_ente) OR (sua = :codice_ente)) ORDER BY denominazione ";
		$ris = $pdo->bindAndExec($sql, $bind);
		if ($ris->rowCount() > 1) {
			?>
			<div style="float:right; text-align:right; width:25%">
				<strong>Filtra Ente</strong><br><select onchange="window.location.href='<?= $_SERVER["PHP_SELF"] ?>?codice_ente='+$(this).val()">
					<option value="">Tutti</option>
					<?
						while ($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
							?><option <?= (!empty($_GET["codice_ente"]) && $rec["codice"] == $_GET["codice_ente"]) ? "selected" : "" ?> value="<? echo $rec["codice"] ?>"><? echo $rec["denominazione"] ?></option><?
						}
					?>
				</select>
			</div>
			<div class="clear"></div>
			<?
		}
	?>
	<br><br>
	<?
		if ($risultato->rowCount() > 0) {
			?>

		<table class="elenco" style="width:100%">
			<thead>
				<tr>
					<td><?= traduci("Data") ?></td>
					<td>CIG</td>
					<td><?= traduci("oggetto") ?></td>
					<?
						if ($_SESSION["ente"]["tipo"] == "SUA") { 
							$enteBeneficiario = $pdo->prepare("SELECT denominazione FROM b_enti WHERE codice = :codice");
							?>
						<td width="200"><?= traduci("Ente") ?></td>
					<? } ?>
				</tr>
			<tbody>
				<?
						while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
							$codice			= $record["codice"];
							$titolo			= $record["titolo"];
							$data			= mysql2date($record["data"]);
							$testo			= strip_tags($record["testo"]);
							$href = $config["protocollo"] . $record["dominio"] . "/gare/avvisi/dettaglio.php?cod=" . $codice;
							?>
					<tr id="<? echo $codice ?>">
						<td width="10"><span style="display:none"><?= $record["data"] ?></span><strong><? echo $data ?></strong></td>
						<td><?= $record["cig"] ?></td>
						<td><strong><a style="text-transform:uppercase" href="<? echo $href ?>" title="<? echo $titolo ?>"><? echo $titolo; ?> - Gara <? echo $record["id"] . ": " . $record["oggetto"] ?></a></strong><br>
							<? echo substr($testo, 0, 255); ?>...
						</td>
						<? if ($_SESSION["ente"]["tipo"] == "SUA") {
							$enteBeneficiario->bindValue(":codice",$record["codice_ente"]);
							$enteBeneficiario->execute(); ?>
							<td>
								<?= $enteBeneficiario->fetch(PDO::FETCH_ASSOC)["denominazione"]; ?>
							</td>
						<? } ?>
					</tr>


				<?php
						}
						?></tbody>
		</table>
		<div class="clear"></div>
	<?php
		} else {
			?>
		<h2 style="text-align:center"><?= traduci("Nessun risultato") ?></h2>
<?
	}
}
include_once($root . "/layout/bottom.php");
?>