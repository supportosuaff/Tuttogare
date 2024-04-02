<? if(isset($record)) {
	if ($record["codice_derivazione"]>0) { ?><ul>
	<li><a href="#pubblica">Pubblicazione</a></li>
	<li><a href="#invito">Inviti</a></li>
</ul>
<div id="pubblica">
	<? include($root."/gare/pubblica/common.php"); ?>
</div>
	<div id="invito">
      <?
			$bind=array();
			$bind[":codice_gara"]=$record["codice_derivazione"];
			$strsql  = "SELECT * FROM r_partecipanti WHERE primo = 'S' AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND codice_gara = :codice_gara";
			$ris_operatori  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
			if ($ris_operatori->rowCount()>0) {
			?>
			<table style="text-align:center; width:100%; font-size:0.8em" id="utenti">
				<thead>
					<tr><th>Ragione Sociale</th><th width="100">Partita IVA</th><th>PEC</th></tr>
				</thead>
				<tbody>
					<?
						while ($record_operatore = $ris_operatori->fetch(PDO::FETCH_ASSOC)) {
							$style = "";
							if ($record_operatore["pec"] == "") $style="background-color:#DDD";
							?>
							<tr style="<? echo $style ?>" id="<? echo $record_operatore["codice"] ?>">
								<td style="text-align:left"><strong><? echo strtoupper($record_operatore["ragione_sociale"]) ?></strong></td>
								<td><? echo strtoupper($record_operatore["partita_iva"]); ?></td>
								<td><? echo $record_operatore["pec"]; ?></td>
							</tr>
							<?
						}
						?>
				</tbody>
			</table>
      <script>
			var elenco = $("#utenti").dataTable({
				"paging": true,
				"lengthChange": true,
				"searching": true,
				"ordering": true,
				"info": false,
				"autoWidth": false,
				"pageLength": -1,
				"lengthMenu": [[5,10,25,50,-1],[5, 10, 25, 50,"Tutti"]]});
		 </script>
         <div class="clear"></div>
          <?
	} else {
		?>
			<div class='ui-state-warning padding'>Nessun operatore abilitato nelle categorie selezionate</div>
		<?
	}
		?>
    </div>
<? } } else {
	?>
		<div class='ui-state-warning padding'>Selezionare il bando di riferimento nell'Elaborazione</div>
	<?
} ?>
