<?
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (!empty($_SESSION["record_utente"]) && ($_SESSION["gerarchia"] === "1" || $_SESSION["gerarchia"] === "0") && !empty($_SESSION["ente"]) && !empty($_GET["type"])) {
    $codice_ente = $_SESSION["ente"]["codice"];
    if ($_SESSION["gerarchia"] === "0" || $_SESSION["record_utente"]["codice_ente"] = $codice_ente) {
      $type = "";
      $mime = "";
      if ($_GET["type"] === "zip") {
        $type = "zip";
        $mime = "application/zip";
      }
      if ($_GET["type"] === "json") {
        $type = "json";
        $mime = "plain/text";
      }
      if (!empty($type)) {
        $filePath = "../../export/{$codice_ente}/data.{$type}";
        if (file_exists($filePath)) {
          header("Content-Type: {$mime}");
          header('Content-Description: File Transfer');
          header('Content-Disposition: attachment; filename=data.'.$type);
          header('Content-Transfer-Encoding: binary');
          header('Expires: 0');
          header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
          header('Pragma: public');

          $chunk = 1024 * 1024;
          $handle = fopen($filePath, 'rb');
          while (!feof($handle)) {
            $buffer = fread($handle, $chunk);
            echo $buffer;
            ob_flush();
            flush();
          }
          fclose($handle);
          die();
        }
      }
    }
  }
  echo "<h1>Impossibile accedere</h1>";
?>