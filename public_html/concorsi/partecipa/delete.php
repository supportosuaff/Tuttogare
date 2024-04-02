<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (is_operatore()) {

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

			if ($accedi && !empty($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["conferma"])) {
				$partecipante = $_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]];


			$strsql = "DELETE FROM r_partecipanti_concorsi WHERE codice = :codice";
			$risultato = $pdo->bindAndExec($strsql,array(":codice"=>$partecipante["codice"]));

			if ($risultato->rowCount()>0) {
				scrivilog("r_partecipanti_concorsi","DELETE",$pdo->getSQL(),-1);


				$strsql = "DELETE FROM r_partecipanti_utenti_concorsi WHERE codice_partecipante = :codice";
				$risultato = $pdo->bindAndExec($strsql,array(":codice"=>$partecipante["codice"]));

				$bind = array();
				$bind[":codice"] = $partecipante["codice"];
				$sql = "SELECT * FROM b_buste_concorsi WHERE codice_partecipante = :codice";
				$risultato = $pdo->bindAndExec($sql,$bind);
				if ($risultato->rowCount()>0) {
					while($rec = $risultato->fetch(PDO::FETCH_ASSOC)) {
						$fileURL = $config["doc_folder"] ."/concorsi/" . $rec["codice_gara"]."/". $fase_attiva["codice"] ."/".$rec["nome_file"];
						$confirmURL = $config["doc_folder"] ."/concorsi/" . $rec["codice_gara"]."/". $fase_attiva["codice"] ."/".$rec["codice_partecipante"]."_conferma.pdf";
						if (file_exists($fileURL)) unlink($fileURL);
						if (file_exists($fileURL.".tsr")) unlink($fileURL.".tsr");
						if (file_exists($confirmURL)) unlink($confirmURL);
						if (file_exists($confirmURL.".tsr")) unlink($confirmURL.".tsr");
					}
				}
				$bind = array();
				$bind[":codice"] = $partecipante["codice"];
				$strsql = "DELETE FROM b_buste_concorsi WHERE codice_partecipante = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("b_buste_concorsi","DELETE",$pdo->getSQL(),-1);

				$strsql = "DELETE FROM b_offerte_concorso WHERE codice_partecipante = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("b_offerte_concorso","DELETE",$pdo->getSQL(),-1);

				$strsql = "DELETE FROM b_dettaglio_offerte_concorso WHERE codice_partecipante = :codice";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("b_dettaglio_offerte_concorso","DELETE",$pdo->getSQL(),-1);


				$oggetto = "Conferma di revoca al concorso " . $record_gara["oggetto"] . " Fase: " . $fase_attiva["oggetto"];
				$corpo = "Il partecipante " . $partecipante["identificativo"] . ",  ha revocato la partecipazione al concorso:<br>";
				$corpo.= "<br><strong>" . $record_gara["oggetto"] . "</strong><br><br>";

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
				$esito = $mailer->send();

				unset($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]);

			?>
      alert('Revoca effettuata con successo');
			window.location.reload();
      <?
			} else {
				?>
				jalert('Errore nella revoca. Partecipante non riconosciuto 1');
				<?
			}
		} else {
			?>
			jalert('Errore nella revoca. Partecipante non riconosciuto 2');
			<?
		}
	} else {
		?>
		jalert('Errore nella revoca. Operatore non riconosciuto 3');
		<?
	}
}
?>
