<?
if (isset($in_elaborazione) && $in_elaborazione) {
	$bind = array();
	$bind[":codice"] = $_POST["codice_gara"];
	$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
	$sql_costo = "DELETE FROM b_costo_documenti WHERE codice_gara = :codice AND codice_ente = :codice_ente";
	$ris_costo = $pdo->bindAndExec($sql_costo,$bind);

	if ($_POST["costo"] == "S" && isset($_POST["costo_documenti"])) {
		$_POST["costo_documenti"]["codice_gara"] = $_POST["codice_gara"];
		$_POST["costo_documenti"]["codice_ente"] = $_SESSION["ente"]["codice"];

		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_costo_documenti";
		$salva->operazione = "INSERT";
		$salva->oggetto = $_POST["costo_documenti"];
		$codice_costo = $salva->save();
	}
}
?>
