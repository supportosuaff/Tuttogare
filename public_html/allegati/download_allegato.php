<?
	session_start();
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && isset($_GET["codice"])) {
			$bind = array();
			$bind[":codice"] = $_GET["codice"];
			$strsql = "SELECT b_allegati.* FROM b_allegati ";
			$strsql .= "WHERE b_allegati.codice = :codice ";

			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {

				$record_allegato = $risultato->fetch(PDO::FETCH_ASSOC);
				$access = false;
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
					if ($ris_check->rowCount() > 0) $access = true;

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

				if ($record_allegato["sezione"] == "verifica-art-80") {

					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$sql_check = "SELECT * FROM b_verifiche_art80 WHERE b_verifiche_art80.codice = :codice_gara AND (b_verifiche_art80.codice_ente = :codice_ente OR b_verifiche_art80.codice_gestore = :codice_ente) ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql_check .= " AND (b_verifiche_art80.codice_ente = :codice_utente_ente OR b_verifiche_art80.codice_gestore = :codice_utente_ente) ";
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
					if ($record_allegato["sezione"] == "mercato") $config["arch_folder"] .= "/mercato_elettronico";
					if ($record_allegato["sezione"] == "sda") $config["arch_folder"] .= "/sda";
					if ($record_allegato["sezione"] == "albo") $config["arch_folder"] .= "/albo";
					if ($record_allegato["sezione"] == "concorsi") $config["arch_folder"] .= "/concorsi";
					if ($record_allegato["sezione"] == "dialogo") $config["arch_folder"] .= "/dialogo";
					if ($record_allegato["sezione"] == "esecuzione") $config["arch_folder"] .= "/esecuzione";
					if ($record_allegato["sezione"] == "nso") $config["arch_folder"] .= "/nso";
					if ($record_allegato["sezione"] == "fabbisogno") $config["arch_folder"] .= "/fabbisogno";
					if ($record_allegato["sezione"] == "progetti") $config["arch_folder"] .= "/progetti";
					if ($record_allegato["sezione"] == "documentale") $config["arch_folder"] .= "/documentale";
					if ($record_allegato["sezione"] == "verifica-art-80") $config["arch_folder"] .= "/verifica-art-80";

					$type = getTypeAndExtension($config["arch_folder"] . "/" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["riferimento"]);
					if ($type != false) {
						header('Content-Description: File Transfer');
						header('Content-Type: ' . $type["type"]);
						if (empty($record_allegato["nomefile"])) {
							header('Content-Disposition: attachment; filename=' . str_replace(" ","_",$record_allegato["titolo"] . $type["ext"]));
						} else {
							header('Content-Disposition: attachment; filename=' . str_replace(" ","_",$record_allegato["nomefile"]));
						}
						header('Content-Transfer-Encoding: binary');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Pragma: public');
						readfile($config["arch_folder"] . "/" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["riferimento"]);
						if ($record_allegato["sezione"]=="gara") {
							log_gare($_SESSION["ente"]["codice"],$record_allegato["codice_gara"],"APERTURA","File riservato - /" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["nome_file"],false);
						} else if ($record_allegato["sezione"]=="concorsi") {
							log_concorso($_SESSION["ente"]["codice"],$record_allegato["codice_gara"],"APERTURA","File riservato - /" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["nome_file"],false);
						} else if ($record_allegato["sezione"]=="esecuzione") {
							log_esecuzione($_SESSION["ente"]["codice"],$record_allegato["codice_gara"],"APERTURA","File riservato - /" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["nome_file"],false);
						}

					} else {
						?><h1>Errore nella lettura del file</h1><?
					}
				} else {
					?><h1>Non si dispone dei permessi necessari o il file non esiste #1</h1><?
				}
			} else {
				?><h1>Non si dispone dei permessi necessari o il file non esiste</h1><?
			}
	} else {
		?><h1>Non si dispone dei permessi necessari</h1><?
	}
?>
