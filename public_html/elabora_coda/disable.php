<?
	session_start();
	include_once("../../config.php");
	include_once($root."/inc/funzioni.php");

	if (isset($_POST["codice"]) && isset($_SESSION["codice_utente"]) && $_SESSION["gerarchia"]==="0") {
		if (is_numeric($_POST["codice"]) && $_POST["codice"] > 0) {
			$qu_list = $pdo->bindAndExec("SELECT b_coda.*, b_enti.pec AS principale, b_pec.pec
																		FROM b_coda LEFT JOIN b_enti ON b_coda.codice_ente = b_enti.codice
																		LEFT JOIN b_pec ON b_coda.codice_pec = b_pec.codice WHERE b_coda.codice = :codice",array(":codice"=>$_POST["codice"]));
			if ($qu_list->rowCount() > 0) {
				$alert = $qu_list->fetch(PDO::FETCH_ASSOC);
				if (!empty($alert["principale"]) || !empty($alert["pec"])) {
					$corpo = "Si segnala che la piattaforma non è in grado di inviare PEC agli OE in quanto la configurazione SMTP inserita è errata.<br>
										Si invita a contattare l’Help Desk al numero ".$_SESSION["numero_assistenza"]." per risolvere la problematica.<br>
										Distinti Saluti<br>
										<br>Il reparto di supporto";

					$mailer = new Communicator();
					$mailer->oggetto = "Configurazione PEC";
					$mailer->corpo = $corpo;
					$mailer->codice_pec = -1;
					$mailer->destinatari = (!empty($alert["pec"])) ? $alert["pec"] : $alert["principale"];
					$esito = $mailer->send();
					if ($esito !== true) {
						echo "alert('Errore nell'invio della pec')";
					} else {
						echo "alert('Reinvio della PEC completato')";
					}
				}
			}
		}
	}
	?>
