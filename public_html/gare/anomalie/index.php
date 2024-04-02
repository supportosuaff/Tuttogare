<?
	include_once("../../../config.php");
	$form_comunicazione = true;
	$codice_gara = $_GET["codice"];
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
					?>
					<h1>GESTIONE ANOMALIE</h1>
					<?
						$record = $risultato->fetch(PDO::FETCH_ASSOC);
						$bind = array();
						$bind[":codice_gara"] = $record["codice"];
						$sql_lotti = "SELECT b_lotti.*, count(r_partecipanti.codice) AS anomalie FROM b_lotti JOIN r_partecipanti ON b_lotti.codice = r_partecipanti.codice_lotto WHERE b_lotti.codice_gara = :codice_gara ";
						$sql_lotti.= " AND r_partecipanti.anomalia = 'S' GROUP BY b_lotti.codice ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						$print_form = false;
						if ($ris_lotti->rowCount()>0) {
							if (isset($_GET["lotto"])) {
								$codice_lotto = $_GET["lotto"];
								$bind = array();
								$bind[":codice"] = $codice_lotto;
								$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice ORDER BY codice";
								$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
								if ($ris_lotti->rowCount()>0) {
									$print_form = true;
									$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
									echo "<h2>" . $lotto["oggetto"] . "</h2>";
								}
							} else {
								while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
								?>
									<a class="submit_big" href ="index.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
										<? echo $lotto["oggetto"] . "<br>Offerte anomale: <strong>" . $lotto["anomalie"] . "</strong>"; ?>
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
							$bind[":codice_gara"] = $record["codice"];
							$bind[":codice_lotto"] = $codice_lotto;
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto
											AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND ((anomalia = 'S' AND escluso = 'N') || (verifica = 'S')) ";
							$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipanti->rowCount()>0) {
								?>
								<div id="tabs">
									<ul>
										<li><a href="#anomalie">Anomalie</a></li>
										<li><a href="#allegati">Allegati</a></li>
									</ul>
								<? if (!$lock) {

									$bind = array();
									$bind[":codice_gara"] = $record["codice"];
									$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND verifica = 'S' ";
									$ris_verificati = $pdo->bindAndExec($sql,$bind);
									if ($ris_partecipanti->rowCount()>$ris_verificati->rowCount()) {
									//	$sql = "UPDATE b_gare SET stato = 5 WHERE codice = :codice_gara AND stato < 5";
									//	$update_stato = $pdo->bindAndExec($sql,$bind);
									}
								?>
									<form name="box" method="post" action="save.php" rel="validate">
										<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
										<div class="comandi">
											<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
										</div>
									<? } ?>
									<div id="anomalie">
										<table width="100%" id="operatori">
											<thead>
												<tr>
													<td>Stato</td>
													<td>Protocollo</td>
													<td>Partita IVA</td>
													<td>Ragione Sociale</td>
													<td>Verifica</td>
													<td>Escluso</td>
												</tr>
											</thead>
											<tbody>
											<?
											while ($record_partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
												include("tr_partecipante.php");
											}
											?>
											</tbody>
										</table>
									</div>
									<div id="allegati">
										<h2>Allegati</h2>
										<table width="100%" id="tab_riservati">
											<?
								$form_upload["id"] = "anomalie";
								$form_upload["cartella"] = "Gestione Anomalie";
								$form_upload["online"] = 'N';
								$form_upload["codice_gara"] = $record["codice"];
								$bind = array();
								$bind[":codice_gara"] = $record["codice"];
								$bind[":cartella"] = $form_upload["cartella"];
								$sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice_gara AND cartella = :cartella AND online = 'N' ORDER BY cartella, codice";
								$ris_riservati = $pdo->bindAndExec($sql,$bind);
								if (isset($ris_riservati) && ($ris_riservati->rowCount()>0)) {
									$cartella_attuale = "";
									while ($allegato = $ris_riservati->fetch(PDO::FETCH_ASSOC)) {
										if ($allegato["cartella"]!=$cartella_attuale) {
											$cartella_attuale = $allegato["cartella"];
											$echo_cartella = strtoupper(str_replace("/"," ",$allegato["cartella"]));
											?>
											<tr><td><span class="fa fa-folder-open fa-2x"></span></td><td colspan="4"><strong><? echo $echo_cartella  ?></strong></td></tr>
											<?
										}
										include($root."/allegati/tr_allegati.php");
									}
								} ?>
							</table>
							<? if (!$lock) { ?>
								<button onClick="open_allegati('anomalie');return false;" style="width:100%; padding:10px; background-color:#F60" class="submit">
									<img src="/allegati/icon.png" alt="Allega" width="15" style="vertical-align:middle"> Allega file
								</button>
							<? } ?>
						</div>
					</div>
					<? if (!$lock) { ?>
						<input type="hidden" id="aggiorna_stato" name="aggiorna_stato" value="NO">
						<input type="submit" class="submit_big" onclick="$('#aggiorna_stato').val('NO'); return true;" value="Salva">
						<button class="submit_big" type="submit" onclick="$('#aggiorna_stato').val('YES'); return true;">Salva e aggiorna lo stato di gara</button>
					</form>
					<? include($root."/allegati/form_allegati.php"); ?>
					<script>
						$("#tabs").tabs();
						$(".escluso").change(function() {
							id = $(this).parents("tr").attr("id");
							if ($(this).val()=="N") {
								$("#"+id+" .motivazione").val("").attr("rel","N;3;255;A").slideUp('fast');
							} else {
								$("#"+id+" .motivazione").attr("rel","S;3;255;A").slideDown('fast');
							}
						});
					</script>
					<?
						} else {
					?>
					<script>
					 $("#tabs").tabs();
					 $(":input").not('.espandi').prop("disabled", true);
					</script>
					<?
					}
				} else {
					echo "<h3>Non sono presenti anomalie da verificare</h3>";
				}
			}
		} else {
			echo "<h1>Gara non trovata</h1>";
		}
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
	include($root."/gare/ritorna.php");
	include_once($root."/layout/bottom.php");
?>
