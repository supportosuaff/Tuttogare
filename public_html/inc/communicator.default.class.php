<?
/**
 * CommunicatorDefault
 */
class CommunicatorDefault
{

  protected $root;
  protected $config;
  protected $pdo;
  protected $error;
  protected $rel_comunicazioni;

  public $type;

  function __construct($parameters)
  {
    global $root, $config;
    $this->root = $root;
    $this->config = $config;
    $this->rel_comunicazioni = array();
    $this->error = "";
    $this->pdo = new myPDO;
    foreach ($parameters as $key => $value) {
      $this->{$key} = $value;
    }
  }

  public function send()
  {
    if($this->init() === TRUE) {
      $this->saveComunicazione();
      if($this->coda === TRUE) {
        $this->saveToQueue();
      } else {
        $this->sendSMTP();
      }
    }

    if (!empty($this->error)) {
      if(!$this->elaborazione_coda) $this->saveToQueue();
      return $this->error;
    } else {
      return TRUE;
    }
  }

  protected function init()
  {
    //Verifico che il corpo del messaggio non sia vuoto
    if (empty($this->corpo)) $this->error .=  "Corpo vuoto<br>";
    //Verifico che l'oggetto del messaggio non sia vuoto
    if(empty($this->oggetto)) $this->error .=  "Oggetto vuoto<br>";
    // Imposto gli indirizzi dei destinatari e verifico che l'elenco non sia vuoto
    $this->checkAddress();
    if (count($this->address) < 1) $this->error .=  "Nessun destinatario indicato<br>";
    return !empty($this->error) ? FALSE : TRUE;
  }

  protected function sendSMTP()
  {
    if($this->setSMTP() === TRUE) {
      $html = $this->getHTML();
      $mail = null;
      $mail = new PHPMailer();
      $mail->setLanguage('it');
      $mail->IsSMTP();
      $mail->Timeout = 30;
      $mail->Host = $this->configurazione["host"];
      $mail->Port = $this->configurazione["smtp_port"];
      if ($this->configurazione["smtp_ssl"] == true) $mail->SMTPSecure = 'ssl';
      $mail->SMTPAuth = true;
      $mail->Username = $this->configurazione["mittente_mail"];
      $mail->Password = $this->configurazione["smtp_password"];
      $mail->CharSet = 'UTF-8';
      if (isset($_SESSION["ente"]) && $this->codice_pec >= 0) {
        $mail->Password = simple_decrypt($this->configurazione["smtp_password"], $_SESSION["ente"]["cf"]);
        $mail->Password = str_replace("&amp;","&",$mail->Password);
      }
      $nome_sito = $this->config["nome_sito"];
      if (isset($_SESSION["ente"])) $nome_sito = "Portale Gare - " . $_SESSION["ente"]["denominazione"];
      $mail->SetFrom($this->configurazione["mittente_mail"], $nome_sito);
      $mail->Subject = strip_tags($nome_sito . " - " . $this->oggetto);
      $mail->MsgHTML($html);

      if(!empty($this->attachment)) {
        if(!is_array($this->attachment)) $this->attachment = array($this->attachment);
        foreach ($this->attachment as $attachment) {
          if(file_exists($attachment)) {
            $mail->addAttachment($attachment);
          }
        }
      }

      $uc = $this->pdo->prepare('UPDATE r_comunicazioni_utenti SET identificativo_messaggio = :identificativo_messaggio WHERE codice = :codice');
      foreach($this->address AS $destinatario) {
        $codice_relazione = $this->codice_relazione;
        if(! $this->elaborazione_coda && ! empty($this->utenti_pec[$destinatario]) && ! empty($this->rel_comunicazioni[$this->utenti_pec[$destinatario]])) $codice_relazione = $this->rel_comunicazioni[$this->utenti_pec[$destinatario]];
        if(! empty($codice_relazione) && $this->codice_pec >= 0) {
          $m = microtime(true);
          $r = uniqid('', true);

          $uc->bindValue(':codice', $codice_relazione);
          $uc->bindValue(':identificativo_messaggio', "<{$codice_relazione}-{$r}-{$m}@tuttogare-pa.it>");
          $uc->execute();

          $mail->MessageID = "<{$codice_relazione}-{$r}-{$m}@tuttogare-pa.it>";
        }

        $mail->AddAddress($destinatario);
        if (!$mail->Send()) {
          $this->error .= "Problema durante invio. - Errore classe: ".$mail->ErrorInfo . "<br>";
        }
        $mail->clearAllRecipients();
      }
    }
  }

