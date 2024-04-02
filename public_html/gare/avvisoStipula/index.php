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
		<h1>Avviso appalto aggiudicato</h1>
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
						<a class="submit_big" href="index.php?codice=<? echo $_GET["codice"] ?>&lotto=
						<? echo $lotto["codice"] ?>">
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
				$sql = "SELECT r_partecipanti.codice AS codice_partecipante, r_partecipanti.*, b_operatori_economici.*, SUM(r_punteggi_gare.punteggio) as totale_punteggio
				FROM r_partecipanti LEFT JOIN r_punteggi_gare ON r_partecipanti.codice = r_punteggi_gare.codice_partecipante LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice
				WHERE r_partecipanti.primo = 'S' AND r_partecipanti.codice_gara = :codice AND r_partecipanti.codice_lotto = :codice_lotto GROUP BY r_partecipanti.codice ";
				$ris_aggiudicatario = $pdo->bindAndExec($sql,$bind);
				if ($ris_aggiudicatario->rowCount()>0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					if (isset($lotto)) {
						$record_gara["oggetto"] .= " - Lotto: " . $lotto["oggetto"];
						$record_gara["soglia_anomalia"] = $lotto["soglia_anomalia"];
						$record_gara["ribasso"] = $lotto["ribasso"];
					}

					$ris_rdo = $pdo->go("SELECT codice FROM b_rdo_ad WHERE codice_gara = :codice",[":codice"=>$record_gara["codice"]]);
					if ($ris_rdo->rowCount() > 0) {
						$strsql = "SELECT r_partecipanti.*, MAX(r_rdo_ad.timestamp_trasmissione) AS time_risposta
								FROM r_partecipanti
								LEFT JOIN r_rdo_ad ON r_partecipanti.codice = r_rdo_ad.codice_partecipante
								WHERE codice_gara = :codice_gara
								AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)
								AND r_rdo_ad.timestamp_trasmissione > 0
								GROUP BY r_partecipanti.codice ORDER BY primo DESC, ragione_sociale ";
							$ris_partecipanti = $pdo->bindAndExec($strsql,[":codice_gara"=>$record_gara["codice"]]);
					} else {
						$sql = "SELECT r_partecipanti.* FROM r_partecipanti WHERE r_partecipanti.codice_gara = :codice AND r_partecipanti.codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";
						$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
					}

					$editor_tipo = "avviso_appalto_aggiudicato";

					$bind = array(
						":codice" => $record_gara["codice"],
						":codice_lotto" => $codice_lotto,
						":tipo" => $editor_tipo,
					);

					$strsql = "SELECT * FROM b_documentale WHERE tipo= :tipo AND attivo = 'S' AND sezione = 'gara' AND codice_gara = :codice AND codice_lotto = :codice_lotto";
					$risultato = $pdo->bindAndExec($strsql, $bind);

					if ($risultato->rowCount()>0) {
						$elemento = $risultato->fetch(PDO::FETCH_ASSOC);
						$html = $elemento["corpo"];
						$operazione = "UPDATE";
						$codice_elemento = $elemento["codice"];
					} else {
						$operazione = "INSERT";
						$codice_elemento = 0;
						$bind = array();
						$bind[":codice"] = $record_gara["criterio"];

						$sql = "SELECT * FROM b_criteri WHERE codice = :codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {
							$rec = $ris->fetch(PDO::FETCH_ASSOC);
							$record_gara["nome_criterio"] = $rec["criterio"];
							$directory = $rec["directory"];
							/* Completamento in caso di Massimo ribasso */
							if ($record_gara["criterio"] == 6) {
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
						$bind[":codice"] = $record_gara["tipologia"];
						$record_gara["tipologie_gara"] = "";
						$sql = "SELECT tipologia FROM b_tipologie WHERE codice = :codice";
						$ris_tipologie = $pdo->bindAndExec($sql,$bind);
						if ($ris_tipologie->rowCount()>0) {
							while($rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC)) {
								$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
							}
						}

						$record_gara["data_accesso"] = mysql2date($record_gara["data_accesso"]);
						$record_gara["data_scadenza"] = mysql2date($record_gara["data_scadenza"]);
						$record_gara["data_apertura"] = mysql2date($record_gara["data_apertura"]);
						$record_gara["data_atto_esito"] = mysql2date($record_gara["data_atto_esito"]);
						$record_gara["url"] = $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
						$vocabolario["#soglia_anomalia#"] = "";
						if ($record_gara["soglia_anomalia"] > 0) $vocabolario["#soglia_anomalia#"] = "Soglia di anomalia: " . $record_gara["soglia_anomalia"];

						$bind = array();
						$bind[":codice_ente"] = $record_gara["codice_ente"];
						$sql = "SELECT * FROM b_enti WHERE codice = :codice_ente";
						$ris_ente = $pdo->bindAndExec($sql,$bind);
						if ($ris_ente->rowCount()>0) $record_appaltatore = $ris_ente->fetch(PDO::FETCH_ASSOC);

						$bind = array();
						$bind[":codice_gestore"] = $record_gara["codice_gestore"];
						$sql = "SELECT * FROM b_enti WHERE codice = :codice_gestore";
						$ris_gestore = $pdo->bindAndExec($sql,$bind);
						if ($ris_gestore->rowCount()>0) $record_gestore = $ris_gestore->fetch(PDO::FETCH_ASSOC);

						$bind = array();
						$bind[":criterio"] = $record_gara["criterio"];
						$sql = "SELECT * FROM b_criteri_punteggi WHERE codice_criterio= :criterio ORDER BY ordinamento";
						$ris_punteggi = $pdo->bindAndExec($sql,$bind);

						$chiavi = array_keys($record_appaltatore);
						foreach($chiavi as $chiave) {
							$vocabolario["#record_appaltatore-".$chiave."#"] = $record_appaltatore[$chiave];
						}
						$vocabolario["#record_appaltatore-logo-path#"] = ! empty($record_appaltatore["logo"]) ? "https://localhost/documenti/enti/{$record_appaltatore["logo"]}" : "https://localhost/img/no_logo.gif";

						$chiavi = array_keys($record_gestore);
						foreach($chiavi as $chiave) {
							$vocabolario["#record_gestore-".$chiave."#"] = $record_gestore[$chiave];
						}
						$vocabolario["#record_gestore-logo-path#"] = ! empty($record_gestore["logo"]) ? "https://localhost/documenti/enti/{$record_gestore["logo"]}" : "https://localhost/img/no_logo.gif";

						$record_gara["prezzoBase"] = number_format($record_gara["prezzoBase"],2,",",".");
						$record_gara["data_pubblicazione"] = mysql2date($record_gara["data_pubblicazione"]);

						$chiavi = array_keys($record_gara);
						foreach($chiavi as $chiave) {
							$vocabolario["#record_gara-".$chiave."#"] = $record_gara[$chiave];
						}

						$bind=array();
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql_organismo = "SELECT * FROM b_organismi_ricorso WHERE codice_ente = :codice_ente";
						$ris_organismo = $pdo->bindAndExec($sql_organismo,$bind);
						$vocabolario["#organismo_responsabile#"] = "";
						if ($ris_organismo->rowCount() > 0) {
							$record_organismo = $ris_organismo->fetch(PDO::FETCH_ASSOC);
							$vocabolario["#organismo_responsabile#"] = $record_organismo["denominazione"] . ", ";
							$vocabolario["#organismo_responsabile#"] .= $record_organismo["indirizzo"] . ", ";
							$vocabolario["#organismo_responsabile#"] .= $record_organismo["citta"] . " (" . $record_organismo["provincia"] . ") " . $record_organismo["cap"] . ", ";
							$vocabolario["#organismo_responsabile#"] .= $record_organismo["pec"];
						}


						$vincitore = "";
						$record_agg = $ris_aggiudicatario->fetch(PDO::FETCH_ASSOC);
						$punteggi = "con un punteggio totale di " . $record_agg["totale_punteggio"];
						$vincitore .= "<strong>" . $record_agg["ragione_sociale"] . "</strong>";
						if ($record_agg["indirizzo_legale"] != "") $vincitore.= " con sede in " . $record_agg["indirizzo_legale"] .", " . $record_agg["citta_legale"] . " P.IVA n. " . $record_agg["partita_iva"];
						$bind = array();
						$bind[":codice_partecipante"] = $record_agg["codice_partecipante"];
						$sql = "SELECT * FROM r_punteggi_gare WHERE codice_partecipante = :codice_partecipante";
						if ($record_agg["tipo"] != "") {
							$bind[":codice_gara"] = $record_gara["codice"];
							$sql = "SELECT r_partecipanti.*, b_operatori_economici.* FROM r_partecipanti LEFT JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice ";
							$sql .= "WHERE codice_capogruppo = :codice_partecipante AND codice_gara = :codice_gara";
							$ris_ragg = $pdo->bindAndExec($sql,$bind);
							$vincitore = "del raggruppamento " . $vincitore;
							if ($ris_ragg->rowCount()>0) {
								while ($record_agg = $ris_ragg->fetch(PDO::FETCH_ASSOC)) {
									$vincitore .= " e <strong>" . $record_agg["ragione_sociale"] . "</strong>";
									if ($record_agg["indirizzo_legale"] != "") $vincitore.= " con sede in " . $record_agg["indirizzo_legale"] .", " . $record_agg["citta_legale"] . " P.IVA n. " . $record_agg["partita_iva"];
								}
							}
						} else {
							$vincitore = "di " . $vincitore;
						}

						if ($ris_partecipanti->rowCount()>0) {
							$ris_partecipanti = $ris_partecipanti->fetchAll(PDO::FETCH_ASSOC);
							$vocabolario["#partecipanti#"] = count($ris_partecipanti);
							$vocabolario["#ammesso#"] = 0;
							$vocabolario["#esclusi#"] = 0;
							foreach($ris_partecipanti as $partecipante) {
								if ($partecipante["ammesso"]=="S") $vocabolario["#ammesso#"]++;
								if ($partecipante["escluso"]=="S") $vocabolario["#esclusi#"]++;
							}
						}

						$importo_aggiudicazione = $record_gara["importoAggiudicazione"];

						if (!isset($lotto)) {
							$bind = array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$sql = "SELECT SUM(b_importi_gara.importo_base) AS base, ";
							$sql.= "SUM(b_importi_gara.importo_oneri_ribasso) AS oneri_ribasso, ";
							$sql.= "SUM(b_importi_gara.importo_oneri_no_ribasso) AS oneri_no_ribasso, ";
							$sql.= "SUM(b_importi_gara.importo_personale) AS personale FROM b_importi_gara WHERE codice_gara = :codice_gara";
							$ris_importi = $pdo->bindAndExec($sql,$bind);
							if ($ris_importi->rowCount()>0) {
								$record_importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
							}
						} else {
							$record_importi["base"] = $lotto["importo_base"];
							$record_importi["oneri_ribasso"] = $lotto["importo_oneri_ribasso"];
							$record_importi["oneri_no_ribasso"] = $lotto["importo_oneri_no_ribasso"];
							$record_importi["personale"] = $lotto["importo_personale"];
							$importo_aggiudicazione = $lotto["importoAggiudicazione"];
						}

						$vocabolario["#importo_aggiudicazione#"] = "&euro; " . number_format($importo_aggiudicazione,2,",",".");

						if (isset($record_importi)) {
							$vocabolario["#importo_base#"] = number_format(($record_importi["base"]),2,",",".");
							// $vocabolario["#importo_base#"] = number_format(($record_importi["base"]+ $record_importi["oneri_ribasso"] + $record_importi["personale"]),2,",",".");
							$vocabolario["#importi_gara#"] = number_format($record_importi["oneri_no_ribasso"],2,",",".");
						}


						$vocabolario["#vincitore#"] = $vincitore;
						$vocabolario["#punteggi#"] = $punteggi;
						$sql_modello = "SELECT * FROM b_modelli_standard WHERE codice = 27"; // Codice 27 - Comunicazione stipula contratto
						$ris_modello = $pdo->query($sql_modello);
						if ($ris_modello->rowCount()>0) {
							$modello = $ris_modello->fetch(PDO::FETCH_ASSOC);
							$bind = array();
							$bind[":codice_modello"] = $modello["codice"];
							$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
							$sql = "SELECT * FROM b_modelli_enti WHERE attivo = 'S' AND codice_modello = :codice_modello AND codice_ente = :codice_ente ";
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
					<form name="box" method="post" action="save.php" rel="validate">
						<input type="hidden" name="operazione" value="<? echo $operazione ?>">
						<input type="hidden" name="codice" value="<? echo $codice_elemento; ?>">
						<input type="hidden" name="codice_gara" value="<? echo $record_gara["codice"]; ?>">
						<input type="hidden" name="codice_lotto" value="<? echo $codice_lotto; ?>">
						<input type="hidden" name="allega" id="allega" value="N">
						<input type="hidden" name="invia" id="invia" value="N">
						<input type="hidden" name="bozza" id="bozza" value="N">
						<div style="float:left; width:65%">
							<?
							$file_title = $editor_tipo;
							include($root."/moduli/editor.php");
							if ($codice_elemento > 0 && $elemento["bozza"] == "N") { ?>
								<input type="button" style="background-color:#C00" class="submit_big" onClick="elimina('<? echo $codice_elemento ?>','gare/avvisoStipula');" src="/img/del.png" value="Rielabora Comunicazione">
								<?
							} else { ?>
								<? if ($codice_elemento > 0 && $elemento["bozza"] == "S") { ?><input type="button" style="background-color:#C00" class="submit_big" onClick="elimina('<? echo $codice_elemento ?>','gare/avvisoStipula');" src="/img/del.png" value="Rielabora Comunicazione"><? } ?>
								<input class="submit_big" type="submit" onClick="$('#bozza').val('S');" value="Salva una bozza">
								<input class="submit_big" type="submit" onClick="$('#bozza').val('N');$('#invia').val('N')" value="Pubblica avviso">
								<input class="submit_big" type="submit" onClick="$('#bozza').val('N');$('#invia').val('S')" value="Pubblica avviso e invia comunicazione">
							<? } ?>
						</div>
						<div style="float:right; width:34%;">
							<h2>Destinatari</h2>
							<?
							if (count($ris_partecipanti)>0) {
								$messaggio = false;
								foreach($ris_partecipanti AS $partecipante) {
									$class = "";
									$alert = "";
									$tipo = "";
									if ($partecipante["tipo"] != "") $tipo = " - <strong>CAPOGRUPPO</strong>";
									if ($partecipante["codice_utente"] == 0) {
										$messaggio = true;
										$class = " errore";
										$alert = "<img src=\"/img/alert.png\" alt=\"Alert\" style=\"vertical-align:middle\"> ";
									}
									?>
									<div class="box<? echo $class ?>">
									<? echo $alert .  $partecipante["ragione_sociale"] . $tipo ?>
									</div>
									<?
								}
								if ($messaggio) echo "<br><strong><img src=\"/img/alert.png\" alt=\"Alert\" style=\"vertical-align:middle\"> Impossibile inviare la comunicazione a tutti i partecipanti</strong>";
							}
							?>
						</div>
					</form>
					<div class="clear"></div>
					<?
					include($root."/gare/ritorna.php");
				} else {
					echo "<h1>La gara non &egrave; stata aggiudicata</h1>";
				}
			} else {
				if($ris_lotti->rowCount() < 1) echo "<h1>Gara non trovata</h1>";
			}
		}
	} else {
		echo "<h1>Gara non trovata</h1>";
	}
	include_once($root."/layout/bottom.php");
?>
