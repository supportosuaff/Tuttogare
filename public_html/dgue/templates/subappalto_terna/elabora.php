<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  include($root."/dgue/config.php");
  if (is_operatore() && isset($_POST["espd"])) {
    $json = json_decode(file_get_contents($root."/dgue/templates/subappalto/definition.json"),true);
    $values = findValues($_POST["espd"],$json);
    $testo = "Subappaltatori proposti:\n\r";
    for ($c_s=1;$c_s<=3;$c_s++) {
      if (!empty($values["_0".$c_s][0]) && !empty($values["_0".$c_s][1])) {
        $testo.= "Ragione Sociale: " . $values["_0".$c_s][0];
        $testo.="\n\r";
        $testo.= "Partita IVA: " . $values["_0".$c_s][1];
        $testo.="\n\r ---------------- \n\r";
      }
    }
    if (!empty($values["_0"][2])) {
      $testo.="\n\r";
      $testo.="Parti che si intendono subappaltare:";
      $testo.="\n\r";
      $testo.=$values["_0"][2];
    }
    $testo = str_replace("<p>", "\n\r", $testo);
    $testo = str_replace("<br>", "\n\r", $testo);
    echo strip_tags($testo);
  }
?>
