<?
session_start();
include_once("../../../config.php");
include_once($root."/layout/top.php");
//$DEBUG = true;
$edit = false;
$lock = true;
$codice = $_GET["codice"];
$ente = $_SESSION["ente"];
if ((isset($_GET["codice"]) || isset($_GET["cod"]))) {
  if (isset($_GET["cod"])) $_GET["codice"] = $_GET["cod"];
  if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
    $codice_fase = getFaseConcorso($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
    if ($codice_fase!==false) {
      $esito = check_permessi_concorso($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
      $edit = $esito["permesso"];
      $lock = $esito["lock"];
    }
    if (!$edit) {
      echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
      die();
    }
  } else {
    echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
    die();
  }
  if (!$lock) { ?>
  <h1>Pubblicità Legale</h1>
  <br/>
  <form name="box" method="post" action="save.php" rel="validate">
    <input type="hidden" name="codice_gara" value="<? echo $codice; ?>">
    <table class="box" id="pubblicita_legale" width="100%">
      <thead>
        <tr>
          <th>Data protocollo</th><th>Numero protocollo</th><th>Descrizione</th><th></th></tr>
        </tr>
      </thead>
      <tbody>
        <?
        $bind = array();
        $bind[":codice"] = $codice;
        $bind[":codice_ente"] = $ente["codice"];
        $sql_pubblicita = "SELECT * FROM b_pubblicita_legale_concorsi WHERE codice_gara = :codice AND codice_ente = :codice_ente";
        $ris_pubblicita = $pdo->bindAndExec($sql_pubblicita,$bind);
        if($ris_pubblicita->rowCount()>0){
          while($record_pubblicita = $ris_pubblicita->fetch(PDO::FETCH_ASSOC)){
            $id = $record_pubblicita["codice"];
            include("tr_pubblicita.php");
          }
        }else{
          $id="i_0";
          $record_pubblicita=get_campi("b_pubblicita_legale_concorsi");
          include("tr_pubblicita.php");
        }
        ?>
      </tbody>
    </table>
    <button class="aggiungi" onClick="aggiungi('tr_pubblicita.php','#pubblicita_legale');return false;"><img src="/img/add.png" alt="Aggiungi pubblicità">Aggiungi pubblicità</button>
    <input type="submit" class="submit_big" value="Salva">
  </form>
  <? }
  /*BOTTOM*/
  include($root."/concorsi/ritorna.php");
} else {
  echo "<h1>Gara non trovata</h1>";
}
include_once($root."/layout/bottom.php");
?>
