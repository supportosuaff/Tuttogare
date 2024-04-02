<?php
session_start();
if (isset($_SESSION["export_user"])) {
  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename=utenti.csv');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  foreach($_SESSION["export_user"] as $user) {
    echo "\"" . implode("\";\"",$user) . "\"\n";
  }
}
?>
