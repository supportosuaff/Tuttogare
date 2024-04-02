<?
	include_once("../../config.php");
	include_once($root."/layout/top.php");
	if (registrazione_abilitata() && isset($_GET["id"]) && isset($_GET["email"]))	{
		?><h1 style="text-align:center"><?= traduci('registrazione-oe') ?></h1><?
		$bind = array(
			":id" => $_GET["id"],
			":email_encode" => urldecode($_GET["email"])
		);

		$strsql = "SELECT b_utenti.*, b_gruppi.id AS id_gruppo, DATE_ADD(b_utenti.timestamp, INTERVAL 2 DAY) AS scadenza_registrazione
							 FROM b_utenti
							 JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
							 WHERE b_utenti.codice = :id
							 AND (b_utenti.pec = :email_encode OR b_utenti.pec = :email_encode )";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$utente = $risultato->fetch(PDO::FETCH_ASSOC);
			if ($utente["attivo"] == "N") {
				if(strtotime($utente["scadenza_registrazione"]) >= strtotime('now')) {
					$bind = array();
					$bind[":codice_utente"] = $utente["codice"];
					$sql = "SELECT b_operatori_economici.* FROM b_operatori_economici WHERE b_operatori_economici.codice_utente = :codice_utente ";
					$ris_operatore = $pdo->bindAndExec($sql,$bind);
					if ($ris_operatore->rowCount() > 0) {

						$msg = "";
						$operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);

						if (empty($utente["email"])) $msg .= "<li>" . traduci("e-mail") . " " . traduci("obbligatorio") . "</li>";
						if (empty($utente["nome"])) $msg .= "<li>" . traduci("Nome") . " " . traduci("obbligatorio") . "</li>";
						if (empty($utente["cognome"])) $msg .= "<li>" . traduci("Cognome") . " " . traduci("obbligatorio") . "</li>";
						if (empty($operatore["codice_fiscale_impresa"])) $msg .= "<li>" . traduci("codice fiscale") . " " . traduci("azienda") . " " . traduci("obbligatorio") . "</li>";
						if (empty($utente["pec"])) $msg .= "<li>" . traduci("pec") . " " . traduci("obbligatorio") . "</li>";
						// $bind=array(":codice_operatore"=>$operatore["codice"]);
						// $strsql = "SELECT * FROM r_cpv_operatori WHERE codice_operatore = :codice_operatore ";
						// $risultato = $pdo->bindAndExec($strsql,$bind);
						// if ($risultato->rowCount() == 0) $msg .= "<li>" . traduci("categorie merceologiche") . " " . traduci("obbligatorio") . "</li>";
						if ($msg != "") {
							?>
							<h2 style="color: #F30; text-align:center"><?= traduci("errore nella procedura di conferma") ?></h2>
							<ul class="ui-state-error">
								<li><h3><?= traduci("alert-errori-registrazione") ?></h3>
									<ul>
										<?= $msg ?>
									</ul>
								</li>
							</ul>
							<?
						} else {
							$bind = array(":codice" => $utente["codice"]);
							$strsql = "UPDATE b_utenti SET attivo = 'S', profilo_completo = 'N' WHERE codice = :codice ";
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount() > 0) {

								$corpo = "In data " . date("d/m/Y H:i:s") . ", l'operatore economico <strong>" . $operatore["ragione_sociale"] . "</strong>
								con codice fiscale <strong>" . $operatore["codice_fiscale_impresa"] . "</strong>  ha effettutato la registrazione al portale.";
								$mailer = new Communicator();
								$mailer->oggetto = "Iscrizione operatore economico";
								$mailer->corpo = $corpo;
								$mailer->codice_pec = -1;
								$mailer->destinatari = $_SESSION["ente"]["pec"];
								$mailer->type = "iscrizione-oe";
								$esito = $mailer->send();

								ob_start();
								?>
								<p>
									Spett.le Operatore Economico,<br>
									la registrazione &egrave; avvenuta con successo ed &egrave; gi&agrave; possibile procedere nell&#39;utilizzo del portale effettuando il login
									e potr&agrave; partecipare alle procedure di gara aperte bandite dall&#39;Ente.
								</p>
								<p>
									Ad ogni modo, le consigliamo di completare il profilo con tutte le informazioni a vostra disposizione quali ad es. certificazioni di qualit&agrave;,
									certificazioni SOA, ecc in quanto la stazione appaltante potrebbe utilizzarle in caso di procedura negoziata o affidamento diretto.
								</p>
								<p>
									Per procedere al completamento delle informazioni del suo profilo &egrave; sufficiente effettuare il login sulla piattaforma
									e cliccare nel menu a sinistra sul tasto che riporta il suo nome e cognome.
								</p>
								<?
								$html = ob_get_clean();
								$mailer = new Communicator();
								$mailer->oggetto = "Conferma iscrizione";
								$mailer->corpo = $html;
								$mailer->codice_pec = -1;
								$mailer->destinatari = $utente["pec"];
								$esito = $mailer->send();

								?>
					      <h2 style="color:#0C3; text-align:center"><?= traduci("CONFERMA DELLA REGISTRAZIONE AVVENUTA CON SUCCESSO") ?></h2>
								<h3 style="text-align:center"><?= traduci("msg-conferma-registrazione") ?></h3>
								<div class="clear"></div>
								<?
								$bind = array(':codice_ente' => $_SESSION["ente"]["codice"]);
								$strsql  = "SELECT * FROM b_bandi_albo WHERE (pubblica = '2' OR pubblica = '1') AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente)
														AND (b_bandi_albo.data_scadenza >= NOW() OR b_bandi_albo.data_scadenza = 0) AND manifestazione_interesse = 'N' ";
								$risultato = $pdo->bindAndExec($strsql,$bind);
								if ($risultato->rowCount() > 0) { ?><br><br><br>
									<h2 style="color: #F30; background-color: rgba(255, 51, 0, 0.1); text-align:center; padding:50px; border:3px dotted #F30;"><strong><?= traduci("attenzione") ?></strong><br>
									<?= traduci('msg-albi-conferma-1') ?><br>
									<?= traduci('msg-albi-conferma-2') ?><a href="/archivio_albo/index.php?scadute=0" title="<?= traduci("Albo fornitori") ?>"><?= traduci("iniziative attive") ?></a></h2>
								<? }
							} else {
								?>
								<h2 style="color: #F30; text-align:center"><?= traduci("errore nella procedura di conferma") ?><br>
								<?= traduci("contattare-helpdesk-numero") ?> <?= $_SESSION["numero_assistenza"] ?></h2>
								<?
							}
						}
					}	else {
						?>
						<h2 style="color: #F30; text-align:center"><?= traduci("errore nella procedura di conferma") ?><br>
						<?= traduci("contattare-helpdesk-numero") ?> <?= $_SESSION["numero_assistenza"] ?></h2>
						<?
					}
				} else {
					$_SESSION["oe"]["user"] = $utente;
					?>
					<h2 style="color:#F30; text-align:center; font-size: 2rem">
						<?= traduci('errore-conferma-scaduto') ?><br>
						<a href="/operatori_economici/link_conferma.php" style="color: #0F80FF"><?= traduci("clicca qui") ?></a> <?= traduci("send-new-link") ?>
					</h2>
					<?
				}
			} else {
				?>
				<h2 style="color:#0C3; text-align:center"><?= traduci("msg-confermato-accedi") ?></h2>
				<?
			}
		} else { ?>
			<h2 style="color: #F30; text-align:center"><?= traduci("errore nella procedura di conferma") ?></h2>
      <?= traduci("alert-generico-registrazione") ?>
			<div class="clear"></div>
		<? }
	} else {
			echo "<h1>".traduci("impossibile accedere")."</h1>";
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
	}

	include_once($root."/layout/bottom.php");
	?>
