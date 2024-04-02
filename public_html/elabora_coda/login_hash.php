<?
  if (!empty($elabora_coda)) {
    /* SVUOTO TABELLA VERIFICA LOGIN CONTEMPORANEI */

      $sql = "DELETE FROM b_login_hash WHERE timestamp < '" . date("Y-m-d H:i:s",strtotime("-1 hour",time())) . "'";
      $ris = $pdo->query($sql);

      $sql = "DELETE FROM b_check_sessions WHERE timestamp < '" . date("Y-m-d H:i:s",strtotime("-1 hour",time())) . "'";
      $ris = $pdo->query($sql);

    /* SVUOTO TABELLA VERIFICA LOGIN CONTEMPORANEI */
  }
?>
