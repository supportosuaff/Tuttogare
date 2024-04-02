<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
						$edit = $esito["permesso"];
						$lock = $esito["lock"];
					}
					if (!$edit) {
						echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
						die();
					}
				} else {
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
				$codice = $_GET["codice"];
				$bind = array();
				$bind[":codice"]=$codice;
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$gara = $risultato->fetch(PDO::FETCH_ASSOC);
?>
				<h1>LOG GARE</h1>
				<?
				$bind = array();
				$bind[":codice"]=$codice;
				$strsql  = "SELECT b_log_gare.*, b_utenti.cognome, b_utenti.nome FROM b_log_gare JOIN b_utenti ON b_log_gare.utente_modifica = b_utenti.codice WHERE b_log_gare.codice_gara = :codice ";
				$strsql .= " ORDER BY timestamp DESC" ;
				$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
				if ($risultato->rowCount() > 0) {
					$aperture = [];
					$ifase = false;
					if ($pdo->go("SELECT codice FROM r_partecipanti_Ifase WHERE codice_gara = :codice_gara",[":codice_gara"=>$codice])->rowCount() > 0) {
						$ifase = true;
						$sql_aperture  = "SELECT b_log_aperture_IFase.*, b_utenti.cognome, b_utenti.nome, b_criteri_buste.nome AS busta, r_partecipanti_Ifase.ragione_sociale ";
						$sql_aperture .= "FROM b_log_aperture_IFase JOIN b_utenti ON b_log_aperture_IFase.utente_modifica = b_utenti.codice ";
						$sql_aperture .= "JOIN b_criteri_buste ON b_log_aperture_IFase.codice_busta = b_criteri_buste.codice ";
						$sql_aperture .= "JOIN r_partecipanti_Ifase ON b_log_aperture_IFase.codice_partecipante = r_partecipanti_Ifase.codice ";
						$sql_aperture .= "WHERE b_log_aperture_IFase.codice_gara = :codice ";
						$sql_aperture .= "ORDER BY timestamp DESC" ;
						$risultato_aperture_Ifase  = $pdo->bindAndExec($sql_aperture,$bind); //invia la query contenuta in $strsql al database apero e connesso
						$aperture["aperture_Ifase"] = $risultato_aperture_Ifase;
					}
					$sql_aperture  = "SELECT b_log_aperture.*, b_utenti.cognome, b_utenti.nome, b_criteri_buste.nome AS busta, r_partecipanti.ragione_sociale ";
					$sql_aperture .= "FROM b_log_aperture JOIN b_utenti ON b_log_aperture.utente_modifica = b_utenti.codice ";
					$sql_aperture .= "JOIN b_criteri_buste ON b_log_aperture.codice_busta = b_criteri_buste.codice ";
					$sql_aperture .= "JOIN r_partecipanti ON b_log_aperture.codice_partecipante = r_partecipanti.codice ";
					$sql_aperture .= "WHERE b_log_aperture.codice_gara = :codice ";
					$sql_aperture .= "ORDER BY timestamp DESC" ;
					$risultato_aperture  = $pdo->bindAndExec($sql_aperture,$bind); //invia la query contenuta in $strsql al database apero e connesso
					$aperture["aperture"] = $risultato_aperture;
					
					$sql_asta = "SELECT b_offerte_economiche_asta.codice, b_offerte_economiche_asta.stato,b_offerte_economiche_asta.timestamp, r_partecipanti.ragione_sociale, r_partecipanti.partita_iva, b_lotti.oggetto FROM
											 b_offerte_economiche_asta JOIN r_partecipanti ON b_offerte_economiche_asta.codice_partecipante = r_partecipanti.codice
											LEFT JOIN b_lotti ON b_offerte_economiche_asta.codice_lotto = b_lotti.codice WHERE b_offerte_economiche_asta.codice_gara = :codice
											ORDER BY b_offerte_economiche_asta.codice_lotto, b_offerte_economiche_asta.codice DESC";

					$risultato_asta = $pdo->bindAndExec($sql_asta,$bind);
					$risultato_conference = $pdo->go("SELECT * FROM b_zoom WHERE sezione = 'gare' AND codice_elemento = :codice ",$bind);
					
				?>
				<div id="tabs">
					<ul>
						<li><a href="#generale">Generale</a></li>
						<? if (isset($risultato_aperture_Ifase) && $risultato_aperture_Ifase->rowCount() > 0) {  ?> <li><a href="#aperture_Ifase">Apertura buste I Fase</a></li> <? } ?>
						<? if ($risultato_aperture->rowCount() > 0) {  ?> <li><a href="#aperture">Apertura buste</a></li> <? } ?>
						<? if ($risultato_asta->rowCount() > 0) {  ?> <li><a href="#asta">Registro Asta</a></li> <? } ?>
						<? if ($risultato_conference->rowCount() > 0) {  ?> <li><a href="#conference">Conference call</a></li> <? } ?>
					</ul>
					<div id="generale">
						<table style="text-align:center; width:100%; font-size:0.8em" id="utenti" class="elenco">
							<thead>
								<tr><th width="150">Utente</th><th>Operazione</th><th width="200">Data</th></tr>
							</thead>
							<tbody>
								<?
									while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
										$nominativo		= $record["cognome"] . " " . $record["nome"];
										$data			= mysql2completedate($record["timestamp"]);

										switch ($record["operazione"]) {
											case "INSERT": $record["operazione"] = "CREAZIONE"; break;
											case "UPDATE": $record["operazione"] = "AGGIORNAMENTO"; break;
											case "DELETE": $record["operazione"] = "ELIMINAZIONE"; break;
										}
										?>
										<tr>
											<td style="text-align:left"><? echo $nominativo ?></td>
											<td><strong><? echo $record["operazione"] . "</strong> " . $record["oggetto"] ?></td>
											<td><? echo $data ?></td>
										</tr>
										<?
									}
								?>
							</tbody>
						</table>
				 </div>
				 <? 
				 foreach($aperture AS $key => $risultato_aperture) {
					 if ($risultato_aperture->rowCount() > 0) {  ?>
						<div id="<?= $key ?>">
							<table style="text-align:center; width:100%; font-size:0.8em" id="utenti" class="elenco">
        				<thead>
									<tr>
										<th width="150">Utente</th>
										<th>Esito</th>
										<th>Busta</th>
										<th>Partecipante</th>
										<th width="200">Data</th>
									</tr>
								</thead>
								<tbody>
									<?
									while ($record = $risultato_aperture->fetch(PDO::FETCH_ASSOC)) {
										$nominativo		= $record["cognome"] . " " . $record["nome"];
										$data			= mysql2completedate($record["timestamp"]);
										$colore = "#C00";
										if ($record["esito"] == "Positivo") $colore = "#0C0";
										?>
										<tr>
											<td style="text-align:left"><? echo $nominativo ?></td>
												<td><strong style="color:<? echo $colore ?>"><? echo $record["esito"] ?></strong></td>
												<td><? echo $record["busta"] ?></td>
												<td><? echo $record["ragione_sociale"] ?></td>
												<td><? echo $data ?></td>
										</tr>
										<?
									}
									?>
        				</tbody>
							</table>
						</div>
					<? }
					} ?>
		<? if ($risultato_asta->rowCount() > 0) {  ?>
		<div id="asta">
		<table style="text-align:center; width:100%; font-size:0.8em">
			<thead>
				<tr><th>Codice Offerta</th><th>Operatore Economico</th><th>Esito</th><th width="200">Data</th></tr>
			</thead>
			<tbody>
		<?
				$lotto_attuale = "";
				while ($record = $risultato_asta->fetch(PDO::FETCH_ASSOC)) {
					if ($record["oggetto"] != $lotto_attuale) {
						?>
						<tr><td class="etichetta" colspan="4"><?= $record["oggetto"] ?></td></tr>
						<?
					}
				$operatore		= $record["partita_iva"] . " - <strong>" . $record["ragione_sociale"] . "</strong>";
				$data			= mysql2completedate($record["timestamp"]);
				$colore = "#C00";
				$esito = "Non confermata";
				if ($record["stato"] == 1) { $colore = "#0C0"; $esito = "Ultima offerta valida"; }
				if ($record["stato"] == 98) { $colore = "#FC0"; $esito = "Offerta superata"; }
				?>
				<tr>
					<td><? echo "#".$record["codice"] ?></td>
					<td style="text-align:left"><? echo $operatore ?></td>
					<td><strong style="color:<? echo $colore ?>"><? echo $record["stato"] . " - " . $esito ?></strong></td>
					<td><? echo $data ?></td>
				</tr>
					<?

				}

				?>
	</tbody>
		</table>

		</div>
								<?
} ?>
<? if ($risultato_conference->rowCount() > 0) {  ?>
		<div id="conferenceDetails" style="display:none; box-shadow:0px 0px 30px #999999; border:1px solid #999; width:80%; position:absolute; top:5%; left: 10%; background-color:#FFF;padding:20px">
			<button onClick="$('#conferenceDetails').hide();"><span class="fa fa-times"></span> Chiudi</button>
			<div id="conferenceDetailsBox">
			</div>
		</div>
		<script>
		function showConferenceDetails(codice,sub_elemento,contesto) {
			$("#conferenceDetailsBox").html('<div style="text-align:center; padding:50px"><span class="fa fa-spinner fa-spin fa-5x"></span></div>');
			$("#conferenceDetailsBox").load('conference.php?codice=<?= $_GET["codice"] ?>&meeting='+codice+'&sub_elemento='+sub_elemento+'&contesto='+contesto);
			$("#conferenceDetails").show();
		}
		</script>
		<div id="conference">
			<table style="text-align:center; width:100%; font-size:0.8em">
				<thead>
					<tr>
						<td>Evento</td>
						<td>Lotto</td>
						<td>Timestamp</td>
						<td>Dettagli</td>
					</tr>
				</thead>
				<tbody>
					<?
						$lotto = $pdo->prepare("SELECT oggetto FROM b_lotti WHERE codice = :codice");
						while($meeting = $risultato_conference->fetch(PDO::FETCH_ASSOC)) {
							?>
							<tr>
								<td>
									<strong><?= ucfirst($meeting["contesto"]) ?></strong>
								</td>
								<td>
									<? if (!empty($meeting["sub_elemento"])) { 
										$lotto->bindValue(":codice",$meeting["sub_elemento"]);
										$lotto->execute();
										echo $lotto->fetch(PDO::FETCH_ASSOC)["oggetto"];
									 } else { ?>
									 Lotto unico
									<? } ?>
								</td>
								<td width="120"><?= mysql2datetime($meeting["timestamp"]) ?></td>
								<td width="10">
									<button onClick="showConferenceDetails(<?= $meeting["codice"] ?>,<?= $meeting["sub_elemento"] ?>,'<?= base64_encode($meeting["contesto"]) ?>');">
										<span class="fa fa-search"></span>
									</button>
								</td>
							</tr>
							<?
						}
					?>
				</tbody>
			</table>
		</div>
<? } ?>
</div>
<form action="allega.php" method="POST">
	<input type="hidden" name="codice" value="<?= $codice ?>">
	<input type="submit" class="submit_big" value="Allega">
</form><script>
		 $("#tabs").tabs();
		 </script>
         <?
				}
				 include($root."/gare/ritorna.php");
			} else {

				echo "<h1>Gara non trovata</h1>";

				}
			} else {

				echo "<h1>Gara non trovata</h1>";

				}

	?>


<?
	include_once($root."/layout/bottom.php");
	?>
