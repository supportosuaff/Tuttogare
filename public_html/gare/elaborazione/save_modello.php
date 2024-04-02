<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	include_once("../../../config.php");
	$edit = false;
	$lock = true;
	$tipi_editor = array("Bando","Disciplinare","Invito");

		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])&& in_array($_POST["tipo"],$tipi_editor)) {
			$strsql = "SELECT * FROM b_gestione_gare WHERE link LIKE '/gare/elaborazione/modello.php%'";
			$risultato = $pdo->query($strsql);
			if ($risultato->rowCount()>0) {
				$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
				$esito = check_permessi_gara($gestione["codice"],$_POST["codice_gara"],$_SESSION["codice_utente"]);
				$edit = $esito["permesso"];
				$lock = $esito["lock"];
			}
			if (!$edit) {
				die();
			}
		} else {
			die();
		}
		if ($edit && !$lock)
	 	{
				$href = "/gare/pannello.php?codice=" . $_POST["codice_gara"];
				$href = str_replace('"',"",$href);
				$href = str_replace(' ',"-",$href);
				$_POST["codice_ente"] = $_SESSION["ente"]["codice"];

				$salva = new salva();
				$salva->debug = false;
				$salva->codop = $_SESSION["codice_utente"];
				$salva->nome_tabella = "b_documentale";
				$salva->operazione = $_POST["operazione"];
				$salva->oggetto = $_POST;
				$codice_elemento = $salva->save();

				if ($codice_elemento != false) {
					$titolo = "";
					switch($_POST["tipo"]) {
						case "Bando": $titolo = "Bando di gara"; break;
						case "Disciplinare": $titolo = "Disciplinare di gara"; break;
						case "Invito": $titolo = "Lettera invito"; break;
					}
					if ($_POST["allega"]=="S") {
						$bind = array();
						$bind[":tipo"] = $_POST["tipo"];
						$bind[":codice"] = $codice_elemento;
						$sql = "SELECT b_documentale.codice, b_allegati.codice AS allegato FROM b_documentale LEFT JOIN b_allegati ON b_documentale.codice_allegato = b_allegati.codice
						WHERE b_documentale.tipo = :tipo  AND b_documentale.codice = :codice";
						$ris = $pdo->bindAndExec($sql,$bind);
						if ($ris->rowCount()>0) {

							$elemento = $ris->fetch(PDO::FETCH_ASSOC);

							$html ="<html>";
							$html.= "<style>";
							$html.= "body { font-size:10px } table { width:100%; } ";
							$html.= "table td { padding:2px; border:1px solid #CCC } ";
							$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
							$html.= "</style>";
							$html.= "<body>";
							$html.= $_POST["corpo"];
							$html.= "</body></html>";

							$percorso = $config["pub_doc_folder"]."/allegati";

							$allegato["online"] = 'S';
							$allegato["codice_gara"] = $_POST["codice_gara"];
							$allegato["codice_ente"] = $_SESSION["ente"]["codice"];

							$percorso .= "/".$allegato["codice_gara"];
							if (!is_dir($percorso)) mkdir($percorso,0777,true);

							$allegato["nome_file"] = $allegato["codice_gara"] . " - ".$titolo.".pdf";
							$allegato["titolo"] = $titolo;

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
							if (file_exists($percorso."/".$allegato["nome_file"]) && ($elemento["allegato"]=="")) {
								$allegato["riferimento"] = getRealName($percorso."/".$allegato["nome_file"]);
								rename($percorso."/".$allegato["nome_file"],$percorso."/".$allegato["riferimento"]);
								$salva = new salva();
								$salva->debug = false;
								$salva->codop = $_SESSION["codice_utente"];
								$salva->nome_tabella = "b_allegati";
								$salva->operazione = "INSERT";
								$salva->oggetto = $allegato;
								$codice_allegato = $salva->save();

								if ($codice_allegato != false) {
									$bind = array();
									$bind[":codice_allegato"] = $codice_allegato;
									$bind[":codice"] = $codice_elemento;
									$sql = "UPDATE b_documentale SET codice_allegato = :codice_allegato WHERE codice = :codice";
									$ris = $pdo->bindAndExec($sql,$bind);
								}

							}
						}
					}
					log_gare($_SESSION["ente"]["codice"],$_POST["codice_gara"],"UPDATE","Elaborazione " . $titolo);
					?>
					alert('Inserimento effettuato con successo');
					window.location.href = '<? echo $href ?>';
					<?
				} else {
					?>
					alert('Errore nel salvataggio. Riprovare');
					<?
				}
	}
?>
