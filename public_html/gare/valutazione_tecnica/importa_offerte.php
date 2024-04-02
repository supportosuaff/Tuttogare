<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
	  	if ($codice_fase !== false) {
	  		$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
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
			$bind=array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$bind[":codice_lotto"] = $_POST["codice_lotto"];
			$strsql = "SELECT r_partecipanti.codice FROM r_partecipanti ";
			$strsql.= " WHERE r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.codice_lotto = :codice_lotto AND r_partecipanti.codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND r_partecipanti.ammesso = 'S'";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			$risultato = $risultato->fetchAll(PDO::FETCH_ASSOC);
			$numero_partecipanti = count($risultato);
			if ($numero_partecipanti>0) {
				$bind=array();
				$bind[":codice_gara"] = $_POST["codice_gara"];
				$strsql = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.valutazione <> '' AND b_criteri_punteggi.economica = 'N' AND  b_criteri_punteggi.temporale = 'N'";
				$ris = $pdo->bindAndExec($strsql,$bind);
				if ($ris->rowCount()>0) {
					while($punteggio = $ris->fetch(PDO::FETCH_ASSOC)) {
						$punteggio_max = $punteggio["punteggio"];
						$offerte = array();
						foreach ($risultato as $record) {
							$bind=array();
							$bind[":codice"] = $record["codice"];
							$bind[":codice_dettaglio"] = $punteggio["codice"];
							$strsql = "SELECT b_offerte_decriptate.* FROM b_offerte_decriptate WHERE codice_partecipante = :codice AND tipo = 'tecnica' AND codice_dettaglio = :codice_dettaglio ORDER BY b_offerte_decriptate.timestamp DESC";
							$ris_offerte = $pdo->bindAndExec($strsql,$bind);
							if ($ris_offerte->rowCount()>0) {
								$offerta = $ris_offerte->fetch(PDO::FETCH_ASSOC);
								$offerte[$record["codice"]] = $offerta["offerta"];
							}
						}
						switch($punteggio["valutazione"]) {
							case "P":
								$max = max($offerte);
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$punteggio_ottenuto = 0;
									if ($max>0) $punteggio_ottenuto = $offerte[$chiave] * $punteggio_max / $max;
									?>
										$('#inputValutazione_<?= $punteggio["codice"] ?>_<? echo $chiave ?>').val('<? echo number_format($punteggio_ottenuto,3,".",""); ?>');
									<?
								}
							break;
							case "I":
								$min = min($offerte);
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$punteggio_ottenuto = $min * $punteggio_max / $offerte[$chiave];
									?>
										$('#inputValutazione_<?= $punteggio["codice"] ?>_<? echo $chiave ?>').val('<? echo number_format($punteggio_ottenuto,3,".",""); ?>');
									<?
								}
							break;
							case "S":
								$chiavi = array_keys($offerte);
								foreach ($chiavi as $chiave) {
									$bind = array();
									$bind[":codice_criterio"] = $punteggio["codice"];
									$bind[":chiave"] = $offerte[$chiave];
									$offerte[$chiave] = 0;
									$sql_step = "SELECT * FROM r_step_valutazione WHERE codice_criterio = :codice_criterio AND minimo <= :chiave AND (massimo >= :chiave OR massimo = 0)";
									$ris_step = $pdo->bindAndExec($sql_step,$bind);
									if ($ris_step->rowCount()>0) {
										$rec_step = $ris_step->fetch(PDO::FETCH_ASSOC);
										$offerte[$chiave] = $rec_step["punteggio"];
									}
								}
								$max = max($offerte);
								foreach ($chiavi as $chiave) {

									$bind = array();
									$bind[":codice_gara"] = $_POST["codice_gara"];
									$sql_check = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 161";
									$ris_check = $pdo->bindAndExec($sql_check,$bind);
									if ($ris_check->rowCount() > 0) {
										$punteggio_ottenuto = 0;
										if ($max>0) $punteggio_ottenuto = $offerte[$chiave] * $punteggio_max / $max;
									} else {
										$punteggio_ottenuto = $offerte[$chiave];
									}
									?>
										$('#inputValutazione_<?= $punteggio["codice"] ?>_<? echo $chiave ?>').val('<? echo number_format($punteggio_ottenuto,3,".",""); ?>');
									<?
								}
							break;
						}
					}
				}
			} else {
			?>
			 alert("Verificare che vi siano partecipanti ammessi alla gara.");
			<?
		}
	}
?>
