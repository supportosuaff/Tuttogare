<?
	if (isset($codice_offerta) && is_operatore()) {
			$percentuale_offerta = 0;
			$bind = array();
			$bind[":codice_offerta"] = $codice_offerta;
			$strsql = "SELECT b_dettaglio_offerte_asta.* FROM b_dettaglio_offerte_asta WHERE codice_offerta = :codice_offerta ORDER BY tipo ";
					$ris_offerte = $pdo->bindAndExec($strsql,$bind);
					if ($ris_offerte->rowCount()>0) {
						if ($ris_offerte->rowCount()>1) {
							$totale_offerta = 0;
							while($offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC)) {
								$codice_partecipante = $offerta["codice_partecipante"];
								$totale_offerta += openssl_decrypt($offerta["offerta"],$config["crypt_alg"],md5($offerta["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
							}
							if ($codice_lotto==0) {

								$bind = array();
								$bind[":codice_gara"] = $codice_gara;

								$sql = "SELECT sum(b_importi_gara.importo_base) AS importo_base, ";
								$sql.= " sum(b_importi_gara.importo_oneri_ribasso) AS importo_oneri_ribasso, ";
								$sql.= " sum(b_importi_gara.importo_oneri_no_ribasso) AS importo_oneri_no_ribasso, ";
								$sql.= " sum(b_importi_gara.importo_personale) AS importo_personale ";
								$sql.= " FROM b_importi_gara WHERE codice_gara = :codice_gara";
								$ris_importi = $pdo->bindAndExec($sql,$bind);
								if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
							} else {
								$bind = array();
								$bind[":codice_gara"] = $codice_gara;
								$bind[":codice_lotto"] = $codice_lotto;
								$sql = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto";
								$ris_importi = $pdo->bindAndExec($sql,$bind);
								if ($ris_importi->rowCount()>0) $importi = $ris_importi->fetch(PDO::FETCH_ASSOC);
							}
							if (isset($importi)) {
								$base_gara = $importi["importo_base"]; // + $importi["importo_oneri_ribasso"] + $importi["importo_personale"];
								$costi = 0;

								$bind = array();
								$bind[":codice_gara"] = $codice_gara;
								$costi += $importi["importo_oneri_no_ribasso"];
								// $costi += $importi["importo_personale"];
								$totale_offerta = $totale_offerta - $costi;
								if ($totale_offerta > 0) {
									$percentuale_offerta = ($base_gara - $totale_offerta)/$base_gara * 100;
								}
							}
						} else {
							$offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC);
							$codice_partecipante = $offerta["codice_partecipante"];
							$percentuale_offerta = openssl_decrypt($offerta["offerta"],$config["crypt_alg"],md5($offerta["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
						}
						if ($percentuale_offerta < 0) $percentuale_offerta = 0;
						$bind = array();
						$bind[":codice"] = $record_gara["criterio"];
						$sql = "SELECT * FROM b_criteri_punteggi WHERE economica = 'S' AND eliminato = 'N' AND codice_criterio = :codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {
							$punteggio = $ris->fetch(PDO::FETCH_ASSOC);
							$bind = array();
							$bind[":punteggio"] = $percentuale_offerta;
							$bind[":codice_partecipante"] = $codice_partecipante;
							$bind[":codice_punteggio"] = $punteggio["codice"];
							$sql = "UPDATE r_punteggi_gare SET punteggio = :punteggio WHERE codice_punteggio = :codice_punteggio
										 AND codice_partecipante = :codice_partecipante";
							$ris = $pdo->bindAndExec($sql,$bind);
						}
					}
				}
?>
