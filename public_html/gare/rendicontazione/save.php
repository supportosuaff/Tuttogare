<?
  session_start();
  include_once("../../../config.php");
  include_once($root."/inc/funzioni.php");
  $edit = false;
  $lock = true;
  if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"]) && !empty($_POST["codice_gara"])) {
    $codice_gara = $_POST["codice_gara"];
    $codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
  	if ($codice_fase !== false) {
  		$esito = check_permessi_gara($codice_fase,$_POST["codice_gara"],$_SESSION["codice_utente"]);
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
    if (isset($_POST["gara"]["contributo_sua"])) {
      $contributo["codice"]=$codice_gara;
      $contributo["contributo_sua"]=$_POST["gara"]["contributo_sua"];
      $salva = new salva();
    	$salva->debug = false;
    	$salva->codop = $_SESSION["codice_utente"];
    	$salva->nome_tabella = "b_gare";
    	$salva->operazione = "UPDATE";
    	$salva->oggetto = $contributo;
    	$codice_gara = $salva->save();
    }
    if (!empty($_POST["incasso"])) {
     foreach($_POST["incasso"] as $codice_incasso => $incasso){
      $operazione = "INSERT";
      if(is_numeric($codice_incasso)) {
        $incasso["codice"] = $codice_incasso;
        $operazione = "UPDATE";
      }
      $incasso["codice_gara"]=$codice_gara;
      $incasso["codice_ente"]=$_SESSION["ente"]["codice"];
      $salva = new salva();
    	$salva->debug = false;
    	$salva->codop = $_SESSION["codice_utente"];
    	$salva->nome_tabella = "b_incassi";
    	$salva->operazione = $operazione;
    	$salva->oggetto = $incasso;
    	$codice_incasso = $salva->save();
      if ($codice_incasso === false) $errore = true;
    }
  }
      log_gare($_SESSION["ente"]["codice"],$codice_gara,"UPDATE","Rendicontazione incassi");

      if (!isset($errore)) {
        $href = "/gare/pannello.php?codice=" . $codice_gara;
        $href = str_replace('"',"",$href);
        $href = str_replace(' ',"-",$href);
        ?>
        alert('Modifica effettuata con successo');
        window.location.href = '<? echo $href ?>';
      <?
      } else { ?>
          alert('Errore nel salvataggio. Riprovare.');
        <?
      }
    }
?>
