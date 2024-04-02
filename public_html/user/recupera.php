<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");

		$bind = array(":username" => $_POST["email_recupero"]);

		$sql = "SELECT * FROM b_utenti WHERE email = :username";
		$ris = $pdo->bindAndExec($sql,$bind);

		if ($ris->rowCount()===1) {
			$record = $ris->fetch(PDO::FETCH_ASSOC);
			$tmp = array();
			$tmp["codice"] = $record["codice"];
			$tmp["password_request"] = date("Y-m-d H:i:s");
			$tmp["password_token"] = tokenGen();

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = 0;
			$salva->nome_tabella = "b_utenti";
			$salva->operazione = "UPDATE";
			$salva->oggetto = $tmp;
			if ($salva->save() > 0) {
				$link = $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/user/change_pwd.php?email=" . base64_encode($record["email"]) . "&token=" . base64_encode($tmp["password_token"]);
				$link = "<a href='".$link."' target=\"_blank\" title=\"Cambia password\">".$link."</a>";

				$messaggio = "<h1>" . $_SESSION["config"]["nome_sito"]. "</h1>";
				$messaggio.= "In data " . date("d/m/Y") . " alle ore " . date("H:i") . " hai richiesto il recupero della password per l'accesso ai servizi del portale ";
				$messaggio.= $link;
				$messaggio.= "<br><br>Clicca o incolla il link nel browser per creare una nuova password. Il link sar&agrave; attivo per le prossime 48 ore<br><br>";
				$oggetto = "Cambio Password";

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
					$errore = "Problema durante l'invio.\n";
					?>
					$("#messaggio_recupero_password").css("color","#F00");
					$("#messaggio_recupero_password").html("<?= $errore ?>);
					$("#messaggio_recupero_password").slideDown();
					<?
				} else {
					$msg = "La procedura per creare una nuova password è stata inviata alla tua casella e-mail";
					if(! empty($record["pec"])) $msg = "La procedura per creare una nuova password è stata inviata sia alla tua casella e-mail che alla tua pec";
					?>
		        $("#recupera_password").dialog('destroy');
		        $("#recupera_password").html("<strong>Recupero Eseguito</strong><br><br><?= $msg ?>.");
		      	$("#recupera_password").dialog({
							position:["center",50],
							modal:true
						});
		        $("#recupera_password").show();
		      <?
		    }
		} else {
			?>
			$("#messaggio_recupero_password").css("color","#F00");
			$("#messaggio_recupero_password").html("Errore nella generazione della mail");
			$("#messaggio_recupero_password").slideDown();
			<?
		}
	} else {
		?>
    $("#messaggio_recupero_password").css("color","#F00");
    $("#messaggio_recupero_password").html("Nessun utente è associato a quest'indirizzo e-mail");
    $("#messaggio_recupero_password").slideDown();
    <?
	}
?>
