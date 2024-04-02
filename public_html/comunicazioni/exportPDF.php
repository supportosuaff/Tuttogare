<?

use Dompdf\Dompdf;
use Dompdf\Options;
session_start();
include_once("../../config.php");
include_once("{$root}/inc/funzioni.php");

if (!is_operatore()) {
    if(isset($_GET["sezione"]) && isset($_GET["codice"])) {
        $bind = [":codice_ente"=>$_SESSION["ente"]["codice"],":codice"=>$_GET["codice"],":sezione"=>$_GET["sezione"]];
        $strsql = "SELECT b_comunicazioni.* FROM b_comunicazioni WHERE b_comunicazioni.codice_ente = :codice_ente AND
                             codice_gara = :codice AND sezione = :sezione ORDER BY b_comunicazioni.timestamp DESC ";
        $elenco_comunicazioni = $pdo->go($strsql,$bind);
        if ($elenco_comunicazioni->rowCount() > 0) {
            ob_start();
            ?>
            <table style="font-size:12px" width="100%" class="elenco">
                <thead>
                    <tr>
                    <td>Data</td>
                    <td>Oggetto</td>
                    <td>Destinatari</td>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $destinatari = $pdo->prepare("SELECT b_operatori_economici.ragione_sociale, r_comunicazioni_utenti.codice AS protocollo_interno, r_comunicazioni_utenti.sync, r_comunicazioni_utenti.timestamp AS data_interna, r_comunicazioni_utenti.protocollo, r_comunicazioni_utenti.data_protocollo, r_comunicazioni_utenti.letto FROM b_operatori_economici JOIN r_comunicazioni_utenti ON b_operatori_economici.codice_utente = r_comunicazioni_utenti.codice_utente WHERE r_comunicazioni_utenti.codice_comunicazione = :codice");
                    $user = $pdo->prepare("SELECT CONCAT(cognome,' ',nome) AS user FROM b_utenti WHERE codice = :codice");
                    while ($comunicazione = $elenco_comunicazioni->fetch(PDO::FETCH_ASSOC)) { 
                        ?>
                    <tr>
                        <td width="13%"><? echo mysql2datetime($comunicazione["timestamp"]) ?></td>
                        <td width="58%">
                        <? echo $comunicazione["oggetto"] ?>
                        <div style="text-align:right">
                            <small>Utente: <? $user->bindValue(":codice",$comunicazione["utente_modifica"]); $user->execute(); echo $user->fetch(PDO::FETCH_ASSOC)["user"]; ?></small>
                        </div>
                        </td>
                        <td width="30%">
                            <?
                            $destinatari->bindValue(":codice",$comunicazione["codice"]);
                            $destinatari->execute();
                            if ($destinatari->rowCount()>0) {
                                while($destinatario = $destinatari->fetch(PDO::FETCH_ASSOC)) {    
                                    ?><strong><? echo $destinatario["ragione_sociale"] ?></strong><br><?
                                    if (!empty($destinatario["protocollo"])) { ?>
                                        <small>Prot. n. <?= $destinatario["protocollo"] ?> del <?= mysql2date($destinatario["data_protocollo"]) ?></small><?
                                    } else {
                                        ?><small>Prot. n. <?= $destinatario["protocollo_interno"] ?> del <?= mysql2date($destinatario["data_interna"]) ?> - Assegnato dal sistema</small><?
                                    }
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <? }  ?>
                </tbody>
            </table>
            <? 
            $corpo = ob_get_clean();

            $html = "<html>";
            $html.= "<style>";
            $html.= "body { font-size:10px; } table { width:100%; } ";
            $html.= "table td { padding:2px; border:1px solid #CCC } ";
            $html.= "table th { padding:2px; background-color: #BBB; font-weight:bold; border:1px solid #CCC } ";
            $html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
            $html.= "</style>";
            $html.= "<body>";
            $html.= $corpo;
            $html.= "</body></html>";
        
            ini_set('max_execution_time', 600);
            ini_set('memory_limit', '-1');

            $options = new Options();
            $options->set('defaultFont', 'Helvetica');
            $options->setIsRemoteEnabled(true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper("A4", "landscape");
            $dompdf->set_option('defaultFont', 'Helvetica');
            $dompdf->render();
            $dompdf->stream("elenco.pdf", array("Attachment" => false));
        }
            
    }
}
die('<meta http-equiv="refresh" content="0;URL=/index.php">');
?>