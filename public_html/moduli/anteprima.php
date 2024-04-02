<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	include_once("../../config.php");
	if (isset($_SESSION["codice_utente"]) && isset($_GET) && ($_SESSION["gerarchia"]<=2)) {
		$bind = array(":codice"=>$_GET["codice"],":codice_ente"=>$_SESSION["ente"]["codice"]);
		$strsql = "SELECT * FROM b_documentale WHERE codice = :codice AND codice_ente = :codice_ente";
		$risultato = $pdo->bindAndExec($strsql,$bind);
		if ($risultato->rowCount()>0) {
				$corpo = $risultato->fetch(PDO::FETCH_ASSOC);
			if (isset($_GET["anteprima"])) {
				$html= "<html>";
				$html.= "<style>";
				$html.= "body { font-size:10px } table { width:100%; } ";
				$html.= "table td { padding:2px; border:1px solid #CCC } ";
				$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
				$html.= "</style><body>";
				$html.= $corpo["corpo"];
				$html.= "</body></html>";

				$options = new Options();
				$options->set('defaultFont', 'Helvetica');
				$options->setIsRemoteEnabled(true);
				$dompdf = new Dompdf($options);
				$dompdf->loadHtml($html);
				$dompdf->setPaper('A4', 'portrait');
				$dompdf->set_option('defaultFont', 'Helvetica');
				$dompdf->render();
				$dompdf->stream("Anteprima.pdf", array("Attachment" => false));
			} else {
					echo $corpo["corpo"];
			}
		}
	}
?>
