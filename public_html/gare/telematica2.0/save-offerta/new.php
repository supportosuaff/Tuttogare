<?
  if (!empty($codice_offerta) && !empty($_POST["offerta"])) {
    $economica = false;
    if ($tipo == "economica") $economica = true;
    $tabella = getFormOfferta($record_gara["codice"],$codice_lotto,$economica,false,$_POST["offerta"],true);
    if (!empty($tabella)) {
      $vocabolario["#tabella#"] = $tabella["html"];
      $inputs = $tabella["inputs"];
      if (count($inputs) > 0) {
        if ($economica) {
          ob_start()
          ?>
          <table width="100%">
            <tr>
              <th class="etichetta"><strong><?= traduci("Costi di sicurezza aziendale interni") ?></strong></th>
            </tr>
            <tr>
              <td style="text-align:center">
                <?= number_format($inputs["sicurezza"][0],2,",",".") ?>
              </td>
            </tr>
            <tr>
              <th class="etichetta"><strong><?= traduci("Costo della manodopera") ?></strong></th>
            </tr>
            <tr>
              <td style="text-align:center">
                <?= number_format($inputs["manodopera"][0],2,",",".") ?>
              </td>
            </tr>
          </table>
          <?
          $vocabolario["#tabella#"] .= ob_get_clean();
        }
        $errore_offerte = false;
        foreach($inputs as $tipo_voce => $voci) {
          foreach($voci AS $codice => $valore) {
            $dettaglio_offerta = array();
            $dettaglio_offerta["codice_offerta"] = $codice_offerta;
            $dettaglio_offerta["codice_partecipante"] = $offerta["codice_partecipante"];
            $dettaglio_offerta["tipo"] = $tipo_voce;
            $dettaglio_offerta["codice_dettaglio"] = $codice;
            $dettaglio_offerta["offerta"] = openssl_encrypt($valore,$config["crypt_alg"],$_POST["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
            if ($dettaglio_offerta["offerta"] !== FALSE) {
              $salva = new salva();
              $salva->debug = false;
              $salva->codop = $_SESSION["codice_utente"];
              $salva->nome_tabella = "b_dettaglio_offerte";
              $salva->operazione = "INSERT";
              $salva->oggetto = $dettaglio_offerta;
              $codice_dettaglio = $salva->save();
              if ($codice_dettaglio === false) $errore_offerte = true;
            } else {
              $errore_offerte = true;
              break;
            }
          }
        }
      }
    }
  }
?>
