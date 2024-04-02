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

				$comando = $config["bash_folder"].'/estrai.bash \'' . $config["arch_folder"] . "/allegati_dialogo/" . $record_allegato["codice_operatore"] . "/" . addslashes($record_allegato["riferimento"]) . '\' \'' . $config["chunk_folder"] . '/'.$record_allegato["riferimento"].'\'';
				$esito = shell_exec("sh " . $comando . " 2>&1");
				if (trim($esito)=="Verification successful") {
					$type = getTypeAndExtension($config["chunk_folder"] . '/'.$record_allegato["riferimento"]);
					header('Content-Description: File Transfer');
					header('Content-Type: '. $type["type"]);
					header('Content-Disposition: attachment; filename=' . substr($record_allegato["nome_file"],0,strpos($record_allegato["nome_file"], ".")).$type["ext"]);
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					readfile($config["chunk_folder"] . '/'.$record_allegato["riferimento"]);
				} else {
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$type = finfo_file($finfo, $config["arch_folder"] . "/allegati_dialogo/" . $record_allegato["codice_operatore"] . "/" . $record_allegato["riferimento"]);
					if (strpos($type, "pdf")!==false) {
						header('Content-Description: File Transfer');
						header('Content-Type: '. $type);
						header('Content-Disposition: attachment; filename=' . $record_allegato["nome_file"] . ".pdf");
						header('Content-Transfer-Encoding: binary');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Pragma: public');
						readfile($config["arch_folder"] . "/allegati_dialogo/" . $record_allegato["codice_operatore"] . "/" . $record_allegato["riferimento"]);
					} else {
						echo $esito;
						echo "errore";
					}
				}
			} else {
				echo "<h1>".traduci('impossibile accedere')."</h1>";

				}
			} else {

				echo "<h1>".traduci('impossibile accedere')."</h1>";
	}
?>
