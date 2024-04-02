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
				if ($risultato->rowCount() > 0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					if ($record_gara["dialogo_chiuso"]=="S") $lock = true;
				?>
				<h1>RICHIESTE</h1>
	         <?
						$bind = array();
						$bind[":codice"]=$codice;
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql = "SELECT * FROM b_dialogo WHERE codice_gara = :codice AND codice_ente = :codice_ente ORDER BY timestamp DESC";
						$ris_dialogo = $pdo->bindAndExec($sql,$bind);
						if ($record_gara["dialogo_chiuso"]!="S") {
						?>
						<hr>
		        <a href="/gare/dialogo/edit.php?codice=0&codice_gara=<?=$codice ?>" title="Nuova richiesta"><div class="add_new">
		        <img src="/img/add.png" alt="Nuova richiesta"><br>
		        Nuova richiesta
		        </div></a>
		        <hr>
						<?
						}
						if ($ris_dialogo->rowCount()>0) {
							?>
							<table width="100%">
								<thead>
									<tr>
										<th>Richiesta</th>
										<th>Operatori</th>
										<th width="150">Timestamp</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								<?
									while ($record = $ris_dialogo->fetch(PDO::FETCH_ASSOC)) {
									?>
									<tr>
										<td><?= $record["titolo"] ?></td>
										<td>
											<?
												$bind = array();
												$bind[":codice_dialogo"] = $record["codice"];
												$sql = "SELECT r_partecipanti.* FROM r_partecipanti JOIN r_dialogo ON r_partecipanti.codice = r_dialogo.codice_partecipante
												WHERE r_dialogo.codice_dialogo = :codice_dialogo";
												$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
												if ($ris_partecipanti->rowCount() > 0) {
													while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
														echo $partecipante["ragione_sociale"] . "<br>";
													}
												}
											?>
										</td>
										<td><?= mysql2datetime($record["timestamp"]) ?></td>
										<td width="10" style="text-align:center">
											<button class='btn-round btn-primary' title="Vedi dati minimi" onClick="window.location.href='/gare/dialogo/edit.php?codice=<? echo $record["codice"] ?>&codice_gara=<? echo $record["codice_gara"] ?>'" title="Vedi"><span class="fa fa-search"></span></button></td>
									</tr>
									<?
									}
								?>
								</tbody>
							</table>
							<? if ($record_gara["dialogo_chiuso"]!="S") { ?>
							<div id="form_chiusura" style="display:none">
								<form action="chiudi.php" method="post" rel="validate">
										<input type="hidden" name="codice" value="<? echo $record_gara["codice"] ?>">
										<input type="hidden" name="dialogo_chiuso" value="S">
										<h2>Termine di presentazione delle offerte</h2>
										<table width="100%" id="date">
											<tr>
												<td class="etichetta">Termine richieste chiarimenti</td>    <td><input type="text" inline="true" class="datetimepick" title="Termine richieste chiarimenti"  name="data_accesso" id="data_accesso" value="<? echo mysql2datetime($record["data_accesso"]); ?>" rel="S;16;16;DT">
												</td>
												<td class="etichetta">Termine ricevimento offerte</td>
												<td>
													<input type="text" class="datetimepick" title="Termine ricevimento offerte"  name="data_scadenza" id="data_scadenza" value="<? echo mysql2datetime($record["data_scadenza"]) ?>" rel="S;16;16;DT;data_accesso;>">
												</td>
												<td class="etichetta">Apertura offerte</td>
												<td>
													<input type="text" class="datetimepick" title="Apertura offerte"  name="data_apertura" id="data_apertura" value="<? echo mysql2datetime($record["data_apertura"]) ?>" rel="S;16;16;DT;data_scadenza;>">
												</td>
											</tr>
										</table>
										<input type="submit" class="submit_big" value="Chiudi il dialogo" onClick="return confirm('I partecipanti riceveranno una PEC di invito a presentare le offerte entro i termini indicati e non sarà piu possibile generare ulteriori richieste. Vuoi continuare?');">
								</form>
							</div>
							<input type="submit" class="submit_big" value="Chiudi il dialogo" onClick="$('#form_chiusura').dialog({width:'800px',modal:'true',title:'Chiusura dialogo'});">
							<? } ?>
							<?
						} else {
							echo "<h2>Nessuna richiesta presente</h2>";
						}
						include($root."/gare/ritorna.php");
			} else {
				echo "<h1>Gara non trovata</h1>";
			}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
	include_once($root."/layout/bottom.php");
?>
