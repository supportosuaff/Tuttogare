<?
session_start();
include_once("../../../../config.php");
include_once($root."/inc/funzioni.php");

$edit = false;
$lock = true;
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
	$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
	if ($codice_fase !== false) {
		$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
		$edit = $esito["permesso"];
		$lock = $esito["lock"];
	}
	if (!$edit) {
		die();
	}
} else {
	die();
}
if ($edit)
{

	include($root."/gare/pubblica/save_common.php");
	if (isset($codice_gara) && $codice_gara == $_POST["codice_gara"]) {
		$destinatari = [];
		$bind = array();
		$bind[":codice"] = $codice_gara;
		$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0) {
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
			$codici_utenti = array(); 
			$bind = array();
			$bind[":codice"] = $record_gara["codice"];
			$sql_estrazione = "SELECT b_estrazioni.*, b_bandi_albo.manifestazione_interesse
												 FROM b_estrazioni LEFT JOIN b_bandi_albo ON b_estrazioni.codice_bando = b_bandi_albo.codice WHERE b_estrazioni.codice_gara = :codice";
			$risultato_estrazione = $pdo->bindAndExec($sql_estrazione,$bind);
			if ($risultato_estrazione->rowCount()>0 && $record_gara["inviato_avviso"] != "S") {
				$write_inviti = false;
				if ($record_gara["pubblica"] > 0) $write_inviti = true;
				$estrazione = $risultato_estrazione->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":codice"] = $estrazione["codice"];
				$sql_estratti = "SELECT b_operatori_economici.codice_utente FROM
        b_operatori_economici JOIN r_estrazioni ON b_operatori_economici.codice = r_estrazioni.codice_operatore
        WHERE r_estrazioni.codice_estrazione = :codice AND r_estrazioni.selezionato = 'S'";
        $ris_estratti = $pdo->bindAndExec($sql_estratti,$bind);
        if ($ris_estratti->rowCount()>0) {
          while($estratto = $ris_estratti->fetch(PDO::FETCH_ASSOC)) $codici_utenti[] = $estratto["codice_utente"];
					if ($estrazione["manifestazione_interesse"]=="S" && isset($_POST["avvisa_esclusi"])) {
						$codici_selezionati = implode(",", $codici_utenti);
						$sql_esclusi = "SELECT codice_utente FROM r_partecipanti_albo WHERE codice_bando = :codice_bando AND codice_utente NOT IN (".$codici_selezionati.") AND r_partecipanti_albo.ammesso = 'S'";
						$ris_esclusi = $pdo->bindAndExec($sql_esclusi,array(":codice_bando"=>$estrazione["codice_bando"]));
						if ($ris_esclusi->rowCount() > 0) {
							$codici_esclusioni = array();
							while($escluso = $ris_esclusi->fetch(PDO::FETCH_ASSOC)) $codici_esclusioni[] = $escluso["codice_utente"];
						}
					}
				}
			} else {
				$write_inviti = true;
				if (isset($_POST["indirizzi"]) && $_POST["indirizzi"] != "") $codici_utenti = explode(";",$_POST["indirizzi"]);
				if (isset($_POST["partecipanti"])) {
					foreach($_POST["partecipanti"] AS $partecipante) {
						if (isset($partecipante["codice_utente"])) unset($partecipante["codice_utente"]);
						if(! empty($partecipante["pec"])) {
							$bind = [":pec"=>$partecipante["pec"]];
							$strsql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti on b_utenti.codice = b_operatori_economici.codice_utente WHERE b_utenti.pec = :pec ORDER BY ragione_sociale LIMIT 0,1";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount()>0) {
								$record = $risultato->fetch(PDO::FETCH_ASSOC);
								$partecipante["codice_utente"] = $record["codice_utente"];
							}
						}
						if (!empty($partecipante["codice_utente"])) {
							$codici_utenti[] = $partecipante["codice_utente"];
						} else if (!empty($partecipante["pec"])) {
							$partecipante["codice_gara"] = $record_gara["codice"];
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "temp_inviti";
							$salva->operazione = "INSERT";
							$salva->oggetto = $partecipante;
							if ($salva->save()) $destinatari[] = $partecipante["pec"];
						}
					}
				}
			}
			if (count($codici_utenti)>0 && $write_inviti) {
				$sql_check = "SELECT codice FROM r_inviti_gare WHERE codice_gara = :codice_gara AND codice_utente = :codice_utente";
				$sql = "INSERT INTO r_inviti_gare (codice_gara,codice_utente) VALUES (:codice_gara,:codice_utente)";
				$update_inviti = $pdo->prepare($sql);
				$update_inviti->bindValue(":codice_gara",$_POST["codice_gara"]);
				$check_inviti = $pdo->prepare($sql_check);
				$check_inviti->bindValue(":codice_gara",$_POST["codice_gara"]);
				foreach ($codici_utenti as $codice_utente) {
					if ($codice_utente != "") {
						$check_inviti->bindValue(":codice_utente", $codice_utente);
						$check_inviti->execute();
						if ($check_inviti->rowCount() == 0) {
							$update_inviti->bindValue(":codice_utente", $codice_utente);
							$update_inviti->execute();
						}
						$destinatari[] = $codice_utente;
					}
				}
			}
			if ($record_gara["pubblica"] > 0 && $record_gara["inviato_avviso"] != "S") {
				$label_conferma = "Pubblicazione";
				$bind = array();
				$bind[":codice_gara"] = $record_gara["codice"];
				$sql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice_gara";
				$ris_invitati = $pdo->bindAndExec($sql,$bind);
				if ($ris_invitati->rowCount() > 0) {
					while($destinatario = $ris_invitati->fetch(PDO::FETCH_ASSOC))
					if (in_array($destinatario["codice_utente"], $destinatari) === false) $destinatari[] = $destinatario["codice_utente"];
				}
				$sql = "SELECT * FROM temp_inviti WHERE codice_gara = :codice_gara AND attivo = 'S'";
				$ris_invitati = $pdo->bindAndExec($sql,$bind);
				if ($ris_invitati->rowCount() > 0) {
					while($destinatario = $ris_invitati->fetch(PDO::FETCH_ASSOC)) {
						if (in_array($destinatario["pec"], $destinatari) === false) $destinatari[] = $destinatario["pec"];
					}
				}
			}
			if (count($destinatari) > 0 && $record_gara["pubblica"] > 0) {
				$bind = array();
				$bind[":codice_gara"] = $record_gara["codice"];
				$oggetto = "Invito procedura " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];

				$corpo = "La S.V. ha ricevuto un invito per presentare un'offerta per la gara:<br>";
				$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
				$corpo.= "Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\">";
				$corpo.= $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
				$corpo.= "</a><br><br>";
				$corpo.= "Distinti Saluti<br><br>";

				$corpo_allegati = "";
				$sql = "SELECT * FROM b_allegati WHERE codice_gara = :codice_gara AND sezione = 'gara' AND online = 'S' ORDER BY cartella, titolo";
				$ris_allegati = $pdo->bindAndExec($sql,$bind);
				$corpo_allegati = "<strong>Allegati</strong><br><table width=\"100%\">";
				$cod_allegati = array();
				if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
					$i = 0;
					while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
						$cod_allegati[] = $allegato["codice"];
						$class= "even";
						$i++;
						if ($i%2!=0) $class = "odd";
						$corpo_allegati  .= "<tr class=\"". $class . "\">";
						$corpo_allegati  .= "<td width=\"10\"><img src=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/img/" . substr($allegato["nome_file"],-3) . ".png\" alt=\"File " . substr($allegato["nome_file"],0,-3) . "\" style=\"vertical-align:middle\"></td>";
						$corpo_allegati  .= "<td><strong><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/documenti/allegati/" . $allegato["codice_gara"] . "/" . $allegato["nome_file"] . "\" target=\"_blank\">" . $allegato["titolo"] . "</a></strong></td>";
						$corpo_allegati  .= "</tr>";
					}
				}
				$cod_allegati = implode(";",$cod_allegati);
				$corpo_allegati .= "</table>";
				if (!empty($destinatari)) {
					$mailer = new Communicator();
					$mailer->oggetto = $oggetto;
					$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo.$corpo_allegati;
					$mailer->codice_pec = $record_gara["codice_pec"];
					$mailer->comunicazione = true;
					$mailer->coda = true;
					$mailer->sezione = "gara";
					$mailer->codice_gara = $record_gara["codice"];
					$mailer->cod_allegati = $cod_allegati;
					$mailer->destinatari = $destinatari;
					$esito = $mailer->send();
				}
				if (isset($codici_esclusioni)) {
					$oggetto = "Esito sorteggio procedura " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];

					$corpo = "Si comunica che la S.V. <strong>non è stata sorteggiata</strong> al fine di presentare un'offerta alla procedura di gara:<br>";
					$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
					$corpo.= "Distinti Saluti<br><br>";

					$mailer = new Communicator();
					$mailer->oggetto = $oggetto;
					$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
					$mailer->codice_pec = $record_gara["codice_pec"];
					$mailer->comunicazione = true;
					$mailer->coda = true;
					$mailer->sezione = "gara";
					$mailer->codice_gara = $record_gara["codice"];
					$mailer->destinatari = $codici_esclusioni;
					$esito = $mailer->send();
				}
				$bind = array();
				$bind[":codice"] = $_POST["codice_gara"];
				$sql = "UPDATE b_gare SET inviato_avviso = 'S' WHERE codice = :codice";
				$update_inviato = $pdo->bindAndExec($sql,$bind);
			}
		}
		$href = "/gare/pannello.php?codice=" . $_POST["codice_gara"];
		$href = str_replace('"',"",$href);
		$href = str_replace(' ',"-",$href);
		?>
		alert('<?= (isset($label_conferma)) ? $label_conferma : "Modifica" ?> effettuata con successo');
		window.location.href = '<? echo $href ?>';
		<?
	} else {
		?>
		alert('Errore nel salvataggio. Riprovare.');
		<?
	}
}
		?>
