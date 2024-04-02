<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("sda",$_SESSION["codice_utente"]);
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
	echo "<h1>GESTIONE BANDI SISTEMI DINAMICI DI ACQUISIZIONE</h1>";


	if ($edit) {
		?>
          <a href="/sda/id0-edit" title="Inserisci nuovo bando"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Aggiungi nuovo bando
        </div></a>
        <?
				$bind = array();
				$bind[":codice_gestore"] = $_SESSION["ente"]["codice"];

				$strsql  = " SELECT * ";
				$strsql .= " FROM b_bandi_sda ";
				$strsql .= " WHERE codice_gestore = :codice_gestore ";
				$strsql .= " ORDER BY codice DESC" ;

				$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso



	if ($risultato->rowCount()>0) {
		$schede = array();
			$schede["nuove_istanze"] = ["where"=>"r_partecipanti_sda.valutato = 'N' AND (r_partecipanti_sda.timestamp_abilitazione IS NULL)","totale"=>0];
			$schede["aggiornamenti"] = ["where"=>"r_partecipanti_sda.valutato = 'N' AND (r_partecipanti_sda.timestamp_abilitazione IS NOT NULL)","totale"=>0];
			$schede["ammessi"] = ["where"=>" r_partecipanti_sda.ammesso = 'S' AND r_partecipanti_sda.valutato = 'S' ","totale"=>0];
			$schede["respinti"] = ["where"=>" r_partecipanti_sda.ammesso = 'N' AND r_partecipanti_sda.valutato = 'S' ","totale"=>0];
	?>
    <table id="pagine" width="100%" id="gare" class="elenco">
    	<thead>
				<tr>
					<td>ID</td><td>Oggetto</td>
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
					$sql = "SELECT * FROM r_partecipanti_sda WHERE codice_bando = :codice AND ";
					$sql_group = " GROUP BY r_partecipanti_sda.codice_operatore";
					?>
					<tr id="<? echo $record["codice"] ?>">
						<td width="5%"><? echo $record["id"] ?></td>
						<td width="75%"><a href="/sda/pannello.php?codice=<? echo $record["codice"] ?>" title="Pannello gara"><? echo $record["oggetto"] ?></a><br>
							<?= $record["descrizione"] ?>
						</td>
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
					<td colspan="2"></td>
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
	}	else {
?><h1 style="text-align:center">
<span class="fa fa-exclamation-circle fa-3x"></span><br>Nessun risultato!</h1>	<?
}

		?>
        <a href="/sda/id0-edit" title="Inserisci nuovo bando"><div class="add_new">
        <span class="fa fa-plus-circle fa-3x"></span><br>
        Aggiungi nuovo bando
        </div></a>
        <? } ?>

<?

	include_once($root."/layout/bottom.php");
	?>
