<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  include($root."/dgue/config.php");
  if (is_operatore() && isset($_POST["espd"])) {
    $json = json_decode(file_get_contents($root."/dgue/templates/violazioni_electronic/definition.json"),true);
    $values = findValues($_POST["espd"],$json);
    $testo = "";
    $testo .= "Si &egrave; stati autorizzati dal giudice delegato ai sensi dell'articolo 110, comma 3, lett. a) del Codice?\n\r";
    $testo .= ($values["_0"][3] == "true") ? "Si" : "No";
    $testo .= "\n\r";
    $testo .= "La partecipazione alla procedura di affidamento &egrave; subordinata ai sensi dell'art. 110, comma 5, all'avvalimento di altro operatore econimico?\n\r";
    $testo .= ($values["_0"][4] == "true") ? "Si" : "No";
    $testo .= "\n\r";
    if ($values["_0"][4] == "true") {
      $testo .= "Impresa ausiliaria: " . $values["_0"][5];
      $testo .= "\n\r";
    }
    echo $testo;
  }
