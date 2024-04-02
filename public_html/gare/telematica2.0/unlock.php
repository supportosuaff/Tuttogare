<?
	session_start();
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (is_operatore()) {
		if (!empty($_POST["codice_gara"]) && isset($_POST["codice_lotto"]) && !empty($_POST["codice_busta"]) && !empty($_POST["salt"])) {

			$bind = array();
			$bind[":codice"] = $_POST["codice_gara"];
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql  = "SELECT b_gare.* FROM b_gare WHERE b_gare.codice = :codice AND b_gare.oe_open = 'S' AND codice_gestore = :codice_ente AND (pubblica = '2' OR pubblica = '1') ";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":codice_gara"] = $record_gara["codice"];
				$bind[":codice_lotto"] = $_POST["codice_lotto"];
				$bind[":codice_busta"] = $_POST["codice_busta"];
				$strsql  = "SELECT * FROM b_date_apertura
										WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_busta = :codice_busta
										ORDER BY codice DESC LIMIT 0,1";
				$check_apertura = $pdo->bindAndExec($strsql,$bind);
				if ($check_apertura->rowCount()>0) {
					$record_data = $check_apertura->fetch(PDO::FETCH_ASSOC);
					$time = strtotime($record_data["data_apertura"]);
					if ($time <= time()) {
						$bind = array();
						$bind[":codice_gara"] = $record_gara["codice"];
						$bind[":codice_lotto"] = $_POST["codice_lotto"];
						$bind[":codice_busta"] = $_POST["codice_busta"];
						$bind[":codice_utente"] = $_SESSION["codice_utente"];

						$strsql  = "SELECT b_buste.*, b_criteri_buste.nome FROM b_buste JOIN r_partecipanti ON b_buste.codice_partecipante = r_partecipanti.codice
												JOIN b_operatori_economici ON r_partecipanti.codice_operatore = b_operatori_economici.codice
												JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice
												WHERE b_buste.codice_gara = :codice_gara AND b_buste.codice_lotto = :codice_lotto AND b_operatori_economici.codice_utente = :codice_utente
												AND b_buste.codice_busta = :codice_busta ORDER BY b_buste.codice DESC";
						$risultato = $pdo->bindAndExec($strsql,$bind);
						if ($risultato->rowCount() > 0) {
							$busta = $risultato->fetch(PDO::FETCH_ASSOC);
							if ($busta["aperto"]=="N") {
								$enc_data = file_get_contents($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"]);
								$data = openssl_decrypt($enc_data,$config["crypt_alg"],$_POST["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
								if ($data !== false) {
									$bind = array();
									$bind[":codice_gara"] = $busta["codice_gara"];
									$bind[":codice_partecipante"] = $busta["codice_partecipante"];

									$sql = "SELECT b_utenti.*,b_operatori_economici.codice_fiscale_impresa FROM b_utenti JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
									$sql.= "JOIN r_partecipanti ON b_utenti.codice = r_partecipanti.codice_utente ";
									$sql.= "WHERE r_partecipanti.codice = :codice_partecipante AND r_partecipanti.codice_gara = :codice_gara AND r_partecipanti.ammesso = 'S' AND r_partecipanti.escluso = 'N'";
									$ris = $pdo->bindAndExec($sql,$bind);
									if ($ris->rowCount()>0) {
										$utente = $ris->fetch(PDO::FETCH_ASSOC);
										if (!is_dir($config["arch_folder"]."/".$busta["codice_gara"]."/".$busta["codice_lotto"]."/".$busta["nome"])) mkdir($config["arch_folder"]."/".$busta["codice_gara"]."/".$busta["codice_lotto"]."/".$busta["nome"],0777,true);
										$percorso = $config["arch_folder"]."/".$busta["codice_gara"]."/".$busta["codice_lotto"]."/".$busta["nome"];

										$file_info = new finfo(FILEINFO_MIME_TYPE);
								    $mime_type = $file_info->buffer($data);
										$estensione = "p7m";
										if (strpos($mime_type, "pdf")!==false) $estensione = "pdf";
										$riferimento = $utente["codice_fiscale_impresa"]."-".getRealNameFromData($data);
										file_put_contents($percorso."/".$riferimento,$data);

										$md5_file = md5($data);

										$allegato = array();
										$allegato["codice_gara"] = $busta["codice_gara"];
										$allegato["codice_ente"] = $_SESSION["ente"]["codice"];
										$allegato["cartella"] = $busta["codice_lotto"]."/".$busta["nome"];
										$allegato["nome_file"] = $utente["codice_fiscale_impresa"].".".$estensione;
										$allegato["riferimento"] = $riferimento;
										$allegato["titolo"] = $utente["codice_fiscale_impresa"];
										$allegato["online"] = "N";

										$salva = new salva();
										$salva->debug = false;
										$salva->codop = $_SESSION["codice_utente"];
										$salva->nome_tabella = "b_allegati";
										$salva->operazione = "INSERT";
										$salva->oggetto = $allegato;
										$codice_allegato = $salva->save();

										$bind = array();
										$bind[":codice_allegato"] = $codice_allegato;
										$bind[":codice"] = $busta["codice"];

										$sql = "UPDATE b_buste SET aperto = 'S', codice_allegato = :codice_allegato WHERE codice = :codice";
										$ris = $pdo->bindAndExec($sql,$bind);


										$log = array();
										$log["esito"] = "Positivo";
										$log["ip"] = get_client_ip();
										$log["codice_gara"] = $record_gara["codice"];
										$log["codice_partecipante"] = $busta["codice_partecipante"];
										$log["codice_busta"] = $_POST["codice_busta"];

										$salva = new salva();
										$salva->debug = false;
										$salva->codop = $_SESSION["codice_utente"];
										$salva->nome_tabella = "b_log_aperture";
										$salva->operazione = "INSERT";
										$salva->oggetto = $log;
										$salva->save();
										?>
										alert("Operazione effettuata con successo");
										window.location.reload();
										<?
									} else {
										$error = "OE non riconosciuto";
									}
								} else {
									$error = "Chiave personalizzata errata";
								}
							} else {
								$error = "Busta aperta";
							}
						} else {
							$error = "Busta non trovata o privilegi insufficienti";
						}
					} else {
						$error = "Non è ancora possibile procedere";
					}
				} else {
					$error = "Data di apertura non impostata";
				}
			} else {
				$error = "Gara non trovata o privilegi insufficienti 3";
			}
		} else {
			$error = "Gara non trovata o privilegi insufficienti 2";
		}
	} else {
		$error = "Gara non trovata o privilegi insufficienti 1";
	}
	if (!empty($error)) {
		echo "alert('{$error}'); window.location.reload();";
	}
?>
