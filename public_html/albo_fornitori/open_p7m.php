<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && isset($_GET["codice"])) {
			$bind = array(":codice"=>$_GET["codice"]);
			$strsql = "SELECT b_allegati_albo.* FROM b_allegati_albo ";
			$strsql .= "WHERE b_allegati_albo.codice = :codice ";
			if (is_operatore()) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql .= " AND b_allegati_albo.utente_modifica = :codice_utente";
			}
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_allegato = $risultato->fetch(PDO::FETCH_ASSOC);
				$file_path = $config["arch_folder"] . "/allegati_albo/" . $record_allegato["codice_operatore"] . "/" . $record_allegato["riferimento"];
				if (file_exists($file_path) && !is_dir($file_path)) {
					ini_set('max_execution_time', 600);
					ini_set('memory_limit', '-1');
					$base64Check = file_get_contents($file_path);
					$base64Check = base64_decode($base64Check,true);
					if (!empty($base64Check)) {
						$file_path = $config["chunk_folder"] . '/'.$record_allegato["riferimento"];
						file_put_contents($file_path,$base64Check);						
					}
					unset($base64Check);
					$comando = $config["bash_folder"].'/estrai.bash \'' . $file_path . '\' \'' . $config["chunk_folder"] . '/'.$record_allegato["riferimento"].'\'';
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
						$type = finfo_file($finfo, $file_path);
						if (strpos($type, "pdf")!==false) {
							header('Content-Description: File Transfer');
							header('Content-Type: '. $type);
							header('Content-Disposition: attachment; filename=' . $record_allegato["nome_file"]);
							header('Content-Transfer-Encoding: binary');
							header('Expires: 0');
							header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
							header('Pragma: public');
							readfile($file_path);
						} else {
							echo $esito;
							echo "errore";
						}
					}
				} else {
					?>
					<h1><?= traduci('impossibile accedere') ?> - ERROR 0</h1>
					<?
				}
			} else {
			?>
      <h1><?= traduci('impossibile accedere') ?> - ERROR 1</h1>
      <?
			}
	} else {
		?>
    <h1><?= traduci('impossibile accedere') ?> - ERROR 2</h1>
    <?
	}
?>
