<?
if (isset($in_elaborazione) && $in_elaborazione) {
	if (isset($_POST["organismo"])) {
		$bind=array();
		$bind[":codice_ente"] = $record_gara["codice_ente"];
		$sql_organismo = "SELECT * FROM b_organismi_ricorso WHERE codice_ente = :codice_ente";
		$ris_organismo = $pdo->bindAndExec($sql_organismo,$bind);
		$organismo_operazione = "INSERT";
		$_POST["organismo"]["codice_ente"] = $record_gara["codice_ente"];
		if ($ris_organismo->rowCount() > 0) {
			$organismo_operazione = "UPDATE";
			$record_organismo = $ris_organismo->fetch(PDO::FETCH_ASSOC);
			$_POST["organismo"]["codice"]  = $record_organismo["codice"];
		}
		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_organismi_ricorso";
		$salva->operazione = $organismo_operazione;
		$salva->oggetto = $_POST["organismo"];
		$codice_organismo = $salva->save();
	}
}
?>
