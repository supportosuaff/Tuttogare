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

				$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice";
				$strsql .= " AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= " AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}

				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);

					?>
					<h1>MONITOR GARA</h1>
					<?
					$bind = array();
					$bind[":codice_gara"] = $record["codice"];
					$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara AND attiva = 'S' ORDER BY codice DESC LIMIT 0,1";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount() > 0) {
					$fase = $ris->fetch(PDO::FETCH_ASSOC);

							echo "<h2>" . $fase["oggetto"] . "</h2>";
							$bind = array();
							$bind[":codice"]=$record["codice"];
							$bind[":codice_fase"]=$fase["codice"];

							$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice_gara = :codice AND codice_fase = :codice_fase ";
							$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipanti->rowCount()>0) {
							?>

							<table id="buste" width="100%">
								<thead>
									<tr>
										<td width="10"></td>
										<td>ID</td>

										<?
										$bind = array();
										$strsql = "SELECT b_fasi_concorsi_buste.* FROM b_fasi_concorsi_buste ORDER BY codice";
										$ris_buste = $pdo->bindAndExec($strsql,$bind);

										if ($ris_buste->rowCount() > 0) {
											$ris_buste = $ris_buste->fetchAll(PDO::FETCH_ASSOC);
											if (count($ris_buste)>0) {
											foreach($ris_buste AS $busta) {
											?>
												<td><? echo $busta["nome"] ?></td>
											<?
											}
										}	?>
									</tr>
								</thead>
								<tbody>
								<?
								$contatore = 0;
								while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
									$contatore++;
									?>
									<tr id="<? echo $record_partecipante["codice"] ?>">
												<td width="10" style="background-color:#<?= ($record_partecipante["conferma"] == TRUE) ? '0F0' : '333' ?>">
													<?= $contatore ?>
												</td>
												<td>
													<strong><? echo $record_partecipante["identificativo"] ?></strong></td>
													<?
														if (count($ris_buste)>0) {
														foreach($ris_buste AS $busta) {
																	$bind = array();
																	$bind[":codice_partecipante"] = $record_partecipante["codice"];
																	$bind[":codice_busta"] = $busta["codice"];
																	$bind[":codice_gara"] = $record["codice"];
																	$sql  = "SELECT * FROM b_buste_concorsi WHERE codice_partecipante = :codice_partecipante AND codice_busta  = :codice_busta";
																	$sql .= " AND codice_gara = :codice_gara";
																	$ris_exist = $pdo->bindAndExec($sql,$bind);
																	?>
																	<td width="150" id="<? echo $record_partecipante["codice"] . "_" . $busta["codice"] ?>" style="text-align:center">
																	<?
																	if ($ris_exist->rowCount()>0) {
																		$rec_busta = $ris_exist->fetch(PDO::FETCH_ASSOC);
																		?>
																		<strong style="color:#0F0">Presentata</strong><br>
																		<small>
																		<?= mysql2datetime($rec_busta["timestamp"]); ?><br>
																		<? if (file_exists($config["doc_folder"] . "/concorsi/" . $rec_busta["codice_gara"] . "/" . $rec_busta["codice_fase"] . "/" . $rec_busta["nome_file"])) {
																			echo human_filesize(filesize($config["doc_folder"] . "/concorsi/" . $rec_busta["codice_gara"] . "/" . $rec_busta["codice_fase"] . "/" . $rec_busta["nome_file"]));
																		} else {
																			echo "File rimosso";
																		}
																		?>
																		</small>
																		<? } else { ?>
																				<strong style="color:#f00">Non presentata</strong>
																			<? } ?>
																		</td>
																<?	}
															} ?>
									</tr>
									<?
								}
								?></tbody>
								</table>
								<?
									}
								} else {
									echo "<h1>Errore</h1>";
									echo "<h3>E' necessario inserire i partecipanti al concorso</h3>";
								}
							}
							include($root."/concorsi/ritorna.php");
						} else {
							echo "<h1>Concorso non trovata</h1>";
						}
					} else {
						echo "<h1>Concorso non trovata</h1>";
					}
?>
<?
	include_once($root."/layout/bottom.php");
	?>
