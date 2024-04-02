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

					?><h1>APERTURA BUSTE</h1><?

					$bind = array();
					$bind[":codice_gara"] = $record["codice"];
					$sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara AND attiva = 'S' AND apertura <= now() ORDER BY codice DESC LIMIT 0,1";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount() > 0) {
					$fase = $ris->fetch(PDO::FETCH_ASSOC);
							echo "<h2>" . $fase["oggetto"] . "</h2>";
							$bind = array();
							$bind[":codice"]=$record["codice"];
							$bind[":codice_fase"]=$fase["codice"];
							$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice_gara = :codice AND codice_fase = :codice_fase AND (conferma = TRUE OR conferma IS NULL)";
							$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipanti->rowCount()>0) {
							?>
								<div style="text-align:center">
									<strong>Caricare la chiave privata</strong><br>
									<input type="file" id="chiave" title="Chiave privata" rel="S;0;0;F"/>
								</div>
								<br>
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
							<table id="buste" width="100%">
								<thead>
									<tr>
										<td>Identificativo</td>
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
											}
										}	?>
									</tr>
								</thead>
								<tbody>
								<?
								while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
									include("tr_partecipante.php");
								}
								?></tbody>
								</table>
								<div class="box">
									<? if (!$lock) { ?>
										<h2 style="cursor:pointer;" onclick="$('#form-date').slideToggle();">Imposta nuove date di apertura</h2>
										<form style="display:none" id="form-date" name="box" method="post" action="save.php" rel="validate">
											<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
											<input type="hidden" name="codice_fase" value="<? echo $fase["codice"]; ?>">
									<? } else { ?>
									<h2>Modifiche alle date di apertura</h2>
									<? } ?>
									<table width="100%" id="date">
										<?

										$bind = array();
										$bind[":codice"]=$record["codice"];
										$bind[":codice_fase"]=$fase["codice"];

										$sql = "SELECT b_date_apertura_concorsi.*, b_fasi_concorsi_buste.nome FROM b_date_apertura_concorsi JOIN b_fasi_concorsi_buste ON b_date_apertura_concorsi.codice_busta = b_fasi_concorsi_buste.codice
														WHERE b_date_apertura_concorsi.codice_gara = :codice AND b_date_apertura_concorsi.codice_fase = :codice_fase ORDER BY codice";
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
											</tr>
										</table>
										<input type="submit" class="submit_big" value="Salva">
									</form>
								</div>
								<? }
									} else {
										echo "<h1>ATTENZIONE</h1>";
										echo "<h3>Non sono presenti partecipanti</h3>";
									}
							} else {
								echo "<h1>IMPOSSIBILE ACCEDERE</h1>";
								echo "<h3>Termini di apertura non raggiunti</h3>";
							}
							$no_msg = true;
							include($root."/concorsi/ritorna.php");
						} else {
							echo "<h1>Concorso non trovato</h1>";
						}
					} else {
						echo "<h1>Concorso non trovato</h1>";
					}
?>
<?
	include_once($root."/layout/bottom.php");
	?>
