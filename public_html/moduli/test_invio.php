<?
	include_once("../../config.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_POST)) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if ($edit) {
				if (isset($_POST["ente"])) $_POST = $_POST["ente"];
				if (isset($_POST["pec"]) && is_array($_POST["pec"])) {
					foreach ($_POST["pec"] as $_POST);
				}
				$messaggio = "Invio eseguito correttamente!";
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->Host = $_POST["smtp"];
				$mail->Port = $_POST["smtp_port"];
				if (isset($_POST["usa_ssl"])) $mail->SMTPSecure = 'ssl';
				$mail->SMTPAuth = true;
				$mail->Username = $_POST["pec"];
				$mail->Password = $_POST["password"];
				$mail->SetFrom($_POST["pec"]);
				$mail->Timeout = 30;
				$mail->AddAddress($_POST["pec"]);
				$mail->Subject = $_SESSION["config"]["nome_sito"] . " - " . "Test configurazione PEC";
				$mail->MsgHTML("La configurazione inserita funziona correttamente!");
				if(!$mail->Send()){
					$messaggio = "Problema durante l'invio.\n";
					$messaggio.= "Errore classe: ".$mail->ErrorInfo;
				}
				unset($mail);
				echo $messaggio;
		}
	}
