	<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("sda",$_SESSION["codice_utente"]);
		if (!$edit) {
			die();
		}
	} else {
		die();
	}

	if (!$edit) {
		echo "alert('Utente non abilitato');";
		die();
	} else {
		$bind = array();
		$bind[":codice"] = $_POST["codice_operatore"];
		$strsql = "SELECT * FROM b_operatori_economici WHERE codice = :codice";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$operatore = $risultato->fetch(PDO::FETCH_ASSOC);
			$bind = array(":codice_bando"=>$_POST["codice_bando"],":codice_operatore"=>$operatore["codice"]);
			$strsql= "SELECT b_bandi_sda.* FROM b_bandi_sda JOIN r_partecipanti_sda ON b_bandi_sda.codice = r_partecipanti_sda.codice_bando WHERE b_bandi_sda.codice = :codice_bando
								AND r_partecipanti_sda.codice_operatore = :codice_operatore";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_bando = $risultato->fetch(PDO::FETCH_ASSOC);
				$bind = array();
				$bind = array(":codice_bando"=>$_POST["codice_bando"],":codice_operatore"=>$operatore["codice"]);
				$bind[":timestamp"] =  date('Y-m-d H:i:s');
				$strsql = "UPDATE r_partecipanti_sda SET valutato = 'S', ammesso = 'S', timestamp_abilitazione = :timestamp WHERE
									codice_operatore = :codice_operatore AND codice_bando = :codice_bando";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("r_partecipanti_sda","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
				

				$oggetto = "Ammissione al bando: " . $record_bando["oggetto"];
				$corpo = "Si informa che a seguito di valutazione, la S.V. &egrave; stata abilitata al sistema dinamico di acquisizione relativo al bando:<br>";
				$corpo.= "<br><strong>" . $record_bando["oggetto"] . "</strong><br><br>";
				$corpo.= "Distinti Saluti<br><br>";
				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
				$mailer->codice_pec = $record_bando["codice_pec"];
				$mailer->comunicazione = true;
				$mailer->coda = false;
				$mailer->sezione = "sda";
				$mailer->codice_gara = $_POST["codice_bando"];
				$mailer->destinatari = $operatore["codice_utente"];;
				$esito = $mailer->send();
				if ($esito !== true) { ?>
					alert("<?= $esito ?>");
				<?
				}

				$href = "/sda/partecipanti/index.php?codice=" . $_POST["codice_bando"];
				$href = str_replace('"',"",$href);
				$href = str_replace(' ',"-",$href);
				?>
				alert('Abilitazione effettuata con successo');
				window.location.href = '<? echo $href ?>';
				<?
			} else {
				echo "alert('Bando inesistente');";
			}
		} else {
			echo "alert('Operatore inesistente');";
		}
	}
	?>
