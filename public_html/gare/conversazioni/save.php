<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/conversazioni/edit.php'";
			$risultato = $pdo->query($strsql);
			if ($risultato->rowCount()>0) {
				$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
				$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}
		if ($edit && !$lock)
	 {
		if (isset($_POST["codice_gara"])) {
			$href = "/gare/conversazioni/edit.php?codice=".$_POST["codice_gara"];
			if ((isset($_POST["codice_gara"]) && !empty($_POST["codice_gara"])) && $_POST["testo"] != "") {
				$sql = "SELECT * FROM b_gare WHERE codice = :codice_gara ";
				$ris_gara = $pdo->bindAndExec($sql,array(":codice_gara"=>$_POST["codice_gara"]));
				if ($ris_gara->rowCount() > 0) {
					$gara = $ris_gara->fetch(PDO::FETCH_ASSOC);
					$msg = array();
					$operazione = "INSERT";
					$msg["codice_ente"] = $_SESSION["ente"]["codice"]; $gara["codice_ente"];
					$msg["codice_gara"] = $_POST["codice_gara"];
					$msg["testo"] = $_POST["testo"];
					$msg["cod_allegati"] = $_POST["cod_allegati"];

					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_messaggi";
					$salva->operazione = "INSERT";
					$salva->oggetto = $msg;
					$codice_msg = $salva->save();
					if ($codice_msg > 0) {
						log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"INSERT","Messaggio interno #" . $codice_msg);
						?>
						alert('Inserimento effettuato con successo');
						<?
						$sql = "SELECT b_utenti.* FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
										WHERE b_utenti.attivo = 'S' AND b_utenti.codice <> :codice_utente AND (
											(
												b_utenti.codice_ente = :codice_ente AND b_gruppi.id = 'ADM'
											) OR
											(
												b_utenti.codice_ente = :codice_gestore AND b_gruppi.id = 'ADM'
											) OR b_utenti.codice IN
											(
												SELECT codice_utente FROM b_permessi WHERE codice_gara = :codice_gara
											)
										)";
						$ris = $pdo->bindAndExec($sql,array(":codice_utente"=>$_SESSION["codice_utente"],":codice_gestore"=>$_SESSION["ente"]["codice"],":codice_ente"=>$gara["codice_ente"],":codice_gara"=>$msg["codice_gara"]));
						if ($ris->rowCount() > 0) {
							$indirizzi = array();
							while($utente = $ris->fetch(PDO::FETCH_ASSOC)) {
								if (!empty($utente["email"])) $indirizzi[] = $utente["email"];
							}
							if (count($indirizzi)>0) {

									$oggetto = "Nuovo messaggio per la gara: " . $gara["oggetto"];
									$corpo = "L'utente " . $_SESSION["record_utente"]["cognome"] . " " . $_SESSION["record_utente"]["nome"] . " ha scritto:<br>";
									$corpo.= $msg["testo"];

									$mailer = new Communicator();
									$mailer->oggetto = $oggetto;
									$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
									$mailer->codice_pec = -1;
									$mailer->comunicazione = false;
									$mailer->coda = true;
									$mailer->destinatari = $indirizzi;
									$esito = $mailer->send();

							}
						}
					} else {
						?>alert('Errore nel salvataggio');<?
					}
				} else {
					?>alert('Errore nel salvataggio');<?
				}
			} else {
				?>alert('Errore nel salvataggio');<?
			}
		?>
			window.location.href = '<? echo $href ?>';
		<?
	}
}
?>
