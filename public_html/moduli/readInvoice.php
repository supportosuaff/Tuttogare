<?
  session_start();
  if (!empty($_POST["filechunk"]) & !empty($_POST["callBackFunction"]) && isset($_SESSION["codice_utente"])) {
    include("../../config.php");
    $invoiceXML = simplexml_load_file($config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"]);
    ?>
    $("#progress_bar").slideUp();
    <?
    if (!empty($invoiceXML)) {
    	include_once($root."/inc/xml2json.php");
      $invoice = xmlToArray($invoiceXML);
      if (!empty($invoice["FatturaElettronica"])) {
        ?>
        invoice = JSON.parse('<?= json_encode($invoice) ?>');
        <?= $_POST["callBackFunction"] . "();" ?>
        <?
        die();
      }
    }
    ?>
    alert('Il file non è una fattura elettronica valida');
    <?
  }
?>
