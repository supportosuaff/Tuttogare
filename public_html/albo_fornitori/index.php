<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
?>
<?php
	echo "<h1>GESTIONE AVVISI ALBO FORNITORI</h1>";
	if ($edit) {
		?>
          <a href="/albo_fornitori/id0-edit" title="Inserisci nuovo avviso"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Aggiungi nuovo avviso
        </div></a>
        <?
				$bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
				$strsql  = " SELECT * ";
				$strsql .= " FROM b_bandi_albo ";
				$strsql .= " WHERE codice_gestore = :codice_ente ";
				if ($_SESSION["gerarchia"] > 0 && $_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"]) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= " AND codice_ente = :codice_ente_utente";
				}
				$strsql .= " ORDER BY codice DESC" ;
				$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso



	if ($risultato->rowCount()>0) {
			$schede = array();
			$schede["nuove_istanze"] = ["where"=>"r_partecipanti_albo.valutato = 'N' AND (r_partecipanti_albo.timestamp_abilitazione IS NULL)","totale"=>0];
			$schede["aggiornamenti"] = ["where"=>"r_partecipanti_albo.valutato = 'N' AND (r_partecipanti_albo.timestamp_abilitazione IS NOT NULL)","totale"=>0];
			$schede["ammessi"] = ["where"=>" r_partecipanti_albo.ammesso = 'S' AND r_partecipanti_albo.valutato = 'S' ","totale"=>0];
			$schede["respinti"] = ["where"=>" r_partecipanti_albo.ammesso = 'N' AND r_partecipanti_albo.valutato = 'S' ","totale"=>0];
	?>
    <table id="pagine" width="100%" id="gare" class="elenco">
    	<thead>
				<tr>
					<td>ID</td><td>Oggetto</td>
					<? if ($_SESSION["ente"]["tipo"] == "SUA") { 
                $enteBeneficiario = $pdo->prepare("SELECT denominazione FROM b_enti WHERE codice = :codice");
                ?>
              <td width="200"><?= traduci("Ente") ?></td>
            <? } ?>
					<?
						foreach($schede AS $key => $scheda) {
							echo "<td>" . ucfirst(str_replace('_', ' ', $key)) . "</td>";
						}
					?>
				</tr>
			</thead>
			<tbody>
				<?
				while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
					$bind = array(":codice"=>$record["codice"]);
					$sql = "SELECT * FROM r_partecipanti_albo WHERE codice_bando = :codice AND ";
					$sql_group = " GROUP BY r_partecipanti_albo.codice_operatore";
					?>
					<tr id="<? echo $record["codice"] ?>">
						<td width="5%"><? echo $record["id"] ?></td>
						<td width="65%"><a href="/albo_fornitori/pannello.php?codice=<? echo $record["codice"] ?>" title="Pannello gara"><? echo $record["oggetto"] ?></a><br>
							<?= $record["descrizione"] ?>
						</td>
						<? if ($_SESSION["ente"]["tipo"] == "SUA") {
							$enteBeneficiario->bindValue(":codice",$record["codice_ente"]);
							$enteBeneficiario->execute(); ?>
							<td>
								<?= $enteBeneficiario->fetch(PDO::FETCH_ASSOC)["denominazione"]; ?>
							</td>
						<? } ?>
						<?
						foreach($schede AS $key => $scheda) {
							$totale = $pdo->go($sql.$scheda["where"].$sql_group,$bind)->rowCount();
							$schede[$key]["totale"] += $totale;
							?>
							<td>
								<h2 style="text-align:center"><?= $totale ?></h2>
							</td>
							<?
							}
						?>
					</tr>
					<?
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="<?= ($_SESSION["ente"]["tipo"] == "SUA") ? 3 : 2 ?>"></td>
					<?
						foreach($schede AS $key => $scheda) {
							?>
							<td>
								<h2 style="text-align:center"><?= $scheda["totale"] ?></h2>
							</td>
							<?
						}
					?>
				</tr>
		</table>
    <div class="clear"></div>

<?php

	}		else {
?><h1 style="text-align:center">
<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
}

		?>
        <a href="/albo_fornitori/id0-edit" title="Inserisci nuovo avviso"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Aggiungi nuovo avviso
        </div></a>
        <? } ?>

<?

	include_once($root."/layout/bottom.php");
	?>
