<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && isset($_POST["codice"])) {
		ini_set('memory_limit', '1536M');
  	ini_set('max_execution_time', 600);
			$bind = array();
			$bind[":codice"] = $_POST["codice"];
			$strsql = "SELECT b_allegati.* FROM b_allegati ";
			$strsql .= "WHERE b_allegati.codice = :codice";

			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_allegato = $risultato->fetch(PDO::FETCH_ASSOC);
				if (empty($record_allegato["cartella"])) {
					$access = false;
					$sendToSuaff = false;
					$bind = array(":codice_gara"=>$record_allegato["codice_gara"]);
					if ($record_allegato["sezione"] == "gara") {
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql_check = "SELECT * FROM b_gare ";
						if ($_SESSION["gerarchia"] > 1) $sql_check .= "JOIN b_permessi ON b_permessi.codice_gara = b_gare.codice ";
						$sql_check .= "WHERE b_gare.codice = :codice_gara AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND (b_gare.codice_ente = :codice_utente_ente OR b_gare.codice_gestore = :codice_utente_ente) ";
							if ($_SESSION["gerarchia"] > 1) {
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql_check.= " AND b_permessi.codice_utente = :codice_utente";
							}
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) {
							$sendToSuaff = true;
							$access = true;
						}
					}

					if ($record_allegato["sezione"] == "concorsi") {
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql_check = "SELECT * FROM b_concorsi ";
						if ($_SESSION["gerarchia"] > 1) $sql_check .= "JOIN b_permessi_concorsi ON b_permessi_concorsi.codice_gara = b_concorsi.codice ";
						$sql_check .= "WHERE b_concorsi.codice = :codice_gara AND (b_concorsi.codice_ente = :codice_ente OR b_concorsi.codice_gestore = :codice_ente) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND (b_concorsi.codice_ente = :codice_utente_ente OR b_concorsi.codice_gestore = :codice_utente_ente) ";
							if ($_SESSION["gerarchia"] > 1) {
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql_check.= " AND b_permessi_concorsi.codice_utente = :codice_utente";
							}
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) $access = true;
					}

					if ($record_allegato["sezione"] == "mercato") {
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql_check = "SELECT * FROM b_bandi_mercato WHERE b_bandi_mercato.codice = :codice_gara ";
						$sql_check .= "AND (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND (b_bandi_mercato.codice_ente = :codice_utente_ente OR b_bandi_mercato.codice_gestore = :codice_utente_ente) ";
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) $access = true;
					}

					if ($record_allegato["sezione"] == "sda") {
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql_check = "SELECT * FROM b_bandi_sda WHERE b_bandi_sda.codice = :codice_gara ";
						$sql_check .= "AND (b_bandi_sda.codice_ente = :codice_ente OR b_bandi_sda.codice_gestore = :codice_ente) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND (b_bandi_sda.codice_ente = :codice_utente_ente OR b_bandi_sda.codice_gestore = :codice_utente_ente) ";
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) $access = true;
					}

					if ($record_allegato["sezione"] == "albo") {
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql_check = "SELECT * FROM b_bandi_albo WHERE b_bandi_albo.codice = :codice_gara ";
						$sql_check .= "AND (b_bandi_albo.codice_ente = :codice_ente OR b_bandi_albo.codice_gestore = :codice_ente) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND (b_bandi_albo.codice_ente = :codice_utente_ente OR b_bandi_albo.codice_gestore = :codice_utente_ente) ";
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) $access = true;
					}

					if ($record_allegato["sezione"] == "dialogo") {
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql_check = "SELECT * FROM b_bandi_dialogo WHERE b_bandi_dialogo.codice = :codice_gara ";
						$sql_check .= "AND (b_bandi_dialogo.codice_ente = :codice_ente OR b_bandi_dialogo.codice_gestore = :codice_ente) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND (b_bandi_dialogo.codice_ente = :codice_utente_ente OR b_bandi_dialogo.codice_gestore = :codice_utente_ente) ";
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) $access = true;
					}

					if ($record_allegato["sezione"] == "esecuzione") {
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql_check = "SELECT * FROM b_contratti WHERE b_contratti.codice = :codice_gara ";
						$sql_check .= "AND (b_contratti.codice_ente = :codice_ente OR b_contratti.codice_gestore = :codice_ente) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND (b_contratti.codice_ente = :codice_utente_ente OR b_contratti.codice_gestore = :codice_utente_ente) ";
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) $access = true;

					}

					if ($record_allegato["sezione"] == "nso") {
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$sql_check = "SELECT * FROM b_nso WHERE b_nso.codice = :codice_gara ";
						$sql_check .= "AND (b_nso.codice_ente = :codice_ente OR b_nso.codice_gestore = :codice_ente) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND (b_nso.codice_ente = :codice_utente_ente OR b_nso.codice_gestore = :codice_utente_ente) ";
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) $access = true;

					}

					if ($record_allegato["sezione"] == "fabbisogno") {
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

						$sql_check = "SELECT * FROM b_fabbisogno WHERE b_fabbisogno.codice = :codice_gara ";
						$sql_check .= "AND b_fabbisogno.codice_gestore = :codice_ente ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND b_fabbisogno.codice_gestore = :codice_utente_ente ";
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) {
							$access = true;
						} else {
							unset($bind[":codice_ente"]);
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check = "SELECT * FROM r_enti_fabbisogno WHERE codice_ente = :codice_utente_ente AND codice_fabbisogno = :codice_gara";
							$ris_check = $pdo->bindAndExec($sql_check,$bind);
							if ($ris_check->rowCount() > 0) $access = true;
						}
					}

					if ($record_allegato["sezione"] == "progetti") {

						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

						$sql_check = "SELECT * FROM b_progetti_investimento WHERE b_progetti_investimento.codice = :codice_gara ";
						$sql_check .= "AND (b_progetti_investimento.codice_ente = :codice_ente OR b_progetti_investimento.codice_gestore = :codice_ente) ";
						if ($_SESSION["gerarchia"] > 0) {
							$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
							$sql_check .= " AND (b_progetti_investimento.codice_ente = :codice_utente_ente OR b_progetti_investimento.codice_gestore = :codice_utente_ente) ";
						}
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) $access = true;

					}

					if ($access) {


						if ($record_allegato["online"] == "N") {
							$record_allegato["online"] = "S";
							$area = "PUBBLICA";
							$source = $config["arch_folder"];
							$destination = $config["pub_doc_folder"]."/allegati";
						} else {
							$sendToSuaff = false;
							$record_allegato["online"] = "N";
							$area = "RISERVATA";
							$source = $config["pub_doc_folder"]."/allegati";
							$destination = $config["arch_folder"];
						}
						$percorso = "";
						if ($record_allegato["sezione"] == "mercato") $percorso = "/mercato_elettronico";
						if ($record_allegato["sezione"] == "sda") $percorso = "/sda";
						if ($record_allegato["sezione"] == "albo") $percorso = "/albo";
						if ($record_allegato["sezione"] == "dialogo") $percorso = "/dialogo";
						if ($record_allegato["sezione"] == "concorsi") $percorso = "/concorsi";
						if ($record_allegato["sezione"] == "esecuzione") $percorso = "/esecuzione";
						if ($record_allegato["sezione"] == "nso") $percorso = "/nso";
						if ($record_allegato["sezione"] == "fabbisogno") $percorso = "/fabbisogno";
						if ($record_allegato["sezione"] == "progetti") $percorso = "/progetti";
						if ($record_allegato["sezione"] == "documentale") $percorso = "/documentale";

						$percorso .= "/".$record_allegato["codice_gara"]."/";

						if (!is_dir($destination.$percorso)) mkdir($destination.$percorso,0777,true);
						$source_name = $record_allegato["riferimento"];

						$copy = copiafile_chunck($record_allegato["riferimento"],$destination.$percorso,$source.$percorso,"",false);

						$record_allegato["riferimento"] = $copy["nome_fisico"];

						if (file_exists($destination.$percorso.$record_allegato["riferimento"])) {
							unlink($source.$percorso.$source_name);
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_allegati";
							$salva->operazione = "UPDATE";
							$salva->oggetto = $record_allegato;
							$codice_allegato = $salva->save();
							if ($codice_allegato !== false) {
								if (!empty($sendToSuaff)) {
									if (class_exists("syncERP")) {
										$sync = new syncERP();
										$sync->sendAllegato($codice_allegato);
									}
								}
								if ($record_allegato["codice_gara"]!=0 && $record_allegato["sezione"] == "gara") {
									log_gare($_SESSION["ente"]["codice"],$record_allegato["codice_gara"],"SPOSTAMENTO IN AREA {$area}","Allegato - " . $record_allegato["titolo"]);
								} else if ($record_allegato["codice_gara"]!=0 && $record_allegato["sezione"]=="esecuzione") {
									log_esecuzione($_SESSION["ente"]["codice"],$record_allegato["codice_gara"],"SPOSTAMENTO AREA","Allegato - " . $record_allegato["titolo"]);
								}
								?>alert('Operazione eseguita con successo.');
								window.location.href = window.location.href;
								<?
							} else {
								?>
								?>alert('Errore durante il salvataggio.');
								<?
							}
						} else {
							?>
							alert('Errore durante l'operazione.');
							<?
						}
					} else {
					?>
		        alert('Non si dispone dei permessi necessari o il file non esiste #3');
		      <?
					}
				} else {
				?>
	    alert('Non si dispone dei permessi necessari o il file non esiste #2');
	      <?
			}
			} else {
			?>
		alert('Il file non può essere spostato');
			<?
		}
	} else {
			?>
    alert('Non si dispone dei permessi necessari #1');
      <?
	}
?>
