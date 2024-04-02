<? if (isset($record)) { ?>
  <table width="100%">
      <tr>
      <td class="etichetta"><strong>Data di pubblicazione</strong></td>
      <td colspan="2" >
        <? if ($record["stato"] < 3) { ?>
          <input type="text" class="datepick" id="data_pubblicazione" name="gara[data_pubblicazione]" title="Data di pubblicazione" value="<? echo mysql2date($record["data_pubblicazione"]) ?>" rel="S;10;10;D">
        <? } else { ?>
          <? echo mysql2date($record["data_pubblicazione"]) ?>
        <? } ?>
      </td><td class="etichetta"><strong>Livello</strong></td>
      <td>
        <select title="Livello"  name="gara[pubblica]" id="valore" rel="S;0;0;N">
          <option value="0">Non pubblicare</option>
          <option value="1">Area riservata</option>
          <option value="2">Area pubblica</option>
        </select>
      </td>
    </tr>
  </table><br>
  <script>
    $("#valore").val('<?= $record["pubblica"] ?>');
  </script>
    <div class="box">
      <h2>Riepilogo</h2>
      <table width="100%">
        <? if ($directory!="dialogo") { ?>
          <tr>
            <td class="etichetta"><strong>Termine richieste chiarimenti</strong></td>
            <td>
              <? if ($record["stato"] < 3) { ?>
                <input type="text" inline="true" class="datetimepick" title="Termine richieste chiarimenti"  name="gara[data_accesso]" id="data_accesso" value="<? echo mysql2datetime($record["data_accesso"]); ?>" rel="S;16;16;DT">
              <? } else { ?>
                <? echo mysql2datetime($record["data_accesso"]); ?>
              <? } ?>
            </td>
            <td class="etichetta"><strong>Termine ricevimento offerte</strong></td>
            <td>
              <? if ($record["stato"] < 3) { ?>
                <input type="text" class="datetimepick" title="Termine ricevimento offerte"  name="gara[data_scadenza]" id="data_scadenza" value="<? echo mysql2datetime($record["data_scadenza"]) ?>" rel="S;16;16;DT;data_accesso;>">
              <? } else { ?>
                <? echo mysql2datetime($record["data_scadenza"]) ?>
              <? } ?>
            </td>
            <td class="etichetta"><strong>Apertura offerte</strong></td>
            <td>
              <? if ($record["stato"] < 3) { ?>
                <input type="text" class="datetimepick" title="Apertura offerte"  name="gara[data_apertura]" id="data_apertura" value="<? echo mysql2datetime($record["data_apertura"]) ?>" rel="S;16;16;DT;data_scadenza;>">
              <? } else { ?>
                <? echo mysql2datetime($record["data_apertura"]) ?>
              <? } ?>
            </td>
          </tr>
        <?
        }
        ?>
        <tr>
          <td class="etichetta" colspan="5">
            Vuoi abilitare la richiesta di sopralluogo integrata nel sistema? Il termine ultimo per inviare le richieste da parte degli OE coincider&agrave; con il termine di richiesta dei chiarimenti
          </td>
          <td>
          <select title="Soppralluogo"  name="gara[attivaSopralluogo]" id="attivaSopralluogo" rel="S;0;0;A">
            <option value="">Seleziona...</option>
            <option value="S">Si</option>
            <option value="N">No</option>
          </select>
          <script>
            $("#attivaSopralluogo").val('<?= $record["attivaSopralluogo"] ?>');
          </script>
          </td>
        </tr>
        <tr>
        <td class="etichetta" colspan="5">
            Vuoi visualizzare la data di apertura in area pubblica?
          </td>
          <td>
          <select title="Visualizzazione in area Pubblica"  name="gara[flag_show_apertura]" id="flag_show_apertura" rel="S;0;0;A">
            <option value="S">Si</option>
            <option value="N">No</option>
          </select>
          <script>
            $("#flag_show_apertura").val('<?= $record["flag_show_apertura"] ?>');
          </script>
          </td>
        </tr>
        <?
        if ($record["stato"] < 3) {
          $sql = "SELECT * FROM b_modalita WHERE codice = :modalita ";
          $ris = $pdo->bindAndExec($sql,array(":modalita"=>$record["modalita"]));
          if ($ris->rowCount()>0) {
            $rec = $ris->fetch(PDO::FETCH_ASSOC);
            if ($rec["online"]=="S") {
              $sql = "SELECT * FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 58";
              $ris = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));
              if ($ris->rowCount() > 0) {
                $sql = "SELECT * FROM b_elenco_prezzi WHERE codice_gara = :codice_gara ORDER BY tipo, codice";
                $ris = $pdo->bindAndExec($sql,array(":codice_gara"=>$record["codice"]));
                if ($ris->rowCount() == 0) {
                  ?>
                  <tr>
                    <td colspan="6"><h3 class="ui-state-error">ATTENZIONE: Non &egrave; stato caricato l'elenco dei prezzi</h3></td>
                  </tr>
                  <?
                }
              }
            } else {
              ?>
              <tr>
                <td colspan="6"><h3 class="ui-state-error">ATTENZIONE: La procedura non &egrave; telematica</h3></td>
              </tr>
              <?
            }
          }
        }
         ?>
    </table>
  </div>
<? } ?>
