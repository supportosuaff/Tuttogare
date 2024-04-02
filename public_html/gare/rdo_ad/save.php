<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/rdo_ad/index.php'";
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
		if (isset($_POST["operazione"])) {
			$errore = false;
			$_POST["codice_ente"] = $_SESSION["ente"]["codice"];
			if ($_POST["operazione"]=="UPDATE") {
				$bind = array(":codice"=>$_POST["codice"]);
				$sql = "SELECT * FROM b_rdo_ad WHERE codice = :codice AND data_apertura > 0";
				$ris = $pdo->bindAndExec($sql,$bind);
				if ($ris->rowCount()>0 && $_POST["data_apertura"] == "") $errore = true;
			}
			if (!$errore) {
				$bind = array();
				$bind[":codice_gara"] = $_POST["codice_gara"];
				$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura, b_procedure.invito AS invito FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice_gara";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_rdo_ad";
					$salva->operazione = $_POST["operazione"];
					$salva->oggetto = $_POST;
					$codice_rdo = $salva->save();
					if ($codice_rdo != false) {
						log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],$_POST["operazione"],"Richiesta Offerta #" . $codice_rdo);
						$codici_utenti = [];
						if (isset($_POST["indirizzi"]) && $_POST["indirizzi"] != "") {
							$codici_utenti = explode(";",$_POST["indirizzi"]);
						}
						$destinatari = [];
						if (isset($_POST["partecipanti"])) {
							foreach($_POST["partecipanti"] AS $partecipante) {
								$continua = true;
								if(! empty($partecipante["pec"])) {
									$bind = [":pec"=>$partecipante["pec"]];
									$strsql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti on b_utenti.codice = b_operatori_economici.codice_utente WHERE b_utenti.pec = :pec ORDER BY ragione_sociale LIMIT 0,1";
									$risultato = $pdo->bindAndExec($strsql,$bind);
									if ($risultato->rowCount()>0) {
										$continua = false;
										$record = $risultato->fetch(PDO::FETCH_ASSOC);
										$codici_utenti[] = $record["codice_utente"];
									}
									if ($continua) {
										$partecipante["codice_gara"] = $_POST["codice_gara"];
										$partecipante["codice_richiesta"] = $codice_rdo;
										$salva = new salva();
										$salva->debug = false;
										$salva->codop = $_SESSION["codice_utente"];
										$salva->nome_tabella = "temp_inviti";
										$salva->operazione = "INSERT";
										$salva->oggetto = $partecipante;
										if ($salva->save()) {
											$destinatari[] = $partecipante["pec"];
											if (!empty($partecipante["email"])) $destinatari[] = $partecipante["email"];
										}
									}
								}
							}
						}
						if (count($codici_utenti)>0 || count($destinatari) > 0 || $_POST["operazione"]=="UPDATE") {
							$oggetto = "Invito a presentare offerta " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];

							$corpo = "La S.V. ha ricevuto un invito a presentare un'offerta per la procedura:<br>
												<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>
												<strong>Termini richieste chiarimenti:</strong> " . $_POST["data_chiarimenti"] . "<br>
												<strong>Termini presentazione offerta:</strong> " . $_POST["data_scadenza"] . "<br><br>
												Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\">";
							$corpo.= $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli
												</a><br><br>
												Distinti Saluti<br><br>";

							$bind = array();
							$bind[":codice_gara"] = $record_gara["codice"];
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

							if (count($destinatari) > 0) {
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

							if (count($codici_utenti) > 0) {

								$destinatari_email = [];

								$mailer = new Communicator();
								$mailer->oggetto = $oggetto;
								$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo.$corpo_allegati;
								$mailer->codice_pec = $record_gara["codice_pec"];
								$mailer->comunicazione = true;
								$mailer->coda = true;
								$mailer->sezione = "gara";
								$mailer->codice_gara = $record_gara["codice"];
								$mailer->cod_allegati = $cod_allegati;
								$mailer->destinatari = $codici_utenti;
								$esito = $mailer->send();

								foreach($codici_utenti AS $codice_utente) {
									$sql="SELECT b_utenti.pec, b_utenti.email, b_utenti.cognome, b_utenti.nome, b_operatori_economici.*
												FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice WHERE b_operatori_economici.codice_utente = :codice_utente ";
									$ris_operatori_economici = $pdo->bindAndExec($sql,array(":codice_utente"=>$codice_utente)) ;
									if ($ris_operatori_economici->rowCount() > 0) {
										$operatore_economico = $ris_operatori_economici->fetch(PDO::FETCH_ASSOC);
										if (isset($_POST["sendMail"])) $destinatari_email[] = $operatore_economico["email"];
										$sql = "SELECT * FROM r_partecipanti WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara";
										$ris_partecipante = $pdo->bindAndExec($sql,array(":codice_utente"=>$codice_utente,":codice_gara"=>$_POST["codice_gara"]));
										if ($ris_partecipante->rowCount() > 0) {
											$codice_partecipante = $ris_partecipante->fetch(PDO::FETCH_ASSOC)["codice"];
										} else {
											$ragione_sociale = $operatore_economico["ragione_sociale"];
											if (empty($ragione_sociale)) $ragione_sociale = $operatore_economico["nome"] . " " . $operatore_economico["cognome"];
											$partecipante = array();
											$partecipante["codice_gara"] = $_POST["codice_gara"];
											$partecipante["codice_lotto"] = $pdo->go("SELECT codice FROM b_lotti WHERE codice_gara = :codice",[":codice"=>$record_gara["codice"]])->fetch(PDO::FETCH_COLUMN);
											$partecipante["codice_operatore"] = $operatore_economico["codice"];
											$partecipante["codice_utente"] = $codice_utente;
											$partecipante["partita_iva"] = $operatore_economico["codice_fiscale_impresa"];
											$partecipante["ragione_sociale"] = $ragione_sociale;
											$partecipante["pec"] = $operatore_economico["pec"];
											$partecipante["identificativoEstero"] = $operatore_economico["identificativoEstero"];
											$partecipante["conferma"] = "0";
											$partecipante["ammesso"] = 'S';
											$salva = new salva();
											$salva->debug = false;
											$salva->codop = $_SESSION["codice_utente"];
											$salva->nome_tabella = "r_partecipanti";
											$salva->operazione = "INSERT";
											$salva->oggetto = $partecipante;
											$codice_partecipante = $salva->save();
										}
										$sql = "SELECT * FROM r_inviti_gare WHERE codice_utente = :codice_utente AND codice_gara = :codice_gara";
										$ris_inviti = $pdo->bindAndExec($sql,array(":codice_utente"=>$codice_utente,":codice_gara"=>$_POST["codice_gara"]));
										if ($ris_inviti->rowCount() == 0) {
											$invito = array();
											$invito["codice_gara"] = $_POST["codice_gara"];
											$invito["codice_utente"] = $codice_utente;
											$salva = new salva();
											$salva->debug = false;
											$salva->codop = $_SESSION["codice_utente"];
											$salva->nome_tabella = "r_inviti_gare";
											$salva->operazione = "INSERT";
											$salva->oggetto = $invito;
											$salva->save();
										}
										if ($codice_partecipante > 0) {
											$r_integrazione = array();
											$r_integrazione["codice_rdo"] = $codice_rdo;
											$r_integrazione["codice_ente"] = $_SESSION["ente"]["codice"];
											$r_integrazione["codice_partecipante"] = $codice_partecipante;
											$r_integrazione["codice_utente"] = $codice_utente;

											$salva = new salva();
											$salva->debug = false;
											$salva->codop = $_SESSION["codice_utente"];
											$salva->nome_tabella = "r_rdo_ad";
											$salva->operazione = "INSERT";
											$salva->oggetto = $r_integrazione;
											$r_comunicazioni = $salva->save();
										}
									}
								}
								if (count($destinatari_email) > 0) {
									$mailer = new Communicator();
									$mailer->oggetto = $oggetto;
									$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo.$corpo_allegati;
									$mailer->codice_pec = $record_gara["codice_pec"];
									$mailer->comunicazione = true;
									$mailer->coda = true;
									$mailer->sezione = "gara";
									$mailer->codice_gara = $record_gara["codice"];
									$mailer->cod_allegati = $cod_allegati;
									$mailer->destinatari = $destinatari_email;
									$mailer->send();
								}
							}
							$bind = array();
							$bind[":codice"] = $_POST["codice_gara"];
							$bind[":data_scadenza"] = datetime2mysql($_POST["data_scadenza"]);
							$bind[":data_accesso"] = datetime2mysql($_POST["data_chiarimenti"]);

							$sql = "UPDATE b_gare SET stato = '3', data_scadenza = :data_scadenza,	data_accesso = :data_accesso, pubblica='1' WHERE codice = :codice";
							$update_stato = $pdo->bindAndExec($sql,$bind);
						} else { ?>
							alert('Nessun destinatario indicato');
					<? }
						if (class_exists("syncERP")) {
							$syncERP = new syncERP();
							if (method_exists($syncERP,"sendUpdateRequest")) {
								$syncERP->sendUpdateRequest($_POST["codice_gara"]);
							}
						}
						
						if ($_POST["operazione"]=="UPDATE") {

							$href = "/gare/rdo_ad/index.php?codice=".$_POST["codice_gara"];
							?>
							alert('Modifica effettuata con successo');
							window.location.href = '<? echo $href ?>';
							<?
						} elseif ($_POST["operazione"]=="INSERT") {
							$href = "/gare/rdo_ad/index.php?codice=".$_POST["codice_gara"];
							?>
							alert('Inserimento effettuato con successo');
							window.location.href = '<? echo $href ?>';
							<?
						}
					} else {
						?>
						alert('Errore nel salvataggio. Si prega di riprovare');
						<?
					}
			} else {
				?>
				alert('Errore nel salvataggio. Si prega di riprovare');
				<?
			}
		} else {
			?>
			alert('Errore nel salvataggio. La data di apertura non può essere vuota');
			<?
		}
	}
}



?>
