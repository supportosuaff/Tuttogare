<?
  if (isset($pdo) && (isset($_POST["offerta"]))) {
    $error_rilancio = [];
    $tabella = getFormOfferta($record_gara["codice"],$codice_lotto,null,false,$_POST["offerta"],true);
    $procedi = false;
    if (!empty($tabella)) {
      $inputs = $tabella["inputs"];
      include("new/verify.php");
    }
    if ((isset($error_rilancio) && count($error_rilancio)===0) || $procedi) {
      $offerta["codice_partecipante"] = $partecipante["codice"];
      $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$offerta["codice_partecipante"]);
      $sql = "UPDATE b_offerte_economiche_asta SET stato = 99 WHERE stato <> 1 AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_partecipante = :codice_partecipante";
      $ris_update_stato = $pdo->bindAndExec($sql,$bind);
      $offerta["codice_gara"] = $record_gara["codice"];
      $offerta["codice_lotto"] = $codice_lotto;
      $salva = new salva();
      $salva->debug = false;
      $salva->codop = $_SESSION["codice_utente"];
      $salva->nome_tabella = "b_offerte_economiche_asta";
      $salva->operazione = "INSERT";
      $salva->oggetto = $offerta;
      $codice_offerta = $salva->save();
      $errore_offerte = false;
      $vocabolario["#tabella#"] = "";
      $offerte_tecniche = "";
      $tipo_prezzo = "";
      $intestazione = false;
      $totale_offerta = 0;
      $echo_totale = false;
      if ($codice_offerta != false) {
        if (!empty($tabella)) {
          $vocabolario["#tabella#"] = $tabella["html"];
          if (count($inputs) > 0) {
            $errore_offerte = false;
            foreach($inputs as $tipo_voce => $voci) {
              foreach($voci AS $codice => $valore) {
                $dettaglio_offerta = array();
                $dettaglio_offerta["codice_offerta"] = $codice_offerta;
                $dettaglio_offerta["codice_partecipante"] = $offerta["codice_partecipante"];
                $dettaglio_offerta["tipo"] = $tipo_voce;
                $dettaglio_offerta["codice_dettaglio"] = $codice;
                $dettaglio_offerta["offerta"] = openssl_encrypt($valore,$config["crypt_alg"],md5($offerta["codice_partecipante"]),OPENSSL_RAW_DATA,$config["enc_salt"]);
                if ($dettaglio_offerta["offerta"] !== FALSE) {
                  $salva = new salva();
                  $salva->debug = false;
                  $salva->codop = $_SESSION["codice_utente"];
                  $salva->nome_tabella = "b_dettaglio_offerte_asta";
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
        if (!$errore_offerte) {
          $create_pdf = true;
        } else {
          $bind = array(":codice_gara"=>$record_gara["codice"],":codice_lotto"=>$codice_lotto,":codice_partecipante"=>$offerta["codice_partecipante"]);
          $sql = "UPDATE b_offerte_economiche_asta SET stato = 99 WHERE stato <> 1 AND codice_gara = :codice_gara AND codice_lotto = :codice_lotto AND codice_partecipante = :codice_partecipante";
          $ris_delete = $pdo->bindAndExec($sql,$bind);
          ?>
            <h3 class="ui-state-error">Errore nel salvataggio dei dettagli dell'offerta</h3>
          <?
        }
      } else {
        ?>
        <h3 class="ui-state-error">Errore nel salvataggio dell'offerta</h3>
        <?
      }
    } else {
      $errore_validazione = true;
      ?><h2 style="color:#F00;">
        <span class="fa fa-exclamation-circle fa-2x"></span>
        <?
      if (count($error_rilancio) > 0) {
        foreach($error_rilancio AS $errore_rilancio) { ?>
          L'offerta pari a <?= $errore_rilancio["offerta"] ?>
          non supera il rilancio minimo pari a <?= $errore_rilancio["rilancio_minimo"] ?>
          - Ultima offerta valida <?= $errore_rilancio["migliore"] ?>
        <? }
      } else {
        ?>Errore generico<?
      }
      ?>
      </h2>
      <?
    }
  }
?>
