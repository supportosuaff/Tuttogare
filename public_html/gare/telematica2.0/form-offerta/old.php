<?
  if (isset($ris_buste) && ($ris_buste->rowCount() > 0) && !empty($submit) && !empty($tipo)) {
    if ($tipo=="economica") {
      $bind = array();
      $bind[":codice_gara"] = $record_gara["codice"];
      $sql_tipo = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 40)";
      $ris_tipo = $pdo->bindAndExec($sql_tipo,$bind);
      $elenco_prezzi = false;
      $rialzo = false;
      $id_offerta = 0;
      if ($ris_tipo->rowCount() > 0) {
        $opzione = $ris_tipo->fetch(PDO::FETCH_ASSOC);
        if ($opzione["opzione"] == "58") {
          $bind[":codice_lotto"] = $codice_lotto;
          $sql_elenco = "SELECT * FROM b_elenco_prezzi WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto ORDER BY tipo ";
          $ris_elenco = $pdo->bindAndExec($sql_elenco,$bind);
          if ($ris_elenco->rowCount()>0) $elenco_prezzi = true;
        } else if ($opzione["opzione"] == "270") {
          $rialzo = true;
        }
      }
      ?>
      <table width="100%">
      <thead>
        <tr>
          <th><?= traduci("Descrizione") ?></th>
          <? if ($elenco_prezzi) { ?>
            <th width="10%"><?= traduci("unita") ?></th><th width="10%"><?= traduci("Quantita") ?></th>
            <th width="10%"><?= traduci("Prezzo Unitario Offerto") ?></th>
          <? } else { ?>
            <th width="10%"><?= ($rialzo) ? traduci("Rialzo") : traduci("Ribasso") ?> <?= traduci("Offerto") ?></th>
          <? } ?>
        </tr>
      </thead>
        <?
        if ($elenco_prezzi) {
          $tipo_prezzo = "";
          while($prezzo = $ris_elenco->fetch(PDO::FETCH_ASSOC)) {
            $id_offerta++;
            if ($tipo_prezzo != $prezzo["tipo"]) {
              $tipo_prezzo = $prezzo["tipo"];
              ?>
              <tr style="font-weight:bold">
                <td colspan="4"><?= strtoupper($tipo_prezzo) ?></td>
              </tr>
              <?
            }
            ?>
            <tr>
              <td><? echo $prezzo["descrizione"] . " - " . $prezzo["tipo"]; ?></td>
              <td><? echo $prezzo["unita"] ?></td>
              <td><? echo number_format($prezzo["quantita"],2,",",".") ?></td>
              <td>
                <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="economica">
                <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="<? echo $prezzo["codice"] ?>">
                <input class="titolo_edit prezzo" type="text" quantita="<?= $prezzo["quantita"] ?>" name="offerta['<? echo $id_offerta ?>'][offerta]" id="offerta_<? echo $id_offerta ?>_economica" title="Offerta" rel="S;0;0;N">
              </td>
            </tr>
            <?
          }
            ?>

            <tr><td colspan="3" style="text-align:right; font-weight:bold">
            <script>
              $(".prezzo").change(function() {
                totale_offerta = 0;
                $(".prezzo").each(function() {
                  if (valida($(this)) == "") {
                    totale_offerta = totale_offerta + +(parseFloat($(this).val())) * $(this).attr("quantita");
                  }
                });
                totale_offerta = number_format(totale_offerta,2,",",".");
                $("#totale_offerta").html("&euro; " + totale_offerta);
              });
            </script>
            <?= traduci("Totale offerta") ?></td><td style="font-weight:bold" id="totale_offerta">&euro; 0,00</td></tr>

            <?
          } else {
            $multiprezzo = false;
            if ($codice_lotto == 0) {
              $bind = array();
              $bind[":codice_gara"] = $record_gara["codice"];
              $sql_multi = "SELECT b_tipologie_importi.titolo, b_tipologie_importi.codice FROM b_tipologie_importi JOIN
                      b_importi_gara ON b_tipologie_importi.codice = b_importi_gara.codice_tipologia WHERE
                      b_importi_gara.codice_gara = :codice_gara ";
              $ris_multi = $pdo->bindAndExec($sql_multi,$bind);
              if ($ris_multi->rowCount()>1 && $record_gara["ribassoSingoliImporti"]) {
                $multiprezzo = true;
                while($singlePrice = $ris_multi->fetch(PDO::FETCH_ASSOC)) {
                  $id_offerta++;
                  ?>
                  <tr>
                    <td <? if ($elenco_prezzi) echo "colspan='3'"; ?>><?= ($rialzo) ? traduci("Rialzo") : traduci("Ribasso") ?> <?= traduci("percentuale") ?> <?= traduci($singlePrice["titolo"]) ?> <?= traduci("offerto") ?></td>
                    <td>
                      <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="economica_<?= $singlePrice["codice"] ?>">
                      <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="0">
                      <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="offerta_<? echo $id_offerta ?>_economica" title="<?= ($rialzo) ? traduci("Rialzo") : traduci("Ribasso") ?>" rel="S;0;0;N">
                    </td>
                  </tr>
                  <?
                }
              }
            }
            $id_offerta++;
        ?>
          <tr>
            <td <? if ($elenco_prezzi) echo "colspan='3'"; ?>><?= ($rialzo) ? traduci("Rialzo") : traduci("Ribasso") ?> <?= traduci("percentuale") ?> <? if ($multiprezzo) echo traduci("complessivo") ?> <?= traduci("offerto") ?></td>
            <td>
              <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="economica">
              <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="0">
              <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="offerta_<? echo $id_offerta ?>_economica" title="<?= ($rialzo) ? traduci("Rialzo") : traduci("Ribasso") ?>" rel="S;0;0;N">
            </td>
          </tr>
        <?
          }
          $bind = array();
          $bind[":codice_gara"] = $record_gara["codice"];
          $sql_valutazione = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.valutazione <> '' AND b_criteri_punteggi.economica = 'S' AND b_criteri_punteggi.migliorativa = 'S' ";
          $ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
          if ($ris_valutazione->rowCount() > 0) {
                while($valutazione_tecnica = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
                  $id_offerta++;
                  ?>
                    <tr>
                      <td <? if ($elenco_prezzi) echo "colspan='3'"; ?>><strong><?= $valutazione_tecnica["descrizione"] ?></strong> -
                      <?
                        switch ($valutazione_tecnica["valutazione"]) {
                          case "P": echo "Valutazione Proporzionale"; break;
                          case "I": echo "Valutazione Proporzionale Inversa"; break;
                          case "S": echo "Valutazione a step"; break;
                        }
                      ?></td>
                      <td>
                        <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="migliorativa">
                        <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="<?= $valutazione_tecnica["codice"] ?>">
                        <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="tecnica_<? echo $id_offerta ?>_<? $valutazione_tecnica["codice"] ?>" title="Valore" rel="S;0;0;N">
                      </td>
                    </tr>
                  <?
                }
          }
          $bind = array();
          $bind[":codice_gara"] = $record_gara["codice"];
          $sql_tempo = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_criteri_punteggi.temporale = 'S' AND b_valutazione_tecnica.codice_gara = :codice_gara";
          $ris_tempo = $pdo->bindAndExec($sql_tempo,$bind);
          if ($ris_tempo->rowCount()>0) {
            $id_offerta++;
            ?>
            <tr>
              <td <? if ($elenco_prezzi) echo "colspan='3'"; ?>><?= traduci("Riduzione percentuale sui tempi di ultimazione") ?></td>
              <td>
                <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="temporale">
                <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="0">
                <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="offerta_<? echo $id_offerta ?>_temporale" title="Ribasso" rel="S;0;0;N">
                </td>
            </tr>
            <?
            }
          $id_offerta++; ?>
          <tr>
            <td <? if ($elenco_prezzi) echo "colspan='3'"; ?>><?= traduci("Costi di sicurezza aziendale interni") ?></td>
            <td>
              <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="sicurezza">
              <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="0">
              <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="offerta_<? echo $id_offerta ?>_sicurezza" title="Valore" rel="S;0;0;N">
            </td>
          </tr>
          <?
          if (strtotime($record_gara["data_pubblicazione"]) >= strtotime('2017-05-20')) {
          $id_offerta++; ?>
            <tr>
              <td <? if ($elenco_prezzi) echo "colspan='3'"; ?>><?= traduci("Costo della manodopera") ?></td>
              <td>
                <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="manodopera">
                <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="0">
                <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="offerta_<? echo $id_offerta ?>_manodopera" title="Valore" rel="S;0;0;N">
              </td>
            </tr>
          <? } ?>
        </table>
      <?
    } else if ($tipo=="tecnica") {
      $bind = array();
      $bind[":codice_gara"] = $record_gara["codice"];
      $sql_valutazione = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.valutazione <> '' AND b_criteri_punteggi.economica = 'N' AND b_criteri_punteggi.temporale = 'N' ";
      $ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
      if ($ris_valutazione->rowCount() > 0) {
        ?>
        <h2><strong><?= traduci("valori quantificabili automaticamente dell'offerta tecnica") ?></strong></h2>
        <table width="100%">
          <tr><th><?= traduci("Denominazione") ?></th><th width="10%"><?= traduci('offerta') ?></th></tr>
          <?
            $id_offerta = 0;
            while($valutazione_tecnica = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
              $id_offerta++;
              ?>
                <tr>
                  <td><strong><?= $valutazione_tecnica["descrizione"] ?></strong> -
                  <?
                    switch ($valutazione_tecnica["valutazione"]) {
                      case "P": echo "Valutazione Proporzionale"; break;
                      case "I": echo "Valutazione Proporzionale Inversa"; break;
                      case "S": echo "Valutazione a step"; break;
                    }
                  ?></td>
                  <td>
                    <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="tecnica">
                    <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="<?= $valutazione_tecnica["codice"] ?>">
                    <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="tecnica_<? echo $id_offerta ?>_<? $valutazione_tecnica["codice"] ?>" title="Valore" rel="S;0;0;N">
                  </td>
                </tr>
              <?
            }
          ?>
        </table>
        <?
      }
    }
  }
?>
