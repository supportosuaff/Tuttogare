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
				$strsql .= " AND data_apertura <= now() ";

				$risultato = $pdo->bindAndExec($strsql,$bind);

				if ($risultato->rowCount() > 0) {
					if (isset($_GET["lotto"])) {
						$codice_lotto = $_GET["lotto"];
					}
					$record = $risultato->fetch(PDO::FETCH_ASSOC);
					$filtro_mercato = "";

					$bind = array();
					$bind[":codice"]=$record["procedura"];

					$strsql = "SELECT * FROM b_procedure WHERE mercato_elettronico = 'S' AND codice = :codice";
					$ris_mercato = $pdo->bindAndExec($strsql,$bind);
					if ($ris_mercato->rowCount()>0) $filtro_mercato = " AND mercato_elettronico = 'S' ";

					$bind = array();
					$bind[":codice"]=$record["procedura"];

					$strsql = "SELECT * FROM b_procedure WHERE fasi = 'S' AND codice = :codice";
					$ris_fase = $pdo->bindAndExec($strsql,$bind);
					$openSeduta = true;
					if ($ris_fase->rowCount()>0) {
						$openSeduta = false;
						$bind = array();
						$bind[":codice"]=$record["codice"];
						$bind[":codice_lotto"]=$codice_lotto;

						$sql_fasi_apertura = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto";
						$ris_fasi_apertura = $pdo->bindAndExec($sql_fasi_apertura,$bind);
						if ($ris_fasi_apertura->rowCount()>0) {
							$openSeduta = true;
						}
					}

					$bind = array();
					$bind[":codice"]=$record["criterio"];

					$sql = "SELECT * FROM b_criteri_buste WHERE codice_criterio= :codice " . $filtro_mercato . " ORDER BY ordinamento ";
					$tmp_buste = $pdo->bindAndExec($sql,$bind);
					$tmp_buste = $tmp_buste->fetchAll(PDO::FETCH_ASSOC);

					$ris_buste = [];
					foreach ($tmp_buste as $busta) {
						if ($_SESSION["gerarchia"] > 0) {
							$sql = "SELECT codice_utente FROM r_permessi_apertura_buste WHERE codice_gara = :codice_gara AND codice_busta = :codice_busta ";
							$ris_perm = $pdo->bindAndExec($sql,[":codice_gara"=>$record["codice"],":codice_busta"=>$busta["codice"]]);
							if ($ris_perm->rowCount() > 0) {
								while($perm = $ris_perm->fetch(PDO::FETCH_ASSOC)) {
									if ($perm["codice_utente"] == $_SESSION["codice_utente"]) {
										$ris_buste[] = $busta;
										continue;
									}
								}
							} else {
								$ris_buste[] = $busta;
							}
						} else {
							$ris_buste[] = $busta;
						}
					}
					if (count($ris_buste) > 0) {
					$operazione = "UPDATE";
					if (!isset($Ifase)) {
					?>
						<h1>APERTURA BUSTE</h1>
					<?
					} else {
						?>
						<h1>DOCUMENTAZIONE I FASE</h1>
						<?
					}
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
								if (isset($Ifase)) {
									$sql = "SELECT * FROM r_partecipanti_Ifase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti_Ifase.conferma = TRUE OR r_partecipanti_Ifase.conferma IS NULL)";
								} else {
									$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
								}
								// $sql.= "";
								$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
								?>
								<tr>
								<td>
									<a class="submit_big" href ="<?= (isset($Ifase)) ? "Ifase" : "edit"  ?>.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
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

							$sql_fasi = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now() ";
							$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
							if ($ris_fasi->rowCount()>0) {
								$print_form = false;
							}
							$sql_fasi = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now() ";
							$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
							if ($ris_fasi->rowCount()>0) {
								$print_form = false;
							}
							if ($print_form || isset($Ifase)) {
								if (isset($Ifase)) {
									$sql = "SELECT * FROM r_partecipanti_Ifase ";
								} else {
									$sql = "SELECT * FROM r_partecipanti ";
								}
							$sql.= "WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (conferma = TRUE OR conferma IS NULL)";
							$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipanti->rowCount()>0) {
								$numero_partecipanti = $ris_partecipanti->rowCount();
								if (!isset($Ifase)) {
									$sql_estrazione = "SELECT * FROM b_estrazioni_campioni WHERE codice_gara = :codice AND codice_lotto = :codice_lotto ";
									$ris_estrazione = $pdo->go($sql_estrazione,$bind);
									?>
									<button class="submit_big btn-warning" onClick="$('#estrai').slideToggle();">Estrazione campione verifica requisiti</button>
									<div class="box" id="estrai" style="display:none">
									<?
									if ($ris_estrazione->rowCount() == 0) {
										?>
										<form action="estrai.php" rel="validate" method="POST">
											<input type="hidden" name="codice_gara" value="<?= $record["codice"] ?>">
											<input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
											<table>
												<tr>
													<td class="etichetta">
														Numero partecipanti
													</td>
													<td>
														<input type="text" name="numero_partecipanti" title="Numero partecipanti" rel="S;0;0;N;<?= $numero_partecipanti ?>;<=" value="<?= ceil($numero_partecipanti*0.1) ?>">
													</td>
													<td width="300">
														<button class="submit_big" onClick="return confirm('Vuoi procedere all\'estrazione?\nL\'operazione non sarà revocabile.');">Estrai</button>
													</td>
												</tr>
											</table>
										</form>
										<?
									} else {
										$codice_gara = $record["codice"];
										include("report.php");
									}
								?>
									</div><br>
								<div style="text-align:center">
									<strong>Caricare la chiave privata</strong><br>
									<input type="file" id="chiave" title="Chiave privata" rel="S;0;0;F"/>
								</div>
								<br>
								<? if ($openSeduta) { ?>
									<button id="seduta_pubblica" class="submit_big <?= ($record["seduta_pubblica"] =='S') ? 'button-action' : 'btn-danger' ?>" onClick="$.ajax({url: 'enable_seduta.php?codice_gara=<?= $record["codice"] ?>',dataType: 'script'}).done(function(response){response; $('.sedutaCommand').slideToggle();})">
										<?= ($record["seduta_pubblica"] =='S') ? 'Chiudi seduta pubblica' : 'Apri seduta pubblica' ?>
									</button>
									<div style="<?= $record["seduta_pubblica"] =='N' ? "display:none": "" ?>" class="sedutaCommand">
										<div style="background-color:#ff9797; border: 1px solid #C00; color: #C00; padding:10px; margin-top:10px; text-align:center">
											ATTENZIONE! Eventuali punteggi memorizzati nel modulo Proposta di Aggiudicazione sono visualizzati ai partecipanti
										</div>
									</div>
									<button id="send-open-msg" class="submit_big sedutaCommand" style="display:<?= ($record["seduta_pubblica"] =='S') ? 'block' : 'none' ?>" onClick="$.ajax({url: 'send_msg.php?codice_gara=<?= $record["codice"] ?>&codice_lotto=<?= $codice_lotto ?>',dataType: 'script'}).done(function(response){response;})">
										<span class="fa fa-paper-plane"></span> Invia comunicazione di apertura agli OOEE
									</button>
								<? } ?>
								<? if (check_permessi("conference",$_SESSION["codice_utente"])) { ?>
									<a href="conference.php?codice=<?= $record["codice"] ?>&sub_elemento=<?= $codice_lotto ?>" style="display:<?= ($record["seduta_pubblica"] =='S') ? 'block' : 'none' ?>" target="_blank" class="submit_big sedutaCommand" id="conferenceRoomButton">
										<span class="fa fa-video-camera"></span> Avvia Conference Room
									</a> 
								<? } ?>
								<br>
								<script>
								if (window.File && window.FileReader && window.FileList && window.Blob) {
								 function handleFileSelect(evt) {
									$("#file").parent().addClass('working');
									var file = evt.target.files[0];
									var r = new FileReader();
									r.onload = function(e) {
										var contents = e.target.result;
										$(".private").val(contents);
										$("#chiave").parent().removeClass('working');
									}
									r.readAsBinaryString(file);
								}
								document.getElementById("chiave").addEventListener('change', handleFileSelect, false);
							} else {
								corpo_alert = '<div style="text-align:center; font-weight:bold">Il tuo browser non supporta la procedura di invio.<br>';
								corpo_alert += 'Si consiglia di aggiornare il browser in uso o di utilizzare uno dei seguenti';
								corpo_alert += '<table width="100%"><tr>';
								corpo_alert += '<td style="text-align:center; width:50%;"><a target="_blank" title="Sito esterno" href="http://www.google.it/intl/it/chrome/browser/">';
								corpo_alert += '<img src="/img/chrome.png" alt="Google Chrome"><br>Google Chrome';
								corpo_alert += '</a></td>';
								corpo_alert += '<td style="text-align:center; width:50%;"><a target="_blank" title="Sito esterno" href="http://www.mozilla.org/it/firefox/new/">';
								corpo_alert += '<img src="/img/firefox.png" alt="Firefox"><br>Firefox';
								corpo_alert += '</a></td>';
								corpo_alert += '</tr>';
								corpo_alert += '</table></div>';
								jalert(corpo_alert);
								$('#buste').after(corpo_alert).remove();
							}
							</script>
							<? } ?>
							<table id="buste" width="100%">
								<thead>
									<tr>
										<td></td>
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
								$i_cont = 0;
								while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
									$i_cont++;
									include("tr_partecipante.php");
								}
								?></tbody>
								</table>
								<script>
									function showInfoEmendamento(codice_gara,codice_partecipante,codice_emendamento) {
										$("#infoEmendamento").remove();
										$("<div id='infoEmendamento'></div>")
											.load('/gare/apribuste/info-emendamento.php',"codice_gara="+codice_gara+"&codice_partecipante="+codice_partecipante+"&codice_emendamento="+codice_emendamento)
											.dialog({
												close: function(){$(this).remove();},
												draggable: true,
												modal: true,
												resizable: false,
												width: '640px',
												position: ['center', 100],
												title: 'Emendamento'
											});
									}
								</script>
								<? if (!isset($Ifase)) { ?>
								<div class="box">
									<? if (!$lock) { ?>
										<h2 style="cursor:pointer;" onclick="$('#form-date').slideToggle();">Imposta nuove date di apertura</h2>
										<form style="display:none" id="form-date" name="box" method="post" action="save.php" rel="validate">
											<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
											<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
									<? } else { ?>
									<h2>Modifiche alle date di apertura</h2>
									<? } ?>
									<table width="100%" id="date">
										<?

										$bind = array();
										$bind[":codice"]=$record["codice"];
										$bind[":codice_lotto"]=$codice_lotto;

										$sql = "SELECT b_date_apertura.*, b_criteri_buste.nome FROM b_date_apertura JOIN b_criteri_buste ON b_date_apertura.codice_busta = b_criteri_buste.codice ";
										$sql .= "WHERE b_date_apertura.codice_gara = :codice AND b_date_apertura.codice_lotto = :codice_lotto ORDER BY codice";
										$ris = $pdo->bindAndExec($sql,$bind);
										if ($ris->rowCount()>0) {
											while ($record_data = $ris->fetch(PDO::FETCH_ASSOC)) {
												include("tr_data.php");
											}
										}
										?></table>
										<? if (!$lock) { ?>
											<table width="100%">
											<tr>
												<td class="etichetta">Busta</td><td width="40%">
													<select name="codice_busta" id="codice_busta" title="Busta" rel="S;0;0;N">
														<option value="">Seleziona</option>
														<?
														if (count($ris_buste)) {
															foreach ($ris_buste AS $record_busta) {
																?><option value="<?= $record_busta["codice"] ?>"><?= $record_busta["nome"] ?></option><?
															}
														}
														?>
													</select>
												</td>
												<td class="etichetta">Apertura busta</td>
												<td width="20%">
													<input type="text" class="datetimepick" title="Apertura offerte"  name="data_apertura" id="data_apertura" value="" rel="S;16;16;DT;<?= date("d/m/Y H:i") ?>;>">
												</td>
												<td class="etichetta">Invia comunicazione</td>
												<td width="10"><input type="checkbox" checked name="invia_comunicazione"></td>
											</tr>
										</table>
										<input type="submit" class="submit_big" value="Salva">
									</form>
								</div>
								<? } ?>
								<? }
									} else {
										echo "<h1>ATTENZIONE</h1>";
										echo "<h3>Non sono presenti partecipanti</h3>";
									}
								} else {
									echo "<h1>IMPOSSIBILE ACCEDERE</h1>";
									echo "<h3>Procedure di negoziazione aperte</h3>";
								}
							}
							$no_msg = true;
							include($root."/gare/ritorna.php");
						} else {
							echo "<h1>Non si dispone dei permessi necessari</h1>";
						}
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
