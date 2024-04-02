<?
if (isset($codice_offerta) && is_operatore() && isset($criteri)) {
	$plain_offer = false;
	include("new/punteggi.php");
}
?>
