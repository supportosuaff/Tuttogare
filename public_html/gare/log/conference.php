<?
  include_once("../../../config.php");
	$error = true;
	$edit = false;
	$lock = true;
  if (isset($_GET["codice"])) {
    if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
      $codice_fase = getFaseReferer($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]);
      if ($codice_fase!==false) {
        $esito = check_permessi_gara($codice_fase,$_GET["codice"],$_SESSION["codice_utente"]);
        $edit = $esito["permesso"];
        $lock = $esito["lock"];
      }
      if ($edit) {
        include_once($root."/inc/zoomMtg.class.php");
        $zoom = new zoomMtg();
        $meeting = $zoom->getMeetingFromDB("gare",$_GET["codice"],$_GET["sub_elemento"],base64_decode($_GET["contesto"]),$_GET["meeting"]);
        if (!empty($meeting)) {
          $response = json_decode($meeting["response"],true);
          $info = $zoom->getPastMeeting($response["uuid"]);
          ?>
          <h1><?= $info["topic"] ?></h1>
          <span class="fa fa-calendar"></span> Data e ora inizio: <strong><?= date("d/m/Y H:i:s",strtotime($info["start_time"])); ?></strong> - 
          Data e ora fine: <strong><?= date("d/m/Y H:i:s",strtotime($info["end_time"])); ?></strong> - 
          <? /* <span class="fa fa-clock-o"></span> Durata: <strong><?= $info["total_minutes"] ?> minuti</strong> - */ ?>
          <span class="fa fa-user"></span> Partecipanti: <strong><?= $info["participants_count"] ?></strong>
          <?
          if (!empty($info["participants"])) {
            ?>
            <table width="100%" class="elenco">
              <? foreach($info["participants"] AS $partecipante) { ?>
                <tr>
                  <td><?= $partecipante["name"] ?></td>
                </tr>
              <? } ?>
            </table>
            <?
          }
        }
      }
    }
  }
?>