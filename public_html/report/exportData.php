<?
  include_once("../../config.php");
  if (isset($_SESSION["exportTable"])) {
    header('Content-type: application/ms-excel');
    header('Content-Disposition: attachment; filename=esportazione.xls');
    echo $_SESSION["exportTable"];
  }

?>
