<?
use Dompdf\Dompdf;
use Dompdf\Options;
include_once("../../../config.php");


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
if ($edit && !empty($_POST["numero_partecipanti"]))
{
	$estrazione = $_POST;
	$codice_gara = $estrazione["codice_gara"];
	$codice_lotto = $estrazione["codice_lotto"];
	$bind = array();
	$bind[":codice"] = $codice_gara;
	$bind[":codice_lotto"] = $codice_lotto;
	$strsql = "SELECT * FROM b_estrazioni_campioni WHERE codice_gara = :codice AND codice_lotto = :codice_lotto";
	$risultato = $pdo->bindAndExec($strsql,$bind);
	$errore = false;
	if ($risultato->rowCount()>0) {
		?>
				alert('Impossibile proseguire. Sorteggio già effettuato.');
		<?
	} else {
		$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) ";
		$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
		if ($ris_partecipanti->rowCount() > 0) {
			$sorteggio = true;
			if ($ris_partecipanti->rowCount() <= $estrazione["numero_partecipanti"]) {
				$sorteggio = false;
				$pdo->go("UPDATE r_partecipanti SET controllo_possesso_requisiti = 'S' 
						   WHERE codice_gara = :codice AND codice_lotto = :codice_lotto
						   AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)",$bind);
				?>

					alert('Sorteggio non necessario. Numero degli operatori disponibili è inferiore o uguale al numero di partecipanti richiesti');

				<?
			}

			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_estrazioni_campioni";
			$salva->operazione = "INSERT";
			$salva->oggetto = $estrazione;
			$codice_estrazione = $salva->save();
			if ($codice_estrazione !== false) {
				$operatori = array();
				while($operatore = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
					$relazione = array();
					$relazione["codice_estrazione"] = $codice_estrazione;
					$relazione["codice_partecipante"] = $operatore["codice"];
					$relazione["escluso"] = "N";
					$relazione["selezionato"] = "N";
					if (!$sorteggio) $relazione["selezionato"] = "S";
					$salva = new salva();
					$salva->debug = false;
					$salva->codop = $_SESSION["codice_utente"];
					$salva->nome_tabella = "r_estrazioni_campioni";
					$salva->operazione = "INSERT";
					$salva->oggetto = $relazione;
					$codice_relazione = $salva->save();
					if ($codice_relazione != false) $operatori[] = array("codice"=>$codice_relazione,"codice_partecipante"=>$operatore["codice"]);
				}
				if ($sorteggio && count($operatori) > 0) {
					$i=0;
					shuffle($operatori);
					$sequenza = range(1,count($operatori));
					shuffle($sequenza);
					$sequenza = array_slice($sequenza,0,$estrazione["numero_partecipanti"]);
					foreach($operatori AS $operatore_selezionato) {
						$i++;
						$selezionato = "N";
						if (in_array($i,$sequenza,true)) $selezionato = "S";
						$bind = array();
						$bind[":selezionato"] = $selezionato;
						$bind[":identificativo"] = $i;
						$bind[":codice_estrazione"] = $codice_estrazione;
						$bind[":codice"] = $operatore_selezionato["codice"];
						$sql_includi = "UPDATE r_estrazioni_campioni SET selezionato = :selezionato, identificativo = :identificativo WHERE codice_estrazione = :codice_estrazione AND codice = :codice";
						$ris_includi = $pdo->bindAndExec($sql_includi,$bind);
						if ($selezionato=="S") {
							$pdo->go("UPDATE r_partecipanti SET controllo_possesso_requisiti = 'S' 
							WHERE codice = :codice ",[":codice"=>$operatore_selezionato["codice_partecipante"]]);
						}
					}
					$bind=array();
					$bind[":sequenza"] = implode(" - ",$sequenza);
					$bind[":codice_estrazione"] = $codice_estrazione;
					$sql_sequenza = "UPDATE b_estrazioni_campioni SET sequenza = :sequenza WHERE codice = :codice_estrazione";
					$ris_sequenza = $pdo->bindAndExec($sql_sequenza,$bind);
				} else {
					if ($sorteggio) {
						$errore = true;
						?>
		
							alert('Impossibile proseguire. Nessun operatore economico soddisfa i requisiti.');
		
						<?
					}
				}
			} else {
				$errore = true;
				?>

					alert('Impossibile proseguire. Errore nel salvataggio del sorteggio.');

				<?
			}
		} else {
			$errore = true;
			?>

					alert('Impossibile proseguire. Nessun partecipante.');

			<?
		}
		?>
		window.location.reload();
		<?	
	}
	if (!$errore) {

		log_gare($_SESSION["ente"]["codice"],$codice_gara,"INSERT","Estrazione campione operatori economici da controllare");
		$html ="<html>";
		$html.= "<style>";
		$html.= "body { font-size:10px } table { width:100%; } ";
		$html.= "table td { padding:2px; border:1px solid #CCC } ";
		$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
		$html.= "</style>";
		$html.= "<body>";
		ob_start();
		include("report.php");
		$report = ob_get_clean();
		$html.=$report;
		$html.= "</body></html>";

		$percorso = $config["arch_folder"];

		$allegato["online"] = 'N';
		$allegato["codice_gara"] = $codice_gara;
		$allegato["codice_ente"] = $_SESSION["ente"]["codice"];

		$percorso .= "/".$allegato["codice_gara"];

		if (!is_dir($percorso)) mkdir($percorso,0777,true);
		$allegato["nome_file"] = $allegato["codice_gara"] . " - ". $codice_lotto ." - Verbale estrazione campione .".time().".pdf";
		$allegato["titolo"] = "Verbale Estrazione campione controlli lotto #{$codice_lotto}";

		$options = new Options();
		$options->set('defaultFont', 'Helvetica');
		$options->setIsRemoteEnabled(true);
		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->set_option('defaultFont', 'Helvetica');
		$dompdf->render();
		$content = $dompdf->output();
		file_put_contents($percorso."/".$allegato["nome_file"],$content);

		if (file_exists($percorso."/".$allegato["nome_file"])) {
			$allegato["riferimento"] = getRealName($percorso."/".$allegato["nome_file"]);
			rename($percorso."/".$allegato["nome_file"],$percorso."/".$allegato["riferimento"]);
			$salva = new salva();
			$salva->debug = false;
			$salva->codop = $_SESSION["codice_utente"];
			$salva->nome_tabella = "b_allegati";
			$salva->operazione = "INSERT";
			$salva->oggetto = $allegato;
			$codice_allegato = $salva->save();

		}
	  }
	}
	?>
