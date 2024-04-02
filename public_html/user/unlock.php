<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");

		$bind = array(":username" => $_POST["email"]);

		$sql = "SELECT * FROM b_utenti WHERE email = :username";
		$ris = $pdo->bindAndExec($sql,$bind);

		if ($ris->rowCount()===1) {
			$record = $ris->fetch(PDO::FETCH_ASSOC);
			$tmp = array();
			$tmp["codice"] = $record["codice"];
			$tmp["unlock_request"] = date("Y-m-d H:i:s");
			$tmp["unlock_token"] = tokenGen();

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = 0;
			$salva->nome_tabella = "b_utenti";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $tmp;
			if ($salva->save() > 0) {
				$link = $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/user/un-user.php?email=" . base64_encode($record["email"]) . "&token=" . base64_encode($tmp["unlock_token"]);
				$link = "<a href='".$link."' target=\"_blank\" title=\"Sblocca utente\">".$link."</a>";

				$messaggio = "<h1>" . $_SESSION["config"]["nome_sito"]. "</h1>";
				$messaggio.= "In data " . date("d/m/Y") . " alle ore " . date("H:i") . " hai richiesto lo sblocco dell'utenza per l'accesso ai servizi del portale <br><br>";
				$messaggio.= $link;
				$messaggio.= "<br><br>Clicca o incolla il link nel browser per sbloccare l'utente. Il link sar&agrave; attivo per le prossime 48 ore<br><br>";
				$oggetto = "Sblocco Utenza";

				if(! empty($record["pec"])) {
					$mailer = new Communicator();
					$mailer->oggetto = $oggetto;
					$mailer->corpo = $messaggio;
					$mailer->codice_pec = -1;
					$mailer->comunicazione = false;
					$mailer->coda = false;
					$mailer->destinatari = $record["pec"];
					$esito = $mailer->send();
				}

				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = $messaggio;
				$mailer->codice_pec = -3;
				$mailer->comunicazione = false;
				$mailer->coda = false;
				$mailer->destinatari = $record["email"];
				$esito = $mailer->send();
				if ($esito !=true) {
					header('HTTP/1.0 403 Forbidden');
					die();
				}
		} else {
			header('HTTP/1.0 403 Forbidden');
			die();
		}
	} else {
		header('HTTP/1.0 403 Forbidden');
		die();
	}


?>
