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
					?>
					<h1>Verbale di aggiudicazione</h1>
					<?
						$bind = array();
						$bind[":codice"] = $codice;
						$sql_lotti = "SELECT b_lotti.* FROM b_lotti WHERE b_lotti.codice_gara = :codice";
						$sql_lotti.= " GROUP BY b_lotti.codice ORDER BY codice";
						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
						$print_form = false;
						if ($ris_lotti->rowCount()>0) {
							if (isset($_GET["lotto"])) {
								$codice_lotto = $_GET["lotto"];
								$bind=array();
								$bind[":codice_lotto"] = $codice_lotto;
								$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice_lotto ORDER BY codice";
								$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
								if ($ris_lotti->rowCount()>0) {
									$print_form = true;
									$lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
									echo "<h2>" . $lotto["oggetto"] . "</h2>";
								}
							} else {
								while($lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC)) {
								?>
									<a class="submit_big" href ="verbale_aggiudicazione.php?codice=<? echo $_GET["codice"] ?>&lotto=<? echo $lotto["codice"] ?>">
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
					$bind[":codice"] = $codice;
					$bind[":codice_lotto"] = $codice_lotto;
					$sql = "SELECT r_partecipanti.codice AS codice_partecipante, r_partecipanti.*, b_operatori_economici.*, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ";
					$sql.= " ON r_partecipanti.codice = r_punteggi_gare.codice_partecipante LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice ";
					$sql .= "WHERE r_partecipanti.primo = 'S' AND r_partecipanti.codice_gara = :codice AND r_partecipanti.codice_lotto = :codice_lotto GROUP BY r_partecipanti.codice ";
					$ris_aggiudicatario = $pdo->bindAndExec($sql,$bind);
					if ($ris_aggiudicatario->rowCount()>0) {
						$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
						$record_gara["oggetto"] = $record_gara["cig"] . " - " . $record_gara["oggetto"];
						if (isset($lotto)) {
							$record_gara["oggetto"] .= " - Lotto: " . $lotto["cig"] . " - " . $lotto["oggetto"];
							$record_gara["ribasso"] = $lotto["ribasso"];
						}
						$bind = array();
						$bind[":codice"] = $codice;
						$bind[":codice_lotto"] = $codice_lotto;

						$sql = "SELECT r_partecipanti.* FROM r_partecipanti ";
						$sql .= "WHERE r_partecipanti.codice_gara = :codice AND r_partecipanti.codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
						$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
						$editor_tipo = "verbale_aggiudicazione";
						$bind = array();
						$bind[":tipo"] = $editor_tipo;
						$bind[":codice"] = $record_gara["codice"];
						$bind[":codice_lotto"] = $codice_lotto;
						$strsql = "SELECT * FROM b_documentale WHERE tipo = :tipo AND attivo = 'S' AND sezione = 'gara' AND  codice_gara = :codice AND codice_lotto = :codice_lotto";
						$risultato = $pdo->bindAndExec($strsql,$bind);
						if ($risultato->rowCount()>0) {
							$elemento = $risultato->fetch(PDO::FETCH_ASSOC);
							$html = $elemento["corpo"];
							$operazione = "UPDATE";
							$codice_elemento = $elemento["codice"];
						} else {
							$operazione = "INSERT";
							$codice_elemento = 0;
							$record_gara["data_accesso"] = mysql2date($record_gara["data_accesso"]);
							$record_gara["data_scadenza"] = mysql2date($record_gara["data_scadenza"]);
							$record_gara["data_apertura"] = mysql2date($record_gara["data_apertura"]);
							$record_gara["data_atto_esito"] = mysql2date($record_gara["data_atto_esito"]);
							$record_gara["data_pubblicazione"] = mysql2date($record_gara["data_pubblicazione"]);
							$record_gara["url"] = $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";

							$bind = array();
							$bind[":codice"] = $record_gara["criterio"];

							$sql = "SELECT * FROM b_criteri WHERE codice = :codice";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								$rec = $ris->fetch(PDO::FETCH_ASSOC);
								$record_gara["nome_criterio"] = $rec["criterio"];
								$directory = $rec["directory"];
								/* Completamento in caso di Massimo ribasso */
								if ($directory == "art_82") {
									$oggetto_ribasso = " sull'importo a base di gara";
									$bind = array();
									$bind[":codice"] = $record_gara["codice"];
									$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione = 58";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) $oggetto_ribasso = " sull'elenco prezzi";
									$record_gara["nome_criterio"] .= $oggetto_ribasso;
								}
								/* Fine completamento MR */
								$record_gara["riferimento_criterio"] = $rec["riferimento_normativo"];
							}
							$bind = array();
							$bind[":codice"] = $record_gara["procedura"];
							$sql = "SELECT * FROM b_procedure WHERE codice=:codice";
							$ris = $pdo->bindAndExec($sql,$bind);
							if ($ris->rowCount()>0) {
								$rec = $ris->fetch(PDO::FETCH_ASSOC);
								$record_gara["nome_procedura"] = $rec["nome"];
								$record_gara["riferimento_procedura"] = $rec["riferimento_normativo"];
							}

							$bind = array();
							$bind[":codice"] = $record_gara["codice"];
							$record_gara["tipologie_gara"] = "";
							$sql = "SELECT tipologia FROM b_tipologie JOIN b_importi_gara ON b_tipologie.codice = b_importi_gara.codice_tipologia WHERE b_importi_gara.codice_gara = :codice";
							$ris_tipologie = $pdo->bindAndExec($sql,$bind);
							if ($ris_tipologie->rowCount()>0) {
								while($rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC)) {
									$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
								}
							}

							$vocabolario["#calcolo_soglia#"] = "";
							if ($record_gara["soglia_anomalia"] > 0)
							{
								$vocabolario["#soglia_anomalia#"] = "Soglia di anomalia: " . $record_gara["soglia_anomalia"];
							}

								$bind = array();
								$bind[":codice_ente"]=$record_gara["codice_ente"];

								$sql = "SELECT * FROM b_enti WHERE codice = :codice_ente";
								$ris_ente = $pdo->bindAndExec($sql,$bind);
								if ($ris_ente->rowCount()>0) $record_appaltatore = $ris_ente->fetch(PDO::FETCH_ASSOC);

								$bind = array();
								$bind[":codice_ente"]=$record_gara["codice_gestore"];
								$sql = "SELECT * FROM b_enti WHERE codice = :codice_ente";
								$ris_gestore = $pdo->bindAndExec($sql,$bind);
								if ($ris_gestore->rowCount()>0) $record_gestore = $ris_gestore->fetch(PDO::FETCH_ASSOC);

								$bind = array();
								$bind[":codice"] = $record_gara["criterio"];
								if ($directory == "art_83" || $record_gara["nuovaOfferta"] == "S") {
									$bind[":codice_gara"] = $record_gara["codice"];
									$bind[":codice_lotto"] = $codice_lotto;
									$sql = "SELECT b_criteri_punteggi.* FROM b_criteri_punteggi JOIN b_valutazione_tecnica ON b_criteri_punteggi.codice = b_valutazione_tecnica.punteggio_riferimento
													WHERE b_criteri_punteggi.codice_criterio=:codice
													AND b_valutazione_tecnica.codice_gara = :codice_gara AND (b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)
													GROUP BY b_criteri_punteggi.codice ORDER BY b_criteri_punteggi.ordinamento ";
								} else {
									$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio=:codice ORDER BY ordinamento";
								}
								$ris_punteggi = $pdo->bindAndExec($sql,$bind);
								$ris_punteggi = $ris_punteggi->fetchAll(PDO::FETCH_ASSOC);

								$chiavi = array_keys($record_appaltatore);
								foreach($chiavi as $chiave) {
									$vocabolario["#record_appaltatore-".$chiave."#"] = $record_appaltatore[$chiave];
								}
								$vocabolario["#record_appaltatore-logo-path#"] = ! empty($record_appaltatore["logo"]) ? "{$config["link_sito"]}/documenti/enti/{$record_appaltatore["logo"]}" : "{$config["link_sito"]}/img/no_logo.gif";

								$chiavi = array_keys($record_gestore);
								foreach($chiavi as $chiave) {
									$vocabolario["#record_gestore-".$chiave."#"] = $record_gestore[$chiave];
								}
								$vocabolario["#record_gestore-logo-path#"] = ! empty($record_gestore["logo"]) ? "{$config["link_sito"]}/documenti/enti/{$record_gestore["logo"]}" : "{$config["link_sito"]}/img/no_logo.gif";

								$chiavi = array_keys($record_gara);
								foreach($chiavi as $chiave) {
									$vocabolario["#record_gara-".$chiave."#"] = $record_gara[$chiave];
								}


								/* ----------------- INIZIO VARIABILI VOCABOLARIO ----------------- */
								$bind = array();
								$bind[":codice"] = $record_gara["criterio"];

								$sql = "SELECT * FROM b_criteri_buste WHERE codice_criterio= :codice ORDER BY ordinamento ";
								$ris_buste = $pdo->bindAndExec($sql,$bind);
								$vocabolario["#verifica_presenza_plichi#"] = "";
								if ($ris_buste->rowCount()>0) {
									$ris_buste->rowCount() . " buste sigillate riportanti all'esterno la dicitura rispettivamente ";
									while ($record_busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
										$vocabolario["#verifica_presenza_plichi#"] .= "<strong>\"" . $record_busta["nome"] . "\"</strong>, ";
									}
									$vocabolario["#verifica_presenza_plichi#"] = substr($vocabolario["#verifica_presenza_plichi#"], 0, -2) . ".";
								}

								$bind = array();
								$bind[":codice"] = $record_gara["codice"];

								$sql = "SELECT * FROM b_commissioni WHERE codice_gara = :codice ORDER BY valutatore";
								$ris_commissione = $pdo->bindAndExec($sql,$bind);
								$vocabolario["#firme_componenti#"] = "";
								$vocabolario["#componenti_commissione#"] = "";
								$current_valutatore = "";
								if ($ris_commissione->rowCount()>0) {
									while($componente = $ris_commissione->fetch(PDO::FETCH_ASSOC)) {
										if ($current_valutatore != $componente["valutatore"]) {
											$current_valutatore = $componente["valutatore"];
											if ($current_valutatore == "S") {
													$vocabolario["#firme_componenti#"] .= "<br><strong>Commissione valuatrice:</strong><br>";
											} else {
												$vocabolario["#firme_componenti#"] .= "<br><strong>Seggio di gara:</strong><br>";
											}
										}
										$vocabolario["#firme_componenti#"] .= "<strong>" . strtoupper($componente["ruolo"]) . ":</strong> " . $componente["cognome"] . " " . $componente["nome"] . "<br>";
									}
									$vocabolario["#componenti_commissione#"] = $vocabolario["#firme_componenti#"];
								}

								$vincitore = "";
								$record_agg = $ris_aggiudicatario->fetch(PDO::FETCH_ASSOC);
								$punteggi = "con un punteggio totale di " . $record_agg["totale_punteggio"];
								$vincitore .= "<strong>" . $record_agg["ragione_sociale"] . "</strong>";
								if ($record_agg["indirizzo_legale"] != "") $vincitore.= " con sede in " . $record_agg["indirizzo_legale"] .", " . $record_agg["citta_legale"] . " P.IVA n. " . $record_agg["partita_iva"];
								if ($record_agg["tipo"] != "") {
									$bind = array();
									$bind[":codice_capogruppo"] = $record_agg["codice_partecipante"];
									$bind[":codice_gara"] = $_GET["codice"];
									$bind[":codice_lotto"] = $codice_lotto;


									$sql = "SELECT r_partecipanti.*, b_operatori_economici.* FROM r_partecipanti LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice
													WHERE codice_capogruppo = :codice_capogruppo AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto";
									$ris_ragg = $pdo->bindAndExec($sql,$bind);
									$vincitore = "del raggruppamento " . $vincitore;
									if ($ris_ragg->rowCount()>0) {
										while ($record_agg = $ris_ragg->fetch(PDO::FETCH_ASSOC)) {
											$vincitore .= " e <strong>" . $record_agg["ragione_sociale"] . "</strong>";
											if ($record_agg["indirizzo_legale"] != "") $vincitore.= " con sede in " . $record_agg["indirizzo_legale"] .", " . $record_agg["citta_legale"] . " P.IVA n. " . $record_agg["partita_iva"];
										}
									}
								}

								$bind = array();
								$bind[":codice_gara"] = $_GET["codice"];
								$bind[":codice_lotto"] = $codice_lotto;

								$sql = "SELECT r_partecipanti.codice AS codice_partecipante, r_partecipanti.*, b_operatori_economici.*, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ";
								$sql.= " ON r_partecipanti.codice = r_punteggi_gare.codice_partecipante LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice ";
								$sql .= "WHERE r_partecipanti.secondo = 'S' AND r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto GROUP BY r_partecipanti.codice ";
								$ris_seconda = $pdo->bindAndExec($sql,$bind);


								$vocabolario["#seconda_classificata#"] = "";
								if ($ris_seconda->rowCount()>0) {
									$record_sec = $ris_seconda->fetch(PDO::FETCH_ASSOC);
									$punteggio_seconda = "con un punteggio totale di " . $record_sec["totale_punteggio"];
									$seconda = "<strong>" . $record_sec["ragione_sociale"] . "</strong>";
									if ($record_sec["indirizzo_legale"] != "") $seconda.= " con sede in " . $record_sec["indirizzo_legale"] .", " . $record_sec["citta_legale"] . " P.IVA n. " . $record_sec["partita_iva"];
									if ($record_sec["tipo"] != "") {
										$bind = array();
										$bind[":codice_capogruppo"] = $record_sec["codice_partecipante"];
										$bind[":codice_gara"] = $_GET["codice"];
										$bind[":codice_lotto"] = $codice_lotto;
										$sql = "SELECT r_partecipanti.*, b_operatori_economici.* FROM r_partecipanti LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice ";
										$sql .= "WHERE codice_capogruppo = :codice_capogruppo AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto";
										$ris_ragg = $pdo->bindAndExec($sql,$bind);
										$seconda = "del raggruppamento " . $seconda;
										if ($ris_ragg->rowCount()>0) {
											while ($record_sec = $ris_ragg->fetch(PDO::FETCH_ASSOC)) {
												$seconda .= " e <strong>" . $record_sec["ragione_sociale"] . "</strong>";
												if ($record_sec["indirizzo_legale"] != "") $seconda.= " con sede in " . $record_sec["indirizzo_legale"] .", " . $record_sec["citta_legale"] . " P.IVA n. " . $record_sec["partita_iva"];
											}
										}
									}
									$vocabolario["#seconda_classificata#"] = "Che la seconda classificata &egrave; " . $seconda . " " . $punteggio_seconda;
								}

								$vocabolario["#partecipanti#"] = $ris_partecipanti->rowCount();
								$vocabolario["#esclusioni#"] = "";
								$escluse = false;
								if ($ris_partecipanti->rowCount()>0) {
									$vocabolario["#elenco_ditte#"] = "<table style='width:100%'>";
									$vocabolario["#elenco_ditte#"] .= "<tr>";
									$vocabolario["#elenco_ditte#"] .= "<td style=\"width:6%\">#</td>";
									$vocabolario["#elenco_ditte#"] .= "<td style=\"width:19%\" colspan='2'>Protocollo</td>";
									$vocabolario["#elenco_ditte#"] .= "<td style=\"width:15%\">Codice Fiscale</td>";
									$vocabolario["#elenco_ditte#"] .= "<td style=\"width:40%\">Ragione Sociale</td>";
									if (count($ris_punteggi)>0) {
									 foreach($ris_punteggi AS $punteggio) {
									 	$vocabolario["#elenco_ditte#"].=  "<td style=\"width:6%\">" . $punteggio["nome"] . "</td>";
										}
									}
									$vocabolario["#elenco_ditte#"].=  "<td style=\"width:6%\">Totale</td>";
									$vocabolario["#elenco_ditte#"] .= "</tr>";
									$i = 0;
									while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
										$i++;
										if ($partecipante["ammesso"]=="S") {

										$vocabolario["#elenco_ditte#"] .= "<tr>";
										$vocabolario["#elenco_ditte#"] .= "<td style=\"width:6%\">" . $i . "</td>";
										if ($partecipante["numero_protocollo"] == "") {
											$vocabolario["#elenco_ditte#"] .= "<td style=\"width:10%\">" . $partecipante["codice"] . "</td>";
											$vocabolario["#elenco_ditte#"] .= "<td style=\"width:9%\">" . mysql2date($partecipante["timestamp"]) . "</td>";
										} else {
											$vocabolario["#elenco_ditte#"] .= "<td style=\"width:10%\">" . $partecipante["numero_protocollo"] . "</td>";
											$vocabolario["#elenco_ditte#"] .= "<td style=\"width:9%\">" . mysql2date($partecipante["data_protocollo"]) . "</td>";
										}
										$vocabolario["#elenco_ditte#"] .= "<td style=\"width:15%\">" . strtoupper($partecipante["partita_iva"]) . "</td>";
										$vocabolario["#elenco_ditte#"] .= "<td style=\"width:40%\">" . strtoupper($partecipante["ragione_sociale"]) . "</td>";
										$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio=" . $record_gara["criterio"] . " ORDER BY ordinamento";
									 	if (count($ris_punteggi)>0) {
											$totale = 0;
											foreach($ris_punteggi AS $punteggio) {
												$punti = 0;
												$sql_punteggi  = "SELECT * FROM r_punteggi_gare WHERE codice_partecipante = " . $partecipante["codice"];
												$sql_punteggi .= " AND codice_gara = " . $record_gara["codice"] . " ";
												$sql_punteggi .= " AND codice_lotto = " . $codice_lotto . " ";
												$sql_punteggi .= " AND codice_punteggio = " . $punteggio["codice"];
												$ris_punteggio = $pdo->bindAndExec($sql_punteggi,$bind);
												if ($ris_punteggio->rowCount()>0) {
													$arr_punti = $ris_punteggio->fetch(PDO::FETCH_ASSOC);
													$punti = $arr_punti["punteggio"];
												}
												$totale += $punti;
												$vocabolario["#elenco_ditte#"] .= "<td style=\"width:6%; text-align:right\">" . number_format($punti,3,",",".") . "</td>";
											}
											$vocabolario["#elenco_ditte#"] .= "<td style=\"width:6%; text-align:right\">" . number_format($totale,3,",",".") . "</td>";
										}
										$vocabolario["#elenco_ditte#"] .= "</tr>";

										} else {
											if (!$escluse) {
												$escluse = true;
												$vocabolario["#esclusioni#"]  = "che la Commissione di gara, previo esame della documentazione prodotta dai concorrenti, ha escluso dalle fasi successive della gara le seguenti imprese:";
												$vocabolario["#esclusioni#"] .=  "<br><br><table style='width:100%'>";
												$vocabolario["#esclusioni#"] .= "<tr>";
												$vocabolario["#esclusioni#"] .= "<td style=\"width:6%\">#</td>";
												$vocabolario["#esclusioni#"] .= "<td style=\"width:19%\" colspan='2'>Protocollo</td>";
												$vocabolario["#esclusioni#"] .= "<td style=\"width:15%\">Codice Fiscale</td>";
												$vocabolario["#esclusioni#"] .= "<td style=\"width:60%\">Ragione Sociale</td></tr>";
											}
											$vocabolario["#esclusioni#"] .= "<tr>";
											$vocabolario["#esclusioni#"] .= "<td style=\"width:6%\">" . $i . "</td>";
											if ($partecipante["numero_protocollo"] == "") {
												$vocabolario["#esclusioni#"] .= "<td style=\"width:10%\">" . $partecipante["codice"] . "</td>";
												$vocabolario["#esclusioni#"] .= "<td style=\"width:9%\">" . mysql2date($partecipante["timestamp"]) . "</td>";
											} else {
												$vocabolario["#esclusioni#"] .= "<td style=\"width:10%\">" . $partecipante["numero_protocollo"] . "</td>";
												$vocabolario["#esclusioni#"] .= "<td style=\"width:9%\">" . mysql2date($partecipante["data_protocollo"]) . "</td>";
											}
											$vocabolario["#esclusioni#"] .= "<td style=\"width:15%\">" . strtoupper($partecipante["partita_iva"]) . "</td>";
											$vocabolario["#esclusioni#"] .= "<td style=\"width:60%\">" . strtoupper($partecipante["ragione_sociale"]) . "</td>";
											$vocabolario["#esclusioni#"] .= "</tr>";
										}
									}
									if ($escluse) $vocabolario["#esclusioni#"] .= "</table><br>";
									$vocabolario["#elenco_ditte#"] .= "</table>";
								}

								include($root."/gare/agg_provvisoria/" . $directory. "/verbale.php");

								if (!isset($lotto)) {
									$sql = "SELECT SUM(b_importi_gara.importo_base) AS base, ";
									$sql.= "SUM(b_importi_gara.importo_oneri_ribasso) AS oneri_ribasso, ";
									$sql.= "SUM(b_importi_gara.importo_oneri_no_ribasso) AS oneri_no_ribasso, ";
									$sql.= "SUM(b_importi_gara.importo_personale) AS personale FROM b_importi_gara WHERE codice_gara = " . $record_gara["codice"];
									$ris_importi = $pdo->bindAndExec($sql,$bind);
									if ($ris_importi->rowCount()>0) {
										$record_importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
									}
									$lotto["codice"] = 0;
								} else {

									$record_importi["base"] = $lotto["importo_base"];
									$record_importi["oneri_ribasso"] = $lotto["importo_oneri_ribasso"];
									$record_importi["oneri_no_ribasso"] = $lotto["importo_oneri_no_ribasso"];
									$record_importi["personale"] = $lotto["importo_personale"];
								}

								if (isset($record_importi)) {
									$ribasso = getImportoAggiudicazione($record_gara["codice"],$lotto["codice"]);
									if (!empty($ribasso)) {
										$percentuale_offerta = $ribasso["percentuale"];
										$importo_aggiudicazione = $ribasso["importo"];
									}
									if (isset($importo_aggiudicazione)) {
										$vocabolario["#importo_aggiudicazione#"] = "&euro; " . number_format($importo_aggiudicazione,2,",",".");
									}
									$vocabolario["#importi_gara#"] = "&euro; " . number_format($record_importi["oneri_no_ribasso"],2,",",".") . " per oneri di sicurezza";
								}
								$bind = array();
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_lotto"] = $codice_lotto;

								$sql = "SELECT b_log_aperture.timestamp, r_partecipanti.partita_iva, r_partecipanti.ragione_sociale
												FROM b_log_aperture JOIN b_criteri_buste ON b_log_aperture.codice_busta = b_criteri_buste.codice
												JOIN r_partecipanti ON b_log_aperture.codice_partecipante = r_partecipanti.codice
												WHERE r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
												AND b_log_aperture.esito = 'Positivo'
												AND b_criteri_buste.tecnica = 'S'
												ORDER BY timestamp ";
								$ris_buste = $pdo->bindAndExec($sql,$bind);
								$vocabolario["#apertura_buste_tecniche#"] = "";
								if ($ris_buste->rowCount()>0) {
									$vocabolario["#apertura_buste_tecniche#"] = "Si riportano le tempetische di apertura delle buste virtuali contenenti le offerte tecniche come da registro che segue:<br><table style='width:100%'>";
									while ($record_busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
										$vocabolario["#apertura_buste_tecniche#"] .= "<tr>
											<td style='width:30%'>" . mysql2completedate($record_busta["timestamp"]) . "</td>
											<td style='width:20%'>". strtoupper($record_busta["partita_iva"])."</td>
											<td style='width:50%'>". strtoupper($record_busta["ragione_sociale"])."</td></tr>";
									}
									$vocabolario["#apertura_buste_tecniche#"] .= "</table>";
								}

								$sql = "SELECT b_log_aperture.timestamp, r_partecipanti.partita_iva, r_partecipanti.ragione_sociale
												FROM b_log_aperture JOIN b_criteri_buste ON b_log_aperture.codice_busta = b_criteri_buste.codice
												JOIN r_partecipanti ON b_log_aperture.codice_partecipante = r_partecipanti.codice
												WHERE r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto
												AND b_log_aperture.esito = 'Positivo'
												AND b_criteri_buste.economica = 'S'
												ORDER BY timestamp ";
								$ris_buste = $pdo->bindAndExec($sql,$bind);
								$vocabolario["#apertura_buste_economiche#"] = "";
								if ($ris_buste->rowCount()>0) {
									$vocabolario["#apertura_buste_economiche#"] = "<table style='width:100%'>";
									while ($record_busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
										$vocabolario["#apertura_buste_economiche#"] .= "<tr>
											<td style='width:30%'>" . mysql2completedate($record_busta["timestamp"]) . "</td>
											<td style='width:20%'>". strtoupper($record_busta["partita_iva"])."</td>
											<td style='width:50%'>". strtoupper($record_busta["ragione_sociale"])."</td></tr>";
									}
									$vocabolario["#apertura_buste_economiche#"] .= "</table>";
								}
								$vocabolario["#vincitore#"] = $vincitore;
								$vocabolario["#punteggi#"] = $punteggi;
								$sql_modello = "SELECT * FROM b_modelli_standard WHERE codice = 3"; // Codice 3 - II Verbale
								$ris_modello = $pdo->bindAndExec($sql_modello,$bind);
								if ($ris_modello->rowCount()>0) {
									$modello = $ris_modello->fetch(PDO::FETCH_ASSOC);
									$bind = array();
									$bind[":codice"] = $modello["codice"];
									$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
									$sql = "SELECT * FROM b_modelli_enti WHERE attivo = 'S' AND codice_modello = :codice AND codice_ente = :codice_ente";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) {
										$modello = $ris->fetch(PDO::FETCH_ASSOC);
									}
									$html = strtr($modello["corpo"],$vocabolario);
									} else {
										echo "<h1>Modello mancante</h1>";
									}

							}
					?>
                        <form name="box" method="post" action="salva_II.php" rel="validate">
                        <input type="hidden" name="operazione" value="<? echo $operazione ?>">
				 		<input type="hidden" name="codice" value="<? echo $codice_elemento; ?>">
                 		<input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"]; ?>">
                 		<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
                 		<input type="hidden" name="allega" id="allega" value="N">
                        <?
							$file_title = "Verbale_ammissione";
							include($root."/moduli/editor.php");
							if ($codice_elemento>0) { ?>
							<input type="button" style="background-color:#C00" class="submit_big" onClick="elimina('<? echo $codice_elemento ?>','gare/verbali/II_verbale');" src="/img/del.png" value="Rielabora verbale">
							<? }  ?>
                       		 <input class="submit_big" type="submit" value="Salva">
							<input class="submit_big" type="submit" onclick="$('#allega').val('S');return true;" value="Salva ed Allega">

                        </form>
            <div class="clear"></div>
		<?

				} else {
					echo "<h1>La gara non &egrave; stata aggiudicata</h1>";
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
