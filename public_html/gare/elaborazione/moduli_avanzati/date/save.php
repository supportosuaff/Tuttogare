<?
if (isset($in_elaborazione) && $in_elaborazione) {
if (isset($_POST["date"])) {
		$_POST["date"]["codice"] = $_POST["codice_gara"];
		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_gare";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $_POST["date"];
		$codice_data = $salva->save();
}
}
?>
