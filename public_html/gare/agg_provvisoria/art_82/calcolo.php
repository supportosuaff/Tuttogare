<?
	if ((isset($edit) && $edit) || (isset($asta_telematica) && ($asta_telematica))) {
		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		$bind[":codice_lotto"] = $_POST["codice_lotto"];

		$strsql = "UPDATE r_partecipanti SET primo = 'N', secondo = 'N' WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$strsql = "UPDATE r_partecipanti SET anomalia = 'N', motivazione_anomalia = '', escluso='N' WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND verifica = 'N' AND anomalia_facoltativa = 'N'";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$strsql = "SELECT r_partecipanti.codice FROM r_partecipanti ";
		$strsql.= " WHERE r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 AND r_partecipanti.ammesso = 'S' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		$numero_partecipanti = $risultato->rowCount();
		$numero_aggiudicatari = 1;
		if (isset($_POST["numero_aggiudicatari"]) && is_numeric($_POST["numero_aggiudicatari"])) $numero_aggiudicatari = $_POST["numero_aggiudicatari"];
		if ($numero_partecipanti>0) {
			// Codice Gruppo 42: Validità dell'aggiudicazione;
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$sql = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 42)";
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount() > 0) {
				$rec = $ris->fetch(PDO::FETCH_ASSOC);
				if ($rec["opzione"] == "62" && $numero_partecipanti < 2) {
					$errore = true;
					$msg.= "Numero di offerte valide insufficiente\\n";
				} else if ($rec["opzione"] == "63" && $numero_partecipanti < 3) {
					$errore = true;
					$msg.= "Numero di offerte valide insufficiente\\n";
				}
				if (!isset($errore) || !$errore) {
						$primi = 0;
						$secondi = 0;
						$partecipanti_minimi = 5;
						$anomalia_siciliana = false;
						$tipologie_lavori = [1,5,7,8,9];
						$tipologie_forniture = [3];
						/* $province_siciliane = ["AG","CL","CT","EN","ME","PA","RG","SR","TP"]; // introdotto controllo su ente beneficiario per applicazione legge regionale sicilia L.13/2019
						$provincia_beneficiario = $pdo->go("SELECT provincia FROM b_enti WHERE codice = :codice_ente ",[":codice_ente"=>$record_gara["codice_ente"]])->fetch(PDO::FETCH_ASSOC)["provincia"];
						if (strtotime($record_gara["data_pubblicazione"]) > strtotime(date('2019-09-30')) && (in_array($record_gara["tipologia"],$tipologie_lavori) !== false) && (in_array($provincia_beneficiario,$province_siciliane) !== false)) {
							$anomalia_siciliana = true;
						} */
						$forceSiciliana = [];
						if (in_array($_POST["codice_gara"],$forceSiciliana) !== false) $anomalia_siciliana = true;
									
						// if (strtotime($record_gara["data_pubblicazione"]) > strtotime('2016-04-20')) $partecipanti_minimi = 5;
						if ($numero_partecipanti < $partecipanti_minimi) {
							$bind = array();
							$bind[":codice_gara"] = $_POST["codice_gara"];
							$bind[":codice_lotto"] = $_POST["codice_lotto"];
							// Se il numero dei partecipanti è inferiore a 5 si procede all'aggiudicazione senza effettuare la verifica delle offerte anomale
							$sql = "SELECT r_partecipanti.codice, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
							$sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara AND r_punteggi_gare.codice_lotto = :codice_lotto
											 AND r_partecipanti.codice_capogruppo = 0 AND r_partecipanti.ammesso = 'S' AND escluso = 'N' AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";
							$sql.= " GROUP BY r_punteggi_gare.codice_partecipante, r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ORDER BY totale_punteggio DESC";
							$ris = $pdo->bindAndExec($sql,$bind);
							$i=0;
							$punteggio=0;
							while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
								$bind = array();
								$bind[":codice"] = $rec["codice"];
								if ($punteggio != $rec["totale_punteggio"]) {
									$punteggio = $rec["totale_punteggio"];
									$i++;
								}
								if ($i<=$numero_aggiudicatari) {
									$sql = "UPDATE r_partecipanti SET primo = 'S' WHERE codice = :codice";
									$ris_update = $pdo->bindAndExec($sql,$bind);
									$primi++;
								} else if ($i==2 && $numero_aggiudicatari == 1)  {
									$sql = "UPDATE r_partecipanti SET secondo = 'S' WHERE codice = :codice";
									$ris_update = $pdo->bindAndExec($sql,$bind);
									$secondi++;
								} else {
									break;
								}
							}
						} else {
							$sogliaInclusa = true;
							if ($record_gara["norma"] == "2023-36") {
								$sogliaInclusa = false;
								if (in_array($record_gara["tipologia"],$tipologie_forniture) === false) {
									$esclusione_automatica = true;
									if (!empty($record_gara["algoritmo_anomalia"])){
										$algoritmo_anomalia = $record_gara["algoritmo_anomalia"];
									} else if (!empty($record_lotto["algoritmo_anomalia"])) {
										$algoritmo_anomalia = $record_lotto["algoritmo_anomalia"];
									} 
									if (empty($algoritmo_anomalia) || $algoritmo_anomalia == "S") {
										$algoritmi = array();
										$algoritmi[] = "A";
										$algoritmi[] = "B";
										shuffle($algoritmi);
										$selezione = rand(0,1);
										$algoritmo_anomalia = $algoritmi[$selezione];
										$algoritmi["selezione"] = $selezione;
										$sequenza_anomalia = json_encode($algoritmi);

										$bind = array();
										$bind[":codice_gara"] = $_POST["codice_gara"];
										$bind[":sequenza_anomalia"] = $sequenza_anomalia;
										if ($_POST["codice_lotto"] == 0) {
											$sql = "UPDATE b_gare SET sequenza_anomalia = :sequenza_anomalia WHERE codice = :codice_gara";
										} else {
											$bind[":codice_lotto"] = $_POST["codice_lotto"];
											$sql = "UPDATE b_lotti SET sequenza_anomalia = :sequenza_anomalia WHERE codice = :codice_lotto AND codice_gara = :codice_gara";
										}

										$update_stato = $pdo->bindAndExec($sql,$bind);
									}
									if ($algoritmo_anomalia == "A") {
										if ($numero_partecipanti < 15) {
											$algoritmo_anomalia = "23A5";
										} else {
											$algoritmo_anomalia = "23A15";
										}
									} else if ($algoritmo_anomalia == "B") {
										$algoritmo_anomalia = "23B";
									} else if ($algoritmo_anomalia == "C") {
										$algoritmo_anomalia = "23C";
									}
									if ($algoritmo_anomalia == "N") {
										$esclusione_automatica = false;
										$skip_anomalia = true;
										$algoritmo_anomalia = "";
									}
								} else {
									$esclusione_automatica = false;
									$skip_anomalia = true;
									$algoritmo_anomalia = "";
								}
							} else {
								$bind = array();
								$bind[":codice_gara"] = $_POST["codice_gara"];

								// Se il numero dei partecipanti è maggiore a 10 ed è stata selezionata l'opzione si escludono automaticamente le offerte anomale
								// Codice Opzione 59: Esclusione automatica delle offerte anomale;
								$sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 59";
								$ris = $pdo->bindAndExec($sql,$bind);
								$esclusione_automatica = false;
								$partecipanti_esclusione_automatica = 10;
								if (strtotime($record_gara["data_pubblicazione"]) >= strtotime('2020-07-16')) {
									$partecipanti_esclusione_automatica = 5;
									if ($record_gara["procedura"] == 1 || $record_gara["procedura"] == 3) $partecipanti_esclusione_automatica = 10;
								}
								if ($ris->rowCount()>0 && $numero_partecipanti >= $partecipanti_esclusione_automatica && (!isset($asta_telematica))) $esclusione_automatica = true;
								if (strtotime($record_gara["data_pubblicazione"]) > strtotime('2019-04-18') && $numero_partecipanti >= $partecipanti_esclusione_automatica) {
									$esclusione_automatica = true;
									if (in_array($record_gara["tipologia"],$tipologie_lavori) !== false && $record_gara["prezzoBase"] >= 5548000) {
										$esclusione_automatica = false;
									} else if (in_array($record_gara["tipologia"],$tipologie_lavori) === false && $record_gara["prezzoBase"] >= 221000) {
										$esclusione_automatica = false;
									}
								}
								// Con l'introduzione del decreto sblocca cantieri l'esclusione automatica si applica in ogni caso quando il numero è superiore a 10 (salvo appalti transfrontalieri)
								if (!empty($record_gara["algoritmo_anomalia"])){
									$algoritmo_anomalia = $record_gara["algoritmo_anomalia"];
								} else if (!empty($record_lotto["algoritmo_anomalia"])) {
									$algoritmo_anomalia = $record_lotto["algoritmo_anomalia"];
								} 
								if (isset($algoritmo_anomalia) && $algoritmo_anomalia == "13.19") $algoritmo_anomalia = "";
								if (empty($algoritmo_anomalia)) {
									if (strtotime($record_gara["data_pubblicazione"]) > strtotime(date('2019-04-18'))) {
										if ($anomalia_siciliana) {
											$algoritmo_anomalia = "13.19";
										} else {
											$algoritmo_anomalia = ($numero_partecipanti >= 15) ? "2" : "2bis";
										}
									} else {
										$algoritmo_anomalia = (!empty($_POST["scelta_anomalia"])) ? $_POST["scelta_anomalia"] : "a";
										if ($algoritmo_anomalia == "z") {
											$algoritmi = array();
											$algoritmi[] = "a";
											$algoritmi[] = "b";
											$algoritmi[] = "c";
											$algoritmi[] = "d";
											$algoritmi[] = "e";
											shuffle($algoritmi);
											$selezione = rand(0,4);
											$algoritmo_anomalia = $algoritmi[$selezione];
											$algoritmi["selezione"] = $selezione;
											$sequenza_anomalia = json_encode($algoritmi);

											$bind = array();
											$bind[":codice_gara"] = $_POST["codice_gara"];
											$bind[":sequenza_anomalia"] = $sequenza_anomalia;
											if ($_POST["codice_lotto"] == 0) {
												$sql = "UPDATE b_gare SET sequenza_anomalia = :sequenza_anomalia WHERE codice = :codice_gara";
											} else {
												$bind[":codice_lotto"] = $_POST["codice_lotto"];
												$sql = "UPDATE b_lotti SET sequenza_anomalia = :sequenza_anomalia WHERE codice = :codice_lotto AND codice_gara = :codice_gara";
											}

											$update_stato = $pdo->bindAndExec($sql,$bind);
										}
									}
								}
							}
							if (!empty($algoritmo_anomalia) || $skip_anomalia) {
								$errore_calcolo_soglia = false;
								$media = 0;
								$soglia_anomalia = 0;
								$scarto_medio = 0;
								$coef_e = 0;
								$msg = "";
								if (empty($skip_anomalia) && $_POST["calcola_anomalia"] == "S") {
									$riferimeto_arrotondamento = (!isset($record_lotto)) ? $record_gara : $record_lotto;
									$decimali_graduatoria = (!empty($_POST["decimali_graduatoria"])) ? $_POST["decimali_graduatoria"] : $riferimeto_arrotondamento["decimali_graduatoria"];
									$arrotondamento = (!empty($_POST["arrotondamento"])) ? $_POST["arrotondamento"] : $riferimeto_arrotondamento["arrotondamento"];
									$solo_soglia = (!empty($_POST["solo_soglia"])) ? $_POST["solo_soglia"] : $riferimeto_arrotondamento["solo_soglia"];
									$interpretazione_anomalia = (!empty($_POST["interpretazione_anomalia"])) ? $_POST["interpretazione_anomalia"] : $riferimeto_arrotondamento["interpretazione_anomalia"];
									include("procedure_anomalia/".$algoritmo_anomalia.".php");
								}

								if (!$errore_calcolo_soglia) {
									if (empty($skip_anomalia) && $_POST["calcola_anomalia"] == "S") {
										$bind = array();
										$bind[":codice_gara"] = $_POST["codice_gara"];
										$bind[":media"] = $media;
										$bind[":soglia_anomalia"] = $soglia_anomalia;
										$bind[":coef_e"] = $coef_e;
										$bind[":scarto_medio"] = $scarto_medio;
										$bind[":algoritmo_anomalia"] = $algoritmo_anomalia;
										$bind[":messaggio_anomalia"] = $msg;
										$bind[":decimali_graduatoria"] = $decimali_graduatoria;
										$bind[":arrotondamento"] = $arrotondamento;
										$bind[":solo_soglia"] = $solo_soglia;
										$bind[":interpretazione_anomalia"] = $interpretazione_anomalia;

										if ($_POST["codice_lotto"] == 0) {
											$sql = "UPDATE b_gare SET coef_e = :coef_e, algoritmo_anomalia = :algoritmo_anomalia, decimali_graduatoria = :decimali_graduatoria, arrotondamento = :arrotondamento, solo_soglia = :solo_soglia, interpretazione_anomalia = :interpretazione_anomalia, messaggio_anomalia = :messaggio_anomalia, soglia_anomalia = :soglia_anomalia, scarto_medio = :scarto_medio, media = :media WHERE codice = :codice_gara";
										} else {
											$bind[":codice_lotto"] = $_POST["codice_lotto"];
											$sql = "UPDATE b_lotti SET coef_e = :coef_e, algoritmo_anomalia = :algoritmo_anomalia, decimali_graduatoria = :decimali_graduatoria, arrotondamento = :arrotondamento, solo_soglia = :solo_soglia, interpretazione_anomalia = :interpretazione_anomalia, messaggio_anomalia = :messaggio_anomalia, soglia_anomalia = :soglia_anomalia, scarto_medio = :scarto_medio, media = :media WHERE codice = :codice_lotto AND codice_gara = :codice_gara";
										}
										$update_stato = $pdo->bindAndExec($sql,$bind);
									} else {
										if (isset($record_lotto)) {
											$soglia_anomalia = $record_lotto["soglia_anomalia"];
										} else {
											$soglia_anomalia = $record_gara["soglia_anomalia"];
										}
									}
									$bind = array();
									$bind[":codice_gara"] = $_POST["codice_gara"];
									$bind[":codice_lotto"] = $_POST["codice_lotto"];
									$bind[":soglia_anomalia"] = $soglia_anomalia;
									$operatoreSoglia = ">=";
									if (!$sogliaInclusa) {
										$operatoreSoglia = ">";
									}
									$sql = "SELECT r_partecipanti.codice, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
									$sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara
													AND r_punteggi_gare.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S'";
									$sql.= " GROUP BY r_punteggi_gare.codice_partecipante, r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ";
									$sql.= " HAVING totale_punteggio {$operatoreSoglia} :soglia_anomalia";

									$ris = $pdo->bindAndExec($sql,$bind);
									$i=0;
									$punteggio=0;
									// settaggio anomalia ed eventuale esclusione punteggi sopra soglia anomalia;
									if ($ris->rowCount()>0) {
										while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) {
												$bind = array();
												$bind[":codice"] = $rec["codice"];
												$sql = "UPDATE r_partecipanti SET anomalia = 'S', motivazione_anomalia = 'Calcolo automatico delle offerte anomale'";
												if ($esclusione_automatica) $sql .= ", escluso = 'S'";
												$sql.= " WHERE codice = :codice AND verifica = 'N' AND anomalia_facoltativa = 'N' ";
												$ris_update = $pdo->bindAndExec($sql,$bind);
										}
									}
									$bind = array();
									$bind[":codice_gara"] = $_POST["codice_gara"];
									$bind[":codice_lotto"] = $_POST["codice_lotto"];

									// Aggiudicazione
									$sql = "SELECT r_partecipanti.codice, SUM(r_punteggi_gare.punteggio) as totale_punteggio FROM r_partecipanti JOIN r_punteggi_gare ON r_partecipanti.codice = ";
									$sql.= " r_punteggi_gare.codice_partecipante WHERE r_punteggi_gare.codice_gara = :codice_gara AND r_punteggi_gare.codice_lotto = :codice_lotto
													 AND r_partecipanti.codice_capogruppo = 0  AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S' AND escluso = 'N' ";
									$sql.= " GROUP BY r_punteggi_gare.codice_partecipante, r_punteggi_gare.codice_gara, r_punteggi_gare.codice_lotto ORDER BY totale_punteggio DESC";
									$ris = $pdo->bindAndExec($sql,$bind);
									$i=0;
									$punteggio=0;
									while ($rec=$ris->fetch(PDO::FETCH_ASSOC)) {

										$bind = array();
										$bind[":codice"] = $rec["codice"];

										if ($punteggio != $rec["totale_punteggio"]) {
											$punteggio = $rec["totale_punteggio"];
											$i++;
										}
										if ($i<=$numero_aggiudicatari) {
											$sql = "UPDATE r_partecipanti SET primo = 'S' WHERE codice = :codice";
											$ris_update = $pdo->bindAndExec($sql,$bind);
											$primi++;
										} else if ($i==2 && $numero_aggiudicatari == 1) {
											$sql = "UPDATE r_partecipanti SET secondo = 'S' WHERE codice = :codice";
											$ris_update = $pdo->bindAndExec($sql,$bind);
											$secondi++;
										} else {
											break;
										}
									}
								} else {
									$errore = true;
									$msg .= "Impossibile procedere al calcolo - Errore 1.\\n";
								}
							} else {
								$errore = true;
								$msg .= "Impossibile procedere al calcolo - Errore 2.\\n";
							}
						}
						if ($primi>$numero_aggiudicatari) $msg.= "Attenzione - Ex aequo su aggiudicazione\\n";
						if ($secondi>1) $msg.= "Attenzione - Ex aequo su secondi classificati\\n";
				}
			} else {
				$errore = true;
				$msg .= "Impossibile procedere al calcolo\\nNumero minimo di offerte per l'aggiudicazione non inserito.\\n";
			}
		} else {
			$errore = true;
			$msg .= "Verificare che vi siano partecipanti ammessi alla gara.\\n";
		}
	}
?>
