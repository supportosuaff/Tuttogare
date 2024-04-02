<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
				if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
				if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
					$codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
					if ($codice_fase!==false) {
						$esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
				$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
				$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$gara = $risultato->fetch(PDO::FETCH_ASSOC);
				?>
				<h1>RICHIESTE INTEGRAZIONI</h1>
	       <?


						$bind = array();
						$bind[":codice"]=$codice;
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql = "SELECT b_integrazioni_concorsi.*, b_fasi_concorsi.oggetto AS nome FROM b_integrazioni_concorsi JOIN b_fasi_concorsi ON b_integrazioni_concorsi.codice_fase = b_fasi_concorsi.codice
										WHERE b_integrazioni_concorsi.codice_gara = :codice AND b_integrazioni_concorsi.codice_ente = :codice_ente ORDER BY timestamp DESC";
						$ris_integrazioni = $pdo->bindAndExec($sql,$bind);

						$bind = array();
						$bind[":codice_gara"] = $gara["codice"];
						$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara AND attiva = 'S' AND apertura <= now() ORDER BY codice DESC LIMIT 0,1";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount() > 0) {
							$fase = $ris->fetch(PDO::FETCH_ASSOC);
						}
						if (isset($fase)) {
							?>
							<hr>
			        <a href="/concorsi/integrazioni/edit.php?codice=0&codice_gara=<?=$codice ?>&codice_fase=<?= $fase["codice"] ?>" title="Richiedi nuova integrazione"><div class="add_new">
			        <img src="/img/add.png" alt="Richiedi nuova integrazione"><br>
			        Richiedi nuova integrazione
			        </div></a>
			        <hr>
							<?
						}
						if ($ris_integrazioni->rowCount()>0) {
							?>
							<table width="100%">
								<thead>
									<tr>
										<th>Richiesta</th>
										<th>Operatori</th>
										<th>Fase</th>
										<th width="150">Timestamp</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								<?
									while ($record = $ris_integrazioni->fetch(PDO::FETCH_ASSOC)) {
									?>
									<tr>
										<td><?= $record["titolo"] ?></td>
										<td>
											<?
												$bind = array();
												$bind[":codice_integrazione"] = $record["codice"];
												$sql = "SELECT r_partecipanti_utenti_concorsi.ragione_sociale FROM r_partecipanti_concorsi
																JOIN r_integrazioni_concorsi ON r_partecipanti_concorsi.codice = r_integrazioni_concorsi.codice_partecipante
																JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
																WHERE r_integrazioni_concorsi.codice_integrazione = :codice_integrazione";
												$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
												if ($ris_partecipanti->rowCount() > 0) {
													while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
														echo $partecipante["ragione_sociale"] . "<br>";
													}
												}
											?>
										</td>
										<td><?= $record["nome"] ?></td>
										<td><?= mysql2datetime($record["timestamp"]) ?></td>
										<td width="10" style="text-align:center">
											<button class='btn-round btn-primary' title="Vedi dati minimi" onClick="window.location.href='/concorsi/integrazioni/edit.php?codice=<? echo $record["codice"] ?>&codice_gara=<? echo $record["codice_gara"] ?>&codice_fase=<? echo $record["codice_fase"] ?>'" title="Vedi"><span class="fa fa-search"></span></button></td>
									</tr>
									<?
									}
								?>
								</tbody>
							</table>
							<?
						} else {
							echo "<h2>Nessuna integrazione richiesta</h2>";
						}
						include($root."/concorsi/ritorna.php");
					}
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
