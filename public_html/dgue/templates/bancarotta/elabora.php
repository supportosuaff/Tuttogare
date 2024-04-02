<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
  include($root."/dgue/config.php");
  if (is_operatore() && isset($_POST["espd"])) {
    $json = json_decode(file_get_contents(__DIR__."/definition.json"),true);
    $values = findValues($_POST["espd"],$json);
    $testo = "";
    if ($values["_0"][0] == "true") {
      $testo .= "Si applica quanto previsto dell’art.95 del Codice della Crisi di Impresa (D.lgs 14/2019)?\n\r";
      $testo .= ($values["_0"][2] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      if ($values["_0"][2] == "true") {
        $testo .= "Provvedimento autorizzativo\n\r";
        $testo .= $values["_0"][3];
        $testo .= "\n\r";
      }
    }
    echo $testo;
  }
