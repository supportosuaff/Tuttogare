<?
  use Dompdf\Dompdf;
  use Dompdf\Options;

	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	$edit = false;
	$lock = true;
		if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
			$codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
			if ($codice_fase !== false) {
				$esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
			$bind = array();
			$bind[":codice_gara"] = $_GET["codice"];
			$strsql = "SELECT * FROM b_gare WHERE codice = :codice_gara";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
        $record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
        $bind = array();
        $bind[":codice_gara"] = $_GET["codice"];
        $strsql = "SELECT r_partecipanti.*, MAX(r_rdo_ad.timestamp_trasmissione) AS time_risposta
                   FROM r_partecipanti
                   LEFT JOIN r_rdo_ad ON r_partecipanti.codice = r_rdo_ad.codice_partecipante
                   WHERE codice_gara = :codice_gara
                   AND codice_capogruppo = 0 
                   GROUP BY r_partecipanti.codice ORDER BY primo DESC, ragione_sociale ";
        $risultato_partecipanti = $pdo->bindAndExec($strsql,$bind);
        $sql = "SELECT * FROM temp_inviti WHERE codice_gara = :codice_gara AND attivo = 'S'";
        $ris_inviti = $pdo->bindAndExec($sql,$bind);
        $partecipanti = [];
        if ($risultato_partecipanti->rowCount() > 0) {
          $partecipanti = $risultato_partecipanti->fetchAll(PDO::FETCH_ASSOC);
        }
        if ($ris_inviti->rowCount() > 0) {
          while($partecipante = $ris_inviti->fetch(PDO::FETCH_ASSOC)) {
            $partecipanti[] = $partecipante;
          }
        }
        ob_start();
        ?>
        <html>
          <head>  
            <style>
            body { font-size:10px } table { width:100%; } 
            table td { padding:2px; border:1px solid #CCC } 
            table.no_border td { padding:2px; border:none; vertical-align:top;} 
            </style>
          </head>
          <body>
            <div style="text-align:center">
              <img src="<?= $config["link_sito"] ?>/documenti/enti/<?= $_SESSION["ente"]["logo"] ?>" width="200"><br>
              <h1><?= $record_gara["oggetto"] ?></h1>
            </div>
            <?= $record_gara["descrizione"] ?>
            <table width="100%">
              <tr>
                <td class="etichetta">Importo di affidamento</td>
                <td><strong>&euro; <?= number_format($record_gara["importoAggiudicazione"],2,",",".") ?></strong></td>
                <td class="etichetta">Estremi atto</td>
                <td><strong><?= $record_gara["numero_atto_esito"] ?> del <?= mysql2date($record_gara["data_atto_esito"]) ?></strong></td>
              </tr>
            </table>
            <?
              if (count($partecipanti) > 0) {
                ?>
                <h2>Partecipanti</h2>
                <table width="100%">
                  <tr>
                    <th>#</th>
                    <th>Partita IVA</th>
                    <th>Ragione Sociale</th>
                    <th>Data offerta</th>
                    <th></th>
                  </tr>
                  <? 
                    $cont = 0;
                    foreach($partecipanti AS $partecipante) {
                      $cont++;
                      ?>
                      <tr>
                        <td><?= $cont ?></td>
                        <td><?= $partecipante["partita_iva"] ?></td>
                        <td><?= $partecipante["ragione_sociale"] ?></td>
                        <td>
                          <?
                            if (!empty($partecipante["time_risposta"])) {
                              echo mysql2datetime($partecipante["time_risposta"]);
                            }
                          ?>
                        </td>
                        <td><?= (isset($partecipante["primo"]) && $partecipante["primo"] == "S") ? "Aggiudicatario" : "" ?></td>
                      </tr>
                      <?
                    }
                  ?>
                </table>
              </body>
            </html>
            <?
            $html = ob_get_clean();
            $options = new Options();
            $options->set('defaultFont', 'Helvetica');
            $options->setIsRemoteEnabled(true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->set_option('defaultFont', 'Helvetica');
            $dompdf->render();
            $content = $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));    
            
          }
			} else {
				?>
					alert('Si è verificato un errore durante il salvataggio. Riprovare');
				<?
			}
	}

?>
