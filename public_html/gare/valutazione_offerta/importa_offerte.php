<?
	include_once("../../../config.php");
	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$strsql = "SELECT * FROM b_gestione_gare WHERE link LIKE '/gare/valutazione_offerta/edit.php%'";
		$risultato = $pdo->query($strsql);
		if ($risultato->rowCount()>0) {
			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
			$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit && !$lock) {
		$punteggi = getPunteggiCriterio($_POST["codice_gara"],$_POST["codice_lotto"],($_POST["economica"]=="S") ? "economica" : "tecnica");
		// INIZIO PUNTEGGI QUALITATIVI
				/*
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$bind[":codice_lotto"] = $_POST["codice_lotto"];
			$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_capogruppo = 0
							AND (
										(ammesso = 'S' AND escluso = 'N') OR
										(r_partecipanti.codice IN (SELECT codice_partecipante FROM b_punteggi_criteri WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto))
									)
							AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
			$ris_r_partecipanti = $pdo->bindAndExec($sql,$bind);
			$n_partecipanti = $ris_r_partecipanti->rowCount();
			if ($n_partecipanti > 0) {
				$sql_criteri = "SELECT b_valutazione_tecnica.*,
												 b_criteri_punteggi.economica,
												 b_criteri_punteggi.temporale,
												 b_criteri_punteggi.migliorativa
									FROM b_valutazione_tecnica
									JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
									WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.tipo = 'Q' AND
												(b_valutazione_tecnica.codice_lotto = :codice_lotto OR b_valutazione_tecnica.codice_lotto = 0)
									AND b_valutazione_tecnica.codice NOT IN
									(SELECT codice_padre FROM b_valutazione_tecnica WHERE codice_padre <> 0 AND codice_gara = :codice_gara)";
				if ($_POST["economica"] == "S") {
					$sql_criteri .= "AND (b_criteri_punteggi.economica = 'S' OR b_criteri_punteggi.temporale = 'S') ";
				} else {
					$sql_criteri .= "AND (b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N') ";
				}
				$ris_criteri = $pdo->bindAndExec($sql_criteri,$bind);
				if ($ris_criteri->rowCount() > 0) {
					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$sql_commissione = "SELECT * FROM b_commissioni WHERE b_commissioni.codice_gara = :codice_gara AND b_commissioni.valutatore = 'S'";
					$ris_commissione = $pdo->bindAndExec($sql_commissione,$bind);
					$n_commissari = $ris_commissione->rowCount();
					$criteri = [];
					while($criterio = $ris_criteri->fetch(PDO::FETCH_ASSOC)) $criteri[$criterio["codice"]] = $criterio;

					$coppie = false;
					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$sql_confronto = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 124";
					$ris_confronto = $pdo->bindAndExec($sql_confronto,$bind);
					if ($ris_confronto->rowCount()>0) $coppie = true;

					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$bind[":codice_lotto"] = $_POST["codice_lotto"];
					if ($coppie) {
						$n_confronti = ((($n_partecipanti * ($n_partecipanti - 1))/2) * $ris_criteri->rowCount());
						$coefficienti = [];
						$sql_valutazioni = "SELECT * FROM b_confronto_coppie WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto ";
						$ris_valutazioni = $pdo->bindAndExec($sql_valutazioni,$bind);
						$n_valutazioni = $ris_valutazioni->rowCount();
						while ($rec_valutazioni = $ris_valutazioni->fetch(PDO::FETCH_ASSOC))
						{
							if ($rec_valutazioni["punteggio_partecipante_1"] == 0 && $rec_valutazioni["punteggio_partecipante_2"] == 0) $n_valutazioni -= 1;
							if (!isset($coefficienti[$rec_valutazioni["codice_criterio"]])) $coefficienti[$rec_valutazioni["codice_criterio"]] = [];
							if (!isset($coefficienti[$rec_valutazioni["codice_criterio"]][$rec_valutazioni["codice_commissario"]])) $coefficienti[$rec_valutazioni["codice_criterio"]][$rec_valutazioni["codice_commissario"]] = [];
							if (!isset($coefficienti[$rec_valutazioni["codice_criterio"]][$rec_valutazioni["codice_commissario"]][$rec_valutazioni["codice_partecipante_1"]])) $coefficienti[$rec_valutazioni["codice_criterio"]][$rec_valutazioni["codice_commissario"]][$rec_valutazioni["codice_partecipante_1"]] = 0;
							if (!isset($coefficienti[$rec_valutazioni["codice_criterio"]][$rec_valutazioni["codice_commissario"]][$rec_valutazioni["codice_partecipante_2"]])) $coefficienti[$rec_valutazioni["codice_criterio"]][$rec_valutazioni["codice_commissario"]][$rec_valutazioni["codice_partecipante_2"]] = 0;
							$coefficienti[$rec_valutazioni["codice_criterio"]][$rec_valutazioni["codice_commissario"]][$rec_valutazioni["codice_partecipante_1"]] += $rec_valutazioni["punteggio_partecipante_1"];
							$coefficienti[$rec_valutazioni["codice_criterio"]][$rec_valutazioni["codice_commissario"]][$rec_valutazioni["codice_partecipante_2"]] += $rec_valutazioni["punteggio_partecipante_2"];
						}
						if ($n_valutazioni == $n_confronti * $n_commissari) {
							$sql_value = "SELECT * FROM b_coefficienti_commissari
														WHERE codice_partecipante = :codice_partecipante
														AND codice_criterio = :codice_criterio
														AND codice_commissario = :codice_commissario ";
							$sql_insert = "INSERT INTO b_coefficienti_commissari (codice_gara,codice_lotto,codice_partecipante,
																																	 codice_criterio,codice_commissario,coefficiente)
														 VALUES (:codice_gara,:codice_lotto,:codice_partecipante,:codice_criterio,:codice_commissario,:coefficiente)";
							$sql_update = "UPDATE b_coefficienti_commissari SET
																		coefficiente = :coefficiente
																		WHERE codice = :codice";
							$ris_value = $pdo->prepare($sql_value);
							$ris_insert = $pdo->prepare($sql_insert);
							$ris_update = $pdo->prepare($sql_update);
							$ris_insert->bindValue(":codice_gara",$_POST["codice_gara"]);
							$ris_insert->bindValue(":codice_lotto",$_POST["codice_lotto"]);
							foreach($coefficienti AS $codice_criterio => $valutazioni) {
								$ris_value->bindValue(":codice_criterio",$codice_criterio);
								$ris_insert->bindValue(":codice_criterio",$codice_criterio);
								foreach($valutazioni AS $codice_commissario => $partecipanti) {
									$ris_value->bindValue(":codice_commissario",$codice_commissario);
									$ris_insert->bindValue(":codice_commissario",$codice_commissario);
									$partecipanti = normalizza($partecipanti,1,$criteri[$codice_criterio]["decimali"]);
									foreach($partecipanti AS $codice_partecipante => $valutazione) {
										$ris_value->bindValue(":codice_partecipante",$codice_partecipante);
										$ris_value->execute();
										if ($ris_value->rowCount() > 0) {
											$codice = $ris_value->fetch(PDO::FETCH_ASSOC)["codice"];
											$ris_update->bindValue(":coefficiente",$valutazione);
											$ris_update->bindValue(":codice",$codice);
											$ris_update->execute();
										} else {
											$ris_insert->bindValue(":codice_partecipante",$codice_partecipante);
											$ris_insert->bindValue(":coefficiente",$valutazione);
											$ris_insert->execute();
										}
									}
								}
							}
						}
					}
					$n_confronti = $ris_criteri->rowCount() * $n_partecipanti;
					$sql_value = "SELECT b_coefficienti_commissari.coefficiente, b_coefficienti_commissari.codice_partecipante, b_coefficienti_commissari.codice_criterio
												FROM b_coefficienti_commissari
												WHERE b_coefficienti_commissari.codice_gara = :codice_gara
												AND b_coefficienti_commissari.codice_lotto = :codice_lotto";
					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$bind[":codice_lotto"] = $_POST["codice_lotto"];
					$ris_values = $pdo->bindAndExec($sql_value,$bind);
					$medie = $totali = [];
					if ($ris_values->rowCount() > 0) {
						while($coef = $ris_values->fetch(PDO::FETCH_ASSOC)) {
							if (!isset($totali[$coef["codice_criterio"]])) $totali[$coef["codice_criterio"]] = [];
							if (!isset($totali[$coef["codice_criterio"]][$coef["codice_partecipante"]])) $totali[$coef["codice_criterio"]][$coef["codice_partecipante"]] = 0;
							$totali[$coef["codice_criterio"]][$coef["codice_partecipante"]] += $coef["coefficiente"];
						}
						foreach($totali AS $cod_criterio => $coef_part) {
							foreach($coef_part AS $cod_partecipante => $somma) {
								$medie[] = ["codice_criterio"=>$cod_criterio,"codice_partecipante"=>$cod_partecipante,"media"=>$somma/$n_commissari];
							}
						}
					}
					if (count($medie) == $n_confronti) {
						$tmp_punt = [];
						foreach($medie AS $media) {
							$criterio = $criteri[$media["codice_criterio"]];
							if (!isset($tmp_punt[$media["codice_criterio"]])) $tmp_punt[$media["codice_criterio"]] = [];
							$tmp_punt[$media["codice_criterio"]][$media["codice_partecipante"]] = $media["media"];
						}
						foreach($tmp_punt AS $codice_criterio => $punt) {
							if ($_POST["riparametraMedie"] == "S") $punt = normalizza($punt,1,999);
							foreach($punt AS $codice_partecipante => $coef) {
								$punteggi[$codice_criterio][$codice_partecipante] = truncate($coef * $criteri[$codice_criterio]["punteggio"],$criteri[$codice_criterio]["decimali"]);
							}
						}
					}
				}
			}
			*/
		// FINE PUNTEGGI QUALITATIVI
		if (count($punteggi) > 0) {
			foreach($punteggi AS $codice_criterio => $punteggio) {
				foreach($punteggio AS $codice_partecipante => $punteggio_ottenuto) {
				?>
					$('#inputValutazione_<?= $codice_criterio ?>_<?= $codice_partecipante ?>').val('<?= $punteggio_ottenuto ?>');
				<?
				}
			}
		}
	}

?>
