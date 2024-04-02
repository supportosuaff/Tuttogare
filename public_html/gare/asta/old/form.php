<?
  if (isset($record_gara)) {
    $bind = array(":codice_gara"=>$record_gara["codice"]);
    $sql_tipo = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione = 58";
    $ris_tipo = $pdo->bindAndExec($sql_tipo,$bind);
    $elenco_prezzi = false;
    $id_offerta = 0;
    if ($ris_tipo->rowCount() > 0) {
      $bind[":codice_lotto"] = $codice_lotto;
      $sql_elenco = "SELECT * FROM b_elenco_prezzi WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto ORDER BY tipo ";
      $ris_elenco = $pdo->bindAndExec($sql_elenco,$bind);
      if ($ris_elenco->rowCount()>0) $elenco_prezzi = true;
    }
    ?>
    <table width="100%">
    <thead>
      <tr><th>Descrizione</th><? if ($elenco_prezzi) { ?><th width="10%">Unit&agrave;</th><th width="10%">Quantit&agrave;</th><th width="10%">Prezzo Unitario Offerto</th><? } else { ?><th width="10%">Offerta %</th><? } ?></tr>
    </thead>
    <?
      if ($elenco_prezzi) {
        $tipo_prezzo = "";
        $totale_ultima_offerta = 0;
        while($prezzo = $ris_elenco->fetch(PDO::FETCH_ASSOC)) {
          $id_offerta++;
          $ultimo_valore = 0;
          $bind = array();
          $bind[":codice_gara"] = $record_gara["codice"];
          $bind[":codice_lotto"] = $codice_lotto;
          $bind[":codice_partecipante"] = $partecipante["codice"];
          $bind[":codice_prezzo"] = $prezzo["codice"];
          $sql_storico = "SELECT b_dettaglio_offerte_asta.* FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                        WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante
                        AND b_dettaglio_offerte_asta.codice_dettaglio = :codice_prezzo AND (stato = 0 OR stato = 1) ORDER BY stato";
          $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
          if (!$errore_validazione) {
            if ($ris_storico->rowCount()>0) {
              $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
              $ultimo_valore = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]) / $prezzo["quantita"];
            } else {
              $bind = array();
              $bind[":codice_partecipante"] = $partecipante["codice"];
              $bind[":codice_prezzo"] = $prezzo["codice"];
              $sql_storico = "SELECT * FROM b_offerte_decriptate WHERE codice_partecipante = :codice_partecipante AND codice_dettaglio = :codice_prezzo";
              $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
              if ($ris_storico->rowCount()>0) {
                $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
                $ultimo_valore = $storico["offerta"] / $prezzo["quantita"];
              }
            }
          } else {
            if (isset($ultima_offerta[$prezzo["codice"]])) $ultimo_valore = $ultima_offerta[$prezzo["codice"]];
          }
          $totale_ultima_offerta += $ultimo_valore * $prezzo["quantita"];
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
              <input class="titolo_edit prezzo" type="text" quantita="<?= $prezzo["quantita"] ?>" name="offerta['<? echo $id_offerta ?>'][offerta]" id="offerta_<? echo $id_offerta ?>_economica" value="<?= $ultimo_valore ?>" title="Offerta" rel="S;0;0;N">
            </td>
          </tr>
          <?
        }
          ?>
            <tr>
              <th colspan="2" style="text-align:right">Totale Offerta Economica</th>
              <td colspan="2" id="totale_offerta" style="text-align:right; font-weight:bold">&euro; <?= number_format($totale_ultima_offerta,2,",",".") ?></td>
            </tr>
          <?
      } else {
        $ultimo_valore = 0;
        if (!$errore_validazione) {
          $bind = array();
          $bind[":codice_gara"] = $record_gara["codice"];
          $bind[":codice_lotto"] = $codice_lotto;
          $bind[":codice_partecipante"] = $partecipante["codice"];
          $sql_storico = "SELECT b_dettaglio_offerte_asta.* FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                        WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante
                        AND b_dettaglio_offerte_asta.codice_dettaglio = 0 AND tipo = 'economica' AND stato = 1";

          $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
          if ($ris_storico->rowCount()>0) {
            $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
            $ultimo_valore = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
          } else {
            $bind = array();
            $bind[":codice_partecipante"] = $partecipante["codice"];

            $sql_storico = "SELECT * FROM b_offerte_decriptate WHERE codice_partecipante = :codice_partecipante AND codice_dettaglio = 0 AND tipo = 'economica'";
            $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
            if ($ris_storico->rowCount()>0) {
              $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
              $ultimo_valore = $storico["offerta"];
            }
          }
        } else {
          if (isset($ultima_offerta["economica"])) $ultimo_valore = $ultima_offerta["economica"];
        }

        $id_offerta++;
    ?>
      <tr>
        <td><?= ($rialzo) ? "Rialzo" : "Ribasso" ?> percentuale offerto</td>
        <td>
          <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="economica">
          <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="0">
          <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="offerta_<? echo $id_offerta ?>_economica" title="Ribasso" value="<?= $ultimo_valore ?>" rel="S;0;0;N;100;<">
        </td>
      </tr>
    <?
      }
          $sql_tempo = "SELECT b_valutazione_tecnica.* FROM b_valutazione_tecnica JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
                        WHERE b_criteri_punteggi.temporale = 'S' AND codice_gara = :codice_gara";
          $ris_tempo = $pdo->bindAndExec($sql_tempo,array(":codice_gara"=>$record_gara["codice"]));
          if ($ris_tempo->rowCount()>0) {
            $ultimo_valore = 0;
            if (!$errore_validazione) {
              $bind = array();
              $bind[":codice_gara"] = $record_gara["codice"];
              $bind[":codice_lotto"] = $codice_lotto;
              $bind[":codice_partecipante"] = $partecipante["codice"];
              $sql_storico = "SELECT b_dettaglio_offerte_asta.* FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                            WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante
                            AND b_dettaglio_offerte_asta.codice_dettaglio = 0 AND tipo = 'temporale' AND stato = 1";

              $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
              if ($ris_storico->rowCount()>0) {
                $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
                $ultimo_valore = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
              } else {
                $bind = array();
                $bind[":codice_partecipante"] = $partecipante["codice"];

                $sql_storico = "SELECT * FROM b_offerte_decriptate WHERE codice_partecipante = :codice_partecipante AND codice_dettaglio = 0 AND tipo = 'temporale'";
                $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
                if ($ris_storico->rowCount()>0) {
                  $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
                  $ultimo_valore = $storico["offerta"];
                }
              }
            } else {
              if (isset($ultima_offerta["temporale"])) $ultimo_valore = $ultima_offerta["temporale"];
            }

            $id_offerta++;
            ?>
            <tr>
              <td <? if ($elenco_prezzi) echo "colspan='3'"; ?>>Riduzione percentuale sui tempi di ultimazione</td>
              <td>
                <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="temporale">
                <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="0">
                <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="offerta_<? echo $id_offerta ?>_economica" value="<?= $ultimo_valore ?>" title="Ribasso" rel="S;0;0;N;100;<">
              </td>
            </tr>
          <?
        }
    ?>
    </table><?
    $sql_valutazione = "SELECT b_valutazione_tecnica.*, b_criteri_punteggi.migliorativa FROM b_valutazione_tecnica
                        JOIN b_criteri_punteggi ON b_valutazione_tecnica.punteggio_riferimento = b_criteri_punteggi.codice
                        WHERE b_valutazione_tecnica.codice_gara = :codice_gara AND b_valutazione_tecnica.valutazione <> ''";
    $ris_valutazione = $pdo->bindAndExec($sql_valutazione,array(":codice_gara"=>$record_gara["codice"]));

    if ($ris_valutazione->rowCount() > 0) {
      ?>
      <br>
      <h2><strong>Valori quantificabili automaticamente dell'offerta tecnica o economica migliorativa</strong></h2>
      <table width="100%">
        <tr><th>Denominazione</th><th width="10%">Valore offerto</th></tr>
        <?
          while($valutazione_tecnica = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
            $ultimo_valore = 0;
            if (!$errore_validazione) {
              $bind = array();
              $bind[":codice_gara"] = $record_gara["codice"];
              $bind[":codice_lotto"] = $codice_lotto;
              $bind[":codice_partecipante"] = $partecipante["codice"];
              $bind[":codice_dettaglio"] = $valutazione_tecnica["codice"];

              $sql_storico = "SELECT b_dettaglio_offerte_asta.* FROM b_dettaglio_offerte_asta JOIN b_offerte_economiche_asta ON b_dettaglio_offerte_asta.codice_offerta = b_offerte_economiche_asta.codice
                            WHERE codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND b_dettaglio_offerte_asta.codice_partecipante = :codice_partecipante
                            AND b_dettaglio_offerte_asta.codice_dettaglio = :codice_dettaglio AND (tipo = 'tecnica' OR tipo = 'migliorativa') AND stato = 1";

              $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
              if ($ris_storico->rowCount()>0) {
                $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
                $ultimo_valore = openssl_decrypt($storico["offerta"],$config["crypt_alg"],md5($partecipante["codice"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
              } else {
                $bind = array();
                $bind[":codice_partecipante"] = $partecipante["codice"];
                $bind[":codice_dettaglio"] = $valutazione_tecnica["codice"];
                $sql_storico = "SELECT * FROM b_offerte_decriptate WHERE codice_partecipante = :codice_partecipante AND codice_dettaglio = :codice_dettaglio AND (tipo = 'tecnica' OR tipo = 'migliorativa')";
                $ris_storico = $pdo->bindAndExec($sql_storico,$bind);
                if ($ris_storico->rowCount()>0) {
                  $storico = $ris_storico->fetch(PDO::FETCH_ASSOC);
                  $ultimo_valore = $storico["offerta"];
                }
              }
            } else {
              if (isset($ultima_offerta_tecnica[$valutazione_tecnica["codice"]])) $ultimo_valore = $ultima_offerta_tecnica[$valutazione_tecnica["codice"]];
            }

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
                  <input type="hidden" name="offerta['<? echo $id_offerta ?>'][tipo]" value="<?= ($valutazione_tecnica["migliorativa"] == "S") ? "migliorativa" : "tecnica"; ?>">
                  <input type="hidden" name="offerta['<? echo $id_offerta ?>'][codice_dettaglio]" value="<?= $valutazione_tecnica["codice"] ?>">
                  <input class="titolo_edit" type="text" name="offerta['<? echo $id_offerta ?>'][offerta]" id="tecnica_<? echo $id_offerta ?>_<? $valutazione_tecnica["codice"] ?>" value="<?= $ultimo_valore ?>" title="Valore" rel="S;0;0;N">
                </td>
            <?
          }
        ?>
      </table>
      <?
    }
    if ($elenco_prezzi) { ?>
      <script>
        $(".prezzo").change(function() {
          totale_offerta = 0;
          $(".prezzo").each(function() {
            if (valida($(this)) == "") {
              totale_offerta = totale_offerta + $(this).val() * $(this).attr("quantita");
            }
          });
          totale_offerta = number_format(totale_offerta,2,",",".");
          $("#totale_offerta").html("&euro; " + totale_offerta);
        });
      </script>
    <? } ?>
<? } ?>
