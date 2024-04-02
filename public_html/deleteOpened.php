<?
  include_once("../config.php");
  if (isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"]==="0") {
    $sql = "SELECT b_buste.*, r_partecipanti.codice_gara, r_partecipanti.codice_lotto, b_log_aperture.timestamp AS apertura
            FROM b_buste
            JOIN r_partecipanti ON b_buste.codice_partecipante = r_partecipanti.codice
            JOIN b_log_aperture ON b_buste.codice_busta = b_log_aperture.codice_busta AND r_partecipanti.codice = b_log_aperture.codice_partecipante
            WHERE b_buste.aperto = 'S' AND b_buste.codice_gara NOT IN (1545,1155,3685,4575,10690,1337,3656,18556,4712) AND DATE(b_log_aperture.timestamp) < '".date('Y-m-d',strtotime('-1 week'))."' AND b_log_aperture.esito = 'Positivo' ORDER BY b_log_aperture.timestamp ASC ";
    $ris_buste_aperte = $pdo->query($sql);
    if ($ris_buste_aperte->rowCount() > 0) {
      $size = 0;
      echo "<ul>";
      while($busta = $ris_buste_aperte->fetch(PDO::FETCH_ASSOC)) {
        if (file_exists($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"])) {
           $single_size = filesize($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"]);
           //if (1) {
           if (unlink($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"])) {
            @unlink($config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"].".tsr");
            echo "<li>" . $config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"] . " - <strong>" . human_filesize($single_size) . "</strong> - " . mysql2date($busta["apertura"])."</li>";
            $size += $single_size;
           } else {
            echo "<li>ERRORE: " . $config["doc_folder"] . "/" . $busta["codice_gara"] . "/" . $busta["codice_lotto"] . "/" . $busta["nome_file"] . "</li>";
          }
        }
      }
      echo "</ul>";
      echo "<br><h1>Spazio recuperato da buste aperte: ";
      echo human_filesize($size);
      echo "</h1>";
    }
  }
?>