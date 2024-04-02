<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");
	$public = true;
	if (isset($_POST["codice_gara"]) && isset($_POST["codice_lotto"]) && is_operatore()) {

		$codice_gara = $_POST["codice_gara"];
		$codice_lotto = $_POST["codice_lotto"];

		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
		$strsql  = "SELECT b_gare.*, b_procedure.invito, b_procedure.fasi, b_procedure.mercato_elettronico FROM b_gare JOIN b_modalita ON b_gare.modalita = b_modalita.codice JOIN b_procedure ON b_gare.procedura = b_procedure.codice
								WHERE b_gare.codice = :codice ";
		$strsql .= "AND b_gare.annullata = 'N' AND b_modalita.online = 'S' ";
		$strsql .= "AND codice_gestore = :codice_ente ";
		$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
		$risultato = $pdo->bindAndExec($strsql,$bind);

		$accedi = false;

		if ($risultato->rowCount() > 0) {
			$bind = array();
			$bind[":codice"] = $codice_gara;
			$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

			$derivazione = "";
			$sql = "SELECT * FROM b_procedure WHERE codice = :codice_procedura";
			$ris = $pdo->bindAndExec($sql,array(":codice_procedura"=>$record_gara["procedura"]));
			if ($ris->rowCount()>0) {
				$rec_procedura = $ris->fetch(PDO::FETCH_ASSOC);
				$directory = $rec_procedura["directory"];
				$record["nome_procedura"] = $rec_procedura["nome"];
				$record["riferimento_procedura"] = $rec_procedura["riferimento_normativo"];
				if ($rec_procedura["mercato_elettronico"] == "S") $derivazione = "me";
				if ($rec_procedura["directory"] == "sda")  $derivazione = "sda";
				if ($rec_procedura["directory"] == "dialogo")  $dialogo = true;

			}

			$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice";
			$ris_inviti = $pdo->bindAndExec($strsql,$bind);
			if ($ris_inviti->rowCount()>0) {
				$bind[":codice_utente"] = $_SESSION["codice_utente"];
				$strsql = "SELECT * FROM r_inviti_gare WHERE codice_gara = :codice AND r_inviti_gare.codice_utente = :codice_utente";
				$ris_invitato = $pdo->bindAndExec($strsql,$bind);
				if ($ris_invitato->rowCount()>0) $accedi = true;
			} else {
				if($record_gara["invito"] == "N" || !empty($derivazione)) {
					$accedi = true;
				}
			}
			if ($derivazione != "") {
				$sql_abilitato = "SELECT * FROM r_partecipanti_".$derivazione." WHERE codice_bando = :codice_derivazione AND ammesso = 'S' AND codice_utente = :codice_utente ";
				$ris_abilitato = $pdo->bindAndExec($sql_abilitato,array(":codice_derivazione"=>$record_gara["codice_derivazione"],":codice_utente"=>$_SESSION["codice_utente"]));
				if ($ris_abilitato->rowCount() == 0) {
					$accedi = false;
				}
			}
		}
		if ($accedi) {
			$print_form = true;
			$record_gara["tipologie_gara"] = "";
			$bind = array();
			$bind[":tipologia"] = $record_gara["tipologia"];
			$sql = "SELECT tipologia FROM b_tipologie WHERE b_tipologie.codice = :tipologia";
			$ris_tipologie = $pdo->bindAndExec($sql,$bind);
			if ($ris_tipologie->rowCount()>0) {
				$rec_tipologia = $ris_tipologie->fetch(PDO::FETCH_ASSOC);
				$record_gara["tipologie_gara"] .= $rec_tipologia["tipologia"] . " ";
			}
			?>
			<h1><?= traduci("RAGGRUPPAMENTO") ?> - ID <? echo $record_gara["id"] ?></h1>
			<h2><strong><? echo traduci(trim($record_gara["tipologie_gara"])) ?></strong> - <? echo $record_gara["oggetto"] ?></h2>
			<?
			$bind = array();
			$bind[":codice_gara"] = $record_gara["codice"];
			$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara ORDER BY codice";
			$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
			if ($ris_lotti->rowCount() > 0) {
				$print_form =false;
				$sql_lotti = "SELECT * FROM b_lotti WHERE codice_gara = :codice_gara AND codice = :codice_lotto ORDER BY codice";
				$bind[":codice_lotto"] = $codice_lotto;
				$ris_check_lotti = $pdo->bindAndExec($sql_lotti,$bind);
				if ($ris_check_lotti->rowCount() > 0) {
						$lotto = $ris_check_lotti->fetch(PDO::FETCH_ASSOC);
						if ($record_gara["modalita_lotti"]==1) {
							$bind =array();
							$bind[":codice_gara"] = $record_gara["codice"];
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND conferma = TRUE AND codice_utente = :codice_utente";
							$ris_partecipazioni = $pdo->bindAndExec($sql,$bind);
							if ($ris_partecipazioni->rowCount() > 0) {
								$bind = array();
								$bind[":lotto"] = $codice_lotto;
								$bind[":codice_gara"] = $record_gara["codice"];
								$bind[":codice_utente"] = $_SESSION["codice_utente"];
								$sql = "SELECT * FROM r_partecipanti WHERE codice_lotto = :lotto AND conferma = TRUE AND codice_gara = :codice_gara AND codice_utente = :codice_utente";
								$ris_partecipante_lotto = $pdo->bindAndExec($sql,$bind);
								if ($ris_partecipante_lotto->rowCount() > 0) {
									$print_form = true;
								} else {
									?>
									<h2 style="color:#C00"><?= traduci("Impossibile partecipare a più lotti") ?></h2>
									<?
								}
							} else {
								$print_form = true;
							}
					} else {
						$print_form = true;
					}
				}
			} else {
				$codice_lotto = 0;
			}

			if ($print_form) {

				$submit = false;

				if (isset($lotto)) {
					$codice_lotto = $lotto["codice"];
					echo "<div class=\"box\"><h3>" . $lotto["oggetto"] . "</h3>";
					echo $lotto["descrizione"]."</div>";
				}

				if (strtotime($record_gara["data_scadenza"]) > time()) {
						$submit = true;
				}
				if ($submit) {
					$error = false;
					$bind = array();
					$bind[":codice_gara"] = $record_gara["codice"];
					$bind[":codice_lotto"] = $codice_lotto;
					$bind[":codice_utente"] = $_SESSION["codice_utente"];
					$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_utente = :codice_utente AND codice_capogruppo = 0 ";
					$ris = $pdo->bindAndExec($sql,$bind);
					if ($ris->rowCount() > 0) {
						$partecipante = $ris->fetch(PDO::FETCH_ASSOC);

						$codice_partecipante = $partecipante["codice"];

						$sql = "DELETE FROM r_partecipanti WHERE codice_capogruppo = :codice_capogruppo";
						$ris_delete = $pdo->bindAndExec($sql,array(":codice_capogruppo"=>$codice_partecipante));

						$sql = "SELECT b_offerte_economiche.* FROM b_offerte_economiche WHERE
										codice_partecipante = :codice_partecipante ";
						$ris_old_offerta = $pdo->bindAndExec($sql,array(":codice_partecipante" => $codice_partecipante));
						$foundTecnica = false;
						if ($ris_old_offerta->rowCount()>0) {
							while($rec_delete = $ris_old_offerta->fetch(PDO::FETCH_ASSOC)) {
								if ($rec_delete["tipo"] == "tecnica") $foundTecnica = true;
								$sql_delete = "DELETE FROM b_dettaglio_offerte WHERE codice_offerta = :codice_offerta AND codice_partecipante = :codice_partecipante";
								$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_offerta" => $rec_delete["codice"],":codice_partecipante" => $codice_partecipante));
								$sql_delete = "DELETE FROM b_offerte_economiche WHERE codice = :codice_offerta AND codice_partecipante = :codice_partecipante";
								$ris_delete = $pdo->bindAndExec($sql_delete,array(":codice_offerta" => $rec_delete["codice"],":codice_partecipante" => $codice_partecipante));
							}
						}

						$sql = "SELECT b_buste.* FROM b_buste JOIN r_partecipanti ON b_buste.codice_partecipante = r_partecipanti.codice
										JOIN b_criteri_buste ON b_buste.codice_busta = b_criteri_buste.codice WHERE (b_criteri_buste.economica = 'S'";
						if ($foundTecnica) {
							$sql .= " OR b_criteri_buste.tecnica = 'S' ";
						}
						$sql.= ") AND r_partecipanti.codice = :codice_partecipante";
						if (isset($seconda_fase)) $sql .= " AND b_buste.nome_file LIKE '%seconda_fase'";
						$ris_old_busta = $pdo->bindAndExec($sql,array(":codice_partecipante" => $codice_partecipante));
						if ($ris_old_busta->rowCount()>0) {
							while($rec_delete = $ris_old_busta->fetch(PDO::FETCH_ASSOC)) {
								$fileURL = $config["doc_folder"] ."/" . $record_gara["codice"]."/".$codice_lotto."/".$rec_delete["nome_file"];
								$confirmURL = $config["doc_folder"] ."/" . $record_gara["codice"]."/".$codice_lotto."/".$codice_partecipante."_conferma.pdf";
								if (file_exists($fileURL)) unlink($fileURL);
								if (file_exists($fileURL.".tsr")) unlink($fileURL.".tsr");
								if (file_exists($confirmURL)) unlink($confirmURL);
								if (file_exists($confirmURL.".tsr")) unlink($confirmURL.".tsr");
								$strsql = "DELETE FROM b_buste WHERE codice_partecipante = :codice_partecipante AND codice = :codice_busta";
								$delete_buste = $pdo->bindAndExec($strsql,array(":codice_busta" => $rec_delete["codice"],":codice_partecipante" => $codice_partecipante));
							}
						}

						/* INIZIO REVOCA SE SOVRASCRIZIONE */
						if ($partecipante["conferma"] == 1) {
							$sql = "UPDATE r_partecipanti SET conferma = 0 WHERE codice = :codice_partecipante";
							$ris = $pdo->bindAndExec($sql,array(":codice_partecipante"=>$codice_partecipante));

							$oggetto = "Conferma di revoca della gara " . $record_gara["oggetto"];
							$corpo = "L'operatore economico " . $partecipante["partita_iva"] . " " . $partecipante["ragione_sociale"] . ",  ha revocato la partecipazione alla gara telematica:<br>";
							$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
							if (isset($lotto) != "") $corpo.= "Lotto: <strong>" . $lotto["oggetto"] . "</strong><br><br>";
							$corpo.= "Distinti Saluti<br><br>";

							$strsql = "SELECT pec FROM b_utenti WHERE codice = :codice_utente";
							$bind = array();
							$bind[":codice_utente"] = $_SESSION["codice_utente"];
							$risultato = $pdo->bindAndExec($strsql,$bind);
							if ($risultato->rowCount()>0) {
								$record = $risultato->fetch(PDO::FETCH_ASSOC);
								$indirizzi[] = $record["pec"];
							}


							$mailer = new Communicator();
							$mailer->oggetto = $oggetto;
							$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
							$mailer->codice_pec = -1;
							$mailer->comunicazione = true;
							$mailer->coda = false;
							$mailer->sezione = "gara";
							$mailer->codice_gara = $record_gara["codice"];
							$mailer->destinatari = $indirizzi;
							$esito = $mailer->send();

							$pec_conferma = getIndirizzoConferma($record_gara["codice_pec"]);

							$mailer = new Communicator();
							$mailer->oggetto = $oggetto;
							$mailer->corpo = "<h2>" . $oggetto . "</h2>".$corpo;
							$mailer->codice_pec = -1;
							$mailer->destinatari = $pec_conferma;
							$mailer->codice_gara = $record_gara["codice"];
							$mailer->type = 'comunicazione-gara';
							$mailer->sezione = "gara";
							$esito = $mailer->send();

						}

						$capogruppo = array();
						$capogruppo["codice"] = $codice_partecipante;
						$capogruppo["tipo"] = "";

						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_partecipanti";
						$salva->operazione = "UPDATE";
						$salva->oggetto = $capogruppo;
						$codice_partecipante = $salva->save();

					} else {
						$sql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice WHERE b_operatori_economici.codice_utente = :codice_utente ";
						$ris_operatori_economici = $pdo->bindAndExec($sql,array(":codice_utente"=>$_SESSION["codice_utente"]));

						$partecipante = array();
						$operatore_economico = $ris_operatori_economici->fetch(PDO::FETCH_ASSOC);
						$partecipante["codice_gara"] = $record_gara["codice"];
						$partecipante["codice_lotto"] = $codice_lotto;
						$partecipante["codice_operatore"] = $operatore_economico["codice"];
						$partecipante["codice_utente"] = $_SESSION["codice_utente"];
						$partecipante["partita_iva"] = $operatore_economico["codice_fiscale_impresa"];
						$partecipante["ragione_sociale"] = $operatore_economico["ragione_sociale"];
						$partecipante["pec"] = $operatore_economico["pec"];
						$partecipante["identificativoEstero"] = $operatore_economico["identificativoEstero"];
						$partecipante["conferma"] = 0;
						$partecipante["ammesso"] = 'N';
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_partecipanti";
						$salva->operazione = "INSERT";
						$salva->oggetto = $partecipante;
						$codice_partecipante = $salva->save();
					}
					if (isset($_POST["partecipante"])) {
						foreach ($_POST["partecipante"] as $membro) {
								$sql = "SELECT b_operatori_economici.*, b_utenti.pec FROM b_operatori_economici JOIN b_utenti ON b_operatori_economici.codice_utente = b_utenti.codice WHERE b_operatori_economici.codice_fiscale_impresa = :partita_iva";
								$membro["codice_gara"] = $record_gara["codice"];
								$membro["codice_lotto"] = $codice_lotto;
								$membro["codice_operatore"] = 0;
								$membro["codice_utente"] = 0;
								$membro["codice_capogruppo"] = $codice_partecipante;
								$ris = $pdo->bindAndExec($sql,array(":partita_iva",$membro["partita_iva"]));
								if ($ris->rowCount()==1) {
									$rec = $ris->fetch(PDO::FETCH_ASSOC);
									$membro["codice_operatore"] = $rec["codice"];
									$membro["codice_utente"] = $rec["codice_utente"];
									$membro["pec"] = $rec["pec"];
								}
								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $_SESSION["codice_utente"];
								$salva->nome_tabella = "r_partecipanti";
								$salva->operazione = "INSERT";
								$salva->oggetto = $membro;
								$codice_membro = $salva->save();
								if ($codice_membro === false)	$error = true;
							}
							$capogruppo = array();
							$capogruppo["codice"] = $codice_partecipante;
							$capogruppo["tipo"] = "04-CAPOGRUPPO";

							$salva = new salva();
							$salva->debug = false;
							$salva->codop = $_SESSION["codice_utente"];
							$salva->nome_tabella = "r_partecipanti";
							$salva->operazione = "UPDATE";
							$salva->oggetto = $capogruppo;
							$codice_partecipante = $salva->save();
						}
						if ($codice_partecipante != false && !$error) {
							?>
							<ul class="success">
								<li><?= traduci("Salvataggio riuscito con successo") ?></li>
							</ul>
							<?
						} else {
							?>
							<h3 class="ui-state-error"><?= traduci('errore-salvataggio') ?></h3>
							<?
						}
					} else {
						echo "<h1>". traduci('impossibile accedere') . " - " . traduci('partecipazione gia inviata') . "</h1>";
					}
				} else {
					echo "<h1>". traduci('impossibile accedere') . " - " . traduci('termini scaduti') . "</h1>";
				}
				?>
				<a class="submit_big" style="background-color:#444" href="/gare/telematica2.0/modulo.php?cod=<?= $codice_gara ?><?= ($codice_lotto > 0) ? "&codice_lotto=".$codice_lotto : "" ?>"><?= traduci("Ritorna al pannello") ?></a>
				<?
			} else {
				echo "<h1>".traduci('impossibile continuare')." - ERROR 1</h1>";
			}
		} else {
			echo "<h1>".traduci('impossibile continuare')." - ERROR 0</h1>";
		}
	include_once($root."/layout/bottom.php");
	?>
