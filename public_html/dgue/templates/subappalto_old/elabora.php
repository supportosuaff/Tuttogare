<?
  session_start();
  include("../../../../config.php");
  include_once($root."/inc/funzioni.php");
;
  if (is_operatore() && isset($_POST["subappalto"])) {
    $_SESSION["testo_subappalto"] = "Subappaltatori proposti:\n\r";
    foreach($_POST["subappalto"]["operatori"] AS $operatore) {
      if (!empty($operatore["ragione_sociale"]) && !empty($operatore["partita_iva"])) {
        $_SESSION["testo_subappalto"].="\n\r";
        $_SESSION["testo_subappalto"].= "Ragione Sociale: " . $operatore["ragione_sociale"];
        $_SESSION["testo_subappalto"].="\n\r";
        $_SESSION["testo_subappalto"].= "Partita IVA: " . $operatore["partita_iva"];
        $_SESSION["testo_subappalto"].="\n\r";
      }
    }
    if (!empty($_POST["subappalto"]["descrizione"])) {
      $_SESSION["testo_subappalto"].="\n\r";
      $_SESSION["testo_subappalto"].="Parti che si intendono subappaltare:";
      $_SESSION["testo_subappalto"].="\n\r";
      $_SESSION["testo_subappalto"].=$_POST["subappalto"]["descrizione"];
    }
  }
