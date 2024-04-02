<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  include($root."/dgue/config.php");
  if (is_operatore() && isset($_POST["espd"])) {
    $json = json_decode(file_get_contents($root."/dgue/templates/criminal_convictions/definition.json"),true);
    $values = findValues($_POST["espd"],$json);
    $testo = "";
    if ($values["_01"][0] == "true") {
      $testo .= "La sentenza di condanna definitiva ha riconosciuto l'attenuante della collaborazione come definita dalle singole fattispecie di reato?\n\r";
      $testo .= ($values["_01"][2] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      $testo .= "La sentenza definitiva di condanna prevede una pena detentiva non superiore a 24 mesi?\n\r";
      $testo .= ($values["_01"][3] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      $testo .= "E' stato risarcito interamente il danno?\n\r";
      $testo .= ($values["_01"][4] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      $testo .= "Si &egrave; impegnato formalmente a risarcire il danno?\n\r";
      $testo .= ($values["_01"][5] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      $testo .= "per le ipotesi 1) e 2 l'operatore economico ha adottato misure di carattere tecnico o organizzativo e relativi al personale idonei a prevenire ulteriori illeciti o reati?\n\r";
      $testo .= ($values["_01"][6] == "true") ? "Si" : "No";
      $testo .= "\n\r";
      if ($values["_01"][6] == "true") {
        $testo .= $values["_01"][7];
        $testo .= "\n\r";
      }
      $testo .= "se le sentenze di condanne sono state emesse nei confronti dei soggetti cessati di cui all'art. 80 comma 3, indicare le misure che dimostrano la completa ed effettiva dissociazione dalla condotta penalmente sanzionata:\n\r";
      $testo .= $values["_01"][8];
    }
    echo $testo;
  }