  protected function setSMTP()
  {
    if ($this->codice_pec >= 0 && isset($_SESSION["ente"])) {
      $bind = array(":codice_ente"=>$_SESSION["ente"]["codice"]);
      $strsql    = "SELECT * FROM b_enti WHERE codice = :codice_ente ";
      $risultato = $this->pdo->bindAndExec($strsql,$bind);
      if ($risultato->rowCount() > 0) {
        $ente = $risultato->fetch(PDO::FETCH_ASSOC);
        $this->configurazione["host"]          = $ente["smtp"];
        $this->configurazione["smtp_port"]     = $ente["smtp_port"];
        $this->configurazione["smtp_ssl"]      = $ente["usa_ssl"];
        $this->configurazione["mittente_mail"] = $ente["pec"];
        $this->configurazione["smtp_password"] = $ente["password"];
      }
      if ($this->codice_pec != 0) {
        $bind = array(":codice_pec"=>$this->codice_pec,":codice_ente"=>$_SESSION["ente"]["codice"]);
        $strsql    = "SELECT * FROM b_pec WHERE codice = :codice_pec AND codice_ente = :codice_ente ";
        $risultato = $this->pdo->bindAndExec($strsql,$bind);
        if ($risultato->rowCount() > 0) {
          $record_pec                      = $risultato->fetch(PDO::FETCH_ASSOC);
          $this->configurazione["host"]          = $record_pec["smtp"];
          $this->configurazione["smtp_port"]     = $record_pec["smtp_port"];
          $this->configurazione["smtp_ssl"]      = $record_pec["usa_ssl"];
          $this->configurazione["mittente_mail"] = $record_pec["pec"];
          $this->configurazione["smtp_password"] = $record_pec["password"];
        }
      }
    } else if ($this->codice_pec < 0) {

      if ($this->codice_pec == -2) {
        $this->configurazione["host"]          = $this->config["smtp_server_newsletter"];
        $this->configurazione["smtp_port"]     = $this->config["smtp_port_newsletter"];
        $this->configurazione["smtp_ssl"]      = $this->config["smtp_ssl_newsletter"];
        $this->configurazione["mittente_mail"] = $this->config["mittente_newsletter"];
        $this->configurazione["smtp_password"] = $this->config["smtp_password_newsletter"];
      } else if ($this->codice_pec == -3) {
        $this->configurazione["host"]          = $this->config["smtp_server_reset"];
        $this->configurazione["smtp_port"]     = $this->config["smtp_port_reset"];
        $this->configurazione["smtp_ssl"]      = $this->config["smtp_ssl_reset"];
        $this->configurazione["mittente_mail"] = $this->config["mittente_reset"];
        $this->configurazione["smtp_password"] = $this->config["smtp_password_reset"];
      } else {
        $this->configurazione["host"]          = $this->config["smtp_server"];
        $this->configurazione["smtp_port"]     = $this->config["smtp_port"];
        $this->configurazione["smtp_ssl"]      = $this->config["smtp_ssl"];
        $this->configurazione["mittente_mail"] = $this->config["mittente_mail"];
        $this->configurazione["smtp_password"] = $this->config["smtp_password"];
      }
    }
    if (count($this->configurazione) > 0) {
      return  true;
    } else {
      $this->error .= "Errore configurazione PEC<br>";
      return FALSE;
    }
  }

  protected function checkAddress()
  {
    if(!empty($this->destinatari)) {
      if (!is_array($this->destinatari)) $this->destinatari = array($this->destinatari);
      foreach($this->destinatari as $destinatario) {
        if (!is_numeric($destinatario)) {
          $this->address[] = $destinatario;
          $user_code = $this->findCode($destinatario);
          if ($user_code != false) {
            $this->utenti[] = $user_code;
            $this->utenti_pec[$destinatario] = $user_code;
          }
        } else if (is_numeric($destinatario)) {
          $user_pec = $this->findAddress($destinatario);
          if ($user_pec != false) {
            $this->address[] = $user_pec;
            $this->utenti[] = $destinatario;
            $this->utenti_pec[$user_pec] = $destinatario;
          }
        }
      }
    } else {
      $this->getDestinatari();
    }
    $this->address = array_unique($this->address);
    $this->utenti = array_unique($this->utenti);
    $this->utenti_pec = array_unique($this->utenti_pec);
    $this->address = array_filter($this->address);
    $this->utenti = array_filter($this->utenti);
    $this->utenti_pec = array_filter($this->utenti_pec);
  }

