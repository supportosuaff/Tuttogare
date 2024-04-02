<?
	$unita = "Giorni";
	if ($record_gara["unita_durata"] == "mm") $unita = "Mesi";
	$html .= "<strong>Durata dell'appalto: </strong> " . $record_gara["durata"] . " " . $unita;
?>
