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
if ($edit && !$lock) {
	$ribasso = array();

	$esito = array();
	$esito["numero_atto_esito"] = $_POST["numero_atto_esito"];
	$esito["data_atto_esito"] = $_POST["data_atto_esito"];
	$esito["allegati_esito"] = $_POST["allegati_esito"];
	$esito["stato"] = 7;
	if ($_POST["numero_atto_esito"] == "" && $_POST["data_atto_esito"] == "") {
		$esito["stato"] = 4;
	} else {
		$esito["pubblica"] = 2;
	}
	$contributo = floatval(0);
	$bind = array();
	$bind[":codice"]=$_POST["codice_gara"];
	$sql = "SELECT codice_ente, codice_gestore FROM b_concorsi WHERE codice = :codice";
	$ris = $pdo->bindAndExec($sql,$bind);
	if($ris->rowCount()>0){
		$rec = $ris->fetch(PDO::FETCH_ASSOC);
		$codice_ente = $rec["codice_ente"];
		$codice_gestore = $rec["codice_gestore"];

		$esito["codice"] = $_POST["codice_gara"];

		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_concorsi";
		$salva->operazione = "UPDATE";
		$salva->oggetto = $esito;
		$codice_gara = $salva->save();
		if ($codice_gara !== false)
			log_concorso($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Aggiudicazione definitiva",false);

			$href = "/concorsi/pannello.php?codice=".$_POST["codice_gara"];

			$bind = array();
			$bind[":codice_gara"]=$_POST["codice_gara"];

			$strsql= "SELECT b_concorsi.* FROM b_concorsi WHERE b_concorsi.codice = :codice_gara";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount()>0) {

				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				$avviso = array();
				$avviso["data"] = date("d-m-Y");
				$avviso["titolo"] = "Pubblicazione Esito - Concorso: " . $record_gara["oggetto"];
				$avviso["testo"] = "Si comunica che &egrave; stato pubblicato l'esito per il concorso in oggetto";
				$avviso["codice_gara"] = $record_gara["codice"];
				$avviso["codice_ente"] = $_SESSION["ente"]["codice"];


				$corpo_allegati = "";
				$cod_allegati = "";
				if (isset($_POST["allegati_esito"]) && $_POST["allegati_esito"] != "") {
					$cod_allegati = $_POST["allegati_esito"];
				}
				$avviso["cod_allegati"] = $cod_allegati;
				if (isset($_POST["avviso"])) {
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_avvisi_concorsi";
					$salva->operazione = "INSERT";
					$salva->oggetto = $avviso;
					$codice = $salva->save();
				}

				if (isset($_POST["pec"])) {
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$sql = "SELECT r_partecipanti_concorsi.*, r_partecipanti_utenti_concorsi.codice_operatore, r_partecipanti_utenti_concorsi.codice_utente,
									r_partecipanti_utenti_concorsi.partita_iva, r_partecipanti_utenti_concorsi.ragione_sociale, r_partecipanti_utenti_concorsi.identificativoEstero,
									r_partecipanti_utenti_concorsi.pec
									FROM r_partecipanti_concorsi
									JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
									WHERE codice_gara = :codice_gara AND (conferma = TRUE OR conferma IS NULL) AND ammesso = 'S' AND escluso = 'N'";
					$ris_partecipanti = $pdo->bindAndExec($sql,$bind);

					$oggetto = $avviso["titolo"];

					$corpo = "Si comunica che &egrave; stato pubblicato l'esito per il concorso:<br>";
					$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
					$corpo.= "Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/concorsi/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli concorso\">";
					$corpo.= $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/concorsi/id" . $record_gara["codice"] . "-dettagli";
					$corpo.= "</a><br><br>";
					$corpo.= "Distinti Saluti<br><br>";

					$codici_utenti = array();
					if ($ris_partecipanti->rowCount()>0) {
						while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
							if (is_numeric($partecipante["codice_utente"])) $codici_utenti[] = $partecipante["codice_utente"];
						}
					}
					if (count($codici_utenti)) {
						$mailer = new Communicator();
						$mailer->oggetto = $oggetto;
						$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
						$mailer->codice_pec = $record_gara["codice_pec"];
						$mailer->comunicazione = true;
						$mailer->coda = true;
						$mailer->sezione = "concorsi";
						$mailer->codice_gara = $record_gara["codice"];
						$mailer->destinatari = $codici_utenti;
						$esito = $mailer->send();
					}
				}
			}
			?>
			alert('Modifica effettuata con successo');
			window.location.href = '<? echo $href ?>';
		<? }
		} else { ?>
	alert('Si è verificato un errore');
	<? } ?>
