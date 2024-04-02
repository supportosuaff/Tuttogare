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
	$strsql .= " AND data_apertura <= now() ";
	$risultato = $pdo->bindAndExec($strsql,$bind);

	if ($risultato->rowCount() > 0) {
		$record = $risultato->fetch(PDO::FETCH_ASSOC);
		$aggiudicazione_multipla = false;

		$bind = array();
		$bind[":procedura"] = $record["procedura"];
		$sql_procedura = "SELECT * FROM b_procedure WHERE codice = :procedura AND aggiudicazione_multipla = 'S'";
		$ris_procedura = $pdo->bindAndExec($sql_procedura,$bind);
		if ($ris_procedura->rowCount()>0) $aggiudicazione_multipla = true;

		$scelta_anomalia = false;
		$calcoloAnomalia = true;
		$bind = array();
		$bind[":criterio"] = $record["criterio"];
		$sql = "SELECT * FROM b_criteri WHERE codice = :criterio AND directory = 'art_82'";
		$ris_scelta = $pdo->bindAndExec($sql,$bind);
		if ($ris_scelta->rowCount() > 0 && (strtotime($record["data_pubblicazione"]) > strtotime('2016-04-20')) && (strtotime($record["data_pubblicazione"]) < strtotime('2019-04-19'))) {
			$scelta_anomalia = true;
			$formSceltaAnomalia = __DIR__ . "/201650/form.php";
		}

		if ($record["norma"] == "2023-36" && $ris_scelta->rowCount() > 0) {
			if ($record["tipologia"] != 3) {
				$scelta_anomalia = true;
				$formSceltaAnomalia = __DIR__."/202336/form.php";
			} else {
				$calcoloAnomalia = false;
			}
		}

		$operazione = "UPDATE";

		?><h1>AGGIUDICAZIONE PROVVISORIA</h1><?

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
				while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {

					$bind = array();
					$bind[":codice"]=$record["codice"];
					$bind[":codice_lotto"] = $lotto["codice"];

					$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND primo = 'S'";
					$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
					$style = "";
					$primo = "";
					if ($ris_partecipanti->rowCount()>0) {
						$primo = $ris_partecipanti->fetch(PDO::FETCH_ASSOC);
						$primo = "<br>" . $primo["partita_iva"] . " - " . $primo["ragione_sociale"];
						$style = "style=\"background-color:#0C0\"";
					} else if ($lotto["deserta"]=="S") {
						$style = "style=\"background-color:#999\"";
						$primo = "<br>Deserto";
					} else if ($lotto["deserta"]=="Y") {
						$style = "style=\"background-color:#333\"";
						$primo = "<br>Non aggiudicato";
					}
					?>
					<a class="submit_big" <?=$style?> href ="edit.php?codice=<? echo $record["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
						<? echo $lotto["oggetto"] . $primo ?>
					</a>
					<?
				}
			}
		} else {
			$print_form = true;
			$codice_lotto = 0;
		}

		if ($print_form)
		{

			$riferimento = $record;
			if (!empty($lotto["algoritmo_anomalia"])) {
				$riferimento = $lotto;
			}
			if ($riferimento["algoritmo_anomalia"] == "N") {
				$calcoloAnomalia = false;
			}
			$bind = array();
			if ($record["nuovaOfferta"] == "S") {
				$bind[":codice"] = $record["codice"];
				$bind[":codice_lotto"] = $codice_lotto;
				$sql = "SELECT b_criteri_punteggi.* FROM b_criteri_punteggi
								JOIN b_valutazione_tecnica on b_criteri_punteggi.codice = b_valutazione_tecnica.punteggio_riferimento
								WHERE b_valutazione_tecnica.codice_gara = :codice
								AND (b_valutazione_tecnica.codice_lotto = 0 OR b_valutazione_tecnica.codice_lotto = :codice_lotto)
								GROUP BY b_criteri_punteggi.codice ORDER BY b_criteri_punteggi.ordinamento ";
			} else {
				$bind[":codice"] = $record["criterio"];
				$sql = "SELECT b_criteri_punteggi.* FROM b_criteri_punteggi
								WHERE b_criteri_punteggi.codice_criterio = :codice
								ORDER BY b_criteri_punteggi.ordinamento ";
			}
			$ris_punteggi = $pdo->bindAndExec($sql,$bind);
			$ris_punteggi = $ris_punteggi->fetchAll(PDO::FETCH_ASSOC);

			$bind = array();
			$bind[":codice"] = $record["codice"];
			$bind[":codice_lotto"] = $codice_lotto;
			$sql_fasi = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now() ";
			$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
			if ($ris_fasi->rowCount()>0) {
				$print_form = false;
			} else {
				$sql_fasi = "SELECT * FROM b_2fase WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_fine < now() AND data_fine > 0";
				$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
				if ($ris_fasi->rowCount()>0) $lock = false;
			}

			$sql_fasi = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_inizio <= now() AND data_fine > now()";
			$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
			if ($ris_fasi->rowCount()>0) {
				$print_form = false;
			} else {
				$sql_fasi = "SELECT * FROM b_aste WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND data_fine < now() AND data_fine > 0 ";
				$ris_fasi = $pdo->bindAndExec($sql_fasi,$bind);
				if ($ris_fasi->rowCount()>0) $lock = true;
			}

			if ($print_form) {
				$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";
				$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);

				if ($ris_r_partecipanti->rowCount()>0)
				{
					$calculated = false;
					if (!empty($lotto["messaggio_anomalia"])) {
						$calculated = true;
						echo "<div class='box'><strong>" . substr($lotto["messaggio_anomalia"],0,-2) . " - Decimali utilizzati: {$lotto["decimali_graduatoria"]} - Arrotondamento: {$lotto["arrotondamento"]} - Applicazione: " . (($lotto["solo_soglia"] == "S") ? "Solo sul risultato finale del calcolo" : "Su tutti i passaggi intermedi del calcolo") . "</strong></div>";
					} else 	if (!empty($record["messaggio_anomalia"])) {
						$calculated = true;
						echo "<div class='box'><strong>" . substr($record["messaggio_anomalia"],0,-2) . " - Decimali utilizzati: {$record["decimali_graduatoria"]} - Arrotondamento: {$record["arrotondamento"]} - Applicazione: " . (($record["solo_soglia"] == "S") ? "Solo sul risultato finale del calcolo" : "Su tutti i passaggi intermedi del calcolo") . "</strong></div>";
					}
					if (!$lock)
					{
						$bind = array();
						$bind[":codice"]=$record["codice"];

						$strsql = "SELECT b_criteri.* FROM b_criteri JOIN b_gare ON b_criteri.codice = b_gare.criterio WHERE b_gare.codice = :codice";
						$ris_criterio = $pdo->bindAndExec($strsql,$bind);
						if ($ris_criterio->rowCount()>0)
						{
							$criterio = $ris_criterio->fetch(PDO::FETCH_ASSOC);

							/* CALCOLO PUNTEGGI TECNICI */
							$sql_opzione = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione = 124";
							$ris_confronto = $pdo->bindAndExec($sql_opzione,$bind);

							$bind = array();
							$bind[":codice"]=$record["codice"];
							$bind[":codice_lotto"] = $codice_lotto;

							$check_sql  = "SELECT b_punteggi_criteri.*
														 FROM b_punteggi_criteri
														 JOIN b_valutazione_tecnica ON b_punteggi_criteri.codice_criterio = b_valutazione_tecnica.codice
														 JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
														 WHERE b_punteggi_criteri.codice_lotto = :codice_lotto
														 AND  b_punteggi_criteri.codice_gara = :codice
														 AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
							$ris_check = $pdo->bindAndExec($check_sql,$bind);

							if (($ris_confronto->rowCount() > 0) || ($ris_check->rowCount() > 0))
							{
								$sql_opzione = "SELECT * FROM b_confronto_coppie WHERE codice_gara = :codice AND codice_lotto= :codice_lotto";
								$ris_opzione = $pdo->bindAndExec($sql_opzione,$bind);

								$bind = array();
								$bind[":codice"]=$record["codice"];
								$sql_qualitativi = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice AND tipo = 'Q'";
								$ris_qualitativi = $pdo->bindAndExec($sql_qualitativi,$bind);
								if (($ris_confronto->rowCount() > 0 && $ris_opzione->rowCount() > 0) || (($ris_confronto->rowCount() == 0 || $ris_qualitativi->rowCount() == 0) && $ris_check->rowCount() > 0)) {
									?>
									<form name="box" method="post" action="<? echo $criterio["directory"] ?>/importa_tecnico.php">
										<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
										<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
										<input type="hidden" name="criterio" value="<? echo $criterio["codice"]; ?>">
										<input type="hidden" id="riparametrazione_semplice" name="riparametrazione_semplice" value="N">
										<input type="submit" class="submit_big" style="background-color: #FC0" value="Importa punteggi tecnici"
										onclick="if (confirm('Applicare la riparametrazione di I livello nell\'importazione?')) { $('#riparametrazione_semplice').val('S'); } else { $('#riparametrazione_semplice').val('N'); } return true;">
									</form>
									<?
								}
							}

							$bind = array();
							$bind[":codice"]=$record["codice"];
							$bind[":codice_lotto"] = $codice_lotto;
							if ($criterio["directory"]=="art_82") {
								$strsql = "SELECT b_offerte_decriptate.*
													 FROM b_offerte_decriptate
													 JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
													 WHERE r_partecipanti.codice_gara = :codice AND r_partecipanti.codice_lotto = :codice_lotto
													 AND b_offerte_decriptate.tipo IN ('economica','elenco_prezzi')";
								$ris_offerte = $pdo->bindAndExec($strsql,$bind);
							} else {
								$check_sql  = "SELECT b_punteggi_criteri.*
															 FROM b_punteggi_criteri
															 JOIN b_valutazione_tecnica ON b_punteggi_criteri.codice_criterio = b_valutazione_tecnica.codice
															 JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
															 WHERE b_punteggi_criteri.codice_lotto = :codice_lotto
															 AND  b_punteggi_criteri.codice_gara = :codice
															 AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
								$ris_offerte = $pdo->bindAndExec($check_sql,$bind);
							}
							if ($ris_offerte->rowCount()>0 || $record["nuovaOfferta"] == "N")
							{
								?>
								<form name="box" method="post" action="<? echo $criterio["directory"] ?>/importa_offerte.php">
									<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
									<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
									<input type="hidden" name="criterio" value="<? echo $criterio["codice"]; ?>">
									<input type="submit" class="submit_big" style="background-color: #FC0" value="Importa offerte economiche">
								</form>
								<?
							}
							?>
							<div class="box">
								<table width="100%">
									<tbody>
										<tr>
											<td style="text-align: center;vertical-align: middle;"><strong>Caricamento massivo dei punteggi</strong></td>
										</tr>
									</tbody>
								</table>
								<form action="upload_csv.php" method="post" enctype="multipart/form-data">
									<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
									<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
									<table class="dettaglio" width="100%">
										<tbody>
											<tr>
												<td width="25%">
													<img src="/img/xls.png" alt="Modello partecipanti"/>
													<a href="download_csv.php?codice=<?=$record["codice"]?>&codice_lotto=<?=$codice_lotto?>" name="punteggi_csv" download style="vertical-align:super">Modello CSV</a>
												</td>
												<td width="70%">
													<input type="file" name="punteggi" id="file">
												</td>
												<td style="5%">
													<input type="submit" name="submit" value="upload">
												</td>
											</tr>
										</tbody>
									</table>
								</form>
							</div>
							<form name="box" method="post" action="save.php" rel="validate">
								<input type="hidden" id="calcola_graduatoria" name="calcola_graduatoria" value="N">
								<input type="hidden" name="codice_gara" value="<? echo $record["codice"]; ?>">
								<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
								<div class="comandi">
									<button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
								</div>
								<?
							}
							?>
							<? if ($aggiudicazione_multipla) { ?>
								<div class="box">
									<h2>Numero di aggiucatari da selezionare:
									<input type="text" rel="S;1;0;N;0;>" name="numero_aggiudicatari" title="Numero aggiucatari"></h2>
								</div>
							<? } 
						}
						$aggiudicato = false;
						?>
						<div id="alertPubblicazioneGraduatoria"></div>
						<table width="100%">
							<thead>
								<tr>
									<td>#</td>
									<td width="10"></td>
									<td>Protocollo</td>
									<td>Partita IVA</td>
									<td>Ragione Sociale</td>
									<td>Ammesso</td>
									<td>Anomalia</td>
									<td>Controllo a campione</td>
									<?
									if (count($ris_punteggi)>0)
									{
										foreach($ris_punteggi AS $punteggio)
										{
											?><td><?=$punteggio["nome"]?></td><?
										}
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?
								while ($record_partecipante = $ris_r_partecipanti->fetch(PDO::FETCH_ASSOC))
								{
									include("tr_partecipante.php");
								}
								?>
							</tbody>
						</table>
						<div class="box" style="padding:20px;">
							<div style="display:none;" id="msg_conferma_invio_esclusione">
								<input type="checkbox" name="invia_esclusione" value="S"> <strong>Invia la comunicazione di esclusione/riammissione agli operatori economici interessati dalla modifica.</strong>
							</div>
							<div>
								<input type="checkbox" name="invia_ammissione" value="S"> <strong>Invia la comunicazione di ammissione agli operatori economici non esclusi.</strong>
							</div>
						</div>
						<? if (!$lock) {
							?>
							<input type="submit" class="submit_big" onClick="$('.parametri_anomalia').attr('rel','N;0;0;A');  $('#calcola_graduatoria').val('N');" value="Salva">
							<? if ($calcoloAnomalia) { ?>
								<button type="button" class="submit_big comandiGraduatoria" onClick="$('.comandiGraduatoria').slideToggle(); $('.parametri_anomalia').attr('rel','S;0;0;A');" style="background-color: #0A0">Salva ed <?= (!$aggiudicato) ? "Elabora graduatoria" : "Aggiorna graduatoria" ?></button>
								<div class="comandiGraduatoria" style="display:none; background-color:rgba(255,255,0,0.5); border:1px solid #FA0; padding:50px; text-align:center;">
							<? 
							
								if ($aggiudicato) {
									if ($ris_r_partecipanti->rowCount() > 4) { ?>
										<label>Calcolo anomalia</label>
										<select name="calcola_anomalia" id="calcola_anomalia" class="parametri_anomalia" rel="N;1;1;N" title="Calcolo anomalia">
											<option value="N">Non ripetere il calcolo dell'anonalia</option>
											<option value="S">Ripeti il calcolo dell'anomalia</option>
										</select>
										<br><br>
									<? } ?>
									<strong>ATTENZIONE: Proseguendo sarà aggiornata la proposta di aggiudicazione. Vuoi continuare?</strong><br>
								<? } else { 
									if ($ris_r_partecipanti->rowCount() > 4) { 
										if ($scelta_anomalia) { 
											include $formSceltaAnomalia;
										}
									?>
										
									<input type="hidden" id="calcola_anomalia" name="calcola_anomalia" value="S">
									<h2>Parametri calcolo anomalia</h2>
									<table class="box" width="100%">
										<tr>
											<td class="etichetta">Decimali</td>
											<td>
												<select name="decimali_graduatoria"  class="parametri_anomalia" id="decimali_graduatoria" rel="N;1;1;N" title="Decimali calcolo graduatoria">
													<option value="">Seleziona...</option>
													<option value="2">2</option>
													<option value="3">3</option>
													<option value="4">4</option>
													<option value="5">5</option>
													<option value="6">6</option>
													<option value="7">7</option>
													<option value="8">8</option>
													<option value="9">9</option>
												</select>
											</td>
											<td class="etichetta">Arrotondamento</td>
											<td>
												<select name="arrotondamento" class="parametri_anomalia" id="arrotondamento" rel="N;1;1;A" title="Arrotondamento">
													<option value="">Seleziona...</option>
													<option value="S">Si</option>
													<option value="N">No</option>
												</select>
											</td>
											<td class="etichetta">Applicare le scelte precedenti a:</td>
											<td>
												<select name="solo_soglia" class="parametri_anomalia" id="solo_soglia" rel="N;1;1;A" title="Applica la selezione">
													<option value="">Seleziona...</option>
													<option value="S">Risultato finale del calcolo dell'anomalia</option>
													<option value="N">Tutti i passaggi intermedi del calcolo dell'anomalia</option>
												</select>
											</td>
										</tr>
										<? if ($ris_r_partecipanti->rowCount() >= 15 && $ris_scelta->rowCount() > 0 && !$scelta_anomalia) { ?>
											<tr>
												<td class="etichetta">Interpretazione art. 97 c. 2 lett. d</td>
												<td colspan="5">
													<select name="interpretazione_anomalia" class="parametri_anomalia" id="interpretazione_anomalia" rel="N;1;1;A" title="Interpretazione">
														<option value="">Seleziona...</option>
														<option value="M">Circolare MIT del 24 Ottobre 2019 - Decremento del valore assoluto risultante dal decremento dello scarto medio</option>
														<!-- <option value="T">Sentenza TAR Marche 622/2019 - Ulteriore decremento percentuale risultate dal decremento dello scarto medio</option> -->
													</select>
												</td>
											</tr>
										<? } ?>
									</table>
									<script>
										$("#decimali_graduatoria").val('<?= (!empty($lotto["decimali_graduatoria"])) ? $lotto["decimali_graduatoria"] : $record["decimali_graduatoria"]; ?>');
										$("#arrotondamento").val('<?= (!empty($lotto["arrotondamento"])) ? $lotto["arrotondamento"] : $record["arrotondamento"]; ?>');
										$("#solo_soglia").val('<?= (!empty($lotto["solo_soglia"])) ? $lotto["solo_soglia"] : $record["solo_soglia"]; ?>');
										$("#interpretazione_anomalia").val('<?= (!empty($lotto["interpretazione_anomalia"])) ? $lotto["interpretazione_anomalia"] : $record["interpretazione_anomalia"]; ?>');
									</script>
									<br><br>
								<? } ?>
									<strong>ATTENZIONE: Proseguendo sarà eseguita la proposta di aggiudicazione. Vuoi continuare?</strong><br>
								<? }?>
								<button class="submit_big" type="submit" onclick="if (confirm('Sei sicuro?')) { $('.parametri_anomalia').attr('rel','S;0;0;A'); $('#calcola_graduatoria').val('S'); return true } else { return false }">Procedi</button>
								<button type="button" class="submit_big" onClick="$('.comandiGraduatoria').slideToggle();" style="background-color: #A00">Annulla</button>
							</div>
						<? } else { ?>
							<button class="submit_big" type="submit" onclick="if (confirm('Sei sicuro?')) { $('#calcola_graduatoria').val('S'); return true } else { return false }">Salva ed Elabora Graduatoria</button>
						<? } ?>
					</form>
					<? if ($aggiudicato) { 
							if (!empty($codice_lotto)) {
								$riferimento = $lotto;
							} else {
								$riferimento = $record;
							}
					?>
						<form id="pubblicaPartecipanti" action="pubblicaPartecipanti.php" method="POST">
							<input type="hidden" name="codice" value="<?= $record["codice"] ?>">
							<input type="hidden" name="codice_lotto" value="<?= $codice_lotto ?>">
						</form>
						<div id="firstAvvisoPubblicazionePartecipanti">
							<?
								if ($riferimento["pubblica_partecipanti"] == "N") {
									?>
									<div style="background-color:#ff9797; border: 1px solid #C00; color: #C00; padding:10px; margin-top:10px; text-align:center">
										ATTENZIONE! La lista dei partecipanti non è visualizzata nell'area pubblica
										<input class="submit_big" style="background-color:#0C0" type="button" onclick="if (confirm('Proseguendo le informazioni saranno pubblicate nell\'area pubblica. Vuoi continuare?')) { $('#pubblicaPartecipanti').submit();return true; } else { return false }" value="Pubblica partecipanti">
									</div>
									<?
								} else { 
									?>
									<div style="background-color:#97FF97; border: 1px solid #0C0; color: #0C0; padding:10px; margin-top:10px; text-align:center">
										La lista dei partecipanti è visualizzata nell'area pubblica
										<input class="submit_big" style="background-color:#C00" type="button" onclick="if (confirm('Proseguendo le informazioni saranno eliminate dall\'area pubblica. Vuoi continuare?')) { $('#pubblicaPartecipanti').submit() ;return true; } else { return false }" value="Nascondi partecipanti">
									</div>
									<?
								}
							?>
							<script>
								$("#alertPubblicazioneGraduatoria").html($("#firstAvvisoPubblicazionePartecipanti").html());
							</script>
						</div>
					<? } ?>
					<script>
						$(".ammesso").change(function() {
							id = $(this).parents("tr").attr("id");
							if ($(this).val()=="S") {
								$("#"+id+" .motivazione").val("").attr("rel","N;3;0;A").slideUp('fast');
							} else {
								$("#"+id+" .motivazione").attr("rel","S;3;0;A").slideDown('fast');
							}
						});
						$(".anomalia").change(function() {
							id = $(this).parents("tr").attr("id");
							if ($(this).val()=="N") {
								$("#"+id+" .motivazione_anomalia").val("").attr("rel","N;3;0;A").slideUp('fast');
								$("#"+id+" .facoltativa").val("N");
							} else {
								$("#"+id+" .motivazione_anomalia").attr("rel","S;3;0;A").slideDown('fast');
								$("#"+id+" .facoltativa").val("S");
							}
						});
					</script>
					<? } else { ?>
					<script>
						$(":input").not('.espandi').prop("disabled", true);
					</script>
					<?
				}
			} else {
				echo "<h1>Attenzione</h1>";
				echo "<h3>Non sono presenti partecipanti</h3>";
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
include_once($root."/layout/bottom.php");
?>
