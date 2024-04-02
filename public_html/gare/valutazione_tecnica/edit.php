<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
			if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
			if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]))
			{
				$codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
				if ($codice_fase!==false) {
					$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
					$edit = $esito["permesso"];
					$lock = $esito["lock"];
				}
				if (!$edit)
				{
					echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
					die();
				}
			}
			else
			{
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
			$strsql .= " AND data_apertura <= now() ";
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {
				$record = $risultato->fetch(PDO::FETCH_ASSOC);
				$codice_gara = $record["codice"];
				$bind=array();
				$bind[":codice"] = $record["criterio"];
				$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio = :codice ORDER BY ordinamento ";
				$ris_punteggi = $pdo->bindAndExec($sql,$bind);
				$operazione = "UPDATE";
				?><h1>VALUTAZIONE OFFERTA</h1><?
				$bind=array();
				$bind[":codice_gara"] = $codice_gara;
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
				$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				$print_form = false;
				if ($ris_lotti->rowCount()>0)
				{
					if (isset($_GET["lotto"]))
					{
						$codice_lotto = $_GET["lotto"];
						$bind=array();
						$bind[":codice_lotto"] = $codice_lotto;

						$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice_lotto ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						if ($ris_lotti->rowCount()>0)
						{
							$print_form = true;
							$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
							echo "<h2>" . $lotto["oggetto"] . "</h2>";
						}
					}
					else
					{
						while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC))
						{
							$bind=array();
							$bind[":codice_gara"] = $codice_gara;
							$bind[":codice_lotto"] = $lotto["codice"];
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0
											AND ammesso = 'S' AND escluso = 'N' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND primo = 'S'";
							$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
							$style = "";
							$primo = "";
							if ($ris_partecipanti->rowCount()>0)
							{
								$primo = $ris_partecipanti->fetch(PDO::FETCH_ASSOC);
								$primo = "<br>" . $primo["partita_iva"] . " - " . $primo["ragione_sociale"];
								$style = "style=\"background-color:#0C0\"";
							}
							?>
								<a class="submit_big" <?= $style ?> href ="edit.php?codice=<?= $record["codice"] ?>&lotto=<?= $lotto["codice"] ?>">
									<?= $lotto["oggetto"] . $primo ?>
								</a>
							<?
						}
					}
				}
				else
				{
					$print_form = true;
					$codice_lotto = 0;
				}
				if ($print_form)
				{
					$bind = array();
					$bind[":codice"]=$record["codice"];
					$bind[":codice_lotto"] = $codice_lotto;

					$sql_fasi = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now() ";
					$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
					if ($ris_fasi->rowCount()>0) {
						$print_form = false;
					} else {
						$sql_fasi = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_fine > now() ";
						$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
						if ($ris_fasi->rowCount()>0) $lock = true;
					}

					$sql_fasi = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now() ";
					$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
					if ($ris_fasi->rowCount()>0) {
						$print_form = false;
					} else {
						$sql_fasi = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_fine < now() AND data_fine > 0 ";
						$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
						if ($ris_fasi->rowCount()>0) $lock = true;
					}
					if ($print_form)
					{
						$bind = array();
						$bind[":codice_gara"] = $codice_gara;
						$bind[":codice_lotto"] = $codice_lotto;
						$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0
										AND (
													(ammesso = 'S' AND escluso = 'N') OR
													(r_partecipanti.codice IN (SELECT codice_partecipante FROM b_punteggi_criteri WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto))
												)
										AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
						$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);

						if ($ris_r_partecipanti->rowCount()>0)
						{
							$bind = array();
							$bind[":codice"]=$record["codice"];
							$strsql = "SELECT b_criteri.* FROM b_criteri JOIN b_gare ON b_criteri.codice = b_gare.criterio WHERE b_gare.codice = :codice";
							$ris_criterio = $pdo->bindAndExec($strsql,$bind);
							if ($ris_criterio->rowCount()>0)
							{
								$bind = array();
								$bind[":codice_gara"] = $codice_gara;
								$n_partecipanti = $ris_r_partecipanti->rowCount();
								$sql_confronto = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 124";
								$ris_confronto = $pdo->bindAndExec($sql_confronto,$bind);
								$confronto_coppie = false;
								if ($ris_confronto->rowCount()>0) {
									$confronto_coppie = true;
								?>
								<div class="padding">
									<h3>STATO AVANZAMENTO CONFRONTO A COPPIE</h3>
									<div id="avanzamento">
										<? include_once("avanzamento.php"); ?>
									</div>
								</div>
								<script type="text/javascript">
									$(document).ready(function() {
										setInterval(function(){
											$('#avanzamento').load(
												'avanzamento.php',
												{codice: "<?= $codice_gara ?>", partecipanti: "<?= $n_partecipanti ?>", lotto: "<?= $codice_lotto ?>"} ,
												function(){
													f_ready();
												}
											);
										}, 30000);
									});
								</script>
								<?
								}
								$bind = array();
								$bind[":codice_gara"] = $codice_gara;
								//Seleziono i criteri di tipo Quantitativo
								$sql_quantitativi  = "SELECT b_valutazione_tecnica.codice, b_valutazione_tecnica.descrizione, b_valutazione_tecnica.punteggio ";
								$sql_quantitativi .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice  ";
								$sql_quantitativi .= "WHERE ( ";
									$sql_quantitativi .= "b_valutazione_tecnica.codice_padre IN ( ";
										$sql_quantitativi .= "SELECT b_valutazione_tecnica.codice ";
										$sql_quantitativi .= "FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice  ";
										$sql_quantitativi .= "WHERE b_valutazione_tecnica.codice_padre = 0  ";
										$sql_quantitativi .= "AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
										$sql_quantitativi .= "AND b_valutazione_tecnica.codice_gara = :codice_gara ";
										if ($confronto_coppie) $sql_quantitativi .= "AND b_valutazione_tecnica.tipo = 'N' ";
									$sql_quantitativi .= ") ";
								$sql_quantitativi .= ") OR ( ";
									$sql_quantitativi .= "b_valutazione_tecnica.codice_padre = 0 ";
									$sql_quantitativi .= "AND b_valutazione_tecnica.codice_gara = :codice_gara ";
									$sql_quantitativi .= "AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
									$sql_quantitativi .= "AND b_valutazione_tecnica.codice NOT IN ( ";
										$sql_quantitativi .= "SELECT b_valutazione_tecnica.codice_padre ";
										$sql_quantitativi .= "FROM b_valutazione_tecnica ";
										$sql_quantitativi .= "WHERE b_valutazione_tecnica.codice_gara = :codice_gara ";
										$sql_quantitativi .= "GROUP BY b_valutazione_tecnica.codice_padre ";
										if ($confronto_coppie) $sql_quantitativi .= "AND b_valutazione_tecnica.tipo = 'N' ";
									$sql_quantitativi .= ") ";
								$sql_quantitativi .= ")";
								if ($confronto_coppie)  $sql_quantitativi .= " AND b_valutazione_tecnica.tipo = 'N'";

								$ris_quantitativi = $pdo->bindAndExec($sql_quantitativi,$bind);
								if ($ris_quantitativi->rowCount() > 0)
								{
									$criteri = array();
									while ($rec_quantitativi = $ris_quantitativi->fetch(PDO::FETCH_ASSOC))
									{
										$criteri[$rec_quantitativi["codice"]] = ["descrizione" => $rec_quantitativi["descrizione"], "punteggio" => $rec_quantitativi["punteggio"], "codice" => $rec_quantitativi["codice"]];
									}

									$ch = "A";
									$i = 0;
									$partecipanti = array();
									while ($rec_partecipanti = $ris_r_partecipanti->fetch(PDO::FETCH_ASSOC)) {
										$partecipanti[$i] = [$rec_partecipanti["codice"], $ch,  $rec_partecipanti["ragione_sociale"]];
										$ch++;
										$i++;
									}


										$values = array();
										if (isset($_POST) && in_array("codice_lotto", array_keys($_POST)) && in_array("codice_gara", array_keys($_POST)) && in_array("valutazione", array_keys($_POST)) && is_array($_POST["valutazione"]))
										{
											$values = $_POST["valutazione"];

											foreach ($values as $codice_criterio => $codici_partecipante)
											{
												$dati = array();
												unset($dati["codice"]);
												$dati["codice_lotto"] = (string)$codice_lotto;
												$dati["codice_gara"] = $codice_gara;
												$dati["codice_criterio"] = $codice_criterio;

												foreach ($codici_partecipante as $codice_partecipante => $valore)
												{
													$dati["codice_partecipante"] = $codice_partecipante;
													$dati["punteggio"] = $valore["punteggio"];

													$dati["punteggio"] = str_replace(",", ".", $dati["punteggio"]);

													if ($dati["punteggio"] == "")
													{
														continue;
													}
													else if (!is_numeric($dati["punteggio"]) || $dati["punteggio"] > $criteri[$codice_criterio]["punteggio"] )
													{
														$errore_salvataggio = true;
														$values[$codice_criterio][$codice_partecipante]["error"] = true;
													}
													else
													{
														$bind = array();
														$bind[":codice_lotto"] = $dati["codice_lotto"];
														$bind[":codice_gara"] = $dati["codice_gara"];
														$bind[":codice_criterio"] = $dati["codice_criterio"];
														$bind[":codice_partecipante"] = $dati["codice_partecipante"];
														$check_sql  = "SELECT * FROM b_punteggi_criteri ";
														$check_sql .= "WHERE codice_lotto = :codice_lotto ";
														$check_sql .= "AND codice_gara = :codice_gara ";
														$check_sql .= "AND codice_criterio = :codice_criterio ";
														$check_sql .= "AND codice_partecipante = :codice_partecipante ";

														$res_check = $pdo->bindAndExec($check_sql,$bind);
														$operazione_query = "INSERT";
														$codice_query = 0;
														$dati["codice"] = 0;
														if ($res_check->rowCount() > 0)
														{
															$rec_check = $res_check->fetch(PDO::FETCH_ASSOC);
															$operazione_query = "UPDATE";
															$codice_query = $rec_check["codice"];
															$dati["codice"] = $codice_query;
														}
														$salva = new salva();
														$salva->debug = false;
														$salva->codop = $_SESSION["codice_utente"];
														$salva->nome_tabella = "b_punteggi_criteri";
														$salva->operazione = $operazione_query;
														$salva->oggetto = $dati;
														$codice = $salva->save();
														if (!$codice)
														{
															$errore_salvataggio = true;
															$values[$codice_criterio][$codice_partecipante]["error"] = true;
														}

													}
												}
											}
											log_gare($_SESSION["ente"]["codice"],$codice_gara,"UPDATE","Valutazione Tecnica");
										}
										else
										{
											foreach ($criteri as $criterio)
											{
												$bind = array();
												$bind[":codice_gara"] = $codice_gara;
												$bind[":codice_lotto"] = $codice_lotto;
												$bind[":codice_criterio"] = $criterio["codice"];
												$sql  = "SELECT * FROM b_punteggi_criteri ";
												$sql .= "WHERE codice_gara = :codice_gara ";
												$sql .= "AND codice_lotto = :codice_lotto ";
												$sql .= "AND codice_criterio = :codice_criterio";
												$ris = $pdo->bindAndExec($sql,$bind);
												if ($ris->rowCount() > 0)
												{
													while ($rec = $ris->fetch(PDO::FETCH_ASSOC))
													{
														$values[$criterio["codice"]][$rec["codice_partecipante"]]["punteggio"] = $rec["punteggio"];
													}
													foreach ($partecipanti as $partecipante)
													{
														if (!isset($values[$criterio["codice"]][$partecipante[0]]["punteggio"]))
														{
															$values[$criterio["codice"]][$partecipante[0]]["punteggio"] = "";
														}
													}
												}
												else
												{
													foreach ($partecipanti as $partecipante)
													{
														$values[$criterio["codice"]][$partecipante[0]]["punteggio"] = "";
													}
												}
											}
										}
										if (!$lock) {
											$bind = array();
											$bind[":codice_gara"] = $record["codice"];
											$bind[":codice_lotto"] = $codice_lotto;
											$sql_check = "SELECT b_offerte_decriptate.* FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
											WHERE r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto AND b_offerte_decriptate.tipo = 'tecnica'";

											$ris_check = $pdo->bindAndExec($sql_check,$bind);
											if ($ris_check->rowCount() > 0) {
											?>
											<form name="box" method="post" action="importa_offerte.php">
												<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
												<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
												<input type="submit" class="submit_big" style="background-color: #FC0" value="Importa Punteggi">
											</form>
											<? } ?>
											<form action="edit.php?codice=<?= $codice_gara ?>&lotto=<?= $codice_lotto ?>" method="POST" role="form" target="_self">
												<input type="hidden" name="codice_gara" id="inputCodice_gara" class="form-control" value="<?= $codice_gara ?>">
												<input type="hidden" name="codice_lotto" id="inputCodice_lotto" class="form-control" value="<?= $codice_lotto ?>">
											<?
										}
									?>
									<div class="padding">
										<?
										$c = "1";
										$sc = "1";
										foreach ($partecipanti as $pos => $partecipante) {
											?>
											<table width="100%">
												<thead>
													<tr>
														<td><strong><?= $partecipante[2] ?></td>
														<td width="30">Punteggi</td>
													</tr>
												</thead>
												<tbody>
													<?
													foreach ($criteri as $chiave => $criterio)
													{
														?>
														<tr>
															<td><strong><?= $criterio["descrizione"] ?></strong> - Max <?= $criterio["punteggio"] ?> punti</td>
															<td><input type="text" name="valutazione[<?= $criterio["codice"] ?>][<?= $partecipante[0] ?>][punteggio]" id="inputValutazione_<?= $criterio["codice"] ?>_<?= $partecipante[0] ?>"
																rel="S;0;0;N;<?= $criterio["punteggio"] ?>;<="
																class="<? if (isset($values[$criterio["codice"]][$partecipante[0]]["error"])) echo 'ui-state-error' ?>"
																value="<? if(isset($values[$criterio["codice"]][$partecipante[0]]["punteggio"])) echo $values[$criterio["codice"]][$partecipante[0]]["punteggio"] ?>" title="Punteggio <?= $criterio["descrizione"] ?> - <?= $partecipante[2] ?>"></td>
														</tr>
														<?
													}
													?>
												</tbody>
											</table>
											<?
										}
									?>
									</div>
									<?
									if (!$lock)
									{
									?>
										<button type="submit"  class="submit_big" style="cursor:pointer">SALVA VALUTAZIONE</button>
									</form>
									<?
									} else { ?>
										<script>
												 $("*:input").not('.espandi').prop("disabled", true);
										</script> <?
									}
								}
						   	}
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
			}
			else
			{
				echo "<h1>Gara non trovata</h1>";
			}
		}
		else
		{
			echo "<h1>Gara non trovata</h1>";
		}
	include_once($root."/layout/bottom.php");
?>
