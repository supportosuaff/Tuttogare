<?
$bind = array();
$bind[":codice"] = $record_gara["codice"];
$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$sql_costo = "SELECT * FROM b_costo_documenti WHERE codice_gara = :codice AND codice_ente = :codice_ente";
	$ris_costo = $pdo->bindAndExec($sql_costo);
	if ($ris_costo->rowCount()) {
		$record_costo = $ris_costo->fetch(PDO::FETCH_ASSOC);
		$html .= "prezzo in cifre, euro: " . number_format($record_costo["costo"],2,",",".") . "<br><br>";
		$html .= "Condizioni e modalit&agrave; di pagamento:<br><br>";
		if ($record_costo["cc_posta"]) $html .= "Versamento sul c/c postale n. " . $record_costo["cc_posta"];
		if ($record_costo["iban"]) {
			$html .= "<br>oppure sul c/c bancario presso " . $record_costo["banca"] . "<br><br>";
			$html .= "codice IBAN " . $record_costo["iban"] . "<br><br>";
			$html .= "intestato a " . $record_costo["intestazione"] . "<br><br>";
		}
		$html .= "oppure direttamente presso la stazione indicando come causale il numero CIG o la denominazione.<br><br>";
	} else {
		$html .= "<strong>No</strong>";
	}
?>
