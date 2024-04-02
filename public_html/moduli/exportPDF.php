<?
	use Dompdf\Dompdf;
	use Dompdf\Options;
	include("../../config.php");
	session_write_close();
	if (isset($_POST["corpo"]) && isset($_SESSION["codice_utente"]) && $_SESSION["gerarchia"] <= 2) {
		if (!isset($_POST["file_title"])) $_POST["file_title"] = "File";
		$formato = "A4";
		if (!empty($_POST["formato"])) $formato = $_POST["formato"];
		$orientamento = "portrait";
		if (!empty($_POST["orientamento"]) && $_POST["orientamento"] == "L") $orientamento = "landscape";
		$html = "<html>";
		$html.= "<style>";
		$html.= "body { font-size:10px; } table { width:100%; } ";
		$html.= "table td { padding:2px; border:1px solid #CCC } ";
		$html.= "table th { padding:2px; background-color: #BBB; font-weight:bold; border:1px solid #CCC } ";
		$html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
		$html.= "</style>";
		$html.= "<body>";
		$html.= $_POST["corpo"];
		$html.= "</body></html>";

		ini_set('max_execution_time', 600);
		ini_set('memory_limit', '-1');

		$options = new Options();
		$options->set('defaultFont', 'Helvetica');
		$options->setIsRemoteEnabled(true);
		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html);
		$dompdf->setPaper($formato, $orientamento);
		$dompdf->set_option('defaultFont', 'Helvetica');
		$dompdf->render();
		$dompdf->stream("Anteprima.pdf", array("Attachment" => false));

	}
?>
