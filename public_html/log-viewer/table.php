<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  $errore = true;
  if (isset($_SESSION["codice_utente"])) {
    if (check_permessi("log-viewer",$_SESSION["codice_utente"])) {
      if(! empty($_POST["data_inizio"]) && ! empty($_POST["data_fine"])) {
        $bind = array();
        $strsql  = "SELECT *
                    FROM b_log
                    WHERE dataop >= :data_inizio  AND dataop <= :data_fine
                    ORDER BY codice";
        $bind[":data_inizio"] = datetime2mysql($_POST["data_inizio"]);
        $bind[":data_fine"] = datetime2mysql($_POST["data_fine"]);
        $risultato  = $pdo->bindAndExec($strsql,$bind);
        if ($risultato->rowCount() > 0) {
          $errore = false;
          header('Content-Description: File Transfer');
          header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename=log-'.basename($bind[":data_inizio"])."-".basename($bind[":data_fine"]).".csv");
          header('Expires: 0');
          header('Cache-Control: must-revalidate');
          header('Pragma: public');
          while($record=$risultato->fetch(PDO::FETCH_ASSOC)) {
            $test = @gzuncompress($record["istruzione"]);
            if (!empty($test)) {
              $record["istruzione"] = base64_decode(gzuncompress($record["istruzione"]));
            }
            $record[] = $record["istruzione"];
            unset($record["istruzione"]);
            echo implode($record,";");
            echo "\n";
          }
        }
      }
    }
  }
  if ($errore) {
    ?>
    <h1>Errore nella richiesta</h1>
    <?
  }
?>
