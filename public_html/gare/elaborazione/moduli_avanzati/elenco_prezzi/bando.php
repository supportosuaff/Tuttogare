<?
$bind = array();
$bind[":codice"] = $record_gara["codice"];
$strsql = "SELECT tipo FROM b_elenco_prezzi WHERE codice_gara = :codice GROUP BY tipo ";
$ris_elenco_prezzi = $pdo->bindAndExec($strsql,$bind);
if ($ris_elenco_prezzi->rowCount()>0) {
	if ($ris_elenco_prezzi->rowCount()>1) {
		$html .= "l’aggiudicazione, con le precisazioni che seguono, avviene con il criterio del prezzo più basso espresso dal ribasso percentuale, applicato con le modalità, alle condizioni e con i limiti previsti qui richiamati espressamente; il ribasso percentuale è offerto mediante offerta di prezzi unitari, ai sensi dell’articolo 82, comma 3, del decreto legislativo n. 163 del 2006 e dell’articolo 119 del d.P.R. n. 207 del 2010;";
	} else {
		if ($tipo_contratto = $ris_elenco_prezzi->fetch(PDO::FETCH_ASSOC)) {
			if ($tipo_contratto["tipo"] == "corpo") {
					$html .= "l’aggiudicazione, con le precisazioni che seguono, avviene con il criterio del prezzo più basso espresso dal ribasso percentuale, applicato con le modalità, alle condizioni e con i limiti qui richiamati espressamente; il ribasso percentuale è offerto mediante offerta di prezzi unitari, ai sensi dell’articolo 82, comma 2, lettera a), seconda fattispecie, del decreto legislativo n. 163 del 2006 e dell’articolo 119 del d.P.R. n. 207 del 2010;";
			} else {
					$html .= "l’aggiudicazione, con le precisazioni che seguono, avviene con il criterio del prezzo più basso espresso dal ribasso percentuale, applicato con le modalità, alle condizioni e con i limiti previsti qui richiamati espressamente; il ribasso percentuale è offerto mediante offerta di prezzi unitari, ai sensi dell’articolo 82, comma 2, lettera a), seconda fattispecie, del decreto legislativo n. 163 del 2006;";
			}
		}
	}
} else {
	$strsql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione = 57";
	$ris_clausola = $pdo->bindAndExec($strsql,$bind);
	if ($ris_clausola->rowCount() > 0) {
		$html .= "l’aggiudicazione, con le precisazioni che seguono, avviene con il criterio del prezzo più basso espresso dal ribasso percentuale, applicato con le modalità, alle condizioni e con i limiti previsti qui richiamati espressamente; il ribasso percentuale è offerto sull'importo posto a base di gara, ai sensi dell’articolo 82, comma 2, lettera b), prima fattispecie, del decreto legislativo n. 163 del 2006 e dell’articolo 118 del d.P.R. n. 207 del 2010;";
	}
	$strsql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione = 58";
	$ris_clausola = $pdo->bindAndExec($strsql,$bind);
	if ($ris_clausola->rowCount() > 0) {
					$html .= "l’aggiudicazione, con le precisazioni che seguono, avviene con il criterio del prezzo più basso espresso dal ribasso percentuale, applicato con le modalità, alle condizioni e con i limiti previsti qui richiamati espressamente; il ribasso percentuale è offerto sull’elenco prezzi posto a base di gara, ai sensi dell’articolo 82, comma 2, lettera a), prima fattispecie, del decreto legislativo n. 163 del 2006;";
	}
	$strsql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice AND opzione = 155";
	$ris_clausola = $pdo->bindAndExec($strsql,$bind);
	if ($ris_clausola->rowCount() > 0) {
					$html .= "l’aggiudicazione, con le precisazioni che seguono, avviene con il criterio del prezzo più basso espresso dal ribasso percentuale, applicato con le modalità, alle condizioni e con i limiti previsti qui richiamati espressamente; il ribasso percentuale è offerto sull'importo posto a base di gara, ai sensi dell’articolo 82, comma 2, lettera b), prima fattispecie, del decreto legislativo n. 163 del 2006 e dell’articolo 118 del d.P.R. n. 207 del 2010;";
	}
}

?>
