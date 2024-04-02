<?
session_start();
include_once("../../../config.php");
include_once($root."/inc/funzioni.php");
$edit = false;
$lock = true;
$codice_gara = $_POST["codice_gara"];
if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
  $codice_fase = getFaseRefererConcorso($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
	if ($codice_fase !== false) {
		$esito = check_permessi_concorso($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
    $edit = $esito["permesso"];
    $lock = $esito["lock"];
  }
  if (!$edit) {
    die();
  }
} else {
  die();
}
if($edit  & !$lock){
  if(isset($_POST["pubblicita"])){
   foreach($_POST["pubblicita"] as $codice_pubblicita => $pubblicita){
    $operazione = "INSERT";
    if(is_numeric($codice_pubblicita)) {
      $pubblicita["codice"] = $pubblicita;
      $operazione = "UPDATE";
    }
    $pubblicita["codice_gara"]=$codice_gara;
    $pubblicita["codice_ente"]=$_SESSION["ente"]["codice"];

    $salva = new salva();
  	$salva->debug = false;
  	$salva->codop = $_SESSION["codice_utente"];
  	$salva->nome_tabella = "b_pubblicita_legale_concorsi";
  	$salva->operazione = $operazione;
  	$salva->oggetto = $pubblicita;
  	$codice_pubblicita = $salva->save();
    if ($codice_pubblicita === false) $errore = true;
  }
  log_concorso($_SESSION["ente"]["codice"],$codice_gara,"UPDATE","Pubblicità legale");
}
  if (!isset($errore)) {
    $href = "/concorsi/pannello.php?codice=" . $codice_gara;
    $href = str_replace('"',"",$href);
    $href = str_replace(' ',"-",$href);
    ?>
    alert('Modifica effettuata con successo');
    window.location.href = '<? echo $href ?>';
  <?
  } else {
      alert('Errore nel salvataggio. Riprovare.');
  }
}
?>
