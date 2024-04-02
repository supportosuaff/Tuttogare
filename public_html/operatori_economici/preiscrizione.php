<?
session_start();
include_once("../../config.php");
include_once($root."/inc/funzioni.php");
if (registrazione_abilitata())	{
	if (!isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$errore = false;
		if (empty($_POST["tipo"])) $errore = true;
		if (isset($_POST["utenti"])) {
			foreach($_POST["utenti"] as $campo) {
				if (empty($campo)) $errore = true;
			}
		} else {
			$errore = true;
		}
		if (isset($_POST["operatori"])) {
			$campi = $_POST["operatori"];
			unset($campi["partita_iva"]);
			if(isset($_POST["tipo"]) && $_POST["tipo"] == 'PRO') unset($campi["ragione_sociale"]);
			foreach($campi as $campo) {
				if (empty($campo)) $errore = true;
			}
		} else {
			$errore = true;
		}
		if (empty($_POST["privacy"]) || empty($_POST["norme_tecniche"])) $errore = true;
		if (empty($_POST["cpv"])) $errore = TRUE;
		if ($errore) {
			?>alert('<?= traduci("Controllare i dati obbligatori") ?>');<?
			die();
		} else {
			$utente_modifica = -1;
			$bind = array();
			$bind[":pec"] = $_POST["utenti"]["pec"];
			$bind[":email"] = $_POST["utenti"]["email"];
			$strsql_utenti = "SELECT * FROM b_utenti WHERE pec = :pec OR email = :email";
			$risultato = $pdo->bindAndExec($strsql_utenti,$bind);
			if ($risultato->rowCount()>0) {
				?>
				alert("<?= traduci("e-mail") ?>/<?= traduci("pec") ?> <?= traduci("gia presente") ?>");
				<?
				die();
			} else {
				$bind = array();
				$bind[":tipo"] = $_POST["tipo"];
				$strsql_gruppi = "SELECT * FROM b_gruppi WHERE id = :tipo AND gerarchia > 2 ";
				$risultato_gruppi = $pdo->bindAndExec($strsql_gruppi,$bind);
				if ($risultato_gruppi->rowCount() == 0) {
					?>alert('<?= traduci("Controllare i dati obbligatori") ?> - 2');<?
				} else {
					$gruppo = $risultato_gruppi->fetch(PDO::FETCH_ASSOC);
					$_POST["utenti"]["password"] = password_hash(md5($_POST["utenti"]["password"]), PASSWORD_BCRYPT);
					$_POST["utenti"]["gruppo"] = $gruppo["codice"];
					$_POST["utenti"]["attivo"] = "N";
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $utente_modifica;
					$salva->nome_tabella = "b_utenti";
					$salva->operazione = "INSERT";
					$salva->oggetto = $_POST["utenti"];
					$codice_utente = $salva->save();
					if ($codice_utente > 0) {

						$salva->debug = false;
						$salva->codop = $codice_utente;
						$salva->nome_tabella = "b_password_log";
						$salva->operazione = "INSERT";
						$salva->oggetto = array("codice_utente"=>$codice_utente);
						$salva->save();

						$_SESSION["tmp_codice_utente"] = $codice_utente;
						$r_enti_operatori["cod_ente"]= $_SESSION["ente"]["codice"];
						$r_enti_operatori["cod_utente"]= $codice_utente;
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $utente_modifica;
						$salva->nome_tabella = "r_enti_operatori";
						$salva->operazione = "INSERT";
						$salva->oggetto = $r_enti_operatori;
						$codice_r = $salva->save();
						if ($codice_r > 0) {

							$_POST["operatori"]["codice_utente"] = $codice_utente;
							if(isset($_POST["tipo"]) && $_POST["tipo"] == 'PRO') {
								$_POST["operatori"]["ragione_sociale"] = $_POST["utenti"]["cognome"] . " " . $_POST["utenti"]["nome"];
							}
							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $utente_modifica;
							$salva->nome_tabella = "b_operatori_economici";
							$salva->operazione = "INSERT";
							$salva->oggetto = $_POST["operatori"];
							$codice_operatore = $salva->save();

							if ($codice_operatore > 0) {

								$risultato = $pdo->bindAndExec("DELETE FROM r_cpv_operatori WHERE codice_operatore = :codice_operatore", array(":codice_operatore" => $codice_operatore));
								if (! empty($_POST["cpv"])) {
									$array_cpv = explode(";",$_POST["cpv"]);
									$codici_cpv = array();
									foreach($array_cpv as $cpv) {
										if ($cpv != "") {
											$insert_cpv["codice"] = $cpv;
											$insert_cpv["codice_operatore"] = $codice_operatore;
											$insert_cpv["codice_utente"] = $codice_utente;
											$salva = new salva();
											$salva->debug = false;
											$salva->codop = $utente_modifica;
											$salva->nome_tabella = "r_cpv_operatori";
											$salva->operazione = "INSERT";
											$salva->oggetto = $insert_cpv;
											$codici_cpv[] = $salva->save();
										}
									}
								}

								$corpo = "Spett.le Operatore Economico,<br><br>
													la registrazione &egrave; avvenuta con successo ed &egrave; quindi possibile partecipare alle procedure di gara aperte pubblicate da {$config["nome_sito"]}.<br>
													<br>
													Prima di continuare &egrave; necessario confermare la tua iscrizione<br><br>
													<a title=\"Link di conferma - Sito esterno\" href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/operatori_economici/conferma.php?id=" . $codice_utente . "&email=" . urlencode($_POST["utenti"]["pec"]) . "\">" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/operatori_economici/conferma.php?id=" . $codice_utente . "&email=" . urlencode($_POST["utenti"]["pec"]) . "</a><br><br>
													Clicca o incolla il link nel tuo browser per continuare.<br>
													Il link sar&agrave; valido per le prossime 48 ore<br><br>
													Si  specifica che per partecipare alla procedure di gara ad inviti non &egrave; sufficiente la registrazione ma occorre richiedere l'iscrizione ad uno o 
													più elenchi dell'Albo Fornitori. Anche a tal fine, si consiglia di completare il profilo con tutte le informazioni a vostra disposizione quali ad es. 
													certificazioni di qualità, certificazioni SOA, ecc. in quanto potrebbero essere utilizzate per selezionare gli operatori economici da invitare.<br><br>
													Per effettuare l'iscrizione all'Albo Fornitori &egrave; sufficiente effettuare il login sulla piattaforma, 
													cliccare nel menu a sinistra Albo dei fornitori, selezionare l'elenco di proprio interesse e seguire le istruzioni ivi indicate.<br><br>
													Per procedere al completamento delle informazioni del suo profilo &egrave; sufficiente effettuare il login sulla piattaforma e
													cliccare nel menu a sinistra sul tasto che riporta il proprio nome e cognome.<br><br>
													";
								$mailer = new Communicator();
								$mailer->oggetto = "Conferma iscrizione";
								$mailer->corpo = $corpo;
								$mailer->codice_pec = -1;
								$mailer->destinatari = $_POST["utenti"]["pec"];
								$esito = $mailer->send();
								if (class_exists("syncERP")) {
									$syncERP = new syncERP();
									if (method_exists($syncERP,"sendOE")) {
										$syncERP->sendOE($codice_operatore);							
									}
								}
								if($esito) {
									?>window.location.href = '/operatori_economici/pre-iscrizione-success.php';<?
									die();
								} else {
									?>jalert('<?= traduci('errore-invio-pec-conferma') ?>');<?
									die();
								}
							} else {
								?>
								alert("<?= traduci('errore-salvataggio') ?> - 1");
								<?
								die();
							}
						} else {
							?>
							alert("<?= traduci('errore-salvataggio') ?> - 2");
							<?
							die();
						}
					} else {
						?>
						alert("<?= traduci('errore-salvataggio') ?> - 3");
						<?
						die();
					}
				}
				
			}
		}
	} else {
		?>
		alert("<?= traduci('errore-salvataggio') ?> - 4");
		<?
		die();
	}
} else {
	?>
	alert("<?= traduci('errore-salvataggio') ?> - 7");
	<?
	die();
}
?>
