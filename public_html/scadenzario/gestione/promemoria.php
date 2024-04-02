<?
  if(!empty($_POST["id"])) {
    session_start();
    include("../../../config.php");
    include_once($root."/inc/funzioni.php");
;
    if(empty($_SESSION["codice_utente"]) || !check_permessi("scadenzario/gestione",$_SESSION["codice_utente"])) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
      die();
    } else {
      $rec_promemoria  = get_campi("b_alert_scadenze");
      $rec_promemoria["codice"] = $id_promemoria = $_POST["id"];
    }
  } else {
    $id_promemoria = $rec_promemoria["codice"];
  }
  ?>
  <div id="promemoria_<?= $id_promemoria ?>" class="box edit-box" style="border-left:5px solid #999;">
    <input type="hidden" name="promemoria[<?= $id_promemoria ?>][codice]" value="<?= $rec_promemoria["codice"] ?>">
    <table style="width:100%">
      <tr>
        <td style="width: 200px;">
          Data e ora promemoria: *
          <input type="text" name="promemoria[<?= $id_promemoria ?>][data_avviso]" value="<?= mysql2datetime($rec_promemoria["data_avviso"]) ?>" class="datetimepick" title="Data e ora" rel="S;16;16;DT">
        </td>
        <td>
          Descrizione:
          <input type="text" class="titolo_edit" name="promemoria[<?= $id_promemoria ?>][descrizione]" value="<?= $rec_promemoria["descrizione"] ?>" title="Descrizione" rel="N;0;0;A">
        </td>
        <td width="10">
          <button type="button" class="button button-caution button-circle button-small" onclick="elimina('<?= $id_promemoria ?>','scadenzario/gestione/promemoria');return false;"><i class="fa fa-times"></i></button>
        </td>
      </tr>
    </table>
  </div>
  <?
?>
