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
    $codice_fase = getFase($_SERVER['QUERY_STRING'],$_SERVER['REQUEST_URI']);
    if ($codice_fase!==false) {
      $esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
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
?>
<h1>Rendicontazione</h1>
<?
$bind=array();
$bind[":codice"] = $codice;
$sql = "SELECT * FROM b_gare WHERE codice = :codice";
$risultato = $pdo->bindAndExec($sql,$bind);
if($risultato->rowCount()>0){
  $record = $risultato->fetch(PDO::FETCH_ASSOC);
  $_SESSION["gara"] = $record;
  $_SESSION["gara"]["contributo_sua"]=$record["contributo_sua"];
  if (empty($record["contributo_sua"])) {
    $codice_gara = $record["codice"];
    include($root."/gare/pubblica/contributo.php");
    if (isset($esito["contributo_sua"])) {
      $record["contributo_sua"] = $esito["contributo_sua"];
    }
  }
  ?>
  <br/>
    <form name="box" method="post" action="save.php" rel="validate">
      <input type="hidden" name="codice_gara" value="<? echo $codice; ?>">
      <table id="incassi" width="100%">
        <thead>
          <tr>
            <td>Contributo dovuto</td>
            <td colspan="3">
              <input type="text" name="gara[contributo_sua]" value="<?= $record["contributo_sua"] ?>" title="Contributo dovuto" rel="N;0;0;N">
            </td>
          <tr>
            <th>Importo</th><th>Descrizione</th><th>Data Incasso</th><th></th>
          </tr>
        </thead>
        <tbody>
          <?
            $bind=array();
            $bind[":codice"] = $codice;
            $bind[":codice_ente"] = $ente["codice"];

            $sql = "SELECT * FROM b_incassi WHERE codice_gara = :codice AND codice_ente = :codice_ente";
            $ris_incassi = $pdo->bindAndExec($sql,$bind);
            if ($ris_incassi->rowCount()>0) {
              while ($record_incasso = $ris_incassi->fetch(PDO::FETCH_ASSOC)) {
                $id_incasso = $record_incasso["codice"];
                include("tr_incasso.php");
              }
            }
          ?>
        </tbody>
        <tfoot>
          <tr id="totale">
          <?
            $sql = "SELECT sum(importo) as totale FROM b_incassi WHERE codice_gara = :codice AND codice_ente = :codice_ente";
            $ris_totale = $pdo->bindAndExec($sql,$bind);
            if ($ris_totale->rowCount()>0)
              while ($record_totale = $ris_totale->fetch(PDO::FETCH_ASSOC))
                echo "<td><strong><span class='importo_totale'>".$record_totale["totale"]."</span></strong></td><td colspan='3'><strong>Totale contributo: &euro; ".$record["contributo_sua"]."</strong></td>";
          ?>
          </tr>
        </tfoot>
<script>
  $("#incassi").on("change", function(){
    var prev_somma=parseFloat($(".importo_totale").text());
    var somma = 0;
    $(".importo").each(function(){
      somma+=parseFloat($(this).val());
    });
    $(".importo_totale").text(somma)
  });
</script>
      </table>
<button class="aggiungi" onClick="aggiungi('tr_incasso.php','#incassi');return false;"><img src="/img/add.png" alt="Aggiungi incasso">Aggiungi incasso</button>
<input type="submit" class="submit_big" value="Salva">
</form>
    <?
  }
  /*BOTTOM*/
    include($root."/gare/ritorna.php");
  } else {
    echo "<h1>Gara non trovata</h1>";
  }

  include_once($root."/layout/bottom.php");
  ?>
