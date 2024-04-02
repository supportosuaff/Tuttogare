<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && isset($_GET["codice"])) {
			$bind = array(":codice"=>$_GET["codice"]);
			$strsql = "SELECT b_allegati_dialogo.* FROM b_allegati_dialogo ";
			$strsql .= "WHERE b_allegati_dialogo.codice = :codice ";
			if (is_operatore()) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql .= " AND b_allegati_dialogo.utente_modifica = :codice_utente";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_allegato = $risultato->fetch(PDO::FETCH_ASSOC);
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename=' . $record_allegato["nome_file"]);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				readfile($config["arch_folder"] . "/allegati_dialogo/" . $record_allegato["codice_operatore"] . "/" . $record_allegato["riferimento"]);
			} else {
				echo "<h1>".traduci('impossibile accedere')."</h1>";

				}
			} else {

				echo "<h1>".traduci('impossibile accedere')."</h1>";
	}
?>
