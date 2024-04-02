<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  include($root."/dgue/config.php");
  if (is_operatore() && isset($_POST["espd"])) {
    $json = json_decode(file_get_contents($root."/dgue/templates/violazioni_concordato/definition.json"),true);
    $values = findValues($_POST["espd"],$json);
    $testo = "";
    $testo .= "&Egrave; stato emesso il decreto di ammissione al concordato preventivo di cui all'art. 47 D.Lgs 14/2019, come previsto dall’art. 110 comma 5?\n\r";
    $testo .= ($values["_0"][1] == "true") ? "Si" : "No";
    $testo .= "\n\r";
    $testo .= "La partecipazione alla procedura di affidamento &egrave; subordinata ai requisiti di cui all'art. 110, comma 6, nonostante sia stato emesso il decreto di ammissione al concordato preventivo di cui all'art. 47 D.Lgs 14/2019?\n\r";
    $testo .= ($values["_0"][2] == "true") ? "Si" : "No";
    $testo .= "\n\r";
    $testo .= "La partecipazione alla procedura di affidamento &egrave; subordinata all'avvalimento dei requisiti di un altro soggetto in quanto non ancora depositato il decreto di ammissione al concordato preventivo cui all’art. 47 D.Lgs 14/2019, come previsto dall'art. 110 comma 4?\n\r";
    $testo .= ($values["_0"][3] == "true") ? "Si" : "No";
    $testo .= "\n\r";
    echo $testo;
  }
