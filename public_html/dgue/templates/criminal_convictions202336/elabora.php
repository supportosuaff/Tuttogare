<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
  include($root."/dgue/config.php");
  if (is_operatore() && isset($_POST["espd"])) {
    $json = json_decode(file_get_contents(__DIR__."/definition.json"),true);
    $values = findValues($_POST["espd"],$json);
    $testo = "";
    if ($values["_01"][0] == "true") {
      $testo .= "E' stato risarcito interamente il danno?\n\r";
      $testo .= ($values["_01"][4] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      $testo .= "Si &egrave; impegnato formalmente a risarcire il danno?\n\r";
      $testo .= ($values["_01"][5] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      $testo .= "Ha chiarito i fatti e le circostanze in modo globale e collaborando attivamente con le autorità investigative?\n\r";
      $testo .= ($values["_01"][9] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      $testo .= "L'operatore economico ha adottato misure di carattere tecnico o organizzativo e relativi al personale idonei a prevenire ulteriori illeciti o reati?\n\r";
      $testo .= ($values["_01"][6] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      if ($values["_01"][6] == "true") {
        $testo .= $values["_01"][7];
        $testo .= "\n\r";
      }
    }
    echo $testo;
  }
