<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  if (is_operatore() && isset($_SESSION["testo_subappalto"])) {
    $_SESSION["testo_subappalto"] = str_replace("<p>", "\n\r", $_SESSION["testo_subappalto"]);
    $_SESSION["testo_subappalto"] = str_replace("<br>", "\n\r", $_SESSION["testo_subappalto"]);
    echo strip_tags($_SESSION["testo_subappalto"]);
  }
