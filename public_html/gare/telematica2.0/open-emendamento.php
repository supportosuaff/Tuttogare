<?
	session_start();
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (is_operatore()) {
		if (!empty($_POST["codice_gara"]) && isset($_POST["codice_lotto"]) && !empty($_POST["busta_originale"]) && !empty($_POST["salt"])) {
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$bind[":codice_lotto"] = $_POST["codice_lotto"];
			$bind[":busta_originale"] = $_POST["busta_originale"];
			$bind[":codice"] = $_POST["codice"];
			$bind[":codice_utente"] = $_SESSION["codice_utente"];
			$strsql  = "SELECT b_emendamenti.* FROM b_emendamenti JOIN r_partecipanti ON b_emendamenti.codice_partecipante = r_partecipanti.codice
									JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice
									WHERE b_emendamenti.codice_gara = :codice_gara AND b_emendamenti.codice_lotto = :codice_lotto AND b_operatori_economici.codice_utente = :codice_utente
									AND b_emendamenti.busta_originale = :busta_originale AND b_emendamenti.codice = :codice ORDER BY b_emendamenti.codice DESC";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$busta = $risultato->fetch(PDO::FETCH_ASSOC);
				if ($busta["aperto"]=="N") {
					$enc_data = file_get_contents($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/emendamenti/" . $busta["nome_file"]);
					$data = openssl_decrypt($enc_data,$config["crypt_alg"],$_POST["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
					if ($data !== false) {
						$estensione = "";
						$tmp_file = $config["chunk_folder"] . "/" . session_id() . ".tmp";
						file_put_contents($tmp_file,$data);
						$type = getTypeAndExtension($tmp_file);
						$estensione =  $type["ext"];
						$type = $type["type"];
				    	unlink($tmp_file);
					}
				} else if ($busta["aperto"]=="S") {
					$bind = array();
					$bind[":codice"] = $busta["codice_allegato"];
					$strsql = "SELECT b_allegati.* FROM b_allegati WHERE b_allegati.codice = :codice ";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$record_allegato = $risultato->fetch(PDO::FETCH_ASSOC);
						$type = getTypeAndExtension($config["arch_folder"] . "/" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["riferimento"]);
						$estensione =  $type["ext"];
						$type = $type["type"];
						$data = file_get_contents($config["arch_folder"] . "/" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["riferimento"]);
					}
				}
				if (!empty($data)) {
					header('Content-Description: File Transfer');
					header('Content-Type: '.$type);
					header('Content-Disposition: attachment; filename=Emendamento'.$estensione);
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					echo $data;
				} else {
					?>
					<h1><?= traduci('impossibile accedere') ?> - ERROR 4</h1>
					<?
				}
			} else {
				?>
				<h1><?= traduci('impossibile accedere') ?> - ERROR 3</h1>
				<?
			}
		} else {
			?>
			<h1><?= traduci('impossibile accedere') ?> - ERROR 2</h1>
			<?
		}
	} else {
		?>
		<h1><?= traduci('impossibile accedere') ?> - ERROR 1</h1>
		<?
	}
?>
