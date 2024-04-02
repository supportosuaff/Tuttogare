<?
	use Dompdf\Dompdf;
	use Dompdf\Options;

	include_once("../../../config.php");
	include_once($root."/inc/p7m.class.php");
	include_once($root."/layout/top.php");

	$public = true;
	if (isset($_POST["codice_gara"]) && is_operatore()) {

		$codice_gara = $_POST["codice_gara"];

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_concorsi.* FROM b_concorsi
								WHERE b_concorsi.codice = :codice ";
		$strsql .= "AND b_concorsi.annullata = 'N' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		if ($risultato->rowCount() > 0) {

			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$i = 0;
			$open = false;
			$last = array();
			$fase_attiva = array();

			$sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara  ";
			$ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice_gara"=>$record_gara["codice"]));
			if ($ris_fasi->rowCount() > 0) {
				$open = true;
				while($fase = $ris_fasi->fetch(PDO::FETCH_ASSOC)) {
					if ($fase["attiva"]=="S") {
						if ($i > 0) $open = false;
						$last = $fase_attiva;
						$fase_attiva = $fase;
					}
					$i++;
				}
			}

			if ($open) {
				$accedi = true;
			} else if (!empty($last["codice"])) {
				$sql_check = "SELECT * FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
								WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND r_partecipanti_concorsi.conferma = 1 AND r_partecipanti_concorsi.ammesso = 'S'
								AND r_partecipanti_concorsi.escluso = 'N' AND r_partecipanti_utenti_concorsi.codice_utente = :codice_utente ";
				$ris_check = $pdo->bindAndExec($sql_check,array(":codice_gara"=>$record_gara["codice"],":codice_fase"=>$last["codice"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_check->rowCount() > 0) $accedi = true;
			}

		if ($accedi && !empty($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"])) {
			$partecipante = $_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]];
			$print_form = true;
			?>
			<h1>FORMULAZIONE OFFERTA - ID <? echo $record_gara["id"] ?></h1>
			<h2><? echo $record_gara["oggetto"] ?> - Fase: <?= $fase_attiva["oggetto"] ?></h2>
			<?

			if (strtotime($fase_attiva["scadenza"]) > time()) {

				$strsql = "SELECT b_fasi_concorsi_buste.* FROM b_fasi_concorsi_buste ";
				$ris_buste = $pdo->bindAndExec($strsql,array());
				if ($ris_buste->rowCount() > 0) {
						$buste = array();
						$msg = "";
						$error = false;
						while($busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
							$buste[$busta["codice"]] = false;
							$sql_in = "SELECT b_buste_concorsi.* FROM b_buste_concorsi WHERE codice_gara = :codice_gara AND codice_busta = :codice_busta AND codice_partecipante = :codice_partecipante ";
							$ris_in = $pdo->bindAndExec($sql_in,array(":codice_busta"=>$busta["codice"],":codice_gara"=>$record_gara["codice"],":codice_partecipante"=>$partecipante["codice"]));
							if ($ris_in->rowCount()>0) {
								 $buste[$busta["codice"]] = true;
								 $presented = $ris_in->fetch(PDO::FETCH_ASSOC);
								 $msg .= "<li><h2>" . $busta["nome"] . "</h2><ul>";
								 if (!empty($presented["md5"])) $msg.= "<li>MD5: <strong>" . $presented["md5"] . "</strong></li>";
								 if (!empty($presented["sha1"])) $msg.= "<li>SHA1: <strong>" . $presented["sha1"] . "</strong></li>";
								 if (!empty($presented["sha256"])) $msg.= "<li>SHA256: <strong>" . $presented["sha256"] . "</strong></li>";
								 $msg .= "</ul></li>";
							} else {
								$error = true;
								$msg.= "<li><h2 class=\"ui-state-error\">" . $busta["nome"] . " non trovata</h2></li>";
							}
						}
						if (!$error) {
							$update = array();
							$update["codice"] = $partecipante["codice"];
							$update["conferma"] = 1;
							$update["ammesso"] = 'S';

							$oggetto = "Conferma di partecipazione al concorso " . $record_gara["oggetto"] . " - Fase: " . $fase_attiva["oggetto"];

							$corpo = "Il concorrente ".$partecipante["identificativo"].",  ha partecipato al concorso:<br>";
							$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
							$corpo.= "Hash delle buste inviate: <ul>";
							$corpo.= $msg;
							$corpo.= "</ul><br><br>";

							$tmp_path = $config["doc_folder"] . "/concorsi/" . $record_gara["codice"] . "/" . $fase_attiva["codice"] . "/" . $partecipante["codice"]."_conferma.pdf";

							$options = new Options();
							$options->set('defaultFont', 'Helvetica');
							$options->setIsRemoteEnabled(true);
							$dompdf = new Dompdf($options);
							$dompdf->loadHtml($html);
							$dompdf->setPaper('A4', 'portrait');
							$dompdf->set_option('defaultFont', 'Helvetica');
							$dompdf->render();
							$content = $dompdf->output();
							file_put_contents($tmp_path, $content);
							if (file_exists($tmp_path)) {
								/* $timestamp = P7Manager::putTimestamp($tmp_path);
								if ($timestamp !== false) { */

									$salva = new salva();
									$salva->debug = false;
									$salva->codop = -1;
									$salva->nome_tabella = "r_partecipanti_concorsi";
									$salva->operazione = "UPDATE";
									$salva->oggetto = $update;
									$codice_partecipante = $salva->save();
									if ($codice_partecipante != false) {

										$sql = "SELECT * FROM r_partecipanti_concorsi WHERE codice = :codice";
										$partecipante = $_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]];
										$ris_partecipante = $pdo->bindAndExec($sql,array(":codice"=>$partecipante["codice"]));
										$ris_partecipante = $ris_partecipante->fetch(PDO::FETCH_ASSOC);
										$partecipante["conferma"] = $ris_partecipante["conferma"];
										$partecipante["timestamp"] = $ris_partecipante["timestamp"];
										$_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]] = $partecipante;
										?>
										<ul class="success">
											<?= $msg ?>
										</ul>
										<?

										$corpo.= "Distinti Saluti<br><br>";

										$pec_conferma = getIndirizzoConferma($record_gara["codice_pec"]);

										$mailer = new Communicator();
										$mailer->oggetto = $oggetto;
										$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
										$mailer->codice_pec = -1;
										$mailer->destinatari = $pec_conferma;
										$mailer->sezione = "concorsi";
										$mailer->codice_gara = $record_gara["codice"];
										$mailer->type = 'comunicazione-concorso';
										$mailer->coda = true;
										$esito = $mailer->send();
										?>
										<ul class="success">
			                <li>La partecipazione &egrave; stata ricevuta con successo.
		                    	<br>Un messaggio di posta elettronica certificata &egrave; stato inviato alla Stazione Appaltante per confermare l'operazione
													<a target="_blank" class="submit_big" href="/concorsi/partecipa/downloadRicevuta.php?codice_concorso=<?= $record_gara["codice"] ?>&codice_fase=<?= $fase_attiva["codice"] ?>">Scarica PDF Ricevuta</a><br>
		                  </li>
		                </ul>
										<?
									} else {
											$error = true;
										?>
										<h3 class="ui-state-error">Impossibile continuare: Errore nel salvataggio della conferma</h3>
										<?
									}
								/* } else {
								 	$error = true;
								 	?>
								 	<h3 class="ui-state-error">Impossibile continuare: Errore nella marcatura temporale di conferma</h3>
								 	<?
								 } */
							} else {
								$error = true;
								?>
								<h3 class="ui-state-error">Impossibile continuare: Errore durante la generazione del PDF di conferma</h3>
								<?
							}
						} else {
								$error = true;
							?>
							<h3 class="ui-state-error">Impossibile continuare: Si sono verificati degli errori</h3>
							<ul>
								<?= $msg ?>
							</ul>
							<?
						}
					} else {
						$error = true;
						?>
							<h3 class="ui-state-error">Impossibile continuare: Errore nella procedura</h3>
						<?
					}
				} else {
					$error = true;
					?>
						<h3 class="ui-state-error">Impossibile continuare: Termini scaduti</h3>
					<?
				}
			} else {
				$error = true;
				?>
				<h3 class="ui-state-error">Impossibile continuare: Privilegi insufficienti</h3>
				<?
			}
		} else {
			$error = true;
			?>
			<h3 class="ui-state-error">Gara inesistente o privilegi insufficienti</h3>
			<?
		}
		?>
		<a class="submit_big" style="background-color:#444"  href="/concorsi/partecipa/modulo.php?cod=<?= $codice_gara ?>">Ritorna al pannello</a>
		<?
	} else {
		echo "<h1>Gara inesistente o privilegi insufficienti</h1>";
	}
	include_once($root."/layout/bottom.php");
	?>
