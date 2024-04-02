<?
include_once(__DIR__."/../../config.php");
$edit = false;
$errore = "";
if (!isset($elaborazioneApi)) {
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("gare",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
} 
if (!$edit && !isset($elaborazioneApi)) {
	die();
} else {
	if (isset($_POST["operazione"])) {
		if (isset($elaborazioneApi)) ob_start();
		$bind = array();
		$bind[":procedura"] = @$_POST["gara"]["procedura"];
		$strsql = "SELECT * FROM b_procedure WHERE mercato_elettronico = 'S' AND directory = 'rdo' AND codice = :procedura ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		$corretto = true;
		$msg_cpv = "";
		if ($risultato->rowCount()>0) {
			$array_cpv = explode(";",$_POST["cpv"]);
			foreach ($array_cpv AS 	$codice_cpv) {
				if ($codice_cpv !== "") {
					$cpv_corrente = $codice_cpv;
					$bind = array();
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql  = "SELECT * FROM r_cpv_bandi_mercato JOIN b_bandi_mercato ON r_cpv_bandi_mercato.codice_bando = b_bandi_mercato.codice ";
					$strsql .= "WHERE (b_bandi_mercato.annullata = 'N' AND  b_bandi_mercato.data_scadenza > now() ";
					$strsql .= "AND (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente) ";
					$strsql .= "AND (b_bandi_mercato.pubblica = '2' OR b_bandi_mercato.pubblica = '1')) AND (";
					$cont = 0;
					while (strlen($codice_cpv)>1) {
						$cont++;
						$bind[":cpv_".$cont] = $codice_cpv;
						$strsql .= "r_cpv_bandi_mercato.codice = :cpv_".$cont." OR ";
						$codice_cpv = substr($codice_cpv,0,-1);
					}
					$strsql = substr($strsql,0,-4);
					$strsql .= ")";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount()===0) {
						$corretto = false;
						$msg_cpv.= "<li>Codice <strong>" . $cpv_corrente . "</strong></li>";
					}
				}
			}
		}
		$bind = array();
		$bind[":procedura"] = @$_POST["gara"]["procedura"];
		$strsql = "SELECT * FROM b_procedure WHERE directory = 'sda' AND codice = :procedura ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		$corretto = true;
		$msg_cpv = "";
		if ($risultato->rowCount()>0) {
			$array_cpv = explode(";",$_POST["cpv"]);
			foreach ($array_cpv AS 	$codice_cpv) {
				if ($codice_cpv !== "") {
					$cpv_corrente = $codice_cpv;
					$bind = array();
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql  = "SELECT * FROM r_cpv_bandi_sda JOIN b_bandi_sda ON r_cpv_bandi_sda.codice_bando = b_bandi_sda.codice ";
					$strsql .= "WHERE (b_bandi_sda.annullata = 'N' AND  b_bandi_sda.data_scadenza > now() ";
					$strsql .= "AND (b_bandi_sda.codice_ente = :codice_ente OR b_bandi_sda.codice_gestore = :codice_ente) ";
					$strsql .= "AND (b_bandi_sda.pubblica = '2' OR b_bandi_sda.pubblica = '1')) AND (";
					$cont = 0;
					while (strlen($codice_cpv)>1) {
						$cont++;
						$bind[":cpv_".$cont] = $codice_cpv;
						$strsql .= "r_cpv_bandi_sda.codice = :cpv_".$cont." OR ";
						$codice_cpv = substr($codice_cpv,0,-1);
					}
					$strsql = substr($strsql,0,-4);
					$strsql .= ")";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount()===0) {
						$corretto = false;
						$msg_cpv.= "<li>Codice <strong>" . $cpv_corrente . "</strong></li>";
					}
				}
			}
		}

		$bind = array();
		$bind[":procedura"] = @$_POST["gara"]["procedura"];
		$strsql = "SELECT * FROM b_procedure WHERE directory = 'dialogo' AND codice = :procedura ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		$corretto = true;
		$msg_cpv = "";
		if ($risultato->rowCount()>0) {
			$array_cpv = explode(";",$_POST["cpv"]);
			foreach ($array_cpv AS 	$codice_cpv) {
				if ($codice_cpv !== "") {
					$cpv_corrente = $codice_cpv;
					$bind = array();
					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
					$strsql  = "SELECT * FROM r_cpv_bandi_dialogo JOIN b_bandi_dialogo ON r_cpv_bandi_dialogo.codice_bando = b_bandi_dialogo.codice ";
					$strsql .= "WHERE (b_bandi_dialogo.annullata = 'N' AND  b_bandi_dialogo.data_apertura < now() ";
					$strsql .= "AND (b_bandi_dialogo.codice_ente = :codice_ente OR b_bandi_dialogo.codice_gestore = :codice_ente) ";
					$strsql .= "AND (b_bandi_dialogo.pubblica = '2' OR b_bandi_dialogo.pubblica = '1')) AND (";
					$cont = 0;
					while (strlen($codice_cpv)>1) {
						$cont++;
						$bind[":cpv_".$cont] = $codice_cpv;
						$strsql .= "r_cpv_bandi_dialogo.codice = :cpv_".$cont." OR ";
						$codice_cpv = substr($codice_cpv,0,-1);
					}
					$strsql = substr($strsql,0,-4);
					$strsql .= ")";
					$risultato = $pdo->bindAndExec($strsql,$bind);

					if ($risultato->rowCount()===0) {
						$corretto = false;
						$msg_cpv.= "<li>Codice <strong>" . $cpv_corrente . "</strong></li>";
					}
				}
			}
		}

		$bind = array();
		$bind[":procedura"] = @$_POST["gara"]["procedura"];
		$strsql = "SELECT * FROM b_procedure WHERE derivata > 0 AND codice = :procedura";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$corretto = false;
			$msg_derivata = "<li>Impossibile procedere al salvataggio - Nessun bando d'appoggio disponibile</li>";
			$procedura_derivata = $risultato->fetch(PDO::FETCH_ASSOC);
			$procedura_derivata = $procedura_derivata["derivata"];
			$bind = array();
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$bind[":codice_derivata"] = $procedura_derivata;
			$strsql  = "SELECT b_gare.* FROM b_gare JOIN r_partecipanti ON b_gare.codice = r_partecipanti.codice_gara ";
			$strsql .= "WHERE ";
			$strsql .= "(b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
			$strsql .= "AND r_partecipanti.primo = 'S' AND b_gare.procedura = :codice_derivata";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$corretto = true;
			}
		}

		$bind = array();
		$bind[":tipologia"] = @$_POST["gara"]["tipologia"];
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		// $bind[":importo_appalto"] = $_POST["gara"]["prezzoBase"];
		$sql = "SELECT * FROM b_limitazioni WHERE (tipologia = :tipologia OR tipologia = 0) AND codice_ente = :codice_ente ORDER BY tipologia DESC, codice DESC LIMIT 0,1";
		$risultato = $pdo->bindAndExec($sql,$bind);
		if ($risultato->rowCount() > 0) {
			$corretto = false;
			$limite = $risultato->fetch(PDO::FETCH_ASSOC)["importo_max"];
			if ($limite >= $_POST["gara"]["prezzoBase"]) {
				$corretto = true;
			} else {
				$msg_limite = "L'Ente non &egrave; abilitato a svolgere procedure per l'importo indicato";
			}
		}

		if (isset($_POST["gara"]["prezzoBase"]) && $_POST["gara"]["prezzoBase"] > $_SESSION["record_utente"]["limiteMassimo"] && !empty($_SESSION["record_utente"]["limiteMassimo"])) {
			$corretto = false;
			$msg_limite = "L'Utente non &egrave; abilitato a svolgere procedure per l'importo indicato";
		}

		if ($corretto) {
			if ($_POST["operazione"]=="INSERT") {
				$bind = array();
				$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
				if (!empty($_POST["gara"]["anno"]) && $_SESSION["ente"]["numerazione"] == "solare") {
					$sql = "SELECT MAX(CAST(SUBSTRING(id,".(strlen($_POST["gara"]["anno"])+2).",length(id)-".(strlen($_POST["gara"]["anno"])).") AS UNSIGNED)) as id FROM b_gare WHERE codice_gestore = :codice_ente AND anno = :anno ";
					$bind[":anno"] = $_POST["gara"]["anno"];
				} else {
					$sql = "SELECT MAX(CAST(id AS UNSIGNED)) as id FROM b_gare WHERE codice_gestore = :codice_ente ";
				}
				$sql.= " GROUP BY codice_gestore ";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount() > 0) {
					$rec = $ris->fetch(PDO::FETCH_ASSOC);
					$_POST["gara"]["id"] = $rec["id"] + 1;
				} else {
					$_POST["gara"]["id"] = 1;
				}
				if (!empty($_POST["gara"]["anno"]) && $_SESSION["ente"]["numerazione"] == "solare") $_POST["gara"]["id"] = $_POST["gara"]["anno"] . "/" . $_POST["gara"]["id"];
				$_POST["gara"]["stato"] = 1;
				$_POST["gara"]["nuovaOfferta"] = "S";
				$_POST["gara"]["timestamp_creazione"] = date('Y-m-d H:i:s');
				$_POST["gara"]["utente_creazione"] = $_SESSION["codice_utente"];
				$permessi_simog = true;
			} else {
				$_POST["gara"]["codice"] = $_POST["codice"];
			}
			if (isset($_POST["gara"]["email_chiave"]) && empty($_POST["gara"]["email_chiave"])) {
				$_POST["gara"]["email_chiave"] = (empty($_SESSION["ente"]["email_chiavi"])) ? $_SESSION["record_utente"]["email"] : $_SESSION["ente"]["email_chiavi"];
			}

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_gare";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST["gara"];
			$codice_gara = $salva->save();
			if ($codice_gara != false) {
			
				log_gare($_SESSION["ente"]["codice"],$codice_gara,$_POST["operazione"],"Dati preliminari");

				if ($_POST["operazione"]=="INSERT" && $_SESSION["gerarchia"] == 2) {
					$utente = array();
					$utente["codice_gara"] = $codice_gara;
					$utente["codice_ente"] = $_SESSION["ente"]["codice"];
					$utente["codice_utente"] = $_SESSION["codice_utente"];

					$salva->nome_tabella = "b_permessi";
					$salva->operazione = "INSERT";
					$salva->oggetto = $utente;
					$permesso = $salva->save();
				}

				$bind = array();
				$bind[":codice_gara"] = $codice_gara;

				$strsql = "SELECT b_gare.* FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice WHERE b_gare.codice = :codice_gara AND b_modalita.online = 'S'";
				$strsql.= " AND b_gare.public_key = ''";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount() > 0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					$res = openssl_pkey_new();
					$path = sys_get_temp_dir() . "/" . rand() . ".pem";
					if (openssl_pkey_export_to_file($res,$path)) {
						$destinatario_chiave = $record_gara["email_chiave"];
						if (empty($destinatario_chiave)) {
							$destinatario_chiave = (empty($_SESSION["ente"]["email_chiavi"])) ? $_SESSION["record_utente"]["email"] : $_SESSION["ente"]["email_chiavi"];
						}

						$corpo = "In allegato alla presente si invia la chiave privata necessaria all'apertura delle offerte per la gara <br><br><strong>ID " . $record_gara["id"] . " - " . $record_gara["oggetto"] . "</strong><br><br>";
						$corpo.= "Distinti Saluti<br><br>";

						$mail = new PHPMailer();
						$mail->IsSMTP();
						$mail->Host = $config["smtp_server"];
						$mail->Port = $config["smtp_port"];
						if ($config["smtp_ssl"] == true) $mail->SMTPSecure = 'ssl';
						$mail->SMTPAuth = true;
						$mail->Username = $config["mittente_mail"];
						$mail->Password = $config["smtp_password"];
						$mail->SetFrom($config["mittente_mail"],$config["nome_sito"]);
						$mail->AddAddress($destinatario_chiave);

						$subject = $config["nome_sito"] . " - IMPORTANTE - Invio chiave privata - ID " . $record_gara["id"] . " - " . $record_gara["oggetto"];
						$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
						$subject = mb_convert_encoding($subject, "UTF-8", "auto");
						$subject = iconv('UTF-8', 'ASCII//TRANSLIT', $subject);
						$subject = preg_replace("/[^a-zA-Z0-9\/_|+ -\<\>]/", '', $subject);

						$corpo = html_entity_decode($corpo, ENT_QUOTES, 'UTF-8');
						$corpo = mb_convert_encoding($corpo, "UTF-8", "auto");
						$corpo = iconv('UTF-8', 'ASCII//TRANSLIT', $corpo);
						$corpo = preg_replace("/[^a-zA-Z0-9\/_|+ -\<\>]/", '', $corpo);

						$mail->Subject = $subject;
						$mail->MsgHTML($corpo);
						$mail->AddAttachment($path);
						if(!$mail->Send()){
							$errore .= "Problema durante l'invio della chiave privata. ";
							// $errore .= "Errore classe: ".$mail->ErrorInfo;
							echo 'alert("'.$errore.'");';
						} else {
							if (!empty($_SESSION["ente"]["email_chavi_bkInterno"])) {
								$mail = new PHPMailer();
								$mail->IsSMTP();
								$mail->Host = $config["smtp_server"];
								$mail->Port = $config["smtp_port"];
								if ($config["smtp_ssl"] == true) $mail->SMTPSecure = 'ssl';
								$mail->SMTPAuth = true;
								$mail->Username = $config["mittente_mail"];
								$mail->Password = $config["smtp_password"];
								$mail->SetFrom($config["mittente_mail"],$config["nome_sito"]);
								$mail->AddAddress($_SESSION["ente"]["email_chavi_bkInterno"]);
								$mail->Subject = $subject;
								$mail->MsgHTML($corpo);
								$mail->AddAttachment($path);
								$mail->Send();
							}

							if (!$_SESSION["developEnviroment"]) {
								$mail = new PHPMailer();
								$mail->IsSMTP();
								$mail->Host = $config["smtp_server"];
								$mail->Port = $config["smtp_port"];
								if ($config["smtp_ssl"] == true) $mail->SMTPSecure = 'ssl';
								$mail->SMTPAuth = true;
								$mail->Username = $config["mittente_mail"];
								$mail->Password = $config["smtp_password"];
								$mail->SetFrom($config["mittente_mail"],$config["nome_sito"]);
								$mail->AddAddress($config["casella_bk_pkey"]);
								$mail->Subject = "ID UNIVOCO G-" . $codice_gara . " - " . $subject;
								$mail->MsgHTML($corpo);
								$mail->AddAttachment($path);
								$mail->Send();
							}
							$public = openssl_pkey_get_details($res);
							$bits = $public["bits"];
							$public = $public["key"];

							$bind = array();
							$bind[":codice_gara"] = $codice_gara;
							$bind[":public"] = $public;

							$strsql = "UPDATE b_gare SET public_key = :public WHERE codice = :codice_gara ";
							$risultato = $pdo->bindAndExec($strsql,$bind);
						}
						unlink($path);
						unset($mail);
					} else {
						$errore .= "Problema durante l'esportazione della chiave privata.\n";
						echo 'alert("'.$errore.'");';
					}
				}

				if (isset($_POST["cpv"])) {
					if ($_POST["cpv"] != "")  {
						$bind = array();
						$bind[":codice_gara"] = $codice_gara;
						$strsql = "DELETE FROM r_cpv_gare WHERE codice_gara = :codice_gara ";
						$risultato = $pdo->bindAndExec($strsql,$bind);
						$array_cpv = explode(";",$_POST["cpv"]);
						foreach($array_cpv as $cpv) {
							if ($cpv != "") {
								$insert_cpv["codice"] = $cpv;
								$insert_cpv["codice_gara"] = $codice_gara;
								$salva->nome_tabella = "r_cpv_gare";
								$salva->operazione = "INSERT";
								$salva->oggetto = $insert_cpv;
								$codici_cpv[] = $salva->save();
							}
						}
					}
				}
				if (isset($_POST["importi"])) {
					if (is_array($_POST["importi"]))  {
						$bind = array();
						$bind[":codice_gara"] = $codice_gara;
						$strsql = "DELETE FROM b_importi_gara WHERE codice_gara = :codice_gara ";
						$risultato = $pdo->bindAndExec($strsql,$bind);
						$array_importi = $_POST["importi"];
						$codici_importi = array();
						$relazioni = array();
						$chiavi_relazioni_escluse = array();
						$chiavi_relazioni_incluse = array();
						$chiavi_testi = array();
						$chiavi_date = array();
						$chiavi_password = array();
						$chiavi_ignora = array("x","y");
						foreach($array_importi as $importo) {
							$importo["codice_gara"] = $codice_gara;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "b_importi_gara";
							$salva->operazione = "INSERT";
							$salva->oggetto = $importo;
							$codici_importi[] = $salva->save();
						}
					}
				}

				$href = "/gare/pannello.php?codice=" . $codice_gara;
				$href = str_replace('"',"",$href);
				$href = str_replace(' ',"-",$href);
				if ($_POST["operazione"]=="UPDATE") {
					?>
					alert('Modifica effettuata con successo');
					<?
				} elseif ($_POST["operazione"]=="INSERT") {

					$href = "/gare/pannello.php?codice=" . $codice_gara;
					$href = str_replace('"',"",$href);
					$href = str_replace(' ',"-",$href);
					?>alert('Inserimento effettuato con successo');<?
				}
				?>window.location.href = '<? echo $href ?>';<?
			} else {
				?>alert("Errore nel salvataggio. Riprovare");<?
			}
		} else {
			if (isset($msg_cpv) && $msg_cpv != "") {
				?>jalert('Categorie CPV incompatibili con i bandi disponibili:<br><ul><? echo $msg_cpv ?></ul>');<?
			}
			if (isset($msg_derivata)) {
				?>jalert("<? echo $msg_derivata ?>");<?
			}
			if (isset($msg_limite)) {
				?>jalert("<? echo $msg_limite ?>");<?
			}
		}
		if (isset($elaborazioneApi)) $return = ob_get_clean();
	}
}
?>
