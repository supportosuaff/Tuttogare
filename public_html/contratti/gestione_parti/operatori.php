<?
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (isset($_GET["term"])) {
		$text = $_GET["term"];
		$condizione = suddivisione_pdo($text,"b_operatori_economici.codice_fiscale_impresa");
		$bind = $condizione["bind"];
		$sql = $condizione["sql"];
		$strsql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice
							 WHERE " . $sql . " ORDER BY b_operatori_economici.ragione_sociale";
		$sth_legale = $pdo->prepare('SELECT * FROM b_rappresentanti WHERE codice_operatore = :codice_operatore LIMIT 0,1');
		$sth_professionista = $pdo->prepare('SELECT * FROM b_utenti WHERE codice = :codice_utente LIMIT 0,1');
		$risultato = $pdo->bindAndExec($strsql,$bind);
	} else if (!empty($codice_utente_gara)) {
		$strsql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice
							 WHERE b_utenti.codice = :codice_utente  ORDER BY b_operatori_economici.ragione_sociale";
		$sth_legale = $pdo->prepare('SELECT * FROM b_rappresentanti WHERE codice_operatore = :codice_operatore LIMIT 0,1');
		$sth_professionista = $pdo->prepare('SELECT * FROM b_utenti WHERE codice = :codice_utente LIMIT 0,1');
		$risultato = $pdo->bindAndExec($strsql,[":codice_utente"=>$codice_utente_gara]);
	}
	if (isset($risultato)) {
		$rec = array();
		if ($risultato->rowCount()>0) {
			while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				$tmp = array(
					"value" => $record["codice_fiscale_impresa"],
					"label" => $record["ragione_sociale"] . " - " . $record["pec"],
					"ragione_sociale" => ucfirst(strtolower(html_entity_decode($record["ragione_sociale"], ENT_QUOTES, 'UTF-8'))),
					"codice_operatore" => $record["codice"],
					"codice_utente" => $record["codice_utente"],
					"sede_partecipante" => $record["indirizzo_legale"],
					"nome_partecipante" => "",
					"cognome_partecipante" => "",
					"data_nascita_partecipante" => "",
					"comune_nascita_partecipante" => "",
					"provincia_nascita_partecipante" => "",
					"cf_partecipante" => "",
					"indirizzo_residenza_partecipante" => "",
					"comune_residenza_partecipante" => "",
					"provincia_residenza_partecipante" => "",
					"ruolo_ore" => ""
				);
				$sth_legale->bindValue(':codice_operatore', $record["codice"]);
				$sth_legale->execute();
				if($sth_legale->rowCount() > 0) {
					$record_operatore = $sth_legale->fetch(PDO::FETCH_ASSOC);
					$tmp["nome_partecipante"] = ucfirst(strtolower(html_entity_decode($record_operatore["nome"], ENT_QUOTES, 'UTF-8')));
					$tmp["cognome_partecipante"] = ucfirst(strtolower(html_entity_decode($record_operatore["cognome"], ENT_QUOTES, 'UTF-8')));
					$tmp["cf_partecipante"] = strtoupper($record_operatore["codice_fiscale"]);
					$tmp["indirizzo_residenza_partecipante"] = ucwords(strtolower(html_entity_decode($record_operatore["indirizzo"], ENT_QUOTES, 'UTF-8')));
					$tmp["comune_residenza_partecipante"] = ucwords(strtolower(html_entity_decode($record_operatore["citta"], ENT_QUOTES, 'UTF-8')));
					$tmp["provincia_residenza_partecipante"] = strtoupper(html_entity_decode($record_operatore["provincia"], ENT_QUOTES, 'UTF-8'));
					$tmp["data_nascita_partecipante"] = dateFromCF($record_operatore["codice_fiscale"]);
					if (!isset($include)) $tmp["data_nascita_partecipante"] = mysql2date($tmp["data_nascita_partecipante"]);
					$tmp["comune_nascita_partecipante"] = cityFromCF($record_operatore["codice_fiscale"]);
				} else {
					$sth_professionista->bindValue(':codice_utente', $record["codice_utente"]);
					$sth_professionista->execute();
					if($sth_professionista->rowCount() > 0) {
						$record_operatore = $sth_professionista->fetch(PDO::FETCH_ASSOC);
						$tmp["nome_partecipante"] = ucfirst(strtolower(html_entity_decode($record_operatore["nome"], ENT_QUOTES, 'UTF-8')));
						$tmp["cognome_partecipante"] = ucfirst(strtolower(html_entity_decode($record_operatore["cognome"], ENT_QUOTES, 'UTF-8')));
						$tmp["cf_partecipante"] = strtoupper($record_operatore["cf"]);
						$tmp["indirizzo_residenza_partecipante"] = ucwords(strtolower(html_entity_decode($record_operatore["indirizzo"], ENT_QUOTES, 'UTF-8')));
						$tmp["comune_residenza_partecipante"] = ucwords(strtolower(html_entity_decode($record_operatore["citta"], ENT_QUOTES, 'UTF-8')));
						$tmp["provincia_residenza_partecipante"] = strtoupper(html_entity_decode($record_operatore["provincia"], ENT_QUOTES, 'UTF-8'));
						$tmp["data_nascita_partecipante"] = dateFromCF($record_operatore["cf"]);
						if (!isset($include)) $tmp["data_nascita_partecipante"] = mysql2date($tmp["data_nascita_partecipante"]);
						$tmp["comune_nascita_partecipante"] = cityFromCF($record_operatore["cf"]);
						$tmp["ruolo_ore"] = "libero professionista";
					}
				}
				$rec[] = $tmp;
			}
		}
		echo json_encode($rec);
	}
?>
