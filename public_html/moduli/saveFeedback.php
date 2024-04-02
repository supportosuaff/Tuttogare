<?
  if (isset($_SESSION["codice_utente"])) {
    $error = true;
    if (!empty($_SESSION["ente"]["codice"]) && !empty($feedback_codice_riferimento) && !empty($feedback_soggetti) && !empty($feedback_tipologia)) {
      $criteri = getCriteriFeedBack();
      if (!isset($feedback_dettaglio_riferimento)) $feedback_dettaglio_riferimento = 0;
      if (!empty($criteri)) {
        $error = false;
        $risCheck = $pdo->prepare("SELECT * FROM b_feedback WHERE codice_riferimento = :riferimento AND dettaglio_riferimento = :dettaglio AND tipologia = :tipologia 
                              AND codice_utente = :codice_utente AND codice_operatore = :codice_operatore AND codice_punteggio = :codice_punteggio AND utente_modifica = :utente");
        $risCheck->bindValue(":riferimento",$feedback_codice_riferimento);
        $risCheck->bindValue(":dettaglio",$feedback_dettaglio_riferimento);
        $risCheck->bindValue(":tipologia",$feedback_tipologia);
        $risCheck->bindValue(":utente",$_SESSION["codice_utente"]);
        foreach($feedback_soggetti AS $i => $soggetto) {
          $risCheck->bindValue(":codice_utente",$soggetto["codice_utente"]);
          $risCheck->bindValue(":codice_operatore",$soggetto["codice_operatore"]);
          foreach($criteri AS $criterio) {
            $risCheck->bindValue(":codice_punteggio",$criterio["codice"]);
            $risCheck->execute();
            $valutazione = [];
            if ($risCheck->rowCount() == 1) $valutazione["codice"] = $risCheck->fetch(PDO::FETCH_ASSOC)["codice"];
            $valutazione["codice_ente"] = $_SESSION["ente"]["codice"];
            $valutazione["codice_riferimento"] = $feedback_codice_riferimento;
            $valutazione["dettaglio_riferimento"] = $feedback_dettaglio_riferimento;
            $valutazione["codice_utente"] = $soggetto["codice_utente"];
            $valutazione["codice_operatore"] = $soggetto["codice_operatore"];
            $valutazione["codice_punteggio"] = $criterio["codice"];
            $valutazione["tipologia"] = $feedback_tipologia;
            $valutazione["valutazione"] = (!empty($soggetto[$criterio["codice"]])) ? $soggetto[$criterio["codice"]] : 0;
            $salva = new salva();
            $salva->debug = false;
            $salva->codop = $_SESSION["codice_utente"];
            $salva->nome_tabella = "b_feedback";
            $salva->operazione = (empty($valutazione["codice"])) ? "INSERT" : "UPDATE";
            $salva->oggetto = $valutazione;
            $salva->save();
          }
        }
        // VERIFICA E CALCOLO AVERAGE PER AGGIORNAMENTO r_enti_operatori
        $ris = $pdo->prepare("SELECT * FROM b_feedback WHERE codice_ente = :codice_ente AND codice_utente = :codice_utente AND codice_operatore = :codice_operatore 
                              AND codice_punteggio = :codice_punteggio AND valutazione > 0");
        $ris->bindValue(":codice_ente",$_SESSION["codice_utente"]);
        $valutazioni = [];
        $tmp = [];
        foreach($criteri AS $criterio) { // TRASFORMO L'ARRAY UTILIZZANDO IL CODICE COME CHIAVE PER COMODITA'
          $tmp[$criterio["codice"]] = $criterio;
        }
        $criteri = $tmp;
        foreach($feedback_soggetti AS $i => $soggetto) { // CICLO I SOGGETTI INTERESSATI E MEMORIZZO LE RISULTANZE DI TUTTI I FEEDBACK RICEVUTI PER OGNI CRITERIO
          $ris->bindValue(":codice_utente",$soggetto["codice_utente"]);
          $ris->bindValue(":codice_operatore",$soggetto["codice_operatore"]);
          foreach($criteri AS $criterio) {
            $ris->bindValue(":codice_punteggio",$criterio["codice"]);
            $ris->execute();
            if ($ris->rowCount() > 0) {
              while ($val = $ris->fetch(PDO::FETCH_ASSOC)) { 
                $key = $val["tipologia"] . $val["codice_riferimento"] . $val["dettaglio_riferimento"];
                if (!isset($valutazioni[$key])) $valutazioni[$key] = [];
                if (!isset($valutazioni[$key][$val["utente_modifica"]])) $valutazioni[$key][$val["utente_modifica"]] = [];
                $valutazioni[$key][$val["utente_modifica"]][$criterio["codice"]] = $val["valutazione"]; 
                /*
                  MEMORIZZO I DATI IN UN ARRAY DA ELABORARE SUCCESSIVAMENTE ARRAY MULTIDIMENSIONALE CON:
                    CONTRATTO o GARA DI RIFERIMENTO
                    -> SOGGETTO VALUTATORE
                      -> CRITERIO DI VALUTAZIONE
                        -> VALUTAZIONE
                */
              }
            }
          }
          $sums = [];
          foreach($valutazioni AS $elemento => $valutatori) {
            if (count($valutatori) >= $_SESSION["ente"]["required_feedback"]) { // CONTROLLO CHE PER IL CONTRATTO o GARA CI SIA UN NUMERO SUFFICIENTE DI VALUTAZIONI
              $proceed = true;
              $sub = [];
              foreach($valutatori AS $codice_valutatore => $val) {
                if (count($val) == count($criteri)) { // CONTROLLO CHE IL NUMERO DI VALUTAZIONI PRESENTI SIA = AL NUMERO DI CRITERI DEFINITI
                  $somma = 0;
                  foreach($val AS $codice_criterio => $val) {
                    $ponderazione = $criteri[$codice_criterio]["ponderazione"];
                    $somma += $val * $ponderazione;
                  }
                  $sub[] = $somma / count($criteri);
                } else {
                  $proceed = false;
                }
              }
              if ($proceed) { // SE IL NUMERO DI VALUTAZIONI PER OGNI VALUTATORE E' COERENTE INSERISCO IL RISULTATO NELL'ARRAY GENERALE
                $sums[] = array_sum($sub) / count($sub);
              }
            }
          }
          if (!empty($sums)) { // SE E' PRESENTE ALMENO UNA VALUTAZIONE VALIDA CALCOLO LA MEDIA E AGGIORNO LA TABELLA r_enti_operatori
            $relazione = $pdo->bindAndExec("SELECT * FROM r_enti_operatori WHERE cod_ente = :codice_ente AND cod_utente = :cod_utente",[":codice_ente"=>$_SESSION["ente"]["codice"],":cod_utente"=>$soggetto["codice_utente"]])->fetch(PDO::FETCH_ASSOC);
            if (!empty($relazione)) {
              $media = array_sum($sums) / count($sums);
              $pdo->go("UPDATE r_enti_operatori SET feedback = {$media} WHERE codice = {$relazione["codice"]} ");
            }
          }
        }
      }
    } 
  }

?>