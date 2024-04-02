<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		if (is_operatore()) {
			$edit = true;
		} else {
			$edit = check_permessi("concorsi",$_SESSION["codice_utente"]);
		}
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

			$_POST["codice_ente"] = $_SESSION["ente"]["codice"];

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_quesiti_concorsi";
			$salva->operazione = $_POST["operazione"];
			$salva->oggetto = $_POST;
			$codice = $salva->save();
			if ($codice != false) {
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$strsql= "SELECT b_concorsi.* FROM b_concorsi WHERE b_concorsi.codice = :codice_gara";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount()>0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					$oggetto = "Richiesta chiarimento sul concorso: " . $record_gara["oggetto"];
					$sql_operatore = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
					$ris_operatore = $pdo->bindAndExec($sql_operatore,array(":codice_utente"=>$_SESSION["codice_utente"]));
					$intestazione = "E' stato ";
					if ($ris_operatore->rowCount() > 0) {
						$record_operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);
						$intestazione = "L'Operatore Economico ";
						$intestazione .= ((!empty($record_operatore["ragione_sociale"])) ? $record_operatore["ragione_sociale"] : $_SESSION["record_utente"]["cognome"] . " " . $_SESSION["record_utente"]["nome"]);
						$intestazione .= " - ".$_SESSION["record_utente"]["pec"]." - ha ";
					}
					$corpo = $intestazione . "richiesto un chiarimento riguardante la concorso:<br>";
					$corpo.= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/concorsi/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $record_gara["oggetto"] . "</strong></a><br><br>";
					$corpo.= "<h2>" . $_POST["testo"] . "</h2>";
					$corpo.= "<br><br>Distinti Saluti<br><br>";

					$corpo_allegati = "";
					$cod_allegati = "";

					$mailer = new Communicator();
					$mailer->oggetto = $oggetto;
					$mailer->corpo = $corpo;
					$mailer->codice_pec = -1;
					$mailer->comunicazione = true;
					$mailer->codice_gara = $record_gara["codice"];
					$mailer->sezione = "concorsi";
					$mailer->coda = false;
					$mailer->destinatari = $_SESSION["codice_utente"];
					$esito = $mailer->send();

					$pec_conferma = getIndirizzoConferma($record_gara["codice_pec"]);

					$mailer = new Communicator();
					$mailer->oggetto = $oggetto;
					$mailer->corpo = $corpo;
					$mailer->codice_pec = -1;
					$mailer->comunicazione = true;
					$mailer->codice_gara = $record_gara["codice"];
					$mailer->sezione = "concorsi";
					$mailer->coda = false;
					$mailer->type = 'comunicazione-concorso';
					$mailer->destinatari = $pec_conferma;
					$esito = $mailer->send();
			}
			if ($esito === true) {
					?>
						alert("Inserimento effettuato con successo");
						$("#testo").val("");
		      <?
			} else {
				?>
				alert("Si sono verificati degli errori durante l'invio. Riprovare - <?= $esito ?>");
				<?
			}
		} else {
			?>
				alert('Si sono verificati degli errori durante il salvataggio. Riprovare');
			<?
		}
	}
}



?>
