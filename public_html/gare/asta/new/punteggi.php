<?
  if (isset($plain_offer)) {
    $totali = [];
		foreach($criteri AS $infoCriterio) {
			$criterio = $infoCriterio["criterio"];
      $other = $infoCriterio["other"];
			$tipo_off = $infoCriterio["tipo"];
			if ($plain_offer) {
				$punteggi = $other;
			} else {
				$punteggi = getPunteggiCriterio($record_gara["codice"],$codice_lotto,$criterio["codice"],$other);
			}
			if (!isset($totali[$criterio["punteggio_riferimento"]])) $totali[$criterio["punteggio_riferimento"]] = [];
			foreach ($punteggi AS $codice_partecipante => $punteggio) {
				if (!isset($totali[$criterio["punteggio_riferimento"]][$codice_partecipante])) $totali[$criterio["punteggio_riferimento"]][$codice_partecipante] = 0;
        if ($codice_partecipante == $partecipante["codice"]) {
          if ($tipo_off != "elenco_prezzi") {
            $punteggio = $inputs[$tipo_off][$criterio["codice"]];
          } else {
            $punteggio = $inputs_totali[$criterio["codice"]];
          }
        }
        if ($criterio["valutazione"]== "E" && $plain_offer && isset($base_gara)) {
          $punteggio = ($base_gara - $punteggio)/$base_gara * 100;
          $punteggio = truncate($punteggio,$criterio["decimali"]);
        }
				$totali[$criterio["punteggio_riferimento"]][$codice_partecipante] += $punteggio;
			}
		}
    $sql_existing = "SELECT SUM(b_punteggi_criteri.punteggio) AS totale_punteggio
										 FROM b_punteggi_criteri
										 JOIN b_valutazione_tecnica ON b_punteggi_criteri.codice_criterio = b_valutazione_tecnica.codice
										 WHERE b_punteggi_criteri.codice_partecipante = :codice_partecipante
										 AND b_valutazione_tecnica.punteggio_riferimento = :punteggio_riferimento
										 AND (
											 (b_valutazione_tecnica.tipo = 'N' AND b_valutazione_tecnica.valutazione = '')
											 OR b_valutazione_tecnica.tipo = 'Q'
										 )
										 GROUP BY b_punteggi_criteri.codice_partecipante ";
		$ris_existing = $pdo->prepare($sql_existing);

		$update_r = "UPDATE r_punteggi_gare SET punteggio = :punteggio
								 WHERE codice_punteggio = :punteggio_riferimento
                 AND codice_partecipante = :codice_partecipante ";
		$ris_update = $pdo->prepare($update_r);

		foreach($totali AS $punteggio_riferimento => $partecipanti) {
			$ris_existing->bindValue(":punteggio_riferimento",$punteggio_riferimento);
			$ris_update->bindValue(":punteggio_riferimento",$punteggio_riferimento);
			foreach($partecipanti AS $codice_partecipante => $totale_punteggio) {
				$ris_existing->bindValue(":codice_partecipante",$codice_partecipante);
				$ris_existing->execute();
				if ($ris_existing->rowCount() > 0) $totale_punteggio += $ris_existing->fetch(PDO::FETCH_ASSOC)["totale_punteggio"];
				$ris_update->bindValue(":codice_partecipante",$codice_partecipante);
				$ris_update->bindValue(":punteggio",$totale_punteggio);
				$ris_update->execute();
			}
		}
  }
?>
