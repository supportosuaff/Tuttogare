<?
if (isset($in_elaborazione) && $in_elaborazione) {
	if (isset($_POST["codice_derivazione"])) {
			$codice_derivazione = array("codice"=>$_POST["codice_gara"],"codice_derivazione"=>$_POST["codice_derivazione"]);

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_gare";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $codice_derivazione;
			$codice_derivazione = $salva->save();
	}
}
?>
