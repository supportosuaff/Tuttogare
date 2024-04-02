<?
	include_once("../../../config.php");
	$public = true;
	if (isset($_POST["codice_gara"]) && isset($_POST["codice_lotto"]) && is_operatore()) {

		$codice_gara = $_POST["codice_gara"];
		$codice_lotto = $_POST["codice_lotto"];

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								WHERE b_gare.codice = :codice ";
		$strsql .= "AND b_gare.annullata = 'N' AND b_modalita.online = 'S' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$accedi = false;

		if ($risultato->rowCount() > 0) {
			$bind = array();
			$bind[":codice"] = $codice_gara;
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$derivazione = "";
			$sql = "SELECT * FROM b_procedure WHERE codice = :codice_procedura";
			$ris = $pdo->bindAndExec($sql,array(":codice_procedura"=>$record_gara["procedura"]));
			if ($ris->rowCount()>0) {
				$rec_procedura = $ris->fetch(PDO::FETCH_ASSOC);
				$directory = $rec_procedura["directory"];
				$record["nome_procedura"] = $rec_procedura["nome"];
				$record["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
				if ($rec_procedura["mercato_elettronico"] == "S") $derivazione = "me";
				if ($rec_procedura["directory"] == "sda")  $derivazione = "sda";
				if ($rec_procedura["directory"] == "dialogo")  $dialogo = true;

			}

			$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice";
			$ris_inviti = $pdo->bindAndExec($strsql,$bind);
			if ($ris_inviti->rowCount()==0) {
				if($record_gara["invito"] == "N" || !empty($derivazione)) {
					$accedi = true;
				}
			}
			if ($derivazione != "") {
				$sql_abilitato = "SELECT * FROM r_partecipanti_".$derivazione." WHERE codice_bando = :codice_derivazione AND ammesso = 'S' AND codice_utente = :codice_utente ";
				$ris_abilitato = $pdo->bindAndExec($sql_abilitato,array(":codice_derivazione"=>$record_gara["codice_derivazione"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_abilitato->rowCount() == 0) {
					$accedi = false;
				}
			}
		}
		if ($accedi) {
			$print_form = true;
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
			if ($ris_lotti->rowCount() > 0) {
				$print_form =false;
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto ORDER BY codice";
				$bind[":codice_lotto"] = $codice_lotto;
				$ris_check_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				if ($ris_check_lotti->rowCount() > 0) {
						$lotto = $ris_check_lotti->fetch(PDO::FETCH_ASSOC);
						if ($record_gara["modalita_lotti"]==1) {
							$bind =array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND conferma = TRUE AND codice_utente = :codice_utente";
							$ris_partecipazioni = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipazioni->rowCount() > 0) {
								$bind = array();
								$bind[":lotto"] = $codice_lotto;
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_lotto = :lotto AND conferma = TRUE AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
								$ris_partecipante_lotto = $pdo->bindAndExec($sql,$bind);
								if ($ris_partecipante_lotto->rowCount() > 0) {
									$print_form = true;
								} 
							} else {
								$print_form = true;
							}
					} else {
						$print_form = true;
					}
				}
			} else {
				$codice_lotto = 0;
			}

			if ($print_form) {

				$submit = false;

				if (isset($lotto)) $codice_lotto = $lotto["codice"];
				if (strtotime($record_gara["data_scadenza"]) > time()) {
						$submit = true;
				}
				if ($submit) {
					$error = false;
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_lotto"] = $codice_lotto;
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND codice_capogruppo = 0 ";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount() == 0) {
						$sql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice WHERE b_operatori_economici.codice_utente = :codice_utente ";
						$ris_operatori_economici = $pdo->bindAndExec($sql,array(":codice_utente"=>$_SESSION["codice_utente"]));

						$partecipante = array();
						$operatore_economico = $ris_operatori_economici->fetch(PDO::FETCH_ASSOC);
						$partecipante["codice_gara"] = $record_gara["codice"];
						$partecipante["codice_lotto"] = $codice_lotto;
						$partecipante["codice_operatore"] = $operatore_economico["codice"];
						$partecipante["codice_utente"] = $_SESSION["codice_utente"];
						$partecipante["partita_iva"] = $operatore_economico["codice_fiscale_impresa"];
						$partecipante["ragione_sociale"] = $operatore_economico["ragione_sociale"];
						$partecipante["pec"] = $operatore_economico["pec"];
						$partecipante["identificativoEstero"] = $operatore_economico["identificativoEstero"];
						$partecipante["conferma"] = 0;
						$partecipante["ammesso"] = 'N';
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_partecipanti";
						$salva->operazione = "INSERT";
						$salva->oggetto = $partecipante;
						$codice_partecipante = $salva->save();
						if ($codice_partecipante != false) {
							?>
							alert("Operazione effettuata con successo!");
							window.location.reload();
							<?
							die();
						}
					}
				} 
			} 
		} 
		?>
		alert("L'operazione non è disponibile");
		<?
		die();
	}
	?>
