<?
  use Dompdf\Dompdf;
  use Dompdf\Options;

	include_once("../../../config.php");
  if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
    if (isset($_GET["codice_gara"]) && isset($_GET["codice_lotto"]) && isset($_GET["economica"])) {
      $strsql = "SELECT * FROM b_gestione_gare WHERE link = '/gare/configura_offerta/index.php'";
  		$risultato = $pdo->query($strsql);
  		if ($risultato->rowCount()>0) {
  			$gestione = $risultato->fetch(PDO::FETCH_ASSOC);
  			$esito = check_permessi_gara($gestione["codice"],$_GET["codice_gara"],$_SESSION["codice_utente"]);
        if ($esito["permesso"]) {
			    $codice = $_GET["codice_gara"];
    			$bind = array();
    			$bind[":codice"]=$codice;
    			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
    			$strsql = "SELECT * FROM b_gare WHERE codice = :codice ";
    			$strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
    			if ($_SESSION["gerarchia"] > 0) {
    				$bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
    				$strsql .= "AND (codice_ente = :codice_ente_utente OR codice_gestore = :codice_ente_utente) ";
    			}
    			$risultato = $pdo->bindAndExec($strsql,$bind);
    			if ($risultato->rowCount() > 0) {
				    $record_gara = $risultato->fetch(PDO::FETCH_ASSOC);
  					if (!empty($_GET["codice_lotto"]))
  					{
  						$codice_lotto = $_GET["codice_lotto"];
  						$bind=array();
  						$bind[":codice_lotto"] = $codice_lotto;
  						$sql_lotti = "SELECT * FROM b_lotti WHERE codice = :codice_lotto ORDER BY codice";
  						$ris_lotti = $pdo->bindAndExec($sql_lotti,$bind);
  						if ($ris_lotti->rowCount()>0)	{
                $lotto = $ris_lotti->fetch(PDO::FETCH_ASSOC);
                $record_gara["oggetto"] .= " - Lotto: " . $lotto["oggetto"];
                $record_gara["cig"] = $lotto["cig"];
                $record_gara["prezzoBase"] = $lotto["importo_base"];
                $record_gara["prezzoBase"] += $lotto["importo_oneri_ribasso"];
                $record_gara["prezzoBase"] += $lotto["importo_oneri_no_ribasso"];
                if ($record_gara["norma"] == "2023-36") {
                  $record_gara["prezzoBase"] += $lotto["importo_personale"];
                }
              }
            } else {
              $codice_lotto = 0;
            }
            $economica = ($_GET["economica"] == "S") ? true : false;
            $tabella = getFormOfferta($record_gara["codice"],$codice_lotto,$economica,false);
            if ($economica) {
              ob_start();
              ?>
              <table width="100%">
                <tr>
                  <th>Oneri di sicurezza aziendale interni</th>
                </tr>
                <tr>
                  <td style="text-align:center">
                    0,00
                  </td>
                </tr>
                <tr>
                  <th>Costo della manodopera</th>
                </tr>
                <tr>
                  <td style="text-align:center">
                    0,00
                  </td>
                </tr>
              </table>
              <?
              $tabella .= ob_get_clean();
            }
            $modello["corpo"] = "#tabella#";
            if ($economica) {
              $sql_modello = "SELECT * FROM b_modelli_standard WHERE codice = 5";
            } else {
              $sql_modello = "SELECT * FROM b_modelli_standard WHERE codice = 7";
            }
            $ris_modello = $pdo->query($sql_modello);
            if ($ris_modello->rowCount()>0) {
              $modello = $ris_modello->fetch(PDO::FETCH_ASSOC);
              $bind=array(":codice_modello"=>$modello["codice"],":codice_ente"=>$_SESSION["ente"]["codice"]);
              $sql = "SELECT * FROM b_modelli_enti WHERE attivo = 'S' AND codice_modello = :codice_modello AND codice_ente = :codice_ente";
              $ris = $pdo->bindAndExec($sql,$bind);
              if ($ris->rowCount()>0) $modello = $ris->fetch(PDO::FETCH_ASSOC);
            }
            $record_gara["prezzoBase"] = "&euro; " . number_format($record_gara["prezzoBase"],2,",",".");
            $vocabolario["#ragione-sociale#"] = "";
            $chiavi = array_keys($record_gara);
            foreach($chiavi as $chiave) {
              $vocabolario["#record_gara-".$chiave."#"] = $record_gara[$chiave];
            }
            $vocabolario["#elenco_operatori#"] = "";
            $vocabolario["#tabella#"] = $tabella;
            $html = strtr($modello["corpo"],$vocabolario);
            $html = "<html>
                      <head>
                        <style>
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


            ini_set('memory_limit', '2048M');
            ini_set('max_execution_time', 60000);

            $options = new Options();
            $options->set('defaultFont', 'Helvetica');
            $options->setIsRemoteEnabled(true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->set_option('defaultFont', 'Helvetica');
            $dompdf->render();
            $dompdf->stream('anteprima.pdf',["Attachment"=>false]);
          }
        }
      }
    }
  }
?>
