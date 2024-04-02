<?
use Dompdf\Dompdf;
use Dompdf\Options;
session_start();
$errore = true;
if (is_numeric($_GET["codice_concorso"]) && is_numeric($_GET["codice_fase"])) {
  if (!empty($_SESSION["concorsi"][$_GET["codice_concorso"]][$_GET["codice_fase"]]["conferma"]) && isset($_SESSION["codice_utente"])) {
    include("../../../config.php");
    include_once($root."/inc/funzioni.php");
;
    $codice_gara = $_GET["codice_concorso"];

    $bind = array();
    $bind[":codice"] = $codice_gara;
    $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
    $strsql  = "SELECT b_concorsi.* FROM b_concorsi
                WHERE b_concorsi.codice = :codice ";
    $strsql .= "AND b_concorsi.annullata = 'N' ";
    $strsql .= "AND codice_gestore = :codice_ente ";
    $strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
    $risultato = $pdo->bindAndExec($strsql,$bind);

    if ($risultato->rowCount() > 0) {

      $record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

      $i = 0;
      $open = false;
      $last = array();
      $fase_attiva = array();

      $sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara  ";
      $ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice_gara"=>$record_gara["codice"]));
      if ($ris_fasi->rowCount() > 0) {
        $open = true;
        while($fase = $ris_fasi->fetch(PDO::FETCH_ASSOC)) {
          if ($fase["attiva"]=="S") {
            if ($i > 0) $open = false;
            $last = $fase_attiva;
            $fase_attiva = $fase;
          }
          $i++;
        }
      }

      if ($open) {
        $accedi = true;
      } else if (!empty($last["codice"])) {
        $sql_check = "SELECT * FROM r_partecipanti_concorsi JOIN r_partecipanti_utenti_concorsi ON r_partecipanti_concorsi.codice = r_partecipanti_utenti_concorsi.codice_partecipante
                WHERE r_partecipanti_concorsi.codice_gara = :codice_gara AND r_partecipanti_concorsi.codice_fase = :codice_fase AND r_partecipanti_concorsi.conferma = 1 AND r_partecipanti_concorsi.ammesso = 'S'
                AND r_partecipanti_concorsi.escluso = 'N' AND r_partecipanti_utenti_concorsi.codice_utente = :codice_utente ";
        $ris_check = $pdo->bindAndExec($sql_check,array(":codice_gara"=>$record_gara["codice"],":codice_fase"=>$last["codice"],":codice_utente"=>$_SESSION["codice_utente"]));
        if ($ris_check->rowCount() > 0) $accedi = true;
      }

    if ($accedi && !empty($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"])) {
      $partecipante = $_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]];
      $print_form = true;
      if (strtotime($fase_attiva["scadenza"]) > time()) {

        $strsql = "SELECT b_fasi_concorsi_buste.* FROM b_fasi_concorsi_buste";
        $ris_buste = $pdo->bindAndExec($strsql,array());
        if ($ris_buste->rowCount() > 0) {
            $buste = array();
            $msg = "";
            $error = false;
            while($busta = $ris_buste->fetch(PDO::FETCH_ASSOC)) {
              $buste[$busta["codice"]] = false;
              $sql_in = "SELECT b_buste_concorsi.* FROM b_buste_concorsi WHERE codice_gara = :codice_gara AND codice_busta = :codice_busta AND codice_partecipante = :codice_partecipante ";
              $ris_in = $pdo->bindAndExec($sql_in,array(":codice_busta"=>$busta["codice"],":codice_gara"=>$record_gara["codice"],":codice_partecipante"=>$partecipante["codice"]));
              if ($ris_in->rowCount()>0) {
                 $buste[$busta["codice"]] = true;
                 $presented = $ris_in->fetch(PDO::FETCH_ASSOC);
                 $msg .= "<li><h2>" . $busta["nome"] . "</h2><ul>";
                 if (!empty($presented["md5"])) $msg.= "<li>MD5: <strong>" . $presented["md5"] . "</strong></li>";
                 if (!empty($presented["sha1"])) $msg.= "<li>SHA1: <strong>" . $presented["sha1"] . "</strong></li>";
                 if (!empty($presented["sha256"])) $msg.= "<li>SHA256: <strong>" . $presented["sha256"] . "</strong></li>";
                 $msg .= "</ul></li>";
              } else {
                $error = true;
              }
            }
            if (!$error) {
              $errore = false;
              $html= "<html>";
              $html.= "<style>";
              $html.= "table { width:100%; }
              body { font-size:10px; }";
              $html.= "table td { padding:2px; border:1px solid #CCC } ";
              $html.= "table.no_border td { padding:2px; border:none; vertical-align:top;} ";
              $html.= "</style><body>";
              $html.= "<h1 style=\"text-align:center\"><img src=\"{$config['link_sito']}/img/tuttogarepa-logo-software-sx.png\" alt=\"Tutto gare\"></h1>";
              ob_start();
              ?>
              <h1>CONFERMA PARTECIPAZIONE - ID <? echo $record_gara["id"] ?></h1>
              <h2><? echo $record_gara["oggetto"] ?> - Fase: <?= $fase_attiva["oggetto"] ?><br></h2>
              <h2>CODICE UNIVOCO IDENTIFICATIVO: <?= $partecipante["identificativo"] ?><br></h2>
              <ul><?= $msg ?></ul><p>
              <h2><strong>Data e ora di conferma:</strong> <?= mysql2completedate($partecipante["timestamp"]) ?></h2>
            </p>
              <?
              $html.=ob_get_clean();
              $html.= "</body></html>";

              $options = new Options();
              $options->set('defaultFont', 'Helvetica');
              $options->setIsRemoteEnabled(true);
              $dompdf = new Dompdf($options);
              $dompdf->loadHtml($html);
              $dompdf->setPaper('A4', 'portrait');
              $dompdf->set_option('defaultFont', 'Helvetica');
              $dompdf->render();
        			$dompdf->stream("Ricevuta-partecipazione.pdf");

            }
          }
        }
      }
    }
  }
}
if ($errore) {
  ?>
  <h1>Errore nell'operazione</h1>
  <?
}
?>
