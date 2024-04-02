<?
	if (isset($codice_offerta) && is_operatore() && isset($criteri)) {
		$plain_offer = false;
		if (count($economiche) === 1)	$plain_offer = true;
		include("new/punteggi.php");
	}
?>
