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
				?>
				<h1>RICHIESTE INTEGRAZIONI</h1>
	            <?
				if ($risultato->rowCount() > 0) {
						$bind = array();
						$bind[":codice"]=$codice;
						$sql_lotti = "SELECT b_lotti.* FROM b_lotti WHERE b_lotti.codice_gara = :codice";
						$sql_lotti.= " GROUP BY b_lotti.codice ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						$print_form = false;
						if ($ris_lotti->rowCount()>0) {
							if (isset($_GET["lotto"])) {
								$codice_lotto = $_GET["lotto"];
								$bind = array();
								$bind[":codice_lotto"]=$codice_lotto;
								$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice_lotto ORDER BY codice";
								$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
								if ($ris_lotti->rowCount()>0) {
									$print_form = true;
									$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
									$record_gara["ribasso"] = $lotto["ribasso"];
									echo "<h2>" . $lotto["oggetto"] . "</h2>";
								}
							} else {
								while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
								?>
									<a class="submit_big" href ="index.php?codice=<? echo $_GET["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
										<? echo $lotto["oggetto"]; ?>
									</a>
								<?
								}
							}
						} else {
							$print_form = true;
							$codice_lotto = 0;
						}
					if ($print_form) {

						$bind = array();
						$bind[":codice"]=$codice;
						$bind[":codice_lotto"]=$codice_lotto;
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql = "SELECT * FROM b_integrazioni WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_ente = :codice_ente ORDER BY timestamp DESC";
						$ris_integrazioni = $pdo->bindAndExec($sql,$bind);
						?>
						<hr>
		        <a href="/gare/integrazioni/edit.php?codice=0&codice_gara=<?=$codice ?>&codice_lotto=<?= $codice_lotto ?>" title="Richiedi nuova integrazione"><div class="add_new">
		        <img src="/img/add.png" alt="Richiedi nuova integrazione"><br>
		        Richiedi nuova integrazione
		        </div></a>
		        <hr>
						<?
						if ($ris_integrazioni->rowCount()>0) {
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
									while ($record = $ris_integrazioni->fetch(PDO::FETCH_ASSOC)) {
									?>
									<tr>
										<td><?= $record["titolo"] ?></td>
										<td>
											<?
												$bind = array();
												$bind[":codice_integrazione"] = $record["codice"];
												$sql = "SELECT r_partecipanti.* FROM r_partecipanti JOIN r_integrazioni ON r_partecipanti.codice = r_integrazioni.codice_partecipante
												WHERE r_integrazioni.codice_integrazione = :codice_integrazione";
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
											<button class='btn-round btn-primary' title="Vedi dati minimi" onClick="window.location.href='/gare/integrazioni/edit.php?codice=<? echo $record["codice"] ?>&codice_gara=<? echo $record["codice_gara"] ?>&codice_lotto=<? echo $record["codice_lotto"] ?>'" title="Vedi"><span class="fa fa-search"></span></button></td>
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
						include($root."/gare/ritorna.php");
					}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
