<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		if (is_operatore()) {
			$edit = true;
		} else {
			$edit = check_permessi("gare/elaborazione",$_SESSION["codice_utente"]);
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		die();
	} else {
		if (isset($_POST["operazione"])) {

			$_POST["codice_ente"] = $_SESSION["ente"]["codice"];
			$_POST["cod_allegati"] = array_filter(explode(';', $_POST["cod_allegati"]));
			foreach ($_POST["cod_allegati"] as $k => $v) { if(! is_numeric($v)) unset($_POST["cod_allegati"][$k]); }
			$_POST["cod_allegati"] = implode(";", $_POST["cod_allegati"]);
				$bind = array();
				$bind[":codice_gara"] = $_POST["codice_gara"];
				$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice_gara AND b_gare.data_accesso > now()";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				if ($risultato->rowCount()>0) {
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "b_quesiti";
					$salva->operazione = $_POST["operazione"];
					$salva->oggetto = $_POST;
					$codice = $salva->save();
					if ($codice != false) {
						$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
						$oggetto = "Richiesta chiarimento sulla gara: " . $record_gara["oggetto"];
						$sql_operatore = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
						$ris_operatore = $pdo->bindAndExec($sql_operatore,array(":codice_utente"=>$_SESSION["codice_utente"]));
						$intestazione = "E' stato ";
						if ($ris_operatore->rowCount() > 0) {
							$record_operatore = $ris_operatore->fetch(PDO::FETCH_ASSOC);
							$intestazione = "L'Operatore Economico ";
							$intestazione .= ((!empty($record_operatore["ragione_sociale"])) ? $record_operatore["ragione_sociale"] : $_SESSION["record_utente"]["cognome"] . " " . $_SESSION["record_utente"]["nome"]);
							$intestazione .= " - ".$_SESSION["record_utente"]["pec"]." - ha ";
						}
						$corpo = $intestazione . "richiesto un chiarimento riguardante la gara:<br>";
						$corpo.= "<br><a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\"><strong>" . $record_gara["oggetto"] . "</strong></a><br><br>";
						$corpo.= "<h2>" . $_POST["testo"] . "</h2>";
						$corpo.= "<br><br>Distinti Saluti<br><br>";

						$corpo_allegati = "";
						if(! empty($_POST["cod_allegati"])) {
							$cod_allegati = implode(",", explode(";", $_POST["cod_allegati"]));
							$corpo_allegati = "<strong>Allegati</strong><br><table width=\"100%\">";
							$allegati = $pdo->bindAndExec("SELECT * FROM b_allegati WHERE codice IN ({$cod_allegati})")->fetchAll(PDO::FETCH_ASSOC);
							if(count($allegati) > 0) {
								$i = 0;
								$att_path = "https://gare.comune.roma.it/documenti/allegati/{$record_gara["codice"]}/";
								foreach ($allegati as $allegato) {
									$class= "even";
									if($i%2!=0) $class = "odd";
									$corpo_allegati .= '<tr class="'.$class.'">';
									$corpo_allegati .= '<td style="vertical-align:middle" width="10"><img src="https://gare.comune.roma.it/img/'.substr($allegato["nome_file"],-3).'.png"></td>';
									$corpo_allegati .= '<td><strong><a href="'.$att_path.$allegato["nome_file"].'" target="_blank">'.$allegato["titolo"].'</a></strong></td>';
									$corpo_allegati .= "</tr>";
									$i++;
								}
							}
	        		$corpo_allegati .= "</table>";
						}

						$cod_allegati = "";

						$mailer = new Communicator();
						$mailer->oggetto = $oggetto;
						$mailer->corpo = "{$corpo}<br><br>{$corpo_allegati}";
						$mailer->codice_pec = -1;
						$mailer->comunicazione = true;
						$mailer->codice_gara = $record_gara["codice"];
						$mailer->sezione = "gara";
						$mailer->coda = false;
						$mailer->destinatari = $_SESSION["codice_utente"];
						$esito = $mailer->send();

						$pec_conferma = getIndirizzoConferma($record_gara["codice_pec"]);

						$mailer = new Communicator();
						$mailer->oggetto = $oggetto;
						$mailer->corpo = "{$corpo}<br><br>{$corpo_allegati}";
						$mailer->codice_pec = -1;
						$mailer->comunicazione = false;
						$mailer->codice_gara = $record_gara["codice"];
						$mailer->sezione = "gara";
						$mailer->type = 'comunicazione-gara';
						$mailer->coda = false;
						$mailer->destinatari = $pec_conferma;
						$esito = $mailer->send();

					}
					if ($esito === true) {
							?>
								alert("Inserimento effettuato con successo");
								window.location.reload();
				      <?
					} else {
						?>
						alert("Si sono verificati degli errori durante l'invio. Riprovare - <?= $esito ?>");
						<?
					}
				} else {
  				?>
  					alert('Si sono verificati degli errori durante il salvataggio. Riprovare');
  				<?
  			}
			} else {
				?>
					alert('Gara non trovata o termini scaduti');
				<?
			}
	}



?>