  protected function getDestinatari()
  {
    if(!empty($_SESSION["ente"]["codice"]) && !empty($this->codice_gara)) {
      if ($this->sezione == "gara" || empty($this->sezione)) {
        /* INIZIO SCRIPT PER GARE */
        $bind = array(":id"=>$this->codice_gara,":codice_ente"=>$_SESSION["ente"]["codice"]);
        $strsql = "SELECT b_gare.*, b_procedure.invito FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice
                   WHERE b_gare.codice = :id AND b_gare.codice_gestore = :codice_ente";
        $risultato = $this->pdo->bindAndExec($strsql,$bind);
        if ($risultato->rowCount() > 0) {
          $gara = $risultato->fetch(PDO::FETCH_ASSOC);
          if ($gara["pubblica"] > 0) {
            if (strtotime($gara["data_scadenza"]) > time()) {
              /* Gara non scaduta */
              if ($gara["invito"] == "S") {
                $strsql = "SELECT b_utenti.codice, b_utenti.pec FROM b_utenti
                        JOIN r_inviti_gare ON b_utenti.codice = r_inviti_gare.codice_utente
                        WHERE r_inviti_gare.codice_gara = :codice_gara ";
                $risultato = $this->pdo->bindAndExec($strsql,array(":codice_gara"=>$this->codice_gara));
              } else {
                if ($this->sendOpen) {
                  $bind = array(":codice_gara"=>$this->codice_gara);
                  $strsql = "SELECT b_cpv.* FROM b_cpv
                            JOIN r_cpv_gare ON b_cpv.codice = r_cpv_gare.codice
                            WHERE r_cpv_gare.codice_gara = :codice_gara ORDER BY codice";
                  $risultato_cpv = $this->pdo->bindAndExec($strsql,$bind);
                  if ($risultato_cpv->rowCount()>0) {
                    $cpv = array();
                    while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
                      $cpv[] = $rec_cpv["codice"];
                    }
                    $string_cpv = implode(";",$cpv);
                  }
                  $bind = array(":codice_ente"=> $_SESSION["ente"]["codice"]);
                  $strsql  = "SELECT b_utenti.codice, b_utenti.pec
                              FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
                              JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente
                              JOIN r_enti_operatori ON r_enti_operatori.cod_utente = b_utenti.codice
                              JOIN r_cpv_operatori ON b_operatori_economici.codice = r_cpv_operatori.codice_operatore
                              WHERE b_gruppi.gerarchia > 2 AND b_utenti.attivo = 'S' AND r_enti_operatori.cod_ente = :codice_ente ";
                  if (isset($string_cpv) && $string_cpv != "") {
                    $strsql .= " AND (";
                    $categorie = explode(";",$string_cpv);
                    $cat=0;
                      foreach($categorie as $codice) {
                        $cat++;
                        if ($codice != "") {
                          $bind[":cat_".$cat] = $codice;
                          $strsql .= "(r_cpv_operatori.codice = :cat_".$cat." ";
                          if (strlen($codice)>2) {
                            $sub = 0;
                            $diff = strlen($codice) - 2;
                            for($i=1;$i<=$diff;$i++) {
                              $sub++;
                              $bind[":cat_".$cat."_".$sub] = substr($codice,0,$i*-1);
                              $strsql .= "OR r_cpv_operatori.codice = :cat_".$cat."_".$sub." ";
                            }
                          }
                        $strsql.=") OR ";
                      }
                    }
                  $strsql = substr($strsql,0,-4);
                  $strsql .= ")";
                  }
                  $strsql .= " GROUP BY b_utenti.codice ";
                  $risultato = $this->pdo->bindAndExec($strsql,$bind);
                }
              }
              if (isset($risultato) && $risultato->rowCount()>0) {
                while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
                  $this->address[] = $record["pec"];
                  $this->utenti[] = $record["codice"];
                  $this->utenti_pec[$record["pec"]] = $record["codice"];
                }
              }
            }

            /* In ogni caso si inseriscono i partecipanti */
            $bind = array();
    				$bind[":codice_gara"] = $this->codice_gara;
    				$strsql = "SELECT b_utenti.*, r_partecipanti.pec AS pec_partecipante
                       FROM r_partecipanti LEFT JOIN b_utenti ON r_partecipanti.codice_utente = b_utenti.codice
                       WHERE r_partecipanti.codice_capogruppo = 0 AND
                       r_partecipanti.codice_gara = :codice_gara ";
            if ($this->codice_lotto > 0) {
              $bind[":codice_lotto"] = $this->codice_lotto;
              $strsql.=" AND r_partecipanti.codice_lotto = :codice_lotto ";
            }

            // Se la gara è scaduta prendere solo i partecipanti che hanno confermato la partecipazione
            if (strtotime($gara["data_scadenza"]) <= time()) $strsql.=" AND (r_partecipanti.conferma = TRUE OR r_partecipanti.conferma IS NULL)";
    				$risultato = $this->pdo->bindAndExec($strsql,$bind);
    				if ($risultato->rowCount()>0) {
    					while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($record["codice"])) {
                  $this->address[] = $record["pec"];
                  $this->utenti[] = $record["codice"];
                  $this->utenti_pec[$record["pec"]] = $record["codice"];
                } else if (!empty($record["pec_partecipante"])) {
    						  $this->address[] = $record["pec_partecipante"];
                }
    					}
    				}
          }
        }
        /* FINE SCRIPT PER GARE */
      } else {
        /* INIZIO SCRIPT ALTRE PROCEDURE */
        $bind = array(":codice_bando"=>$this->codice_gara);
        $strsql = "SELECT b_cpv.* FROM b_cpv
                   JOIN r_cpv_bandi_".$this->sezione." ON b_cpv.codice = r_cpv_bandi_".$this->sezione.".codice
                   WHERE r_cpv_bandi_".$this->sezione.".codice_bando = :codice_bando ORDER BY codice";
        $risultato_cpv = $this->pdo->bindAndExec($strsql,$bind);
        if ($risultato_cpv->rowCount()>0) {
          $cpv = array();
          while($rec_cpv = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
            $cpv[] = $rec_cpv["codice"];
          }
          $string_cpv = implode(";",$cpv);
        }
        $bind = array(":codice_ente"=> $_SESSION["ente"]["codice"]);
        $strsql  = "SELECT b_utenti.codice, b_utenti.pec
                    FROM b_utenti JOIN b_gruppi ON b_utenti.gruppo = b_gruppi.codice
                    JOIN b_operatori_economici ON b_utenti.codice = b_operatori_economici.codice_utente
                    JOIN r_enti_operatori ON r_enti_operatori.cod_utente = b_utenti.codice
                    JOIN r_cpv_operatori ON b_operatori_economici.codice = r_cpv_operatori.codice_operatore
                    WHERE b_gruppi.gerarchia > 2 AND b_utenti.attivo = 'S' AND r_enti_operatori.cod_ente = :codice_ente ";
        if (isset($string_cpv) && $string_cpv != "") {
          $strsql .= " AND (";
          $categorie = explode(";",$string_cpv);
          $cat=0;
            foreach($categorie as $codice) {
              $cat++;
              if ($codice != "") {
                $bind[":cat_".$cat] = $codice;
                $strsql .= "(r_cpv_operatori.codice = :cat_".$cat." ";
                if (strlen($codice)>2) {
                  $sub = 0;
                  $diff = strlen($codice) - 2;
                  for($i=1;$i<=$diff;$i++) {
                    $sub++;
                    $bind[":cat_".$cat."_".$sub] = substr($codice,0,$i*-1);
                    $strsql .= "OR r_cpv_operatori.codice = :cat_".$cat."_".$sub." ";
                  }
                }
              $strsql.=") OR ";
            }
          }
        $strsql = substr($strsql,0,-4);
        $strsql .= ")";
        }
        $strsql .= " GROUP BY b_utenti.codice ";
        $risultato = $this->pdo->bindAndExec($strsql,$bind);
        if ($risultato->rowCount()>0) {
          while($record = $risultato->fetch(PDO::FETCH_ASSOC)) {
            $this->address[] = $record["pec"];
            $this->utenti[] = $record["codice"];
            $this->utenti_pec[$record["pec"]] = $record["codice"];
          }
        }
        /* FINE SCRIPT ALTRE PROCEDURE */
      }
    }
  }

  protected function findAddress($id)
  {
    $sql = "SELECT pec FROM b_utenti WHERE codice = :id AND pec <> ''";
    $ris = $this->pdo->bindAndExec($sql, array(":id"=>$id));
    if ($ris->rowCount() == 1) {
      $rec = $ris->fetch(PDO::FETCH_ASSOC);
      return trim($rec["pec"]);
    }
    return false;
  }

  protected function findCode($pec)
  {
    $sql = "SELECT codice FROM b_utenti WHERE pec = :pec ";
    $ris = $this->pdo->bindAndExec($sql, array(":pec"=>$pec));
    if ($ris->rowCount() == 1) {
      $rec = $ris->fetch(PDO::FETCH_ASSOC);
      return $rec["codice"];
    }
    return false;
  }

  protected function getHTML()
  {
    $head = "";
    $bottom = "";
    $this->oggetto = $this->oggetto;
    $this->corpo = mb_convert_encoding($this->corpo, "HTML-ENTITIES", "UTF-8");
    if ($this->intestazione) {
      $head = '<html><head><style>body { font-family: Tahoma, Geneva, sans-serif; margin:0px; padding:0px } .padding { padding:20px; } tr.odd { background-color:#F6F6F6;} tr.even { background-color:#ECECEC; } #bottom { padding:20px; background-color: #DDD; text-align:right }</style></head><body>';
      $head .= '<div class="padding" style="width:100%"><table style="width:100%">';
      if (isset($_SESSION["ente"])) {
        $head .= '<tr><td style="width:150px"><img src="https://gare.comune.roma.it/documenti/enti/'.$_SESSION["ente"]["logo"].'" width="150"></td>';
        $head .= '<td style="width:700px"><div class="padding">';
        $head .= '<h1>'.$_SESSION["ente"]["denominazione"].'</h1>';
        $head .= '<strong>'.$_SESSION["ente"]["indirizzo"].' - '.$_SESSION["ente"]["citta"].' ('.$_SESSION["ente"]["provincia"].')</strong><br>';
        if ($_SESSION["ente"]["telefono"] != "") $head .= 'Tel. '.$_SESSION["ente"]["telefono"].'<br>';
        if ($_SESSION["ente"]["fax"] != "") $head .= 'Fax. '.$_SESSION["ente"]["fax"].'<br>';
        if ($_SESSION["ente"]["email"] != "") $head .= 'Email: <a href="mailto:'.$_SESSION["ente"]["email"].'">'.$_SESSION["ente"]["email"].'</a><br>';
        if(! empty($_SERVER["ente"]["pec_footer"])) {
          if ($_SESSION["ente"]["pec_footer"] != "") $head .= 'PEC: <a href="mailto:'.$_SESSION["ente"]["pec_footer"].'">'.$_SESSION["ente"]["pec_footer"].'</a><br>';
        } else {
          if ($_SESSION["ente"]["pec"] != "") $head .= 'PEC: <a href="mailto:'.$_SESSION["ente"]["pec"].'">'.$_SESSION["ente"]["pec"].'</a><br>';
        }
        $head .= "</div></td></tr>";
      } else {
        $head .= '<tr><td style="width:10%"><img src="https://gare.comune.roma.it/img/tuttogarepa-logo-software-sx-small.png" alt="Tutto Gare"></td>';
        $head .= '<td style="width:90%">';
        $head .= '<h1>Tutto Gare</h1>';
        $head .= '</td></tr>';
      }
      $head .= '</table></div>';
      $head .= '<hr><div class="padding">';

      $bottom = "</div>";
      if(! empty($_SESSION["ente"]["messaggio_comunicazioni"])) {
        $bottom .= '<hr><div class="padding" style="font-style: italic; color: #808080 !important">';
        $bottom .= $_SESSION["ente"]["messaggio_comunicazioni"];
        $bottom .= "</div>";
      }
      $bottom .= '<div id="bottom">';
      $bottom .= '<img src="https://gare.comune.roma.it/img/tuttogarepa-logo-software-sx-small.png" alt="Tutto Gare">';
      $bottom .= '</div>';
      $bottom .= '</body></html>';
    }
    return $head.$this->corpo.$bottom;
  }

  protected function saveToQueue()
  {

    $sql = "INSERT INTO b_coda (`codice_relazione`,`indirizzo`,`oggetto`,`corpo`,`codice_pec`,`codice_ente`,`inviata`,`comunicazione_tecnica`,`utente_modifica`,`timestamp`,`timestamp_creazione`) VALUES
            (:codice_relazione,:indirizzo,:oggetto,:corpo,:codice_pec,:codice_ente,'N',:comunicazione_tecnica,:utente_modifica,:timestamp,:timestamp_creazione)";
    $ris = $this->pdo->prepare($sql);
    $ris->bindValue(":utente_modifica",!empty($_SESSION["codice_utente"]) ? $_SESSION["codice_utente"] : -1);
    $ris->bindValue(":timestamp",date('Y-m-d H:i:s'));
    $ris->bindValue(":timestamp_creazione",date('Y-m-d H:i:s'));
    $ris->bindValue(':comunicazione_tecnica', $this->comunicazione_tecnica ? 1 : 0);

    // $salva = new salva();
    // $salva->debug = false;
    // $salva->codop = !empty($_SESSION["codice_utente"]) ? $_SESSION["codice_utente"] : -1;
    // $salva->nome_tabella = "b_coda";
    // $salva->operazione = "INSERT";

    $nome_sito = $this->config["nome_sito"];
    if (isset($_SESSION["ente"])) $nome_sito = "Portale Gare - " . $_SESSION["ente"]["denominazione"];

    foreach($this->address AS $destinatario) {
      // $coda = array();
      $ris->bindValue(":indirizzo",$destinatario);
      $ris->bindValue(":oggetto", $nome_sito . " - " . $this->oggetto);
      $ris->bindValue(":corpo",$this->getHTML());
      $ris->bindValue(":codice_pec" ,$this->codice_pec);
      $codice_relazione = 0;
      if(!empty($this->rel_comunicazioni[$this->findCode($destinatario)])) {
        $codice_relazione = $this->rel_comunicazioni[$this->findCode($destinatario)];
      }
      $ris->bindValue(":codice_relazione",$codice_relazione);
      // if (!empty($codice_relazione)) $coda["codice_relazione"] = $codice_relazione;
      if (!empty($_SESSION["ente"])) {
        $ris->bindValue(":codice_ente",$_SESSION["ente"]["codice"]);
      } else {
        $ris->bindValue(":codice_ente",0);
      }
      // $coda["inviata"] = "N";
      // $salva->oggetto = $coda;
      // $salva->debug = FALSE;
      // $codice_coda = $salva->save();
      if (!$ris->execute())  $this->error .= "Errore nel salvataggio nella coda dell'indirizzo " . $destinatario . "<br>";
      // if ($codice_coda == false) $this->error .= "Errore nel salvataggio nella coda dell'indirizzo " . $destinatario . "<br>";
    }
  }

  protected function saveComunicazione()
  {
    if ($this->comunicazione && !empty($_SESSION["ente"]) && count($this->utenti) > 0) {
      $comunicazione = array();
      $comunicazione["codice_ente"] = $_SESSION["ente"]["codice"];
      if (!empty($this->codice_gara)) {
        $comunicazione["codice_gara"] = $this->codice_gara;
        if (!empty($this->sezione)) $comunicazione["sezione"] = $this->sezione;
      }
      $comunicazione["oggetto"] = $this->oggetto;
      $comunicazione["corpo"] = $this->corpo;
      if($this->codice_pec >= 0) $comunicazione["codice_pec"] = $this->codice_pec;
      if (!empty($this->cod_allegati)) $comunicazione["cod_allegati"] = $this->cod_allegati;

      $salva = new salva();
      $salva->codop = $_SESSION["codice_utente"];
      $salva->nome_tabella = "b_comunicazioni";
      $salva->operazione = "INSERT";
      $salva->oggetto = $comunicazione;
      $codice_comunicazione = $salva->save();
      if ($codice_comunicazione != false) {
        $salva->nome_tabella = "r_comunicazioni_utenti";
        $uc = $this->pdo->prepare('UPDATE r_comunicazioni_utenti SET identificativo_messaggio = :identificativo_messaggio WHERE codice = :codice');
        foreach($this->utenti as $codice) {
          if ($codice != "") {
            $r_comunicazione = array();
            $r_comunicazione["codice_comunicazione"] = $codice_comunicazione;
            $r_comunicazione["codice_ente"] = $_SESSION["ente"]["codice"];
            $r_comunicazione["codice_utente"] = $codice;
            $salva->oggetto = $r_comunicazione;
            $codice_relazione = $salva->save();
            if ($codice_relazione == false) {
              $this->error .= "Errore nel salvataggio della comunicazione per l'utente #" . $codice . "<br>";
            } else {
              $m = microtime(true);
              $r = uniqid('', true);
              $uc->bindValue(':codice', $codice_relazione);
              $uc->bindValue(':identificativo_messaggio', "<{$codice_relazione}-{$r}-{$m}@tuttogare-pa.it>");
              $uc->execute();
              $this->rel_comunicazioni[$codice] = $codice_relazione;
              $this->identifiers[$codice] = "<{$codice_relazione}-{$r}-{$m}@tuttogare-pa.it>";
            }
          }
        }
      } else {
        $this->error .= "Errore generale nel salvataggio della comunicazione <br>";
      }
    }
  }

}

?>
