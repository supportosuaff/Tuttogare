<?
include_once("../../config.php");
$edit = false;
$errore = "";
if (isset($_SESSION["codice_utente"])) {
	$edit = check_permessi("concorsi",$_SESSION["codice_utente"]);
	if (!$edit) {
		die();
	}
} else {
	die();
}

if (!$edit) {
	die();
} else {
	if (isset($_POST["operazione"])) {
		if ($_POST["operazione"]=="INSERT") {
			$bind = array();
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			if (!empty($_POST["gara"]["anno"])) {
				$sql = "SELECT MAX(CAST(SUBSTRING(id,".(strlen($_POST["gara"]["anno"])+2).",length(id)-".(strlen($_POST["gara"]["anno"])).") AS UNSIGNED)) as id FROM b_concorsi WHERE codice_gestore = :codice_ente AND anno = :anno ";
				$bind[":anno"] = $_POST["gara"]["anno"];
			} else {
				$sql = "SELECT MAX(CAST(id AS UNSIGNED)) as id FROM b_concorsi WHERE codice_gestore = :codice_ente ";
			}
			$sql.= " GROUP BY codice_gestore ";
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount() > 0) {
				$rec = $ris->fetch(PDO::FETCH_ASSOC);
				$_POST["gara"]["id"] = $rec["id"] + 1;
			} else {
				$_POST["gara"]["id"] = 1;
			}
			if (!empty($_POST["gara"]["anno"])) $_POST["gara"]["id"] = $_POST["gara"]["anno"] . "/" . $_POST["gara"]["id"];
			$_POST["gara"]["stato"] = 1;
		} else {
			$_POST["gara"]["codice"] = $_POST["codice"];
		}

		$salva = new salva();
		$salva->debug = FALSE;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_concorsi";
		$salva->operazione = $_POST["operazione"];
		$salva->oggetto = $_POST["gara"];
		$codice_gara = $salva->save();
		if ($codice_gara != false) {

			log_concorso($_SESSION["ente"]["codice"],$codice_gara,$_POST["operazione"],"Dati preliminari");

			if ($_POST["operazione"]=="INSERT" && $_SESSION["gerarchia"] == 2) {
				$utente = array();
				$utente["codice_gara"] = $codice_gara;
				$utente["codice_ente"] = $_SESSION["ente"]["codice"];
				$utente["codice_utente"] = $_SESSION["codice_utente"];

				$salva->nome_tabella = "b_permessi_concorsi";
				$salva->operazione = "INSERT";
				$salva->oggetto = $utente;
				$permesso = $salva->save();
			}

			$bind = array();
			$bind[":codice_gara"] = $codice_gara;

			$strsql = "SELECT b_concorsi.* FROM b_concorsi WHERE b_concorsi.codice = :codice_gara AND b_concorsi.public_key = ''";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$res = openssl_pkey_new();
				$path = sys_get_temp_dir() . "/" . rand() . ".pem";
				if (openssl_pkey_export_to_file($res,$path)) {

					$destinatario_chiave = (empty($_SESSION["ente"]["email_chiavi"])) ? $_SESSION["record_utente"]["email"] : $_SESSION["ente"]["email_chiavi"];

					$corpo = "In allegato alla presente si invia la chiave privata necessaria all'apertura delle offerte per il concorso di progettazione <br><br><strong>ID " . $record_gara["id"] . " - " . $record_gara["oggetto"] . "</strong><br><br>";
					$corpo.= "Distinti Saluti<br><br>";

					$subject = $config["nome_sito"] . " - IMPORTANTE - Invio chiave privata - ID " . $record_gara["id"] . " - Concorso di progettazione - " . $record_gara["oggetto"];
					$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
					$subject = mb_convert_encoding($subject, "UTF-8", "auto");
					$subject = iconv('UTF-8', 'ASCII//TRANSLIT', $subject);
					$subject = preg_replace("/[^a-zA-Z0-9\/_|+ -\<\>]/", '', $subject);

					$corpo = html_entity_decode($corpo, ENT_QUOTES, 'UTF-8');
					$corpo = mb_convert_encoding($corpo, "UTF-8", "auto");
					$corpo = iconv('UTF-8', 'ASCII//TRANSLIT', $corpo);
					$corpo = preg_replace("/[^a-zA-Z0-9\/_|+ -\<\>]/", '', $corpo);


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
					$mail->Subject = $subject;
					$mail->MsgHTML($corpo);
					$mail->AddAttachment($path);
					if(!$mail->Send()){
						$errore .= "Problema durante l'invio della chiave privata. ";
						$errore .= "Errore classe: ".$mail->ErrorInfo;
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
							$mail->Subject = "ID UNIVOCO C-" . $codice_gara . " - " . $subject;
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

						$strsql = "UPDATE b_concorsi SET public_key = :public WHERE codice = :codice_gara ";
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
					$strsql = "DELETE FROM r_cpv_concorsi WHERE codice_gara = :codice_gara ";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					$array_cpv = explode(";",$_POST["cpv"]);
					foreach($array_cpv as $cpv) {
						if ($cpv != "") {
							$insert_cpv["codice"] = $cpv;
							$insert_cpv["codice_gara"] = $codice_gara;
							$salva->nome_tabella = "r_cpv_concorsi";
							$salva->operazione = "INSERT";
							$salva->oggetto = $insert_cpv;
							$codici_cpv[] = $salva->save();
						}
					}
				}
			}

			$href = "/concorsi/pannello.php?codice=" . $codice_gara;
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			if ($_POST["operazione"]=="UPDATE") {
				?>
				alert('Modifica effettuata con successo');
				<?
			} elseif ($_POST["operazione"]=="INSERT") {

				$href = "/concorsi/pannello.php?codice=" . $codice_gara;
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
}
?>
