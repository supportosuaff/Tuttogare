<?
  session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  $errore = true;
  if (isset($_SESSION["codice_utente"])) {
    if (check_permessi("log-viewer",$_SESSION["codice_utente"])) {
      $strsql  = "SELECT *
                  FROM b_log_extract
                  WHERE 1
                  ORDER BY codice";
      $risultato  = $pdo->query($strsql);
      if ($risultato->rowCount() > 0) {
        $errore = false;
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=log-extract.csv');
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
          echo "\"";
          echo implode(";",$record);
          echo "\"\n";
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
