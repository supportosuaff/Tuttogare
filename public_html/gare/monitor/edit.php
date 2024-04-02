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

				$strsql = "SELECT * FROM b_gare WHERE codice = :codice";
				$strsql .= " AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
				if ($_SESSION["gerarchia"] > 0) {
					$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
					$strsql .= " AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
				}

				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$filtro_mercato = "";

					$bind = array();
					$bind[":codice"]=$record["procedura"];

					$strsql = "SELECT * FROM b_procedure WHERE mercato_elettronico = 'S' AND codice = :codice";
					$ris_mercato = $pdo->bindAndExec($strsql,$bind);
					if ($ris_mercato->rowCount()>0) $filtro_mercato = " AND mercato_elettronico = 'S' ";

					$bind = array();
					$bind[":codice"]=$record["criterio"];

					$sql = "SELECT * FROM b_criteri_buste WHERE codice_criterio= :codice " . $filtro_mercato . " ORDER BY ordinamento ";
					$ris_buste = $pdo->bindAndExec($sql,$bind);
					$ris_buste = $ris_buste->fetchAll(PDO::FETCH_ASSOC);
					$operazione = "UPDATE";
					?>
					<h1>MONITOR GARA</h1>
					<?
					$bind = array();
					$bind[":codice"]=$record["codice"];
					$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice ORDER BY codice";
					$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
					$print_form = false;
					if ($ris_lotti->rowCount()>0) {
						if (isset($_GET["lotto"])) {
							$codice_lotto = $_GET["lotto"];
							$bind = array();
							$bind[":codice"]=$codice_lotto;
							$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice ORDER BY codice";
							$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
							if ($ris_lotti->rowCount()>0) {
								$print_form = true;
								$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
								echo "<h2>" . $lotto["oggetto"] . "</h2>";
							}
						} else {
							?>
							<table width="100%">
								<tr><th>Lotto</th><th width="10">Partecipanti</th></tr>
							<?
							while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
								$bind = array();
								$bind[":codice"]=$record["codice"];
								$bind[":codice_lotto"]=$lotto["codice"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0";
								$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
								?>
								<tr>
								<td>
									<a class="submit_big" href ="edit.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
										<? echo $lotto["oggetto"] ?>
									</a>
								</td>
								<td style="text-align:center">
									<strong style="font-size:24px"><? echo $ris_partecipanti->rowCount() ?></strong>
								</td></tr>
								<?
							}
							?>
							</table>
							<?
						}
					} else {
						$print_form = true;
						$codice_lotto = 0;
					}
						if ($print_form) {

							$bind = array();
							$bind[":codice"]=$record["codice"];
							$bind[":codice_lotto"]=$codice_lotto;

							if ($print_form) {
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 ";
							$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipanti->rowCount()>0) {
							?>

							<table id="buste" width="100%">
								<thead>
									<tr>
										<td width="10"></td>
										<td>Partita IVA</td>
										<td>Ragione Sociale</td>
										<?
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
												<td width="10">
													<strong><? echo $record_partecipante["partita_iva"] ?></strong></td>
													<td><? if ($record_partecipante["tipo"] != "") echo "<strong>RAGGRUPPAMENTO</strong> - " ?><? echo $record_partecipante["ragione_sociale"] ?>    </td>
													<?
														if (count($ris_buste)>0) {
														foreach($ris_buste AS $busta) {
																	$bind = array();
																	$bind[":codice_partecipante"] = $record_partecipante["codice"];
																	$bind[":codice_busta"] = $busta["codice"];
																	$bind[":codice_gara"] = $record["codice"];
																	$sql  = "SELECT * FROM b_buste WHERE codice_partecipante = :codice_partecipante AND codice_busta  = :codice_busta";
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
																		<? if (file_exists($config["doc_folder"] . "/" . $rec_busta["codice_gara"] . "/" . $rec_busta["codice_lotto"] . "/" . $rec_busta["nome_file"])) {
																			echo human_filesize(filesize($config["doc_folder"] . "/" . $rec_busta["codice_gara"] . "/" . $rec_busta["codice_lotto"] . "/" . $rec_busta["nome_file"]));
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
									} else {
										echo "<h1>Errore</h1>";
										echo "<h3>E' necessario inserire i partecipanti alla gara</h3>";
									}
								} else {
									echo "<h1>IMPOSSIBILE ACCEDERE</h1>";
									echo "<h3>Procedure di negoziazione aperte</h3>";
								}
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
