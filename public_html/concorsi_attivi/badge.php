<?
	$span = true;
	if (!isset($pdo)) {
		session_start();
		include_once("../../config.php");
		include_once($root."/inc/funzioni.php");
		$span = false;
	}
	if (is_operatore()) {

		$echo_span = 0;

		if (isset($_SESSION["ente"])) {
			$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
			$strsql  = "SELECT b_concorsi.*, b_ente_gestore.dominio, b_enti.denominazione, b_conf_stati_concorsi.titolo AS fase, b_conf_stati_concorsi.colore  ";
			$strsql .= "FROM b_concorsi  ";
			$strsql .= "JOIN b_conf_stati_concorsi ON b_concorsi.stato = b_conf_stati_concorsi.fase ";
			$strsql .= "JOIN b_enti ON b_concorsi.codice_ente = b_enti.codice ";
			$strsql .= "JOIN b_enti AS b_ente_gestore ON b_concorsi.codice_gestore = b_ente_gestore.codice ";
			$strsql .= "WHERE pubblica > 0 AND (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
			$strsql .= "GROUP BY b_concorsi.codice ";
			$strsql .= "ORDER BY codice DESC" ;

			$risultato  = $pdo->bindAndExec($strsql,$bind); //invia la query contenuta in $strsql al database apero e connesso
			if ($risultato->rowCount() > 0) {
				while ($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
				$show = false;
				if (($record["stato"]==3) && (strtotime($record["data_scadenza"])>time())) {
					$sql = "SELECT * FROM r_cpv_operatori JOIN r_cpv_concorsi ON r_cpv_operatori.codice = r_cpv_concorsi.codice WHERE r_cpv_operatori.codice_utente = :codice_utente ";
					$ris = $pdo->bindAndExec($sql,array(":codice_utente"=>$_SESSION["codice_utente"]));
					if ($ris->rowCount() > 0) $show = true;
				}
				if (!$show) {
					$sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara ";
					$ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice_gara"=>$record["codice"]));
					if ($ris_fasi->rowCount() > 0) {
						$ris_fasi = $ris_fasi->fetchAll(PDO::FETCH_ASSOC);
						$last = array();
						$fase_attiva = array();
						foreach($ris_fasi AS $fase) {
							if ($fase["attiva"]=="S") {
								$last = $fase_attiva;
								$fase_attiva = $fase;
							}
						}
						if (!empty($last["codice"])) {
							if ((strtotime($fase_attiva["scadenza"])>time())) {

								$sql_check = "SELECT * FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
												WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND r_partecipanti_concorsi.conferma = 1 AND r_partecipanti_concorsi.ammesso = 'S'
												AND r_partecipanti_concorsi.escluso = 'N' AND r_partecipanti_utenti_concorsi.codice_utente = :codice_utente ";
								$ris_check = $pdo->bindAndExec($sql_check,array(":codice_gara"=>$record["codice"],":codice_fase"=>$last["codice"],":codice_utente"=>$_SESSION["codice_utente"]));
								if ($ris_check->rowCount() > 0) $show = true;
							}
						}
					}
				}
				if ($show) $echo_span++;
			}

			if ($span) {
				echo "<span class=\"badge disable-update\"";
				if ($echo_span===0) echo " style=\"display:none\"";
				echo ">";
			}
			if ($echo_span>0) echo $echo_span;
			if ($span) echo "</span>";
		}
	}
}
?>
