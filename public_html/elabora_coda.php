<?
  set_time_limit (590);
  session_start();
  include_once("../config.php");
  if ((isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] === "0") || get_client_ip() === $_SERVER["SERVER_ADDR"]) {
    include_once($root."/inc/funzioni.php");

    function system_shutdown_and_connection_closing() {
      if(isset($pdo)) unset($pdo);
    }

    register_shutdown_function('system_shutdown_and_connection_closing');

    $elabora_coda = true;
    include($root."/elabora_coda/login_hash.php");
    include($root."/elabora_coda/update_pem.php");
    include($root."/elabora_coda/disabilita_utenti_inattivi.php");
    include($root."/elabora_coda/check_coda.php");
    include($root."/elabora_coda/memo.php");
    include($root."/elabora_coda/check_revisioni.php");
    include($root."/elabora_coda/alert_revisioni.php");
    include($root."/elabora_coda/smaltisci_coda.php");
    include($root."/elabora_coda/send_scadute.php");
  }
 ?>
