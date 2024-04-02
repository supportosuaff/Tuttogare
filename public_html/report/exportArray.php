<?
  include_once("../../config.php");
  $token = "";
  if (isset($_GET["token"])) $token = $_GET["token"];
  if (isset($_SESSION["reportExport{$token}"])) {
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename=esportazione.csv');
    foreach($_SESSION["reportExport{$token}"] AS $row) {
      foreach($row AS $index => $value) {
        $value = html_entity_decode($value);
        $value = strip_tags($value);
        $value = preg_replace("/[^a-zA-Z0-9\/_|+ -\<\>\"\']/", '', $value);
        $row[$index] = $value;
      }
      echo '"' . implode('";"',$row) . '"' . PHP_EOL;
    };
  }

?>
