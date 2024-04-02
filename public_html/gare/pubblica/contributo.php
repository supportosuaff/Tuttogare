<?
if (isset($codice_gara)) {
	$bind = array();
	$bind[":codice_gara"] = $codice_gara;
	$sql = "SELECT codice_ente, codice_gestore, prezzoBase FROM b_gare WHERE codice = :codice_gara";
	$ris = $pdo->bindAndExec($sql,$bind);
	if($ris->rowCount()>0){
		$rec = $ris->fetch(PDO::FETCH_ASSOC);
		$codice_ente = $rec["codice_ente"];
		$codice_gestore = $rec["codice_gestore"];
		$contributo = $rec["prezzoBase"];
		$procedura_standard = true;

		if (file_exists($root."/gare/rendicontazione/" . $_SESSION["ente"]["codice"] . "/script.php")) include($root."/gare/rendicontazione/" . $_SESSION["ente"]["codice"] . "/script.php");

		if ($procedura_standard) {
			$calcolo_sua = false;
			$esito = array();
			if ($codice_ente != $codice_gestore) {
				$bind = array();
				$bind[":codice_ente"] = $codice_ente;
				$strsql = "SELECT contributo_sua, tipo_contributo_sua FROM b_enti WHERE contributo_sua > 0 AND codice = :codice_ente";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if($risultato->rowCount()>0){
					$record_contributo = $risultato->fetch(PDO::FETCH_ASSOC);
					$codice_relazione = $codice_ente;
				} else {
					$bind = array();
					$bind[":codice_ente"] = $codice_gestore;
					$strsql = "SELECT contributo_sua, tipo_contributo_sua FROM b_enti WHERE contributo_sua > 0 AND codice = :codice_ente";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if($risultato->rowCount()>0) {
						$record_contributo = $risultato->fetch(PDO::FETCH_ASSOC);
						$codice_relazione = $codice_gestore;
					}
				}
				if (isset($record_contributo)) {
					if ($record_contributo["contributo_sua"]==2) {
						if ($record_contributo["tipo_contributo_sua"]==1) {
							$bind = array();
							$bind[":codice_gara"] = $codice_gara;
							$strsql = "SELECT b_gare.* FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice WHERE b_gare.codice = :codice_gara AND b_modalita.online = 'S'";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount()>0) $calcolo_sua = true;
						} else {
							$calcolo_sua = true;
						}
						if ($calcolo_sua) {
							$bind = array();
							$bind[":contributo"] = $contributo;
							$bind[":codice_relazione"] = $codice_relazione;
							$sql_contributo = "SELECT * FROM r_contributi_sua WHERE minimo <= :contributo AND (massimo > :contributo OR massimo = 0) AND codice_ente = :codice_relazione";
							$ris_contributo = $pdo->bindAndExec($sql_contributo,$bind);
							if($ris_contributo->rowCount()>0){
								$rec_contr = $ris_contributo->fetch(PDO::FETCH_ASSOC);
								if(strcmp($rec_contr["tipo"],"fisso")==0) {
									$esito["contributo_sua"] = (float)number_format($rec_contr["valore"], 2, ".","");
								}	else if (strcmp($rec_contr["tipo"],"percentuale")==0) {
									$esito["contributo_sua"] = (float)number_format($contributo * (float)($rec_contr["valore"]/100), 2, ".","");
								}
							}
						}
					}
				}
			}
		}

		$calcolo_gestore = false;
		$bind = array();
		$bind[":codice_ente"] = $codice_gestore;
		$strsql = "SELECT contributo_gestore, tipo_contributo_gestore FROM b_enti WHERE codice = :codice_ente";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if($risultato->rowCount()>0){
			if ($record_contributo = $risultato->fetch(PDO::FETCH_ASSOC)) {
				if ($record_contributo["contributo_gestore"]==2) {
					if ($record_contributo["tipo_contributo_gestore"]==1) {
						$bind = array();
						$bind[":codice_gara"] = $codice_gara;
						$strsql = "SELECT b_gare.* FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice WHERE b_gare.codice = :codice_gara AND b_modalita.online = 'S'";
						$risultato = $pdo->bindAndExec($strsql,$bind);
						if ($risultato->rowCount()>0) $calcolo_gestore = true;
					} else {
						$calcolo_gestore = true;
					}
					if ($calcolo_gestore) {
						$bind = array();
						$bind[":contributo"] = $contributo;
						$bind[":codice_relazione"] = $codice_gestore;
						$sql_contributo = "SELECT * FROM r_contributi_gestore where minimo <= :contributo AND (massimo > :contributo OR massimo = 0) AND codice_ente = :codice_relazione";
						$ris_contributo = $pdo->bindAndExec($sql_contributo,$bind);
						if($ris_contributo->rowCount()>0){
							$rec_contr = $ris_contributo->fetch(PDO::FETCH_ASSOC);
							if(strcmp($rec_contr["tipo"],"fisso")==0) {
								$esito["contributo_gestore"]=(float)number_format($rec_contr["valore"], 2, ".","");
							}	else if (strcmp($rec_contr["tipo"],"percentuale")==0) {
								$esito["contributo_gestore"] =(float)number_format($contributo * (float)($rec_contr["valore"]/100), 2, ".","");
							}
						}
					}
				}
			}
		}
		if (isset($esito["contributo_sua"]) || isset($esito["contributo_gestore"])) {
			$esito["codice"] = $codice_gara;
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_gare";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $esito;
			$codice_contributo = $salva->save();
		}
	}
}
