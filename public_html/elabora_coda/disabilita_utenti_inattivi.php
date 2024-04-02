<?
  if (!empty($elabora_coda)) {
    $current_time = (int) date('Hi');
    if($current_time >= 0 &&  $current_time <= 9) {
      $sql = "UPDATE b_utenti SET scaduto = 'S' 
              WHERE gruppo <> 1 AND gruppo <> 6 AND attivo = 'S' AND scaduto = 'N'
              AND last_login IS NOT NULL 
              AND DATE(last_login) < '" . date("Y-m-d",strtotime("-6 month",time())) . "'";
      $ris = $pdo->query($sql);
    }
  }
?>
