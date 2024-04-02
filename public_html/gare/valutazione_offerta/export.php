<?
  use Dompdf\Dompdf;
  use Dompdf\Options;
  $error = true;
  include_once("../../../config.php");
  if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
    if (is_numeric($_GET["codice"])) {
      $codice_gara = $_GET["codice"];
      $codice_lotto = 0;
      $economica = true;
      if ($_GET["tecnica"] == "true") $economica = false;
      if (isset($_GET["lotto"]) && is_numeric($_GET["lotto"])) $codice_lotto = $_GET["lotto"];
      $name_session = "exportValutazioneOfferta";
      $name_session .= ($economica) ? "Economica" : "Tecnica";
      $name_session .= $codice_gara;
      $name_session .= "_".$codice_lotto;
      if (isset($_SESSION[$name_session])) {
        ob_start();
        $error = false;
        $gara = $_SESSION[$name_session]["gara"];
        $lotto = $_SESSION[$name_session]["lotto"];
        $partecipanti = $_SESSION[$name_session]["partecipanti"];
        $criteri = $_SESSION[$name_session]["criteri"];
        ?>
        <h1><?= $gara["oggetto"] ?></h1>
        <? if (empty($lotto)) {
            if (!empty($gara["cig"])) { ?>
              <h2>CIG: <?= $gara["cig"] ?></h2>
        <?  }
          } else { ?>
          <h2><? if (!empty($lotto["cig"])) { ?>CIG: <?= $lotto["cig"] ?> - <? } ?><?= $lotto["oggetto"] ?></h2>
        <? } ?>
        <?
          $i = 0;
          foreach($partecipanti AS $partecipante) {
            $i++;
            ?>
            <strong><?= $i ?> - <?= $partecipante["partita_iva"] ?> - <?= $partecipante["ragione_sociale"] ?></strong>
            <table width="100%">
              <thead>
                <tr>
                  <th width="20">#</th>
                  <th>Criterio</th>
                  <th width="100">Punteggio</th>
                </tr>
              </thead>
              <tbody>
                <?
                  $j = 0;
                  $totale = 0;
                  $totale_partecipante = 0;
                  foreach($criteri AS $criterio) {
                    $j++;
                    $totale += $criterio["punteggio"];
                    $totale_partecipante += $partecipante["punteggi"][$criterio["codice"]];
                    ?>
                    <tr>
                      <td style="text-align:center"><?= $j ?></td>
                      <td><?= $criterio["descrizione"] ?></td>
                      <td style="text-align:center"><strong><?= $partecipante["punteggi"][$criterio["codice"]] ?></strong> / <?= $criterio["punteggio"] ?></td>
                    </tr>
                    <?
                  }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" style="text-align:right">
                    <strong>Totale</strong>
                  </td>
                  <td style="text-align:center"><strong><?= $totale_partecipante ?></strong> / <?= $totale ?></td>
                </tr>
              </tfoot>
            </table><br>
            <?
          }
          $html = ob_get_clean();
          $html = "<html>
                    <head>
                      <style>
                        body { font-size: 10px }
                        table, th, td {
                          border: 1px solid grey;
                        }
                        th {
                          text-align:center;
                          background-color:#CCC;
                        }
                      </style>
                    </head>
                    <body>" . $html . "</body></html>";

          $options = new Options();
          $options->set('defaultFont', 'Helvetica');
          $options->setIsRemoteEnabled(true);
          $dompdf = new Dompdf($options);
          $dompdf->loadHtml($html);
          $dompdf->setPaper('A4', 'portrait');
          $dompdf->set_option('defaultFont', 'Helvetica');
          $dompdf->render();
          $dompdf->stream('export.pdf',["Attachment"=>false]);
      }
    }
  }
  if ($error) {
    echo "<h1>Errore nella richiesta</h1>";
  }
?>
