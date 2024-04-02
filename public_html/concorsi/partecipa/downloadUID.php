<?

use Dompdf\Dompdf;
use Dompdf\Options;
session_start();
if (is_numeric($_GET["codice_concorso"]) && is_numeric($_GET["codice_fase"])) {
  if (!empty($_SESSION["concorsi"][$_GET["codice_concorso"]][$_GET["codice_fase"]]["identificativo"]) && isset($_SESSION["codice_utente"])) {
    include("../../../config.php");

    $html= "<html>";
    $html.= "<style>";
    $html.= "body { font-size:10px; } table { width:100%; } ";
    $html.= "table td { padding:2px; border:1px solid #CCC } ";
    $html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
    $html.= "</style><body>";
    $html.= "<h1 style=\"text-align:center\"><img src=\"{$config['link_sito']}/img/tuttogarepa-logo-software-sx.png\" alt=\"Tutto gare\"><br>CONCORSO CODICE ". $_GET["codice_concorso"] . "<br><br></h1>";
    $html.= "<h1 style=\"text-align:center\">CODICE UNIVOCO IDENTIFICATIVO<br><br>" . $_SESSION["concorsi"][$_GET["codice_concorso"]][$_GET["codice_fase"]]["identificativo"] . "<br><BR><br></h1>";
    $html.= "<h1 style=\"text-align:center\">PASSWORD<br><br>" . $_SESSION["concorsi"][$_GET["codice_concorso"]][$_GET["codice_fase"]]["salt"] . "</h1>";
    $html.= "</body></html>";

    $options = new Options();
    $options->set('defaultFont', 'Helvetica');
    $options->setIsRemoteEnabled(true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->set_option('defaultFont', 'Helvetica');
    $dompdf->render();
		$dompdf->stream("UID.pdf");
  }
}
?>
