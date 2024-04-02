<?
session_start();
include_once("../../../config.php");
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
if ($edit && !$lock) {
	$bind = array();
	$bind[":codice_gara"] = $_POST["codice_gara"];
	$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice_gara";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	if ($risultato->rowCount()>0) {
		$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
		log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Scadenze II Fase");
		$operazione = "INSERT";
		$codice = 0;

		$bind = array();
		$bind[":codice_gara"] = $_POST["codice_gara"];
		$bind[":codice_lotto"] = $_POST["codice_lotto"];
		$sql = "SELECT * FROM b_2fase WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto";
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
		$salva->nome_tabella = "b_2fase";
		$salva->operazione = $operazione;
		$salva->oggetto = $_POST;
		$codice_fase = $salva->save();

		if ($codice_fase != false) {

			$salva->nome_tabella = "b_gare";
			$salva->operazione = "UPDATE";
			$salva->oggetto = ["codice"=>$_POST["codice_gara"],"data_accesso"=>$_POST["data_chiarimenti"]];
			$salva->save();


			$sql_backup = "SELECT * FROM r_partecipanti_Ifase WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto ";
			$ris_backup = $pdo->bindAndExec($sql_backup,$bind);
			if ($ris_backup->rowCount()==0) {
				$sql_buste = "SELECT * FROM b_buste WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto ";
				$ris_buste = $pdo->bindAndExec($sql_buste,$bind);
				if ($ris_buste->rowCount() > 0) {
					while($busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "b_buste_Ifase";
						$salva->operazione = "INSERT";
						$salva->oggetto = $busta;
						if ($salva->save() != false) {
							$sql_delete = "DELETE FROM b_buste WHERE codice = :codice_busta";
							$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_busta"=>$busta["codice"]));
						}
					}
				}
				$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto ";
				$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
				if ($ris_partecipanti->rowCount() > 0) {
					while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
						$sql_log = "SELECT * FROM b_log_aperture WHERE codice_partecipante = :codice_partecipante ";
						$ris_log = $pdo->bindAndExec($sql_log,[":codice_partecipante"=>$partecipante["codice"]]);
						if ($ris_log->rowCount() > 0) {
							while($log = $ris_log->fetch(PDO::FETCH_ASSOC)) {
								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $_SESSION["codice_utente"];
								$salva->nome_tabella = "b_log_aperture_IFase";
								$salva->operazione = "INSERT";
								$salva->oggetto = $log;
								if ($salva->save() != false) {
									$sql_delete = "DELETE FROM b_log_aperture WHERE codice = :codice_log";
									$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_log"=>$log["codice"]));
								}
							}
						}
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_partecipanti_Ifase";
						$salva->operazione = "INSERT";
						$salva->oggetto = $partecipante;
						if ($salva->save() != false) {
							if ($partecipante["ammesso"]=="S") {
								$partecipante["conferma"] = 0;
								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $_SESSION["codice_utente"];
								$salva->nome_tabella = "r_partecipanti";
								$salva->operazione = "UPDATE";
								$salva->oggetto = $partecipante;
								$codice_partecipante = $salva->save();
							} else {
								$sql_delete = "DELETE FROM r_partecipanti WHERE codice = :codice_partecipante";
								$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_partecipante"=>$partecipante["codice"]));
							}
						}
					}
				}
			}

			$_POST["data_inizio"] = datetime2mysql($_POST["data_inizio"]);
			$_POST["data_fine"] = datetime2mysql($_POST["data_fine"]);
			$bind = array();
			$bind[":codice_gara"] = $_POST["codice_gara"];
			$bind[":codice_lotto"] = $_POST["codice_lotto"];

			$sql_partecipanti = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND ammesso = 'S' AND escluso = 'N'";
			$ris_partecipanti = $pdo->bindAndExec($sql_partecipanti,$bind);
			if ($ris_partecipanti->rowCount()>0) {

				$oggetto = "Invito alla presentazione dell'offerta per la gara: " . $record_gara["oggetto"];

				$corpo = "La S.V. &egrave; stata invitata ad inviare la propria offerta per la gara in oggetto<br>";
				$corpo.= "Si specifica che le offerte potranno essere presentate dal " . mysql2completedate($_POST["data_inizio"]) . " al " . mysql2completedate($_POST["data_fine"]) . "<br>";
				$corpo.= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" .

				$record_gara["oggetto"] . "</strong></a><br><br>";

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
