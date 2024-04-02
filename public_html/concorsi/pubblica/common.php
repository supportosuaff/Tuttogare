<? if (isset($record)) {

  $bind = array();
  $bind[":codice_gara"] = $record["codice"];
  $sql = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara ORDER BY codice LIMIT 0,1";
  $ris = $pdo->bindAndExec($sql,$bind);
  if ($ris->rowCount() > 0) {
    $fase = $ris->fetch(PDO::FETCH_ASSOC);

  ?>
  <table width="100%">
      <tr>
      <td class="etichetta"><strong>Data di pubblicazione</strong></td>
      <td width="20%">
        <? if ($record["stato"] < 3) { ?>
          <input type="text" class="datepick" id="data_pubblicazione" name="gara[data_pubblicazione]" title="Data di pubblicazione" value="<? echo mysql2date($record["data_pubblicazione"]) ?>" rel="S;10;10;D">
        <? } else { ?>
          <? echo mysql2date($record["data_pubblicazione"]) ?>
        <? } ?>
        </td><td class="etichetta"><strong>Livello</strong></td>
        <td>
          <select title="Livello" name="gara[pubblica]" id="valore" rel="S;0;0;N">
            <option value="0">Non pubblicare</option>
            <option value="1">Area riservata</option>
            <option value="2">Area pubblica</option>
          </select>
        </td>
      </tr>
     </table>
    <script>
      $("#valore").val('<?= $record["pubblica"] ?>');
    </script>
    <div class="box">
      <h2>Riepilogo</h2>
      <table width="100%">
          <tr>
            <td class="etichetta"><strong>Termine richieste chiarimenti</strong></td>
            <td>
              <? if ($record["stato"] < 3) { ?>
                <input type="text" inline="true" class="datetimepick" title="Termine ultimo accesso"  name="fase[chiarimenti]" id="chiarimenti" value="<? echo mysql2datetime($fase["chiarimenti"]); ?>" rel="S;16;16;DT">
              <? } else { ?>
                <? echo mysql2datetime($fase["chiarimenti"]); ?>
              <? } ?>
            </td>
            <td class="etichetta"><strong>Termine ricevimento offerte</strong></td>
            <td>
              <? if ($record["stato"] < 3) { ?>
                <input type="text" class="datetimepick" title="Termine ricevimento offerte"  name="fase[scadenza]" id="scadenza" value="<? echo mysql2datetime($fase["scadenza"]) ?>" rel="S;16;16;DT;chiarimenti;>">
              <? } else { ?>
                <? echo mysql2datetime($fase["scadenza"]) ?>
              <? } ?>
            </td>
            <td class="etichetta"><strong>Apertura offerte</strong></td>
            <td>
              <? if ($record["stato"] < 3) { ?>
                <input type="text" class="datetimepick" title="Apertura offerte"  name="fase[apertura]" id="apertura" value="<? echo mysql2datetime($fase["apertura"]) ?>" rel="S;16;16;DT;scadenza;>">
              <? } else { ?>
                <? echo mysql2datetime($fase["apertura"]) ?>
              <? } ?>
            </td>
          </tr>
        <?
        if ($record["stato"] < 3) {
          ?>
            <tr>
              <td colspan="6"><h3 class="ui-state-error">ATTENZIONE: Prima di procedere verificare di aver ricevuto la chiave privata necessaria all'apertura delle buste</h3></td>
            </tr>
          <?
        }
         ?>
    </table>
  </div>
  <? } else { ?>
    <h3 class="ui-state-error">ATTENZIONE: Fasi non presenti</h3>
  <? } ?>
<? } ?>
