<?
use Dompdf\Dompdf;
use Dompdf\Options;
session_start();
$errore = true;
if (is_numeric($_GET["codice"]) && is_numeric($_GET["codice_fase"])) {
  if (!empty($_SESSION["riepilogo_fase_concorso"][$_GET["codice"]][$_GET["codice_fase"]])) {
    include("../../../config.php");
    include_once($root."/inc/funzioni.php");
    $html= "<html>";
    $html.= "<style>";
    $html.= "body { font-size:10px; } table { width:100%; } ";
    $html.= "table td { padding:2px; border:1px solid #CCC } ";
    $html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
    $html.= "</style><body>";
    // $html.= "<h1 style=\"text-align:center\"><img src=\"/img/tuttogarepa-logo-software-sx.png\" alt=\"Tutto gare\"></h1>";
    $html.=$_SESSION["riepilogo_fase_concorso"][$_GET["codice"]][$_GET["codice_fase"]];
    $html.= "</body></html>";

    $options = new Options();
    $options->set('defaultFont', 'Helvetica');
    $options->setIsRemoteEnabled(true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->set_option('defaultFont', 'Helvetica');
    $dompdf->render();
    $dompdf->stream("Riepilog.pdf");
    $errore = false;
  }
}

if ($errore) {
  ?>
  <h1>Errore nell'operazione</h1>
  <?
}
?>
