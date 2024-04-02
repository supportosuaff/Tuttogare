<?
	$unita = "Giorni";
	if ($record_gara["unita_durata"] == "mm") $unita = "Mesi";
	$html .= $record_gara["durata"] . " " . $unita;
?>
