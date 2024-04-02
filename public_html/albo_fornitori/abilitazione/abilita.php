	<?
	session_start();
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	if (isset($_SESSION["codice_utente"])) {
		$edit = check_permessi("albo_fornitori",$_SESSION["codice_utente"]);
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
		$bind[":codice_operatore"] = $_POST["codice_operatore"];
		$strsql = "SELECT * FROM b_operatori_economici WHERE codice = :codice_operatore ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$operatore = $risultato->fetch(PDO::FETCH_ASSOC);
			$bind[":codice_operatore"] = $operatore["codice"];;
			$bind[":codice_bando"] = $_POST["codice_bando"];
			$strsql= "SELECT b_bandi_albo.* FROM b_bandi_albo JOIN r_partecipanti_albo ON b_bandi_albo.codice = r_partecipanti_albo.codice_bando WHERE
								b_bandi_albo.codice = :codice_bando
								AND r_partecipanti_albo.codice_operatore = :codice_operatore ";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$record_bando = $risultato->fetch(PDO::FETCH_ASSOC);
				$bind[":timestamp"] = date('Y-m-d H:i:s');
				$strsql = "UPDATE r_partecipanti_albo SET valutato = 'S', ammesso = 'S', timestamp_abilitazione = :timestamp WHERE codice_operatore = :codice_operatore AND codice_bando = :codice_bando";
				$risultato = $pdo->bindAndExec($strsql,$bind);
				scrivilog("r_partecipanti_albo","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
				if ($record_bando["manifestazione_interesse"] == "N") {
					$oggetto = "Ammissione all'elenco dei fornitori: " . $record_bando["oggetto"];
				} else if ($record_bando["manifestazione_interesse"] == "S") {
					$oggetto = "Ammissione all'indagine di mercato: " . $record_bando["oggetto"];
				}
				$corpo = "Si informa che a seguito di valutazione, la S.V. &egrave; stata abilitata ";
				if ($record_bando["manifestazione_interesse"] == "N") {
					$corpo .= "all'elenco dei fornitori:<br>";
				} else if ($record_bando["manifestazione_interesse"] == "S") {
					$corpo .= "alla fase preliminare dell’indagine di mercato:<br>";
				}
				$corpo.= "<br><strong>" . $record_bando["oggetto"] . "</strong><br><br>";
				$corpo.= "Distinti Saluti<br><br>";

				$mailer = new Communicator();
				$mailer->oggetto = $oggetto;
				$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
				$mailer->codice_pec = $record_bando["codice_pec"];
				$mailer->comunicazione = true;
				$mailer->coda = false;
				$mailer->sezione = "albo";
				$mailer->codice_gara = $_POST["codice_bando"];
				$mailer->destinatari = $operatore["codice_utente"];;
				$esito = $mailer->send();
				if ($esito !== true) { ?>
					alert("<?= $esito ?>");
				<?
				}
				$href = "/albo_fornitori/partecipanti/index.php?codice=" . $_POST["codice_bando"];
				$href = str_replace('"',"",$href);
				$href = str_replace(' ',"-",$href);
				transferToAlboCommissari("INSERT",$operatore["codice"],$record_bando["codice"]);
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
