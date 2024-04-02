<?
  session_start();
  include_once "../../config.php";
  include_once $root . "/inc/funzioni.php";

  $tabelle = array('b_allegati_albo','b_allegati_dialogo','b_allegati_contratto','b_allegati','b_allegati_me','b_allegati_sda',);
  if(empty($_POST["codice_allegato"]) || empty($_POST["tabella"]) || is_operatore() || empty($_SESSION["codice_utente"]) || !in_array("b_".$_POST["tabella"], $tabelle)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    die();
  } else {
    $sth = $pdo->prepare("REPLACE INTO `b_metadati`(`codice_schema`, `codice_file`, `tabella`, `valore`, `utente_modifice`) VALUES (:codice_schema,:codice_file,:tabella,:valore,:utente_modifice)");
    $sth->bindValue(':codice_file', $_POST["codice_allegato"]);
    $sth->bindValue(':tabella', $_POST["tabella"]);
    $sth->bindValue(':utente_modifice', $_SESSION["codice_utente"]);
    if(!empty($_POST["meta"])) {
      foreach ($_POST["meta"] as $codice_schema => $valore) {
        if(!empty($valore)) {
          $sth->bindValue(':codice_schema', $codice_schema);
          $sth->bindValue(':valore', $valore);
          $sth->execute();
        }
      }
    }
    ?>
    $("#div_metadati").dialog( "close" );
    <?
  }
?>
