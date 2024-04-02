<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$lock = true;
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
	$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
	if ($codice_fase !== false) {
		$esito = check_permessi_gara($codice_fase,$_SESSION["gara"]["codice"],$_SESSION["codice_utente"]);
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
	$bind = array();
	$bind[":codice_gara"] = $_POST["codice_gara"];
	$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice_gara";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
		log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Perfezionamento Asta");
		$operazione = "INSERT";
		$codice = 0;
		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		$bind[":codice_lotto"] = $_POST["codice_lotto"];
		$sql = "SELECT * FROM b_aste WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto";
		$ris = $pdo->bindAndExec($sql,$bind);
		if ($ris->rowCount()>0) {
			$operazione = "UPDATE";
			$existent = $ris->fetch(PDO::FETCH_ASSOC);
			$codice = $existent["codice"];
		}
		$_POST["codice"] = $codice;

		$salva = new salva();
		$salva->debug = false;
		$salva->codop = $_SESSION["codice_utente"];
		$salva->nome_tabella = "b_aste";
		$salva->operazione = $operazione;
		$salva->oggetto = $_POST;
		$codice_asta = $salva->save();

		if ($codice_asta != false) {
			$bind = array();
			$bind[":codice"]=$codice_asta;
			$sql = "SELECT * FROM b_aste WHERE codice = :codice";
			$ris = $pdo->bindAndExec($sql,$bind);
			if ($ris->rowCount()>0) {
				$asta = $ris->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind[":codice_gara"] = $_POST["codice_gara"];
				$bind[":codice_lotto"] = $_POST["codice_lotto"];
				$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND ammesso = 'S' AND escluso = 'N'  AND (conferma IS NULL OR conferma = TRUE)";
				$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
				if ($ris_partecipanti->rowCount()>0) {
					$visualizzazione = "Al buio";
					if ($asta["visualizzazione"] == "1") $visualizzazione = "Visibile";
					$oggetto = "Invito alla partecipazione  all'asta elettronica per la gara: " . $record_gara["oggetto"];

					$corpo = "La S.V. &egrave; stata invitata ad inviare la propria offerta per la gara in oggetto<br>";
					$corpo.= "Si specifica che l'asta sarà attiva con le seguenti caratteristiche:<br>
										<table>
										<tr><td>Apertura asta</td><td><strong>" . $asta["data_inizio"] . "</strong></td></tr>
										<tr><td>Chiusura asta</td><td><strong>" . $asta["data_fine"] . "</strong></td></tr>
										<tr><td>Tempo base</td><td><strong>" . $asta["tempo_base"] . " minuti</strong></td></tr>
										<tr><td>Rilancio minimo</td><td><strong>" . $asta["rilancio_minimo"] . " %</strong></td></tr>
										<tr><td>Visualizzazione altre offerte</td><td><strong>" . $visualizzazione . "</strong></td></tr>
										</table>";

					$corpo.= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $record_gara["oggetto"] . "</strong></a><br><br>";
					$corpo.= "<br><br>Distinti Saluti<br><br>";
					$corpo_allegati = "";
					$cod_allegati = "";
					if (isset($_POST["cod_allegati"]) && $_POST["cod_allegati"] != "" && preg_match("/^[0-9\;]+$/",$_POST["cod_allegati"])) {
						$cod_allegati = $_POST["cod_allegati"];
						$allegati = explode(";",$_POST["cod_allegati"]);
						$str_allegati = ltrim(implode(",",$allegati),",");
						$sql = "SELECT * FROM b_allegati WHERE codice IN (" . $str_allegati . ") AND online = 'S'";
						$ris_allegati = $pdo->query($sql);
						$corpo_allegati = "<strong>Allegati</strong><br><table width=\"100%\">";
						if (isset($ris_allegati) && ($ris_allegati->rowCount()>0)) {
							$i = 0;
							while ($allegato = $ris_allegati->fetch(PDO::FETCH_ASSOC)) {
								$class= "even";
								$i++;
								if ($i%2!=0) $class = "odd";
								$corpo_allegati  .= "<tr class=\"". $class . "\">";
								$corpo_allegati  .= "<td width=\"10\"><img src=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/img/" . substr($allegato["nome_file"],-3) . ".png\" alt=\"File " . substr($allegato["nome_file"],0,-3) . "\" style=\"vertical-align:middle\"></td>";
								$corpo_allegati  .= "<td><strong><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/documenti/allegati/" . $allegato["nome_file"] . "\" target=\"_blank\">" . $allegato["titolo"] . "</a></strong></td>";
								$corpo_allegati  .= "</tr>";
							}
						}
						$corpo_allegati .= "</table>";
					}

				$indirizzi = array();
				while ($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
					$indirizzi[] = $partecipante["codice_utente"];
				}
				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo.$corpo_allegati;
				$mailer->codice_pec = $record_gara["codice_pec"];
				$mailer->comunicazione = true;
				$mailer->coda = true;
				$mailer->sezione = "gara";
				$mailer->codice_gara = $record_gara["codice"];
				$mailer->codice_lotto = $_POST["codice_lotto"];
				$mailer->cod_allegati = $cod_allegati;
				$mailer->destinatari = $indirizzi;
				$esito = $mailer->send();
			}
		}

		$href = "/gare/pannello.php?codice=" . $_POST["codice_gara"];
		$href = str_replace('"',"",$href);
		$href = str_replace(' ',"-",$href);
		?>
		alert('Modifica effettuata con successo');
		window.location.href = '<? echo $href ?>';
		<?
		} else {
			?>
			alert('Errore nel salvataggio');
			<?
		}
	}
}
?>
