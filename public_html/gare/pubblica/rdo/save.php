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
			$bind = array();
			$bind[":codice"] = $codice_gara;
			$sql = "UPDATE b_gare SET stato = 3 WHERE codice = :codice AND stato < 3 ";
			$update_stato = $pdo->bindAndExec($sql,$bind);

			log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Stato pubblicazione");
			$bind = array();
			$bind[":codice"] = $_POST["codice_gara"];
			$strsql= "SELECT b_gare.*, b_procedure.nome AS nome_procedura FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice WHERE b_gare.codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount()>0) {
				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
				if ((isset($_POST["gara"]["pubblica"]) && $_POST["gara"]["pubblica"] == "1") || ($record_gara["pubblica"] == "1") || (isset($_POST["gara"]["pubblica"]) && $_POST["gara"]["pubblica"] == "2") || ($record_gara["pubblica"] == 2)) {
					$codici_utenti = array();
					$invio = false;
					if (isset($_POST["indirizzi"]) && $_POST["indirizzi"] != "") {
						$invio = true;
						$codici_utenti = explode(";",$_POST["indirizzi"]);
						$oggetto = "Invito procedura " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];
						$corpo = "La S.V. ha ricevuto un invito per presentare un'offerta per la gara:<br>";
						$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
						$corpo.= "Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\">";
						$corpo.= $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
						$corpo.= "</a><br><br>";
						$corpo.= "Distinti Saluti<br><br>";

						$sql = "INSERT INTO r_inviti_gare (codice_gara,codice_utente) VALUES ";
						$bind = array();
						$bind[":codice"] = $_POST["codice_gara"];
						$cont_inviti = 0;
						foreach ($codici_utenti as $codice_utente) {
							if ($codice_utente != "") {
								$cont_inviti++;
								$bind[":invito_".$cont_inviti] = $codice_utente;
								$sql .= "(:codice, :invito_".$cont_inviti."),";
							}
						}
						$sql = substr($sql,0,-1);
						$update_inviti = $pdo->bindAndExec($sql,$bind);
					} else if ($record_gara["inviato_avviso"] == "N") {
						$oggetto = "Pubblicazione procedura " . $record_gara["nome_procedura"] . ": " . $record_gara["oggetto"];
						$corpo = "Si informa la S.V. che &egrave; stata pubblicata la seguente gara:<br>";
						$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";
						$corpo.= "Maggiori informazioni sono disponibili all'indirizzo <a href=\"" . $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli\" title=\"Dettagli gara\">";
						$corpo.= $config["protocollo"] . $_SERVER["SERVER_NAME"] . "/gare/id" . $record_gara["codice"] . "-dettagli";
						$corpo.= "</a><br><br>";
						$corpo.= "Distinti Saluti<br><br>";

						$strsql = "SELECT b_cpv.* FROM b_cpv JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice WHERE r_cpv_gare.codice_gara = :codice ORDER BY codice";
						$risultato_cpv = $pdo->bindAndExec($strsql,$bind);
						if ($risultato_cpv->rowCount()>0) {
							$cpv = array();
							while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
								$cpv[] = $rec_cpv["codice"];
							}
							$string_cpv = implode(";",$cpv);
						}

						$bind = array();
						$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
						$bind[":codice_derivazione"] = $record_gara["codice_derivazione"];

						$strsql  = "SELECT b_utenti.codice ";
						$strsql .= "FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice LEFT JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente ";
						$strsql .= "JOIN r_enti_operatori ON r_enti_operatori.cod_utente = b_utenti.codice JOIN r_cpv_operatori ON b_operatori_economici.codice = r_cpv_operatori.codice_operatore ";
						$strsql .= "JOIN r_partecipanti_me ON b_utenti.codice = r_partecipanti_me.codice_utente ";
						$strsql .= "WHERE b_gruppi.gerarchia > 2 AND b_utenti.attivo = 'S' AND r_enti_operatori.cod_ente = :codice_ente AND r_partecipanti_me.ammesso = 'S' and r_partecipanti_me.codice_bando = :codice_derivazione ";
						if (isset($string_cpv) && $string_cpv != "") {
							$strsql .= " AND (";
							$categorie = explode(";",$string_cpv);
							$cont = 0;
							foreach($categorie as $codice) {
								$cont++;
								if ($codice != "") {
									$bind[":cpv_".$cont] = $codice;
									$strsql .= "(r_cpv_operatori.codice = :cpv_" . $cont . " ";
									if (strlen($codice)>2) {
										$diff = strlen($codice) - 2;
										for($i=1;$i<=$diff;$i++) {
											$bind[":cpv_".$cont."_".$i] = substr($codice,0,$i*-1);
											$strsql .= "OR r_cpv_operatori.codice = :cpv_".$cont."_".$i." ";
										}
									}
								$strsql.=") OR ";
								}
							}
							$strsql = substr($strsql,0,-4);
							$strsql .= ")";
						}
						$strsql .= " GROUP BY b_utenti.codice ";
						$risultato = $pdo->bindAndExec($strsql,$bind);
						if ($risultato->rowCount()>0) {
							$invio = true;
							while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
								$codici_utenti[] = $record["codice"];
							}
						}
					}

				// invio comunicazione;

				if ($invio) {
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

					$bind = array();
					$bind[":codice"] = $_POST["codice_gara"];

					$sql = "UPDATE b_gare SET inviato_avviso = 'S' WHERE codice = :codice";
					$update_inviato = $pdo->bindAndExec($sql,$bind);
				}
			}
			}
			$href = "/gare/pannello.php?codice=" . $_POST["codice_gara"];
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			?>
				alert('Pubblicazione effettuata con successo');
    	        window.location.href = '<? echo $href ?>';
        	<?
				} else {
					?>alert('Errore nel salvataggio. Riprovare.');<?
				}
	}



?>
