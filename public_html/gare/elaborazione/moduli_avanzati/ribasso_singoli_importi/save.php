<?
$bind_check_ribasso = array(":codice_gara"=>$_POST["codice_gara"]);
$sql_check_ribasso = "SELECT * FROM b_importi_gara WHERE codice_gara = :codice_gara";
$ris_check_ribasso = $pdo->bindAndExec($sql_check_ribasso,$bind_check_ribasso);
if ($ris_check_ribasso->rowCount() > 1) {
	if (isset($in_elaborazione) && $in_elaborazione) {
		if (isset($_POST["ribassoSingoliImporti"])) {
				$update_ribasso = array();
				$update_ribasso["codice"] = $_POST["codice_gara"];
				if ($_POST["ribassoSingoliImporti"] == "1") {
					$update_ribasso["ribassoSingoliImporti"] = 1;
				} else {
					$update_ribasso["ribassoSingoliImporti"] = 0;
				}
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_gare";
				$salva->operazione = "UPDATE";
				$salva->oggetto = $update_ribasso;
				$codice_ribasso = $salva->save();
		}
	}
}
?>
