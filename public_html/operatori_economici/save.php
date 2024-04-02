<?

	session_start();
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	$edit = false;
	$bozza = false;
	$update_date = false;
	$super_ad = false;
	 if ((isset($_SESSION["codice_utente"])) && ($_SESSION["codice_utente"] === $_POST["codice"]) && !empty($_POST["codice"])) {
		$edit = true;
		$utente_modifica = $_SESSION["codice_utente"];
		if(! empty($_SESSION["ente"]["profilo_completo_oe"]) && $_SESSION["ente"]["profilo_completo_oe"] == "S" && ! empty($_SESSION["record_utente"]["profilo_completo"]) && $_SESSION["record_utente"]["profilo_completo"] == "N") $bozza = true;
	} else if ((isset($_SESSION["tmp_codice_utente"])) && ($_SESSION["tmp_codice_utente"] === $_POST["codice"]) && !empty($_POST["codice"])) {
		$edit = true;
		$bozza = true;
		$update_date = true;
		$utente_modifica = $_SESSION["tmp_codice_utente"];
	} else if (isset($_SESSION["amministratore"]) && $_SESSION["amministratore"] && !empty($_POST["codice"])) {
		$edit = true;
		$super_ad = true;
		$utente_modifica = $_SESSION["codice_utente"];
	}
	if ($edit) {
		$sql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente AND codice = :codice ";
		$ris = $pdo->bindAndExec($sql,array(":codice_utente"=>$_POST["codice"],":codice"=>$_POST["operatori"]["codice"]));
		if ($ris->rowCount() !== 1) $edit = false;
	}
	if ($edit) {
		$salva = new salva();
		if (isset($_POST["utenti"]["password"])) $_POST["utenti"]["password"] = password_hash(md5($_POST["utenti"]["password"]), PASSWORD_BCRYPT);
		$_POST["utenti"]["codice"] = $_POST["codice"];

		if (! $_SESSION["amministratore"] && isset($_POST["utenti"]["gruppo"])) unset($_POST["utenti"]["gruppo"]);
		if ($update_date) {
			$bind = array(':codice' => $_POST["codice"]);
			$sql_update = "UPDATE b_utenti SET timestamp = now() WHERE codice = :codice";
			$ris_update = $pdo->bindAndExec($sql_update,$bind);
		}

		$salva->debug = false;
		$salva->codop = $utente_modifica;
		$salva->nome_tabella = "b_utenti";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $_POST["utenti"];
		$salva->oggetto["profilo_completo"] = 'N';
		$codice_utente = $salva->save();
		if ($codice_utente > 0) {

			if (isset($_POST["utenti"]["password"])) {
				$salva->debug = false;
				$salva->codop = (isset($_SESSION["codice_utente"])) ? $_SESSION["codice_utente"] : $codice_utente;
				$salva->nome_tabella = "b_password_log";
				$salva->operazione = "INSERT";
				$salva->oggetto = array("codice_utente"=>$codice_utente);
				$salva->save();
			}

			if (!isset($_SESSION["codice_utente"])) {
				$under_level = $_SESSION["tmp_codice_utente"];
			} else {
				$under_level = $_SESSION["codice_utente"];
			}
				if (!is_dir($config["pub_doc_folder"]."/operatori/".$_POST["operatori"]["codice"])) mkdir($config["pub_doc_folder"]."/operatori/".$_POST["operatori"]["codice"],0770,true);
				if (isset($_POST["curriculum"]) && ($_POST["curriculum"]!="")) {
					$copy_curriculum =  copiafile_chunck($_POST["curriculum"],$config["pub_doc_folder"]."/operatori/".$_POST["operatori"]["codice"]."/",$config["chunk_folder"]."/".$under_level);
					$_POST["operatori"]["curriculum"] = $copy_curriculum["nome_file"];
					$_POST["operatori"]["riferimento_curriculum"] = $copy_curriculum["nome_fisico"];
				}
				if (isset($_POST["iscrizione_ordine"]) && ($_POST["iscrizione_ordine"]!="")) {
					$copy_iscrizione_ordine =  copiafile_chunck($_POST["iscrizione_ordine"],$config["pub_doc_folder"]."/operatori/".$_POST["operatori"]["codice"]."/",$config["chunk_folder"]."/".$under_level);
					$_POST["operatori"]["iscrizione_ordine"] = $copy_iscrizione_ordine["nome_file"];
					$_POST["operatori"]["riferimento_iscrizione_ordine"] = $copy_iscrizione_ordine["nome_fisico"];
				}
				if (isset($_POST["certificato_camerale"]) && ($_POST["certificato_camerale"]!="")) {
					$copy_certificato_camerale =  copiafile_chunck($_POST["certificato_camerale"],$config["pub_doc_folder"]."/operatori/".$_POST["operatori"]["codice"]."/",$config["chunk_folder"]."/".$under_level);
					$_POST["operatori"]["certificato_camerale"] = $copy_certificato_camerale["nome_file"];
					$_POST["operatori"]["riferimento_certificato_camerale"] = $copy_certificato_camerale["nome_fisico"];
				}

			if ($_POST["tipo"] == "PRO") {
				$_POST["operatori"]["ragione_sociale"] = $_POST["utenti"]["cognome"] . " " . $_POST["utenti"]["nome"];
				$_POST["operatori"]["indirizzo_operativa"] = $_POST["utenti"]["indirizzo"];
				if ($_POST["operatori"]["partita_iva"] != "") {
					$_POST["operatori"]["codice_fiscale_impresa"] = $_POST["operatori"]["partita_iva"];
				} else {
					$_POST["operatori"]["codice_fiscale_impresa"] = $_POST["utenti"]["cf"];
				}
				$_POST["operatori"]["citta_operativa"] = $_POST["utenti"]["citta"];
				$_POST["operatori"]["provincia_operativa"] = $_POST["utenti"]["provincia"];
				$_POST["operatori"]["regione_operativa"] = $_POST["utenti"]["regione"];
				$_POST["operatori"]["stato_operativa"] = $_POST["utenti"]["stato"];
			}

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $utente_modifica;
			$salva->nome_tabella = "b_operatori_economici";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $_POST["operatori"];
			$codice_operatore = $salva->save();

			if ($codice_operatore > 0) {

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $utente_modifica;
				$salva->nome_tabella = "b_operatori_economici";
				$salva->operazione = "UPDATE";
				$salva->oggetto = $_POST["operatori"];
				$codice_operatore = $salva->save();

				if (isset($_POST["ccnl"])) {
					$codici_ccnl = array();
					foreach($_POST["ccnl"] as $ccnl) {
							$operazione_ccnl = "UPDATE";
							$ccnl["codice_operatore"] = $codice_operatore;
							$ccnl["codice_utente"] = $codice_utente;
						if ($ccnl["codice"] == "") {
							$ccnl["codice"] = 0;
							$operazione_ccnl = "INSERT";
						}

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "b_ccnl";
						$salva->operazione = $operazione_ccnl;
						$salva->oggetto = $ccnl;
						$codici_ccnl[] = $salva->save();

					}
				}

				if (isset($_POST["rappresentanti"])) {
					$codici_rappresentanti = array();
					foreach($_POST["rappresentanti"] as $rappresentanti) {
							$operazione_rappresentanti = "UPDATE";
							$rappresentanti["codice_operatore"] = $codice_operatore;
							$rappresentanti["codice_utente"] = $codice_utente;
						if ($rappresentanti["codice"] == "") {
							$rappresentanti["codice"] = 0;
							$operazione_rappresentanti = "INSERT";
						}

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "b_rappresentanti";
						$salva->operazione = $operazione_rappresentanti;
						$salva->oggetto = $rappresentanti;
						$codici_rappresentanti[] = $salva->save();
					}
				}
				$bind=array(":codice_operatore"=>$codice_operatore);
				$strsql = "DELETE FROM r_cpv_operatori WHERE codice_operatore = :codice_operatore ";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if (isset($_POST["cpv"])) {
					if ($_POST["cpv"] != "")  {
						$array_cpv = explode(";",$_POST["cpv"]);
						$codici_cpv = array();
						foreach($array_cpv as $cpv) {
							if ($cpv != "") {
								$insert_cpv["codice"] = $cpv;
								$insert_cpv["codice_operatore"] = $codice_operatore;
								$insert_cpv["codice_utente"] = $codice_utente;
								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $utente_modifica;
								$salva->nome_tabella = "r_cpv_operatori";
								$salva->operazione = "INSERT";
								$salva->oggetto = $insert_cpv;
								$codici_cpv[] = $salva->save();
							}
						}
					}
				}

				if (isset($_POST["committenti"])) {
					$codici_committenti = array();
					foreach($_POST["committenti"] as $committenti) {
							$operazione_committenti = "UPDATE";
							$committenti["codice_operatore"] = $codice_operatore;
							$committenti["codice_utente"] = $codice_utente;
						if ($committenti["codice"] == "") {
							$committenti["codice"] = 0;
							$operazione_committenti = "INSERT";
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "b_committenti";
						$salva->operazione = $operazione_committenti;
						$salva->oggetto = $committenti;
						$codici_committenti[] = $salva->save();
					}
				}

				if (isset($_POST["qualita"])) {
					$codici_qualita = array();
					foreach($_POST["qualita"] as $qualita) {
							$operazione_qualita = "UPDATE";
							$qualita["codice_operatore"] = $codice_operatore;
							$qualita["codice_utente"] = $codice_utente;
						if ($qualita["codice"] == "") {
							$qualita["codice"] = 0;
							$operazione_qualita = "INSERT";
              $qualita["certificato"] = "";
						}
            if (isset($_POST["qualita_".$qualita["id"]."_certificato"]) && ($_POST["qualita_".$qualita["id"]."_certificato"]!="")) {
							$copy_certificato = copiafile_chunck($_POST["qualita_".$qualita["id"]."_certificato"],$config["pub_doc_folder"]."/operatori/".$codice_operatore."/",$config["chunk_folder"]."/".$under_level);
							$qualita["certificato"] = $copy_certificato["nome_file"];
							$qualita["riferimento"] = $copy_certificato["nome_fisico"];
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "b_certificazioni_qualita";
						$salva->operazione = $operazione_qualita;
						$salva->oggetto = $qualita;
						$codici_qualita[] = $salva->save();

					}
				}

				if (isset($_POST["soa"])) {
					$codici_soa = array();
					foreach($_POST["soa"] as $soa) {
							$operazione_soa = "UPDATE";
							$soa["codice_operatore"] = $codice_operatore;
							$soa["codice_utente"] = $codice_utente;
						if ($soa["codice"] == "") {
							$soa["codice"] = 0;
							$operazione_soa = "INSERT";
              $soa["certificato"] = "";
						}
            if (isset($_POST["soa_".$soa["id"]."_certificato"]) && ($_POST["soa_".$soa["id"]."_certificato"]!="")) {
							$copy_certificato =  copiafile_chunck($_POST["soa_".$soa["id"]."_certificato"],$config["pub_doc_folder"]."/operatori/".$codice_operatore."/",$config["chunk_folder"]."/".$under_level);
							$soa["certificato"] = $copy_certificato["nome_file"];
							$soa["riferimento"] = $copy_certificato["nome_fisico"];
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "b_certificazioni_soa";
						$salva->operazione = $operazione_soa;
						$salva->oggetto = $soa;
						$codice_soa = $salva->save();
						if ($codice_soa !== false && $soa["codice_classifica"]==="0" && !empty($soa["fatturato"])) {
							foreach($soa["fatturato"] AS $anno => $fatturato) {
								$tmp = array();
								$operazione_fatturato = "INSERT";
								$tmp["codice"] = 0;
								$sql = "SELECT * FROM b_fatturato_soa WHERE codice_attestazione = :codice_attestazione AND anno = :anno ";
								$ris_fatturato = $pdo->bindAndExec($sql,array(":codice_attestazione"=>$codice_soa,":anno"=>$anno));
								if ($ris_fatturato->rowCount() === 1) {
									$tmp["codice"] = $ris_fatturato->fetch(PDO::FETCH_ASSOC)["codice"];
									$operazione_fatturato = "UPDATE";
								}
								$tmp["codice_utente"] = $codice_utente;
								$tmp["codice_attestazione"] = $codice_soa;
								$tmp["anno"] = $anno;
								$tmp["fatturato"] = $fatturato["fatturato"];

								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $utente_modifica;
								$salva->nome_tabella = "b_fatturato_soa";
								$salva->operazione = $operazione_fatturato;
								$salva->oggetto = $tmp;
								$codice_soa_fatturato = $salva->save();
							}
						}
					}
				}

				if (isset($_POST["progettazione"])) {
					$codici_progettazione = array();
					foreach($_POST["progettazione"] as $progettazione) {
							$operazione_progettazione = "UPDATE";
							$progettazione["codice_operatore"] = $codice_operatore;
							$progettazione["codice_utente"] = $codice_utente;
						if ($progettazione["codice"] == "") {
							$progettazione["codice"] = 0;
							$operazione_progettazione = "INSERT";
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "b_esperienze_progettazione";
						$salva->operazione = $operazione_progettazione;
						$salva->oggetto = $progettazione;
						$codici_progettazione[] = $salva->save();
					}
				}

				if (isset($_POST["ambientali"])) {
					$codici_ambientali = array();
					foreach($_POST["ambientali"] as $ambientali) {
						$operazione_ambientali = "UPDATE";
						$ambientali["codice_operatore"] = $codice_operatore;
						$ambientali["codice_utente"] = $codice_utente;
						if ($ambientali["codice"] == "") {
							$operazione_ambientali = "INSERT";
              $ambientali["certificato"] = "";
						}
            if (isset($_POST["ambientali_".$ambientali["id"]."_certificato"]) && ($_POST["ambientali_".$ambientali["id"]."_certificato"]!="")) {
							$copy_certificato =  copiafile_chunck($_POST["ambientali_".$ambientali["id"]."_certificato"],$config["pub_doc_folder"]."/operatori/".$codice_operatore."/",$config["chunk_folder"]."/".$under_level);
							$ambientali["certificato"] = $copy_certificato["nome_file"];
							$ambientali["riferimento"] = $copy_certificato["nome_fisico"];
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "b_certificazioni_ambientali";
						$salva->operazione = $operazione_ambientali;
						$salva->oggetto = $ambientali;
						$codici_ambientali[] = $salva->save();
					}
				}

				if (isset($_POST["certificazioni"])) {
					$codici_certificazioni = array();
					foreach($_POST["certificazioni"] as $certificazioni) {
							$operazione_certificazioni = "UPDATE";
							$certificazioni["codice_operatore"] = $codice_operatore;
							$certificazioni["codice_utente"] = $codice_utente;
						if ($certificazioni["codice"] == "") {
							$certificazioni["codice"] = 0;
							$operazione_certificazioni = "INSERT";
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "b_altre_certificazioni";
						$salva->operazione = $operazione_certificazioni;
						$salva->oggetto = $certificazioni;
						$codici_certificazioni[] = $salva->save();
					}
				}
				if (isset($_POST["brevetti"])) {
					$codici_brevetti = array();
					foreach($_POST["brevetti"] as $brevetti) {
							$operazione_brevetti = "UPDATE";
							$brevetti["codice_operatore"] = $codice_operatore;
							$brevetti["codice_utente"] = $codice_utente;
						if ($brevetti["codice"] == "") {
							$brevetti["codice"] = 0;
							$operazione_brevetti = "INSERT";
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "b_brevetti";
						$salva->operazione = $operazione_brevetti;
						$salva->oggetto = $brevetti;
						$codici_brevetti[] = $salva->save();
					}
				}



				$bind = array();
				$bind[":codice_utente"] = $codice_utente;
				$sql = "SELECT b_utenti.*, b_gruppi.id AS id_gruppo FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice WHERE b_utenti.codice = :codice_utente ";
				$ris_utente = $pdo->bindAndExec($sql,$bind);

				$bind = array();
				$bind[":codice_operatore"] = $codice_operatore;
				$sql = "SELECT b_operatori_economici.* FROM b_operatori_economici WHERE b_operatori_economici.codice = :codice_operatore ";
				$ris_operatore = $pdo->bindAndExec($sql,$bind);

				$msg = "";
				if ($ris_utente->rowCount() > 0 && $ris_operatore->rowCount() > 0) {
					$utente = $ris_utente->fetch(PDO::FETCH_ASSOC);
					$operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);

					if (empty($utente["email"])) $msg .= "<li>" . traduci("e-mail") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["nome"])) $msg .= "<li>" . traduci("Nome") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["cognome"])) $msg .= "<li>" . traduci("Cognome") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["luogo"])) $msg .= "<li>" . traduci("Luogo di nascita") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["provincia_nascita"])) $msg .= "<li>" . traduci("Provincia di nascita") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["dnascita"])) $msg .= "<li>" . traduci("Data di nascita") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["sesso"])) $msg .= "<li>" . traduci("Sesso") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["cf"])) $msg .= "<li>" . traduci("Codice fiscale")  . " ".traduci('obbligatorio')."</li>";

					if (empty($utente["indirizzo"])) $msg .= "<li>" . traduci("Indirizzo") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["citta"])) $msg .= "<li>" . traduci("Citta") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["provincia"])) $msg .= "<li>" . traduci("Provincia") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["regione"])) $msg .= "<li>" . traduci("Regione") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["stato"])) $msg .= "<li>" . traduci("nazione") . " ".traduci('obbligatorio')."</li>";
					if (empty($utente["pec"])) $msg .= "<li>" . traduci("PEC") . " ".traduci('obbligatorio')."</li>";


					if ($utente["id_gruppo"] == "PRO") {

						if (empty($operatore["titolo_studio"])) $msg .= "<li>" . traduci("Titolo di studio") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["ordine_professionale"])) $msg .= "<li>" . traduci("Ordine professionale") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["iscrizione_ordine"])) $msg .= "<li>" . traduci("Iscrizione") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["numero_iscrizione_professionale"])) $msg .= "<li>" . traduci("Numero iscrizione") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["data_iscrizione_professionale"])) $msg .= "<li>" . traduci("Data iscrizione") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["curriculum"])) $msg .= "<li>" . traduci("Curriculum") . " ".traduci('obbligatorio')."</li>";

					} else if ($utente["id_gruppo"] == "OPE") {

						if (empty($operatore["ruolo_referente"])) $msg .= "<li>" . traduci("Ruolo") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["partita_iva"])) $msg .= "<li>" . traduci("Partita IVA") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["ragione_sociale"])) $msg .= "<li>" . traduci("Ragione sociale") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["codice_fiscale_impresa"])) $msg .= "<li>" . traduci("Codice fiscale") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["pmi"])) $msg .= "<li>Dimensione ".traduci('obbligatorio')."</li>";
						if (empty($operatore["indirizzo_legale"])) $msg .= "<li>" . traduci("Indirizzo") . " - " . traduci("sede legale") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["citta_legale"])) $msg .= "<li>" . traduci("Citta") . " - " . traduci("sede legale") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["provincia_legale"])) $msg .= "<li>" . traduci("Provincia") . " - " . traduci("sede legale") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["regione_legale"])) $msg .= "<li>" . traduci("Regione") . " - " . traduci("sede legale") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["stato_legale"])) $msg .= "<li>" . traduci("nazione") . " - " . traduci("sede legale") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["indirizzo_operativa"])) $msg .= "<li>" . traduci("Indirizzo") . " - " . traduci("sede operativa") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["citta_operativa"])) $msg .= "<li>" . traduci("Citta") . " - " . traduci("sede operativa") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["provincia_operativa"])) $msg .= "<li>" . traduci("Provincia") . " - " . traduci("sede operativa") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["regione_operativa"])) $msg .= "<li>" . traduci("Regione") . " - " . traduci("sede operativa") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["stato_operativa"])) $msg .= "<li>" . traduci("nazione") . " - " . traduci("sede operativa") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["banca"])) $msg .= "<li>" . traduci("Banca") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["iban"])) $msg .= "<li>" . traduci("IBAN") . " ".traduci('obbligatorio')."</li>";
						if (empty($operatore["intestatario"])) $msg .= "<li>" . traduci("Intestatario") . " ".traduci('obbligatorio')."</li>";

						$bind=array(":codice_operatore"=>$codice_operatore);

						$strsql = "SELECT * FROM b_rappresentanti WHERE codice_operatore = :codice_operatore ";
						$risultato = $pdo->bindAndExec($strsql,$bind);
						if ($risultato->rowCount() == 0) $msg .= "<li>" . traduci("Rappresentanti legali") . " " . traduci("obbligatorio") . " </li>";
					}

					$bind=array(":codice_operatore"=>$codice_operatore);
					$strsql = "SELECT * FROM r_cpv_operatori WHERE codice_operatore = :codice_operatore ";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() == 0) $msg .= "<li>" . traduci("Categorie Merceologiche") . " " . traduci("obbligatorio") . "</li>";

				} else {
					$fatal = true;
				}

				if (isset($fatal)) {
					?>
					<h1 style="text-align:center; color:#F30"><?= traduci("ERRORE NELLA REGISTRAZIONE") ?> - ERRORE #1</h1>
					<br>
					<h2 style="text-align:center"><?= traduci('errore-salvataggio') ?></h2>
					<?
				} else {
					if (class_exists("syncERP")) {
						$syncERP = new syncERP();
						if (method_exists($syncERP,"sendOE")) {
							$syncERP->sendOE($codice_operatore);							
						}
					}
					if ($msg != "") {
						?>
						<h1 style="text-align:center"><?= traduci("MODIFICA EFFETTUATA CON SUCCESSO") ?></h1>
						<ul class="ui-state-error">
							<li>
								<h3><?= traduci("errore nella procedura di conferma") ?></h3>
								<b><?= traduci("alert-errori-registrazione") ?></b>
								<ul>
									<?= $msg ?>
								</ul>
							</li>
						</ul>
						<?
					} else {
						$sql = "UPDATE b_utenti SET profilo_completo = 'S' WHERE codice = :codice";
						$bind = array(':codice' => $utente["codice"]);
						$pdo->bindAndExec($sql, $bind);
						?>
						<h1 style="text-align:center"><?= traduci("conferma della registrazione avvenuta con successo") ?></h1>
						<?
					}
				}

			} else {
				?>
				<h1 style="text-align:center; color:#F30"><?= traduci("ERRORE NELLA REGISTRAZIONE") ?> - ERRORE #2.1</h1>
				<br>
				<h2 style="text-align:center"><?= traduci('errore-salvataggio') ?></h2>
				<?
			}
		} else {
			?>
			<h1 style="text-align:center; color:#F30"><?= traduci("ERRORE NELLA REGISTRAZIONE") ?> - ERRORE #2</h1>
			<br>
			<h2 style="text-align:center"><?= traduci('errore-salvataggio') ?></h2>
			<?
		}
	} else {
		?>
		<h1 style="text-align:center; color:#F30"><?= traduci("ERRORE NELLA REGISTRAZIONE") ?> - ERRORE #3</h1>
		<br>
		<h2 style="text-align:center"><?= traduci('errore-salvataggio') ?></h2>
		<?
	}


include_once($root."/layout/bottom.php");

?>
