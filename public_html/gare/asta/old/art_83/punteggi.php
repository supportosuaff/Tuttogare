<?
if (isset($codice_offerta) && is_operatore()) {

				$punteggio_economico = 0;
				$punteggio_temporale = 0;
				$punteggio_migliorativo = 0;

				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];

				$sql = "SELECT * FROM b_criteri_punteggi WHERE economica = 'S' AND migliorativa = 'N' AND eliminato = 'N' AND codice_criterio = :codice_criterio";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount()>0) {
					$codice_economica = $ris->fetch(PDO::FETCH_ASSOC);
					$codice_economica = $codice_economica["codice"];

					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_economica"] = $codice_economica;

					$strsql = "SELECT sum(punteggio) AS punteggio FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND punteggio_riferimento = :codice_economica";
					$ris = $pdo->bindAndExec($strsql,$bind);
					if ($ris->rowCount()>0) {
						$punteggio = $ris->fetch(PDO::FETCH_ASSOC);
						$punteggio_economico = $punteggio["punteggio"];
					}
				}
				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];
				$sql = "SELECT * FROM b_criteri_punteggi WHERE economica = 'S' AND migliorativa = 'S' AND eliminato = 'N' AND codice_criterio = :codice_criterio";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount()>0) {
					$codice_migliorativa = $ris->fetch(PDO::FETCH_ASSOC);
					$codice_migliorativa = $codice_migliorativa["codice"];

					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_migliorativa"] = $codice_migliorativa;

					$strsql = "SELECT sum(punteggio) AS punteggio FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND punteggio_riferimento = :codice_migliorativa";
					$ris = $pdo->bindAndExec($strsql,$bind);
					if ($ris->rowCount()>0) {
						$punteggio = $ris->fetch(PDO::FETCH_ASSOC);
						$punteggio_migliorativo = $punteggio["punteggio"];
					}
				}

				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];

				$sql = "SELECT * FROM b_criteri_punteggi WHERE temporale = 'S' AND eliminato = 'N' AND codice_criterio = :codice_criterio";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount()>0) {
					$codice_temporale = $ris->fetch(PDO::FETCH_ASSOC);
					$codice_temporale = $codice_temporale["codice"];

					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_temporale"] = $codice_temporale;

					$strsql = "SELECT sum(punteggio) AS punteggio FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND punteggio_riferimento = :codice_temporale";
					$ris = $pdo->bindAndExec($strsql,$bind);
					if ($ris->rowCount()>0) {
						$punteggio = $ris->fetch(PDO::FETCH_ASSOC);
						$punteggio_temporale = $punteggio["punteggio"];
					}
				}

				$offerte_economica = array();
				$offerte_migliorativa = array();
				$offerte_temporale = array();
				$offerte_tecnica = array();

				$bind = array();
				$bind[":codice_gara"] = $record_gara["codice"];
				$bind[":codice_lotto"] = $codice_lotto;

				$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_lotto = :codice_lotto AND codice_gara = :codice_gara AND ammesso = 'S' AND escluso = 'N'";
				$ris_elenco_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
				if ($ris_elenco_partecipanti->rowCount() > 0) {
					$costi = 0;

					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];

					$sql_tipo = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 58";
					$ris_tipo = $pdo->bindAndExec($sql_tipo,$bind);
					$elenco_prezzi = false;
					if ($ris_tipo->rowCount() > 0) {

						$bind = array();
						$bind[":codice_gara"] = $record_gara["codice"];
						$bind[":codice_lotto"] = $codice_lotto;

						$sql_elenco = "SELECT * FROM b_elenco_prezzi WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto ORDER BY tipo ";
						$ris_elenco = $pdo->bindAndExec($sql_elenco,$bind);
						if ($ris_elenco->rowCount()>0) $elenco_prezzi = true;
					}
					if ($elenco_prezzi) {
						if ($codice_lotto==0) {
							$bind = array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$sql = "SELECT sum(b_importi_gara.importo_base) AS importo_base, ";
							$sql.= " sum(b_importi_gara.importo_oneri_ribasso) AS importo_oneri_ribasso, ";
							$sql.= " sum(b_importi_gara.importo_oneri_no_ribasso) AS importo_oneri_no_ribasso, ";
							$sql.= " sum(b_importi_gara.importo_personale) AS importo_personale ";
							$sql.= " FROM b_importi_gara WHERE codice_gara = :codice_gara";
							$ris_importi = $pdo->bindAndExec($sql,$bind);
							if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
						} else {

							$bind = array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$bind[":codice_lotto"] = $codice_lotto;

							$sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto";
							$ris_importi = $pdo->bindAndExec($sql,$bind);
							if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
						}
						if (isset($importi)) {
							$base_gara = $importi["importo_base"]; // + $importi["importo_oneri_ribasso"] + $importi["importo_personale"];

							$bind = array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$costi += $importi["importo_oneri_no_ribasso"];
							// $costi += $importi["importo_personale"];
						}
					}
					while ($record_partecipante = $ris_elenco_partecipanti->fetch(PDO::FETCH_ASSOC)) {

						$offerte_economica[$record_partecipante["codice"]] = 0;

						$bind = array();
						$bind[":codice_gara"] = $record_gara["codice"];
						$bind[":codice_lotto"] = $codice_lotto;
						$bind[":codice_partecipante"] = $record_partecipante["codice"];

						if ($elenco_prezzi) {
							$sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta JOIN
															b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
															WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante
															AND codice_gara = :codice_gara
															AND codice_lotto = :codice_lotto
															AND tipo = 'economica' AND stato = 1 ORDER BY codice_partecipante";
							$ris_storico = $pdo->bindAndExec($sql_storico,$bind);
							if ($ris_storico->rowCount()>0) {
								while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
									$offerte_economica[$record_partecipante["codice"]] += openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($record_partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
								}
							} else {
								$sql_storico = "SELECT SUM(b_offerte_decriptate.offerta) AS offerta FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
															WHERE b_offerte_decriptate.tipo = 'economica' AND r_partecipanti.codice = :codice_partecipante
															AND r_partecipanti.codice_gara= :codice_gara
															AND r_partecipanti.codice_lotto = :codice_lotto
															GROUP BY r_partecipanti.codice_gara,r_partecipanti.codice_lotto";
								$ris_storico = $pdo->bindAndExec($sql_storico,$bind);
								if ($ris_storico->rowCount()>0) {
									$storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
									$offerte_economica[$record_partecipante["codice"]] = $storico["offerta"];
								}
							}
					} else {
						$sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta JOIN
														b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
														WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante
														AND codice_gara = :codice_gara
														AND codice_lotto = :codice_lotto
														AND b_dettaglio_offerte_asta.codice_dettaglio = 0	AND tipo = 'economica' AND stato = 1";
						$ris_storico = $pdo->bindAndExec($sql_storico,$bind);
						if ($ris_storico->rowCount()>0) {
							while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
								$offerte_economica[$record_partecipante["codice"]] += openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($record_partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
							}
						} else {
							$sql_storico = "SELECT SUM(b_offerte_decriptate.offerta) AS offerta FROM b_offerte_decriptate JOIN
															r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
															WHERE codice_partecipante = :codice_partecipante AND b_offerte_decriptate.tipo = 'economica'
															AND r_partecipanti.codice_gara= :codice_gara
															AND r_partecipanti.codice_lotto = :codice_lotto
															AND b_offerte_decriptate.codice_dettaglio = 0 GROUP BY r_partecipanti.codice_gara,r_partecipanti.codice_lotto";
							$ris_storico = $pdo->bindAndExec($sql_storico,$bind);
							if ($ris_storico->rowCount()>0) {
								$storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
								$offerte_economica[$record_partecipante["codice"]] = $storico["offerta"];
							}
						}
					}
					if ($elenco_prezzi) {
						$offerte_economica[$record_partecipante["codice"]] = $offerte_economica[$record_partecipante["codice"]] - $costi;
						$offerte_economica[$record_partecipante["codice"]] = ($base_gara - $offerte_economica[$record_partecipante["codice"]])/$base_gara * 100;
					}

					$offerte_temporale[$record_partecipante["codice"]] = 0;
					$sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta JOIN
													b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
													WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante
													AND codice_gara = :codice_gara
													AND codice_lotto = :codice_lotto
													AND b_dettaglio_offerte_asta.codice_dettaglio = 0	AND tipo = 'temporale' AND stato = 1";
					$ris_storico = $pdo->bindAndExec($sql_storico,$bind);
					if ($ris_storico->rowCount()>0) {
						while($storico = $ris_storico->fetch(PDO::FETCH_ASSOC)) {
							$offerte_temporale[$record_partecipante["codice"]] += openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($record_partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
						}
					} else {
						$sql_storico = "SELECT SUM(b_offerte_decriptate.offerta) AS offerta FROM b_offerte_decriptate JOIN
														r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
														WHERE codice_partecipante = :codice_partecipante
														AND b_offerte_decriptate.tipo = 'temporale'
														AND r_partecipanti.codice_gara= :codice_gara
														AND r_partecipanti.codice_lotto = :codice_lotto
														AND b_offerte_decriptate.codice_dettaglio = 0 GROUP BY r_partecipanti.codice_gara,r_partecipanti.codice_lotto";
						$ris_storico = $pdo->bindAndExec($sql_storico,$bind);
						if ($ris_storico->rowCount()>0) {
							$storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
							$offerte_temporale[$record_partecipante["codice"]] = $storico["offerta"];
						}
					}

					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];

					$sql_criteri_tecnici = "SELECT * FROM b_valutazione_tecnica WHERE valutazione <> '' AND codice_gara = :codice_gara";
					$ris_criteri_tecnici = $pdo->bindAndExec($sql_criteri_tecnici,$bind);
					if ($ris_criteri_tecnici->rowCount()>0) {
						$elenco_criteri = array();
						$bind = array();
						$bind[":codice_gara"] = $record_gara["codice"];
						$bind[":codice_lotto"] = $codice_lotto;
						$bind[":codice_partecipante"] = $record_partecipante["codice"];
						while($criterio = $ris_criteri_tecnici->fetch(PDO::FETCH_ASSOC)) {
							$bind[":criterio"] = $criterio["codice"];
							$elenco_criteri[] = $criterio;
							$sql_storico = "SELECT b_dettaglio_offerte_asta.offerta, b_dettaglio_offerte_asta.codice_partecipante FROM b_dettaglio_offerte_asta JOIN
															b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
															WHERE b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante
															AND codice_gara = :codice_gara
															AND codice_lotto = :codice_lotto
															AND b_dettaglio_offerte_asta.codice_dettaglio = :criterio
															AND (tipo = 'tecnica' OR tipo = 'migliorativa')  AND stato = 1";
							$ris_storico = $pdo->bindAndExec($sql_storico,$bind);
							if ($ris_storico->rowCount()>0) {
								$storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
								$offerte_tecnica[$criterio["codice"]][$record_partecipante["codice"]] = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($record_partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
							} else {
								$sql_storico = "SELECT b_offerte_decriptate.offerta FROM b_offerte_decriptate JOIN r_partecipanti ON b_offerte_decriptate.codice_partecipante = r_partecipanti.codice
																WHERE codice_partecipante = :codice_partecipante
																AND (b_offerte_decriptate.tipo = 'tecnica' OR b_offerte_decriptate.tipo = 'migliorativa')
																AND r_partecipanti.codice_gara= :codice_gara
																AND r_partecipanti.codice_lotto = :codice_lotto
																AND b_offerte_decriptate.codice_dettaglio = :criterio";
								$ris_storico = $pdo->bindAndExec($sql_storico,$bind);
								if ($ris_storico->rowCount()>0) {
									$storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
									$offerte_tecnica[$criterio["codice"]][$record_partecipante["codice"]] = $storico["offerta"];
								}
							}
						}
					}
				}
			}
			if (isset($elenco_criteri)) {
				foreach ($elenco_criteri as $criterio) {
					$punteggio_max = $criterio["punteggio"];
					switch($criterio["valutazione"]) {
						case "P":
							$max = max($offerte_tecnica[$criterio["codice"]]);
							$chiavi = array_keys($offerte_tecnica[$criterio["codice"]]);
							foreach ($chiavi as $chiave) {
								$punteggio_ottenuto = 0;
								if ($max>0) $punteggio_ottenuto = $offerte_tecnica[$criterio["codice"]][$chiave] * $punteggio_max / $max;
								$bind = array();
								$bind[":punteggio"] = number_format($punteggio_ottenuto,3);
								$bind[":codice_criterio"] = $criterio["codice"];
								$bind[":codice_partecipante"] = $chiave;
								$sql_update = "UPDATE b_punteggi_criteri SET punteggio = :punteggio WHERE codice_criterio = :codice_criterio AND codice_partecipante = :codice_partecipante";
								$ris_update = $pdo->bindAndExec($sql_update,$bind);
							}
						break;
						case "I":
							$min = min($offerte_tecnica[$criterio["codice"]]);
							$chiavi = array_keys($offerte_tecnica[$criterio["codice"]]);
							foreach ($chiavi as $chiave) {
								$punteggio_ottenuto = $min * $punteggio_max / $offerte_tecnica[$criterio["codice"]][$chiave];
								$bind = array();
								$bind[":punteggio"] = number_format($punteggio_ottenuto,3);
								$bind[":codice_criterio"] = $criterio["codice"];
								$bind[":codice_partecipante"] = $chiave;
								$sql_update = "UPDATE b_punteggi_criteri SET punteggio = :punteggio WHERE codice_criterio = :codice_criterio AND codice_partecipante = :codice_partecipante";
								$ris_update = $pdo->bindAndExec($sql_update,$bind);
							}
						break;
						case "S":
							$chiavi = array_keys($offerte_tecnica[$criterio["codice"]]);
							foreach ($chiavi as $chiave) {
								$punteggio_ottenuto = 0;

								$bind = array();
								$bind[":offerta"] = $offerte_tecnica[$criterio["codice"]][$chiave];
								$bind[":codice_criterio"] = $criterio["codice"];

								$sql_step = "SELECT * FROM r_step_valutazione WHERE codice_criterio = :codice_criterio
														 AND minimo <= :offerta
														 AND (massimo > :offerta OR massimo = 0)";
								$ris_step = $pdo->bindAndExec($sql_step,$bind);
								if ($ris_step->rowCount()>0) {
									$rec_step = $ris_step->fetch(PDO::FETCH_ASSOC);
									$punteggio_ottenuto = $rec_step["punteggio"];
								}

								$bind = array();
								$bind[":punteggio"] = number_format($punteggio_ottenuto,3);
								$bind[":codice_criterio"] = $criterio["codice"];
								$bind[":codice_partecipante"] = $chiave;

								$sql_update = "UPDATE b_punteggi_criteri SET punteggio = :punteggio WHERE codice_criterio = :codice_criterio AND codice_partecipante = :codice_partecipante";
								$ris_update = $pdo->bindAndExec($sql_update,$bind);
							}
						break;
					}
				}

				$bind = array();
				$bind[":codice_gara"] = $record_gara["codice"];
				$bind[":codice_lotto"] = $codice_lotto;

				$sql_punteggi_tecnici = "SELECT codice_partecipante, SUM(punteggio) AS punteggio FROM b_punteggi_criteri WHERE codice_gara = :codice_gara
																AND codice_lotto = :codice_lotto
																GROUP BY codice_gara,codice_lotto,codice_partecipante";
				$ris_punteggi_tecnici = $pdo->bindAndExec($sql_punteggi_tecnici,$bind);
				if ($ris_punteggi_tecnici->rowCount()>0) {
					while($punteggi_tecnici=$ris_punteggi_tecnici->fetch(PDO::FETCH_ASSOC)) {
						$bind = array();
						$bind[":codice_criterio"] = $record_gara["criterio"];
						$sql = "SELECT * FROM b_criteri_punteggi WHERE (economica = 'N' OR migliorativa = 'S') AND temporale = 'N' AND eliminato = 'N' AND codice_criterio = :codice_criterio";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {
							$punteggio = $ris->fetch(PDO::FETCH_ASSOC);
							if ($punteggi_tecnici["punteggio"] < 0) $punteggi_tecnici["punteggio"] = 0;
							$bind = array();
							$bind[":punteggio"] = number_format($punteggi_tecnici["punteggio"],3,".","");
							$bind[":codice_punteggio"] = $punteggio["codice"];
							$bind[":codice_partecipante"] = $punteggi_tecnici["codice_partecipante"];
							$sql = "UPDATE r_punteggi_gare SET punteggio = :punteggio WHERE
										codice_punteggio = :codice_punteggio
										AND codice_partecipante = :codice_partecipante";
							$ris = $pdo->bindAndExec($sql,$bind);
						}
					}
				}
			}

			$max_economica = max($offerte_economica);
			$chiavi = array_keys($offerte_economica);
			foreach ($chiavi as $chiave) {
				$punteggio_partecipante = 0;
				if ($max_economica>0) $punteggio_partecipante = $offerte_economica[$chiave] * $punteggio_economico / $max_economica;
				if ($punteggio_partecipante < 0) $punteggio_partecipante = 0;

				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];

				$sql = "SELECT * FROM b_criteri_punteggi WHERE economica = 'S' AND eliminato = 'N' AND codice_criterio = :codice_criterio";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount()>0) {
					$punteggio = $ris->fetch(PDO::FETCH_ASSOC);
					$bind = array();
					$bind[":punteggio"] = number_format($punteggio_partecipante,3,".","");
					$bind[":codice_punteggio"] = $punteggio["codice"];
					$bind[":codice_partecipante"] = $chiave;

					$sql = "UPDATE r_punteggi_gare SET punteggio = :punteggio WHERE codice_punteggio = :codice_punteggio
								AND codice_partecipante = :codice_partecipante";
					$ris = $pdo->bindAndExec($sql,$bind);
				}
			}

			$max_temporale = max($offerte_temporale);
			$chiavi = array_keys($offerte_temporale);
			foreach ($chiavi as $chiave) {
				$punteggio_partecipante = 0;
				if ($max_temporale > 0) $punteggio_partecipante = $offerte_temporale[$chiave] * $punteggio_temporale / $max_temporale;
				if ($punteggio_partecipante < 0) $punteggio_partecipante = 0;

				$bind = array();
				$bind[":codice_criterio"] = $record_gara["criterio"];

				$sql = "SELECT * FROM b_criteri_punteggi WHERE temporale = 'S' AND eliminato = 'N' AND codice_criterio = :codice_criterio";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount()>0) {
					$punteggio = $ris->fetch(PDO::FETCH_ASSOC);
					$bind = array();
					$bind[":punteggio"] = number_format($punteggio_partecipante,3,".","");
					$bind[":codice_punteggio"] = $punteggio["codice"];
					$bind[":codice_partecipante"] = $chiave;
					$sql = "UPDATE r_punteggi_gare SET punteggio = :punteggio WHERE codice_punteggio = :codice_punteggio
								AND codice_partecipante = :codice_partecipante";
					$ris = $pdo->bindAndExec($sql,$bind);
				}
			}
		}
?>
