<?
if (isset($in_elaborazione) && $in_elaborazione && $record_gara["nuovaOfferta"] == "N") {
	$bind = array();
	$bind[":codice_gara"] = $_POST["codice_gara"];
	$strsql = "DELETE FROM r_step_valutazione WHERE codice_criterio IN (SELECT codice FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara)";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$strsql = "DELETE FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if (isset($_POST["criterio_valutazione"])) {
		$ids = array();
		foreach ($_POST["criterio_valutazione"] as $record) {
			$record["codice_gara"] = $_POST["codice_gara"];
			if ($record["codice_padre"] != "0" && $record["codice_padre"] != "") {
				 $record["tipo"] = $ids[$record["codice_padre"]]["tipo"];
				 $record["punteggio_riferimento"] = $ids[$record["codice_padre"]]["punteggio_riferimento"];
				 $record["codice_padre"] = $ids[$record["codice_padre"]]["codice"];
			} else {
				$record["codice_padre"] = "0";
			}

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_valutazione_tecnica";
			$salva->operazione = "INSERT";
			$salva->oggetto = $record;
			$codice_criterio = $salva->save();

			$ids[$record["codice"]] = array();
			$ids[$record["codice"]]["codice"] = $codice_criterio;
			$ids[$record["codice"]]["tipo"] = $record["tipo"];
			$ids[$record["codice"]]["punteggio_riferimento"] = $record["punteggio_riferimento"];
			if (isset($record["valutazione"])) $ids[$record["codice"]]["valutazione"] = $record["valutazione"];
		}
		if (isset($_POST["step_valutazione"])) {
			foreach ($_POST["step_valutazione"] as $step) {
				if (isset($ids[$step["codice_criterio"]]["valutazione"]) && $ids[$step["codice_criterio"]]["valutazione"] == "S") {
					$step["codice_criterio"] = $ids[$step["codice_criterio"]]["codice"];
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "r_step_valutazione";
					$salva->operazione = "INSERT";
					$salva->oggetto = $step;
					$codice_step = $salva->save();
				}
			}
		}
	}
}
?>
