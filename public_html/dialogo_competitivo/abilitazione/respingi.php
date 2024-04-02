<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
if (isset($_SESSION["codice_utente"])) {
	$edit = check_permessi("dialogo_competitivo",$_SESSION["codice_utente"]);
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
		$strsql= "SELECT b_bandi_dialogo.* FROM b_bandi_dialogo JOIN r_partecipanti_dialogo ON b_bandi_dialogo.codice = r_partecipanti_dialogo.codice_bando WHERE
							b_bandi_dialogo.codice = :codice_bando
							AND r_partecipanti_dialogo.codice_operatore = :codice_operatore ";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount() > 0) {
			$record_bando = $risultato->fetch(PDO::FETCH_ASSOC);
			$strsql = "UPDATE r_partecipanti_dialogo SET valutato = 'S', ammesso = 'N' WHERE codice_operatore = :codice_operatore AND codice_bando = :codice_bando";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			scrivilog("r_partecipanti_dialogo","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
			

			$oggetto = "Respinta ammissione al bando: " . $record_bando["oggetto"];

			$corpo = "Si informa che a seguito di valutazione, la S.V. non &egrave; stata abilitata al dialogo competitivo relativo al bando:<br>";
			$corpo.= "<br><strong>" . $record_bando["oggetto"] . "</strong><br><br>";
			$corpo.= "Ulteriori informazioni:<br><br>";
			$corpo.= $_POST["corpo"];
			$corpo.= "<br><br>Distinti Saluti<br><br>";

			$mailer = new Communicator();
			$mailer->oggetto = $oggetto;
			$mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
			$mailer->codice_pec = $record_bando["codice_pec"];
			$mailer->comunicazione = true;
			$mailer->coda = false;
			$mailer->sezione = "dialogo";
			$mailer->codice_gara = $_POST["codice_bando"];
			$mailer->destinatari = $operatore["codice_utente"];;
			$esito = $mailer->send();
			if ($esito !== true) { ?>
				alert("<?= $esito ?>");
			<?
			}

			$href = "/dialogo_competitivo/partecipanti/index.php?codice=" . $_POST["codice_bando"];
			$href = str_replace('"',"",$href);
			$href = str_replace(' ',"-",$href);
			?>
			alert('Operazione effettuata con successo');
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
