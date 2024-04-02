<?
  if (!empty($codice_offerta)) {
    $errore_offerte = false;
    $tipo_prezzo = "";
    $intestazione = false;
    $totale_offerta = 0;
    $echo_totale = false;

    $bind = array();
    $bind[":codice_gara"] = $record_gara["codice"];
    $sql_tipo = "SELECT opzione FROM b_opzioni_selezionate WHERE codice_gara = :codice_gara AND opzione IN (SELECT codice FROM b_opzioni WHERE codice_gruppo = 40)";
    $ris_tipo = $pdo->bindAndExec($sql_tipo,$bind);
    $elenco_prezzi = false;
    $rialzo = false;
    $id_offerta = 0;
    if ($ris_tipo->rowCount() > 0) {
      $opzione = $ris_tipo->fetch(PDO::FETCH_ASSOC);
      if ($opzione["opzione"] == "270") $rialzo = true;
    }

    foreach($_POST["offerta"] as $dettaglio_offerta) {
      $dettaglio_offerta["codice_offerta"] = $codice_offerta;
      $dettaglio_offerta["codice_partecipante"] = $offerta["codice_partecipante"];
      $offerta_troncata = $dettaglio_offerta["offerta"];
      if ($dettaglio_offerta["codice_dettaglio"]!=0 && $dettaglio_offerta["tipo"] == "economica") {
        $bind = array(":codice_dettaglio"=>$dettaglio_offerta["codice_dettaglio"]);
        $sql_prezzo = "SELECT * FROM b_elenco_prezzi WHERE codice = :codice_dettaglio";
        $ris_prezzo = $pdo->bindAndExec($sql_prezzo,$bind);
        if ($prezzo = $ris_prezzo->fetch(PDO::FETCH_ASSOC)) {
          $offerta_troncata = $dettaglio_offerta["offerta"] = intval(($dettaglio_offerta["offerta"] * 1000)) / 1000;
          $dettaglio_offerta["offerta"] = $dettaglio_offerta["offerta"] * $prezzo["quantita"];
          $totale_offerta += $dettaglio_offerta["offerta"];
          $echo_totale = false;
        }
      }
      $offerta_plain = $dettaglio_offerta["offerta"];
      $dettaglio_offerta["offerta"] = openssl_encrypt($dettaglio_offerta["offerta"],$config["crypt_alg"],$_POST["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
      if ($dettaglio_offerta["offerta"] !== FALSE) {
        if ($dettaglio_offerta["codice_dettaglio"]==0) {
          if (!$echo_totale && $totale_offerta > 0) {
            $echo_totale = true;
            $vocabolario["#tabella#"] .= "<tr style=\"font-weight:bold\"><td colspan=\"4\"><h3>Totale offerta</h3></td><td style=\"text-align:right\"><h3>&euro; " .  number_format($totale_offerta,3,",",".") ."</h3></td></tr>";
          }
          if ($dettaglio_offerta["tipo"] == "temporale") {
            $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">Riduzione percentuale sui tempi di ultimazione</h3></td></tr>";
            $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">" . number_format($offerta_plain,3,",",".") . "</h3></td></tr>";
          } else if ($dettaglio_offerta["tipo"] == "economica") {
            if (!isset($multi)) {
              $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">";
              $vocabolario["#tabella#"] .= ($rialzo) ? "Rialzo" : "Ribasso";
              $vocabolario["#tabella#"] .= " percentuale offerto</h3></td></tr>";
            } else {
              $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">";
              $vocabolario["#tabella#"] .= ($rialzo) ? "Rialzo" : "Ribasso";
              $vocabolario["#tabella#"] .= " percentuale complessivo offerto</h3></td></tr>";
            }
            $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">" . number_format($offerta_plain,3,",",".") . "</h3></td></tr>";
            $bind = array(":codice_gara"=>$record_gara["codice"]);
            if ((strtotime($record_gara["data_pubblicazione"]) > strtotime('2016-04-20'))) {
              $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\">";
              $vocabolario["#tabella#"] .= ($rialzo) ? "Rialzo" : "Ribasso";
              $vocabolario["#tabella#"] .= " percentuale sull'importo a base di gara, al netto dei costi di sicurezza";
              if (!$rialzo) $vocabolario["#tabella#"] .= " si prende atto pertanto che il ribasso non si applica agli oneri per l'attuazione dei piani di sicurezza predeterminati dalla Stazione Appaltante negli atti di gara e non soggetti a ribasso;";
              $vocabolario["#tabella#"] .= "</td></tr>";
            } else {
              $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\">";
              $vocabolario["#tabella#"] .= ($rialzo) ? "Rialzo" : "Ribasso";
              $vocabolario["#tabella#"] .= " percentuale sull'importo a base di gara, al netto del costo del personale e dei costi di sicurezza";
              if (!$rialzo) $vocabolario["#tabella#"] .= " si prende atto pertanto che il ribasso non si applica agli oneri per l'attuazione dei piani di sicurezza e al costo per la manodopera predeterminati dalla Stazione Appaltante negli atti di gara e non soggetti a ribasso;";
              $vocabolario["#tabella#"] .= "</td></tr>";
            }
          } else if (strpos($dettaglio_offerta["tipo"],"economica_") !== false) {
            $multi = true;
            $codice_importo = explode("_",$dettaglio_offerta["tipo"]);
            $codice_importo = $codice_importo[1];
            $bind=array(":codice_gara"=>$record_gara["codice"],":codice_importo"=>$codice_importo);
            $sql_multi = "SELECT b_tipologie_importi.titolo, b_tipologie_importi.codice FROM b_tipologie_importi JOIN
                    b_importi_gara ON b_tipologie_importi.codice = b_importi_gara.codice_tipologia WHERE
                    b_importi_gara.codice_gara = :codice_gara AND b_tipologie_importi.codice = :codice_importo ";
            $ris_multi = $pdo->bindAndExec($sql_multi,$bind);
            if ($ris_multi->rowCount()>0) {
              if ($record_importo = $ris_multi->fetch(PDO::FETCH_ASSOC)) {
                $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">";
                $vocabolario["#tabella#"] .= ($rialzo) ? "Rialzo" : "Ribasso";
                $vocabolario["#tabella#"] .= " percentuale su " . $record_importo["titolo"]." offerto</h3></td></tr>";
                $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">" . number_format($offerta_plain,3,",",".") . "</h3></td></tr>";
              } else {
                $errore_offerte = true;
              }
            } else {
              $errore_offerte = true;
            }
          } else if ($dettaglio_offerta["tipo"] == "sicurezza") {
            $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">Costi di sicurezza aziendale interni</h3></td></tr>";
            $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">&euro;" . number_format($offerta_plain,3,",",".") . "</h3></td></tr>";;
          } else if ($dettaglio_offerta["tipo"] == "manodopera") {
            $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">Costo della manodopera</h3></td></tr>";
            $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">&euro;" . number_format($offerta_plain,3,",",".") . "</h3></td></tr>";;
          }
        } else {
          if ($dettaglio_offerta["tipo"] == "economica") {
            $bind = array(":codice_dettaglio"=>$dettaglio_offerta["codice_dettaglio"]);
            $sql_prezzo = "SELECT * FROM b_elenco_prezzi WHERE codice = :codice_dettaglio";
            $ris_prezzo = $pdo->bindAndExec($sql_prezzo,$bind);
            if ($prezzo = $ris_prezzo->fetch(PDO::FETCH_ASSOC)) {
              if (!$intestazione) {
                $intestazione = true;
                $vocabolario["#tabella#"] .= "<tr><td style=\"width:50%; \">Descrizione</td>";
                $vocabolario["#tabella#"] .= "<td style=\"width:10%\">Unit&agrave;</td>";
                $vocabolario["#tabella#"] .= "<td style=\"width:10%;text-align:right;\">Quantit&agrave;</td>";
                $vocabolario["#tabella#"] .= "<td style=\"width:15%;text-align:right;\">Prezzo Unitario</td>";
                $vocabolario["#tabella#"] .= "<td style=\"width:15%;text-align:right;\">Offerta totale</td></tr>";
              }
              if ($tipo_prezzo != $prezzo["tipo"]) {
                $tipo_prezzo = $prezzo["tipo"];
                $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3>" . strtoupper($tipo_prezzo) ."</h3></td></tr>";
              }
              $vocabolario["#tabella#"] .= "<tr><td style=\"width:50%; \">" . $prezzo["descrizione"] . ": &nbsp;&nbsp;</td>";
              $vocabolario["#tabella#"] .= "<td style=\"width:10%\">" . $prezzo["unita"] . "</td>";
              $vocabolario["#tabella#"] .= "<td style=\"width:10%;text-align:right;\">" . number_format($prezzo["quantita"],2,",",".") . "</td>";
              $vocabolario["#tabella#"] .= "<td style=\"width:15%;text-align:right;\">&euro; " . number_format($offerta_troncata, 3, ",", ".") . "</td>";
              $vocabolario["#tabella#"] .= "<td style=\"width:15%;text-align:right;\">&euro; " . number_format($offerta_plain,3,",",".") . "</td></tr>";
            } else {
              $errore_offerte = true;
            }
          } else if ($dettaglio_offerta["tipo"] == "migliorativa") {
            $bind = array(":codice_dettaglio"=>$dettaglio_offerta["codice_dettaglio"]);
            $sql_valutazione = "SELECT * FROM b_valutazione_tecnica WHERE codice = :codice_dettaglio";
            $ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
            if ($record_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
              $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">" . $record_valutazione["descrizione"] . "</h3></td></tr>";
              $vocabolario["#tabella#"] .= "<tr><td colspan=\"5\"><h3 style=\"text-align:center;\">" . number_format($offerta_plain,3,",",".") . "</h3></td></tr>";
            } else {
              $errore_offerte = true;
            }
          } else if ($dettaglio_offerta["tipo"] == "tecnica") {
            $bind = array(":codice_dettaglio"=>$dettaglio_offerta["codice_dettaglio"]);
            $sql_valutazione = "SELECT * FROM b_valutazione_tecnica WHERE codice = :codice_dettaglio";
            $ris_valutazione = $pdo->bindAndExec($sql_valutazione,$bind);
            if ($record_valutazione = $ris_valutazione->fetch(PDO::FETCH_ASSOC)) {
              $vocabolario["#tabella#"] .= "<tr><td style=\"width:85%;\">" . $record_valutazione["descrizione"] . ": &nbsp;&nbsp;</td>";
              $vocabolario["#tabella#"] .= "<td style=\"width:15%;text-align:right;\">" . number_format($offerta_plain,3,",",".") . "</td></tr>";
            } else {
              $errore_offerte = true;
            }
          } else {
            $errore_offerte = true;
          }
        }
        if (!$errore_offerte) {
          $salva = new salva();
          $salva->debug = false;
          $salva->codop = $_SESSION["codice_utente"];
          $salva->nome_tabella = "b_dettaglio_offerte";
          $salva->operazione = "INSERT";
          $salva->oggetto = $dettaglio_offerta;
          $codice_dettaglio = $salva->save();
          if ($codice_dettaglio === false) $errore_offerte = true;
        }
      } else {
        $errore_offerte = true;
      }
    }
    $vocabolario["#tabella#"] = "<table style='width:100%'>" . $vocabolario["#tabella#"] . "</table>";
  }
?>
