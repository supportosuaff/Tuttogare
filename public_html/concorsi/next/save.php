<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$lock = true;
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
	$codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
	if ($codice_fase !== false) {
		$esito = check_permessi_concorso($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
		$edit = $esito["permesso"];
		$lock = $esito["lock"];
	}
	if (!$edit) {
		die();
	}
} else {
	die();
}
if ($edit && !empty($_POST["fase"]))
	{
		$esito_salvataggio = false;
		$codice_gara = $_POST["codice_gara"];
		$codice_fase = $_POST["codice"];

    $bind = array();
		$bind[":codice_gara"] = $codice_gara;
    $bind[":codice_fase"] = $_POST["fase_old"];
		$sql = "SELECT r_partecipanti_concorsi.*, r_partecipanti_utenti_concorsi.codice_operatore, r_partecipanti_utenti_concorsi.codice_utente,
						r_partecipanti_utenti_concorsi.partita_iva, r_partecipanti_utenti_concorsi.ragione_sociale, r_partecipanti_utenti_concorsi.identificativoEstero,
						r_partecipanti_utenti_concorsi.pec
						FROM r_partecipanti_concorsi
						JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
						WHERE codice_gara = :codice_gara AND codice_fase = :codice_fase AND (conferma = TRUE OR conferma IS NULL) AND escluso = 'N'";
		$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
		if ($ris_partecipanti->rowCount()>0) {
			$bind = array();
			$bind[":codice_gara"] = $codice_gara;
	    $bind[":codice_fase"] = $codice_fase;
	    $sql = "SELECT b_concorsi.codice_pec, b_concorsi.oggetto AS concorso, b_fasi_concorsi.* FROM b_fasi_concorsi JOIN b_concorsi ON b_fasi_concorsi.codice_concorso = b_concorsi.codice
							WHERE b_fasi_concorsi.codice_concorso = :codice_gara AND b_fasi_concorsi.codice = :codice_fase AND attiva = 'N' ";
	    $ris = $pdo->bindAndExec($sql,$bind);
	    if ($ris->rowCount() > 0) {
	      $fase = $ris->fetch(PDO::FETCH_ASSOC);
	      $_POST["fase"]["codice"] = $fase["codice"];
	      $_POST["fase"]["attiva"] = "S";
				$salva = new salva();
			  $salva->debug = false;
	      $salva->codop = $_SESSION["codice_utente"];
	      $salva->nome_tabella = "b_fasi_concorsi";
	      $salva->operazione = "UPDATE";
	      $salva->oggetto = $_POST["fase"];
	      $esito_salvataggio = $salva->save();
	    }
		}

		if ($esito_salvataggio != false) {

			$oggetto = "Ammissione alla fase " . $fase["oggetto"] . " del concorso " . $fase["concorso"];
			$oggetto_escluso = "Esclusione alla fase " . $fase["oggetto"] . " del concorso " . $fase["concorso"];

			$begin = "La S.V. &egrave; stata ammessa alla fase:<br>";
			$begin_escluso = "La S.V. &egrave; stata esclusa dalla fase per la seguente motivazione:<br>";

			$corpo = "<br><strong>" . $fase["oggetto"] . "</strong><br><br>";
			$corpo.= "del concorso <br><strong>" . $fase["concorso"] . "</strong><br><br>";
			$corpo.= "Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/concorsi/id" . $fase["codice_concorso"] . "-dettagli\" title=\"Dettagli concorso\">";
			$corpo.= $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/concorsi/id" . $fase["codice_concorso"] . "-dettagli";
			$corpo.= "</a><br><br>";
			$corpo.= "Distinti Saluti<br><br>";
			$codici_ammessi = array();
			$codici_esclusi = array();
			while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
				if (is_numeric($partecipante["codice_utente"])) {
					if ($partecipante["ammesso"] == "S") {
						$codici_ammessi[] = $partecipante["codice_utente"];
					} else {
						$codici_esclusi[$partecipante["codice_utente"]] = $partecipante["motivazione"];
					}
				}
			}
			if (count($codici_ammessi)) {
				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $begin . $corpo;
				$mailer->codice_pec = $fase["codice_pec"];
				$mailer->comunicazione = true;
				$mailer->coda = true;
				$mailer->sezione = "concorsi";
				$mailer->codice_gara = $fase["codice_concorso"];
				$mailer->destinatari = $codici_ammessi;
				$esito = $mailer->send();
			}
			if (count($codici_esclusi)) {
				foreach($codici_esclusi AS $codice_utente => $motivazione) {
					$mailer = new Communicator();
					$mailer->oggetto = $oggetto_escluso;
					$mailer->corpo = "<h2>" . $oggetto_escluso . "</h2>" . $begin_escluso . $motivazione . $corpo;
					$mailer->codice_pec = $fase["codice_pec"];
					$mailer->comunicazione = true;
					$mailer->coda = true;
					$mailer->sezione = "concorsi";
					$mailer->codice_gara = $fase["codice_concorso"];
					$mailer->destinatari = $codice_utente;
					$esito = $mailer->send();
				}
			}

			log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Apertura fase successiva");

			$href = "/concorsi/pannello.php?codice=" . $_POST["codice_gara"];
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			?>
			alert('Operazione effettuata con successo');
			window.location.href = '<? echo $href ?>';
			<?
		} else {
			?>
			alert('Errore nel salvataggio. Riprovare.');
			<?
		}
	}
		?>
