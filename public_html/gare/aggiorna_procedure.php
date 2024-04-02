<?
	session_start();
	if (isset($_POST["codici_tipologia"]) && isset($_SESSION["codice_utente"])) {
		include("../../config.php");
		include_once($root."/inc/funzioni.php");
		$tipologie = explode(";",$_POST["codici_tipologia"]);
		$bind = array();
		$bind[":criterio_a"] = $_POST["criterio"];
		$bind[":criterio_b"] = $_POST["criterio"] . ";%";
		$bind[":criterio_c"] = "%;" . $_POST["criterio"] . ";%";
		$bind[":criterio_d"] = "%;" . $_POST["criterio"];
		$where = "AND (criteri = :criterio_a OR criteri LIKE :criterio_b OR criteri LIKE :criterio_c OR criteri LIKE :criterio_d) AND (";
		$count = 0;
		foreach ($tipologie as $tipologia){
			$count++;
			$bind[":tipologia_".$count."_a"] = $tipologia;
			$bind[":tipologia_".$count."_b"] = $tipologia . ";%";
			$bind[":tipologia_".$count."_c"] = "%;" . $tipologia . ";%";
			$bind[":tipologia_".$count."_d"] = "%;" . $tipologia;
			if ($tipologia != "") $where .= " (tipologie = :tipologia_".$count."_a OR tipologie LIKE :tipologia_".$count."_b OR tipologie LIKE :tipologia_".$count."_c OR tipologie LIKE :tipologia_".$count."_d) AND ";
		}
		$where = substr($where,0,-5);
		$where .= ")";
		if (!empty($_SESSION["record_utente"]["procedureAttive"])) {
			$procedure = explode(",", $_SESSION["record_utente"]["procedureAttive"]);
			$continua = true;
			foreach($procedure AS $procedura) if (!is_numeric($procedura)) $continua = false;
			if ($continua) $where .= " AND codice IN (".$_SESSION["record_utente"]["procedureAttive"].") ";
		}
		$sql = "SELECT * FROM b_procedure WHERE attivo = 'S' AND eliminato = 'N' " . $where . " ORDER BY codice";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) {
			?><option value="">Seleziona...</option><?
			while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
				$echo = true;
				$continua_check = true;
				$bind = array();
				$bind[":procedura"] = $rec["codice"];
				$strsql = "SELECT * FROM b_procedure WHERE mercato_elettronico = 'S' AND codice = :procedura ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$echo = false;
					$continua_check = false;
					$bind_interno = array();
					$bind_interno[":codice_ente"] = $_SESSION["ente"]["codice"];
					$str_check  = "SELECT b_bandi_mercato.codice
											FROM r_partecipanti_me JOIN b_bandi_mercato ON r_partecipanti_me.codice_bando = b_bandi_mercato.codice
											WHERE (b_bandi_mercato.annullata = 'N' AND  b_bandi_mercato.data_scadenza > now()
											AND (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente)
											AND (b_bandi_mercato.pubblica = '2' OR b_bandi_mercato.pubblica = '1')) AND r_partecipanti_me.ammesso = 'S' LIMIT 0,1";
					$ris_check = $pdo->bindAndExec($str_check,$bind_interno);
					if ($ris_check->rowCount() > 0) $echo = true;
				}
				if ($continua_check) {
					$strsql = "SELECT * FROM b_procedure WHERE directory = 'sda' AND codice = :procedura ";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount()>0) {
						$echo = false;
						$continua_check = false;
						$bind_interno = array();
						$bind_interno[":codice_ente"] = $_SESSION["ente"]["codice"];
						$str_check  = "SELECT b_bandi_sda.codice
												FROM r_partecipanti_sda JOIN b_bandi_sda ON r_partecipanti_sda.codice_bando = b_bandi_sda.codice
												WHERE (b_bandi_sda.annullata = 'N' AND  b_bandi_sda.data_scadenza > now()
												AND (b_bandi_sda.codice_ente = :codice_ente OR b_bandi_sda.codice_gestore = :codice_ente)
												AND (b_bandi_sda.pubblica = '2' OR b_bandi_sda.pubblica = '1')) AND r_partecipanti_sda.ammesso = 'S' LIMIT 0,1";
						$ris_check = $pdo->bindAndExec($str_check,$bind_interno);
						if ($ris_check->rowCount() > 0) $echo = true;
					}
				}

				if ($continua_check) {
					$strsql = "SELECT * FROM b_procedure WHERE directory = 'dialogo' AND codice = :procedura ";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount()>0) {
						$echo = false;
						$continua_check = false;
						$bind_interno = array();
						$bind_interno[":codice_ente"] = $_SESSION["ente"]["codice"];
						$str_check  = "SELECT b_bandi_dialogo.codice
												FROM r_partecipanti_dialogo JOIN b_bandi_dialogo ON r_partecipanti_dialogo.codice_bando = b_bandi_dialogo.codice
												WHERE (b_bandi_dialogo.annullata = 'N' AND  b_bandi_dialogo.data_scadenza <= now()
												AND (b_bandi_dialogo.codice_ente = :codice_ente OR b_bandi_dialogo.codice_gestore = :codice_ente)
												AND (b_bandi_dialogo.pubblica = '2' OR b_bandi_dialogo.pubblica = '1')) AND r_partecipanti_dialogo.ammesso = 'S' LIMIT 0,1";
						$ris_check = $pdo->bindAndExec($str_check,$bind_interno);
						if ($ris_check->rowCount() > 0) $echo = true;
					}
				}
				if ($echo) {
					?><option value="<? echo $rec["codice"] ?>"><? echo $rec["nome"] ?></option><?
				}
			}
		}
	}
?>
