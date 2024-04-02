<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$lock = true;
		if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
			if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
			if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]))
			{
				$fase["codice"] = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
				if ($fase["codice"]!==false) {
					$esito = check_permessi_concorso($fase["codice"],$_GET["codice"],$_SESSION["codice_utente"]);
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
			$strsql = "SELECT * FROM b_concorsi WHERE codice = :codice ";
			$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
			if ($_SESSION["gerarchia"] > 0) {
				$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
				$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {
				$record = $risultato->fetch(PDO::FETCH_ASSOC);
				$codice_gara = $record["codice"];
				$bind = array();
				$bind[":codice_gara"] = $record["codice"];
				$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara AND attiva = 'S' AND apertura <= now() ORDER BY codice DESC LIMIT 0,1";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount() > 0) {
						$fase = $ris->fetch(PDO::FETCH_ASSOC);
						$bind = array();
						$bind[":codice"]=$record["codice"];
						$bind[":codice_fase"]=$fase["codice"];
						$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice_gara = :codice AND codice_fase = :codice_fase AND (conferma = TRUE OR conferma IS NULL)";
						$ris_partecipanti = $pdo->bindAndExec($sql,$bind);

						$bind=array();
						$bind[":codice"] = $fase["codice"];
						$sql = "SELECT * FROM b_criteri_valutazione_concorsi WHERE codice_fase = :codice ";
						$ris_punteggi = $pdo->bindAndExec($sql,$bind);
						$operazione = "UPDATE";
				?><h1>VALUTAZIONE TECNICA</h1><?

						$bind = array();
						$bind[":codice_gara"] = $codice_gara;
						$bind[":codice_fase"] = $fase["codice"];
						$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND ammesso = 'S' AND escluso = 'N' AND (r_partecipanti_concorsi.conferma = TRUE OR r_partecipanti_concorsi.conferma IS NULL) ORDER BY codice ";
						$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);

						if ($ris_r_partecipanti->rowCount()>0)
						{
								$bind = array();
								$bind[":codice_gara"] = $codice_gara;
								$bind[":codice_fase"] = $fase["codice"];
								//Seleziono i criteri di tipo Quantitativo
								$sql_quantitativi  = "SELECT b_criteri_valutazione_concorsi.codice, b_criteri_valutazione_concorsi.descrizione, b_criteri_valutazione_concorsi.punteggio ";
								$sql_quantitativi .= "FROM b_criteri_valutazione_concorsi ";
								$sql_quantitativi .= "WHERE ( ";
									$sql_quantitativi .= "b_criteri_valutazione_concorsi.codice_padre IN ( ";
										$sql_quantitativi .= "SELECT b_criteri_valutazione_concorsi.codice ";
										$sql_quantitativi .= "FROM b_criteri_valutazione_concorsi ";
										$sql_quantitativi .= "WHERE b_criteri_valutazione_concorsi.codice_padre = 0  ";
										$sql_quantitativi .= "AND b_criteri_valutazione_concorsi.codice_gara = :codice_gara ";
										$sql_quantitativi .= "AND b_criteri_valutazione_concorsi.codice_fase = :codice_fase ";
									$sql_quantitativi .= ") ";
								$sql_quantitativi .= ") OR ( ";
									$sql_quantitativi .= "b_criteri_valutazione_concorsi.codice_padre = 0 ";
									$sql_quantitativi .= "AND b_criteri_valutazione_concorsi.codice_gara = :codice_gara ";
									$sql_quantitativi .= "AND b_criteri_valutazione_concorsi.codice_fase = :codice_fase ";
									$sql_quantitativi .= "AND b_criteri_valutazione_concorsi.codice NOT IN ( ";
										$sql_quantitativi .= "SELECT b_criteri_valutazione_concorsi.codice_padre ";
										$sql_quantitativi .= "FROM b_criteri_valutazione_concorsi ";
										$sql_quantitativi .= "WHERE b_criteri_valutazione_concorsi.codice_gara = :codice_gara ";
										$sql_quantitativi .= "AND b_criteri_valutazione_concorsi.codice_fase = :codice_fase ";
										$sql_quantitativi .= "GROUP BY b_criteri_valutazione_concorsi.codice_padre ";
									$sql_quantitativi .= ") ";
								$sql_quantitativi .= ")";

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
										$partecipanti[$i] = [$rec_partecipanti["codice"], $ch,  $rec_partecipanti["identificativo"]];
										$ch++;
										$i++;
									}


										if (isset($_POST) && in_array("codice_gara", array_keys($_POST)) && in_array("valutazione", array_keys($_POST)) && is_array($_POST["valutazione"]))
										{
											$values = $_POST["valutazione"];

											foreach ($values as $codice_criterio => $codici_partecipante)
											{
												$dati = array();
												unset($dati["codice"]);
												$dati["codice_fase"] = (string)$fase["codice"];
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
														$bind[":codice_fase"] = $dati["codice_fase"];
														$bind[":codice_gara"] = $dati["codice_gara"];
														$bind[":codice_criterio"] = $dati["codice_criterio"];
														$bind[":codice_partecipante"] = $dati["codice_partecipante"];
														$check_sql  = "SELECT * FROM b_punteggi_criteri_concorsi ";
														$check_sql .= "WHERE codice_fase = :codice_fase ";
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
														$salva->nome_tabella = "b_punteggi_criteri_concorsi";
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
											log_concorso($_SESSION["ente"]["codice"],$codice_gara,"UPDATE","Valutazione Tecnica");
										}
										else
										{
											foreach ($criteri as $criterio)
											{
												$bind = array();
												$bind[":codice_gara"] = $codice_gara;
												$bind[":codice_fase"] = $fase["codice"];
												$bind[":codice_criterio"] = $criterio["codice"];
												$sql  = "SELECT * FROM b_punteggi_criteri_concorsi ";
												$sql .= "WHERE codice_gara = :codice_gara ";
												$sql .= "AND codice_fase = :codice_fase ";
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
											$bind[":codice_fase"] = $fase["codice"];
											$sql_check = "SELECT b_offerte_decriptate_concorso.* FROM b_offerte_decriptate_concorso JOIN r_partecipanti_concorsi ON b_offerte_decriptate_concorso.codice_partecipante = r_partecipanti_concorsi.codice
											WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND b_offerte_decriptate_concorso.tipo = 'tecnica'";

											$ris_check = $pdo->bindAndExec($sql_check,$bind);
											if ($ris_check->rowCount() > 0) {
											?>
											<form name="box" method="post" action="importa_offerte.php">
												<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
												<input type="hidden" name="codice_fase" value="<? echo $fase["codice"]; ?>">
												<input type="submit" class="submit_big" style="background-color: #FC0" value="Importa Punteggi">
											</form>
											<? } ?>
											<form action="edit.php?codice=<?= $codice_gara ?>" method="POST" role="form" target="_self">
												<input type="hidden" name="codice_gara" id="inputCodice_gara" class="form-control" value="<?= $codice_gara ?>">
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
																value="<? if(!empty($values[$criterio["codice"]][$partecipante[0]]["punteggio"])) echo $values[$criterio["codice"]][$partecipante[0]]["punteggio"] ?>" title="Punteggio <?= $criterio["descrizione"] ?> - <?= $partecipante[2] ?>"></td>
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
						} else {
							echo "<h1>Errore</h1>";
							echo "<h3>E' necessario inserire i partecipanti alla gara</h3>";
						}
					} else {
						echo "<h1>IMPOSSIBILE ACCEDERE</h1>";
						echo "<h3>Procedure di negoziazione aperte</h3>";
					}
				}
				include($root."/concorsi/ritorna.php");
			}
			else
			{
				echo "<h1>Gara non trovata</h1>";
			}

	include_once($root."/layout/bottom.php");
?>
