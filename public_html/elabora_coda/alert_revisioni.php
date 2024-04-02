<?
  if (!empty($elabora_coda)) {
    $current_time = (int) date('Hi');
    if($current_time >= 800 &&  $current_time <= 809) {
      $types = array("albo","mercato","sda");
      foreach ($types AS $type) {
        $sql_bandi = "SELECT * FROM b_bandi_".$type." WHERE periodo_revisione > 0 AND periodo_revisione IS NOT NULL AND (data_scadenza > now() OR data_scadenza = 0) AND pubblica > 0";
        $ris_bandi = $pdo->query($sql_bandi);
        if ($ris_bandi->rowCount() > 0) {
          while($bando = $ris_bandi->fetch(PDO::FETCH_ASSOC)) {
            $sql = "SELECT * FROM b_enti WHERE codice = :codice AND attivo = 'S'";
            $ris_ente = $pdo->bindAndExec($sql,array(":codice"=>$bando["codice_gestore"]));
            if ($ris_ente->rowCount() > 0) {
              $ente = $ris_ente->fetch(PDO::FETCH_ASSOC);
              $_SESSION["ente"] = $ente;

              $folder = $type;
              if ($type=="albo") $folder = "albo_fornitori";
              if ($type=="mercato") $folder = "mercato_elettronico";
              $href = "https://".$_SESSION["ente"]["dominio"]."/".$folder."/id".$bando["codice"]."-dettaglio";

              $limiti = array();
              // $limiti["60"] = date('Y-m-d',strtotime("+2 month"));
              $limiti["30"] = date('Y-m-d',strtotime("+1 month"));
              $limiti["15"] = date('Y-m-d',strtotime("+15 day"));
              $limiti["7"] = date('Y-m-d',strtotime("+7 day"));
              $limiti["3"] = date('Y-m-d',strtotime("+3 day"));
              $limiti["1"] = date('Y-m-d',strtotime("+1 day"));

              $limite_ultimo = date('Y-m-d');

              $limit = $bando["periodo_revisione"]-2;
              $limit = date('Y-m-d',strtotime("-".$limit." month"));

              $tabella = "r_partecipanti_" . $type;
              if ($type == "mercato") $tabella = "r_partecipanti_me";
              $sql = "SELECT ".$tabella.".* FROM " . $tabella . " WHERE ammesso = 'S' AND DATE(timestamp_abilitazione) <= '".$limit."' AND codice_bando = " . $bando["codice"];
              $ris_partecipanti = $pdo->query($sql);
              if ($ris_partecipanti->rowCount() > 0) {
                while($partecipante = $ris_partecipanti->fetch(PDO::FETCH_ASSOC)) {
                  $send = false;

                  $time = strtotime($partecipante["timestamp_abilitazione"]);
                  $scadenza = date('Y-m-d',strtotime('+'.$bando["periodo_revisione"].'month',$time));
                  $time_richiesta = strtotime($partecipante["timestamp_richiesta"]);
                  if ($time > $time_richiesta) {
                    if ($partecipante["valutato"]=="S") {
                      foreach($limiti AS $gg => $limite) {
                        if ($limite == $scadenza) {

                          $scadenza_string = $gg . " giorni";
                          // if ($gg=="60") $scadenza_string = "2 mesi";
                          if ($gg=="30") $scadenza_string = "1 mese";
                          if ($gg=="1") $scadenza_string = "1 giorno";

                          $oggetto = $scadenza_string . " alla scadenza iscrizione " . $bando["oggetto"];

                  				$corpo = "Si informa la S.V. che il ".mysql2date($scadenza)." scadr&agrave; l'abilitazione relativa al bando:<br>";
                  				$corpo.= "<br><strong>" . $bando["oggetto"] . "</strong><br><br>";
                          $corpo.= "La invitiamo ad aggiornare quanto prima i suoi dati collegandosi al link: ";
                          $corpo.= "<a href='".$href."' title='Sito esterno'>" . $href . "</a><br><br>";
                  				$corpo.= "Distinti Saluti<br><br>";

                          if(file_exists("{$root}/elabora_coda/custom/{$type}/{$_SESSION["ente"]["codice"]}.php")) include("{$root}/elabora_coda/custom/{$type}/{$_SESSION["ente"]["codice"]}.php");

                          $_SESSION["codice_utente"] = -1;
                          $mailer = new Communicator();
                          $mailer->oggetto = $oggetto;
                          $mailer->corpo = "<h2>" . $oggetto . "</h2>" . $corpo;
                          $mailer->codice_pec = $bando["codice_pec"];
                          $mailer->comunicazione = true;
                          $mailer->coda = false;
                          $mailer->sezione = $type;
                          $mailer->codice_gara = $bando["codice"];
                          $mailer->destinatari = $partecipante["codice_utente"];
                          $esito = $mailer->send();
                          unset($mailer);
                          unset($_SESSION["codice_utente"]);

                          break;
                        }
                      }
                    }
                  }
                }
              }
              if (isset($_SESSION["ente"])) unset($_SESSION["ente"]);
            }
          }
        }
        if (isset($_SESSION["ente"])) unset($_SESSION["ente"]);
      }
    }
  }
?>
