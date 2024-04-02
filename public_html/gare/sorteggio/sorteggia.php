<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");

	$edit = false;
	$lock = true;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$codice_fase = getFase($_SERVER['QUERY_STRING'], "/gare/sorteggio/edit.php");
		if ($codice_fase !== false) {
			$esito = check_permessi_gara($codice_fase,$_POST["codice"],$_SESSION["codice_utente"]);
			$edit = $esito["permesso"];
			$lock = $esito["lock"];
		}
		if (!$edit) {
			die();
		}
	} else {
		die();
	}
	if ($edit && !$lock) {
		$estrazione = [];
		$estrazione["codice_gara"] = $codice_gara = $_POST["codice"];
		$estrazione["codice_lotto"] = $codice_lotto = $_POST["codice_lotto"];

		$bind=array();
		$bind[":codice"] = $codice_gara;

		$sql = "UPDATE b_gare SET stato = 6 WHERE codice = :codice";
		$update_stato = $pdo->bindAndExec($sql,$bind);
		
		$bind = array();
		$bind[":codice"] = $codice_gara;
		$bind[":codice_lotto"] = $codice_lotto;
		$strsql = "SELECT * FROM b_sorteggi WHERE codice_gara = :codice AND codice_lotto = :codice_lotto";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		$errore = false;
		if ($risultato->rowCount()>0) {
			?>
					alert('Impossibile proseguire. Sorteggio già effettuato.');
			<?
		} else {
			$sql = "SELECT * FROM r_partecipanti WHERE codice_gara = :codice AND codice_lotto = :codice_lotto AND codice_capogruppo = 0 AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL) AND primo = 'S' ";
			$ris_partecipanti = $pdo->bindAndExec($sql,$bind);
			if ($ris_partecipanti->rowCount() > 1) {
				$pdo->go("UPDATE r_partecipanti SET primo = 'N', secondo = 'N' WHERE codice_gara = :codice AND codice_lotto = :codice_lotto ",$bind);
				$sorteggio = true;
				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_sorteggi";
				$salva->operazione = "INSERT";
				$salva->oggetto = $estrazione;
				$codice_estrazione = $salva->save();
				if ($codice_estrazione !== false) {
					$operatori = array();
					while($operatore = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
						$relazione = array();
						$relazione["codice_estrazione"] = $codice_estrazione;
						$relazione["codice_partecipante"] = $operatore["codice"];
						$salva = new salva();
						$salva->debug = false;
						$salva->codop = $_SESSION["codice_utente"];
						$salva->nome_tabella = "r_sorteggi";
						$salva->operazione = "INSERT";
						$salva->oggetto = $relazione;
						$codice_relazione = $salva->save();
						if ($codice_relazione != false) $operatori[] = array("codice"=>$codice_relazione,"codice_partecipante"=>$operatore["codice"]);
					}
					if ($sorteggio && count($operatori) > 0) {
						$i=0;
						shuffle($operatori);
						$sequenza = range(0,count($operatori)-1);
						shuffle($sequenza);
						foreach($sequenza AS $identificativo) {
							$operatore_selezionato = $operatori[$identificativo];
							$bind = array();
							$bind[":identificativo"] = $identificativo;
							$bind[":codice_estrazione"] = $codice_estrazione;
							$bind[":codice"] = $operatore_selezionato["codice"];
							$bind[":selezionato"] = "N";
							if ($i==0) {
								$bind[":selezionato"] = "S";
								$pdo->go("UPDATE r_partecipanti SET primo = 'S' 
								WHERE codice = :codice ",[":codice"=>$operatore_selezionato["codice_partecipante"]]);
							} 
							if ($i == 1) {
								$pdo->go("UPDATE r_partecipanti SET secondo = 'S' 
								WHERE codice = :codice ",[":codice"=>$operatore_selezionato["codice_partecipante"]]);
							}
							$sql_includi = "UPDATE r_sorteggi SET selezionato = :selezionato, identificativo = :identificativo WHERE codice_estrazione = :codice_estrazione AND codice = :codice";
							$ris_includi = $pdo->bindAndExec($sql_includi,$bind);
							$i++;
						}
						$bind=array();
						$bind[":sequenza"] = implode(" - ",$sequenza);
						$bind[":codice_estrazione"] = $codice_estrazione;
						$sql_sequenza = "UPDATE b_sorteggi SET sequenza = :sequenza WHERE codice = :codice_estrazione";
						$ris_sequenza = $pdo->bindAndExec($sql_sequenza,$bind);
					} else {
						$errore = true;
						?>
						alert('Impossibile proseguire. Si sono verificati degli errori durante il sorteggio.');
						<?
						}
					}
				} else {
					$errore = true;
					?>
					alert('Impossibile proseguire. Sorteggio non necessario.');
					<?
				}
			}
			?>
			window.location.reload();
			<?	
			if (!$errore) {

				log_gare($_SESSION["ente"]["codice"],$codice_gara,"INSERT","Sorteggio per risoluzione ex aequo operatori");
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
				$allegato["nome_file"] = $allegato["codice_gara"] . " - ". $codice_lotto ." - Verbale estrazione aggiudicatario .".time().".pdf";
				$allegato["titolo"] = "Verbale Estrazione aggiudicatario #{$codice_lotto}";

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
		} else {
			?>
			alert('Errore durante il salvtaggio. Riprovare.');
			<?
		}
	}



?>
