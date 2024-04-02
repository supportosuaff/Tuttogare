<?
include_once("../../../config.php");
$disable_alert_sessione = true;
include_once($root . "/layout/top.php");
include_once($root . "/inc/p7m.class.php");
$public = true;
if ((isset($_GET["cod"]) || isset($_POST["cod"])) && is_operatore()) {
  if (isset($_POST["cod"])) $_GET["cod"] = $_POST["cod"];
  $codice = $_GET["cod"];
  $bind = array();
  $bind[":codice"] = $codice;
  $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
  $strsql  = "SELECT * FROM b_bandi_albo WHERE codice = :codice ";
  $strsql .= "AND annullata = 'N' AND (data_scadenza > now() OR data_scadenza = 0)";
  $strsql .= "AND (codice_ente = :codice_ente OR codice_gestore = :codice_ente) ";
  $strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
  $risultato = $pdo->bindAndExec($strsql, $bind);
  if ($risultato->rowCount() > 0) {
    $record_bando = $risultato->fetch(PDO::FETCH_ASSOC);
    if($record_bando["dati_minimi_profilo"] == 'S' && ! empty($_SESSION["record_utente"]["profilo_completo"]) && $_SESSION["record_utente"]["profilo_completo"] !== 'S') {
      echo '<meta http-equiv="refresh" content="0;URL=/operatori_economici/id'.$_SESSION["record_utente"]["codice"].'-edit">'; die();
    }
    $bind = array();
    $bind[":codice_utente"] = $_SESSION["codice_utente"];
    $strsql = "SELECT * FROM b_operatori_economici WHERE codice_utente = :codice_utente";
    $ris = $pdo->bindAndExec($strsql,$bind);
    $operatore = $ris->fetch(PDO::FETCH_ASSOC);
    $bind[":codice_bando"] = $record_bando["codice"];
    $sql = "SELECT * FROM r_partecipanti_albo WHERE codice_bando = :codice_bando AND codice_utente = :codice_utente";
    $ris = $pdo->bindAndExec($sql,$bind);
    $partecipato = false;
    if ($ris->rowCount() > 0) {
      $partecipante = $ris->fetch(PDO::FETCH_ASSOC);
    }
    ?>
    <h1><?= traduci("PRESENTAZIONE ISTANZA") ?> <?= ($record_bando["manifestazione_interesse"] == "S") ? traduci("Indagine di mercato") : traduci("Elenco dei fornitori") ?> - ID <? echo $record_bando["id"] ?></h1>
    <h2><? echo $record_bando["oggetto"] ?></h2>
    <br>
    <strong>ATTENZIONE: Prima di inviare l'istanza è necessario compilare tutti i dati obbligatori del form seguente</strong>
    <br>
    <br>
    <?
    $domande = json_decode($record_bando["jsonQuestionario"],true);
    if (!empty($domande)) {
      if (!empty($partecipante["dataQuestionario"])) {
        $risposte = json_decode($partecipante["dataQuestionario"],true);
      }
      ob_start();
      ?>
      <form id="modulo" rel="validate" method="post" target="_self" action="save.php">
        <input type="hidden" id="codice_bando" name="codice_bando" value="<? echo $record_bando["codice"] ?>">
        <? include(__DIR__."/form.php"); ?>
        <input class="submit_big" type="submit" value="<?= traduci("Salva") ?>">
        <? if (!empty($partecipante["hashQuestionario"])) { ?>
          <a class="submit_big" style="background-color:#FA0" target="_blank" href="/albo_fornitori/questionario/download.php?partecipante=<?= $partecipante["codice"] ?>&codice_bando=<?= $record_bando["codice"] ?>">
            Scarica PDF
          </a>
        <? } ?>
        <? if (!empty($risposte)) { ?>
          <a class="submit_big" style="background-color:#333"  href="/albo_fornitori/modulo.php?cod=<?= $record_bando["codice"] ?>">Torna al pannello</a><br>
        <? } ?>
      </form>
      <?
      $content = ob_get_clean();
      echo $content;
    } else {
      echo "<h1>Errore di configurazione, si prega di contattare l'help desk</h1>";
    }
  	include_once($root."/layout/bottom.php");
  } else {
    echo "<h1>".traduci('impossibile accedere')."</h1>";
  }
} else {
  echo "<h1>".traduci('impossibile accedere')."</h1>";
}
?>