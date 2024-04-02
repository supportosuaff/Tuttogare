<?
  @session_start();
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  if (!empty($cliCall) || ($_SESSION["amministratore"] && isset($_SESSION["ente"]))) {
    if (! empty($_POST["oggetto"]) && ! empty($_POST["messaggio"])) {
      ini_set('memory_limit', '-1');
      ini_set('max_execution_time', 0);
      $operatori = [];
      $agganci = [];
      $errori = [];
      if (isset($_POST["source"]) && $_POST["source"] == "jsonDPA") {
        if ($_POST["filechunk"] != "")	{
          $unlink_path = $path = $config["chunk_folder"]."/".$_SESSION["codice_utente"]."/".$_POST["filechunk"];
          $unlunk_tmpPath = $tmpPath = $config["chunk_folder"] . "/" . $_SESSION["codice_utente"] . "/unzip/" . time() . "/";
          $zip = new ZipArchive;
          $res_zip = $zip->open($path);
          if ($res_zip === true) {
            $zip->extractTo($tmpPath);
            $zip->close();
            if (is_dir($tmpPath."app/data")) {
              $it = new RecursiveDirectoryIterator($tmpPath."app/data", RecursiveDirectoryIterator::SKIP_DOTS);
              $jsons = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
              foreach($jsons as $json) {
                if ($json->getBaseName() == "list.js") {
                  $fileInfo = file_get_contents($json->getRealPath());
                  $fileInfo = substr($fileInfo,strpos($fileInfo,"["),strlen($fileInfo));
                  $fileInfo = json_decode($fileInfo,true);
                  break;
                }
              }
              foreach($jsons as $json) {
                if ($json->getBaseName() != "list.json" && $json->getBaseName() != "list.js") {
                  $name = str_replace(".json","",$json->getBaseName());
                  $source = json_decode(file_get_contents($json->getRealPath()),true);
                  if (!empty($source)) {
                    $utente = [];
                    $utente[":nome"] = (!empty($source["Fornitore"]["nome"])) ? $source["Fornitore"]["nome"] : "";
                    $utente[":cognome"] = (!empty($source["Fornitore"]["cognome"])) ? $source["Fornitore"]["cognome"] : "";
                    $utente[":dnascita"] = (!empty($source["Fornitore"]["data_nascita"])) ? $source["Fornitore"]["data_nascita"] : "";
                    $utente[":cf"] = (!empty($source["Fornitore"]["cf"])) ? $source["Fornitore"]["cf"] : "";
                    $utente[":luogo"] = (!empty($source["Fornitore"]["comune_nascita"])) ? $source["Fornitore"]["comune_nascita"] : "";
                    $utente[":email"] = (!empty($source["Fornitore"]["User"]["email_address"])) ? $source["Fornitore"]["User"]["email_address"] : "";
                    $utente[":pec"] = (!empty($source["Fornitore"]["pec"])) ? $source["Fornitore"]["pec"] : "";
                    $utente[":indirizzo"] = (!empty($source["Fornitore"]["indirizzo_residenza"])) ? $source["Fornitore"]["indirizzo_residenza"] : "";
                    $utente[":citta"] = (!empty($source["Fornitore"]["comune_residenza"])) ? $source["Fornitore"]["comune_residenza"] : "";
                    $utente[":provincia"] = (!empty($source["Fornitore"]["provincia_residenza"])) ? $source["Fornitore"]["provincia_residenza"] : "";
                    $utente[":cap"] = (!empty($source["Fornitore"]["cap_residenza"])) ? $source["Fornitore"]["cap_residenza"] : "";
                    $utente[":regione"] = "";
                    $utente[":stato"] = "";

                    $operatore = [];
                    $operatore[":data_emissione_certificato"] = (!empty($source["cciaa_scadenza"])) ? date('Y-m-d',strtotime($source["cciaa_scadenza"].' -6 months')) : "";
                    $operatore[":titolo_studio"] = "";
                    $operatore[":ordine_professionale"] = "";
                    $operatore[":numero_iscrizione_professionale"] = "";
                    $operatore[":data_iscrizione_professionale"] = "";
                    if (!empty($source["OrdiniProfessionali"])) {
                      $ordine = $source["OrdiniProfessionali"][0];
                      $operatore[":ordine_professionale"] = (!empty($ordine["ordine"])) ? ($ordine["ordine"] . " " . $ordine["sede"]) : "";
                      $operatore[":numero_iscrizione_professionale"] = (!empty($ordine["matricola"])) ? $ordine["matricola"] : "";
                      $operatore[":data_iscrizione_professionale"] = (!empty($ordine["anno"])) ? $ordine["anno"]."-01-01" : "";
                    }
                    if (!empty($source["TitoliStudio"])) {
                      $titolo = $source["TitoliStudio"][0];
                      $operatore[":titolo_studio"] = (!empty($titolo["titolo"])) ? ($titolo["titolo"] . " " . $titolo["istituto"]) : "";
                    }

                    $operatore[":codice_fiscale_impresa"] = (!empty($source["Fornitore"]["cf_azienda"])) ? $source["Fornitore"]["cf_azienda"] : "";
                    $operatore[":ragione_sociale"] = (!empty($source["Fornitore"]["denominazione"])) ? $source["Fornitore"]["denominazione"] : "";
                    $operatore[":partita_iva"] = (!empty($source["Fornitore"]["iva"])) ? $source["Fornitore"]["iva"] : "";
                    $operatore[":indirizzo_legale"] = (!empty($source["Fornitore"]["indirizzo_attivita"])) ? $source["Fornitore"]["indirizzo_attivita"] : "";
                    $operatore[":citta_legale"] = (!empty($source["Fornitore"]["comune_attivita"])) ? $source["Fornitore"]["comune_attivita"] : "";
                    $operatore[":provincia_legale"] = (!empty($source["Fornitore"]["provincia_attivita"])) ? $source["Fornitore"]["provincia_attivita"] : "";
                    $operatore[":indirizzo_operativa"] = (!empty($source["Fornitore"]["indirizzo_attivita"])) ? $source["Fornitore"]["indirizzo_attivita"] : "";
                    $operatore[":citta_operativa"] = (!empty($source["Fornitore"]["comune_attivita"])) ? $source["Fornitore"]["comune_attivita"] : "";
                    $operatore[":provincia_operativa"] = (!empty($source["Fornitore"]["provincia_attivita"])) ? $source["Fornitore"]["provincia_attivita"] : "";
                    $operatore[":stato_operativa"] = (!empty($source["Fornitore"]["nazione_attivita"])) ? $source["Fornitore"]["nazione_attivita"] : "";
                    $operatore[":stato_legale"] = (!empty($source["Fornitore"]["nazione_attivita"])) ? $source["Fornitore"]["nazione_attivita"] : "";
                    $operatore[":regione_legale"] = "";
                    $operatore[":regione_operativa"] = "";
                    $operatore[":numero_iscrizione_cc"] = (!empty($source["Fornitore"]["rea"])) ? $source["Fornitore"]["rea"] : "";
                    $operatore[":sede_cc"] = (!empty($source["Fornitore"]["registro_imprese_luogo"])) ? $source["Fornitore"]["registro_imprese_luogo"] : "";
                    $operatore[":data_iscrizione_cc"] = (!empty($source["Fornitore"]["registro_imprese_data"])) ? $source["Fornitore"]["registro_imprese_data"] : "";
                    $operatore[":cooperativa_b"] = "";
                    $operatore[":matricola_inps"] = (!empty($source["Fornitore"]["matricola_inps"])) ? $source["Fornitore"]["matricola_inps"] : "";
                    $operatore[":sede_inps"] = (!empty($source["Fornitore"]["sede_inps"])) ? $source["Fornitore"]["sede_inps"] : "";
                    $operatore[":codice_inail"] = (!empty($source["Fornitore"]["codice_inail"])) ? $source["Fornitore"]["codice_inail"] : "";
                    $operatore[":pat_inail"] = (!empty($source["Fornitore"]["posizione_inail"])) ? $source["Fornitore"]["posizione_inail"] : "";
                    $operatore[":sede_inail"] = (!empty($source["Fornitore"]["sede_inail"])) ? $source["Fornitore"]["sede_inail"] : "";
                    $operatore[":codice_cassaedile"] = (!empty($source["Fornitore"]["codice_cassa_edile"])) ? $source["Fornitore"]["codice_cassa_edile"] : "";
                    $operatore[":matricola_cassaedile"] = (!empty($source["Fornitore"]["impresa_cassa_edile"])) ? $source["Fornitore"]["impresa_cassa_edile"] : "";
                    $operatore[":sede_cassaedile"] = "";
                    $operatore[":n_dipendenti"] = "";
                    $operatore[":pmi"] = "";
                    $operatore[":capitale_sociale"] = "";
                    $operatore[":capitale_versato"] = "";
                    
                    // $file["documento_riconoscimento"] = (!empty($source["doc_identita"])) ? $tmpPath . "fornitori/" . $name . "/" . $source["doc_identita"] : "";
                    if (!empty($fileInfo)) {
                      foreach($fileInfo AS $info) {
                        if (!empty($info["Fornitore"]["code"]) && !empty($info["files"])) {
                          if ($info["Fornitore"]["code"] == $name) {
                            foreach($info["files"] AS $fileType => $filePath) {
                              if ($fileType == "cciaa_doc") {
                                $file["certificato_camerale"] = $tmpPath . $filePath;
                              } else if ($fileType == "doc_curriculum") {
                                $file["curriculum"] = $tmpPath . $filePath;
                              } else if ($fileType == "doc_attivita") {
                                $file["curriculum"] = $tmpPath . $filePath;
                              }
                            }
                            break;
                          }
                        }
                      }
                    }
                    $qualita = [];
                    $ambientali = [];
                    $soa = []; 
                    $altro = [];
                    if (!empty($source["CertificazioniExtra"])) {
                      foreach($source["CertificazioniExtra"] AS $cert) {
                        $certificato = [];
                        $certificato["ente"] = $cert["ente"];
                        $certificato["data_scadenza"] = $cert["scadenza"];
                        $certificato["settore"] = $cert["settori"];
                        $certificato["norma"] = $cert["standard"];
                        if ($cert["tipo"] == "qualita") {
                          $qualita[] = $certificato;
                        } else if ($cert["tipo"] == "ambientale") { 
                          $ambientali[] = $certificato;
                        } else { 
                          $altro[] = $certificato;
                        }
                      }
                    }
                    // CategorieLP
                    // IMPOSSIBILE ASSOCIARE L'ID DELLA SOA ALLA NOSTRA

                    $data = [];
                    $data["utente"] = $utente;
                    $data["operatore"] = $operatore;
                    $data["file"] = (isset($file)) ? $file : [];
                    $data["qualita"] = (isset($qualita)) ? $qualita : [];
                    $data["ambientali"] = (isset($ambientali)) ? $ambientali : [];
                    $data["soa"] = (isset($soa)) ? $soa : [];
                    $data["altro"] = (isset($altro)) ? $altro : [];
                    $operatori[] = $data;
                  }
                }
              }
            }
          }
				}         
      } else if (isset($_POST["source"]) && $_POST["source"] == "csv" || !empty($cliCall)) {
        if (empty($cliCall)) {
          $upload_path = $config["chunk_folder"]."/";
          $allowed_filetypes = array('.csv');
          $filename = $_FILES["utenti"]["name"];
          $ext = substr($filename, strpos($filename, '.'), strlen($filename) - 1);
          $msg='';
          if (!in_array($ext, $allowed_filetypes)) {
            echo 'File non corretto, riprovare.';
          } else {
            if (move_uploaded_file($_FILES["utenti"]["tmp_name"], $upload_path . $filename)) {
              ini_set('auto_detect_line_endings', TRUE);
              $file = fopen("{$upload_path}{$filename}", "r");
            }
          }
        } else {
          if (file_exists($csvPath)) {
            ini_set('auto_detect_line_endings', TRUE);
            $file = fopen($csvPath, "r");
          }
        }
        if (!empty($file)) {
          $i = 1;
          $errori = $agganci = array();
          $rel_type="";
          while (!feof($file)) {
            $line = fgetcsv($file, 0, ';');
            if(strtolower($line[0]) == "nome" && strtolower($line[1]) == "cognome") {
              continue;
            }
            $utente = [];
            $utente[":nome"] = $line[0];
            $utente[":cognome"] = $line[1];
            $utente[":dnascita"] = date2mysql($line[4]);
            $utente[":cf"] = $line[6];
            $utente[":luogo"] = $line[2];
            $utente[":email"] = $line[14];
            $utente[":pec"] = $line[15];
            $utente[":indirizzo"] = $line[8];
            $utente[":citta"] = $line[10];
            $utente[":provincia"] = $line[11];
            $utente[":cap"] = $line[9];
            $utente[":regione"] = $line[12];
            $utente[":stato"] = $line[13];

            $operatore = [];
            $operatore[":codice_fiscale_impresa"] = $line[17];
            $operatore[":ragione_sociale"] = $line[18];
            $operatore[":partita_iva"] = $line[16];
            $operatore[":indirizzo_legale"] = $line[20];
            $operatore[":ruolo_referente"] = $line[7];
            $operatore[":citta_legale"] = $line[21];
            $operatore[":pmi"] = $line[19];
            $operatore[":provincia_legale"] = $line[22];
            $operatore[":indirizzo_operativa"] = $line[25];
            $operatore[":citta_operativa"] = $line[26];
            $operatore[":provincia_operativa"] = $line[27];
            $operatore[":stato_operativa"] = $line[29];
            $operatore[":stato_legale"] = $line[24];
            $operatore[":regione_legale"] = $line[23];
            $operatore[":regione_operativa"] = $line[28];
            $operatore[":banca"] = $line[30];
            $operatore[":iban"] = $line[31];
            $operatore[":intestatario"] = $line[32];
            $data = [];
            $data["utente"] = $utente;
            $data["operatore"] = $operatore;
            $data["cpv"] = $line[33];
            $operatori[] = $data;
          }
        }
      }
      $anagrafiche = count($operatori);
      $inserite = $countOE = 0;
      if (!empty($operatori)) {
        $pdo->debug = TRUE;
        $sth_check_utente = $pdo->prepare("SELECT * FROM b_utenti WHERE email = :email OR pec = :pec LIMIT 0,1");
        $sth_check_oe = $pdo->prepare("SELECT b_operatori_economici.* FROM b_operatori_economici WHERE b_operatori_economici.codice_utente = :utente LIMIT 0,1");
        $sth_check_relazione = $pdo->prepare("SELECT codice FROM r_enti_operatori WHERE cod_ente = :codice_ente AND cod_utente = :codice_utente LIMIT 0,1");
        $insert_utenti = "INSERT INTO b_utenti (codice_ente, gruppo,";
        $keys_utenti = array_keys($operatori[0]["utente"]);
        foreach($keys_utenti AS $key) {
          $key = str_replace(":","",$key);
          $insert_utenti .= "{$key},"; 
        }
        $insert_utenti .= "password, attivo, tentativi, utente_modifica, password_token, password_request) VALUES (0, 4, ";
        foreach($keys_utenti AS $key) $insert_utenti .= "{$key},"; 
        $insert_utenti .= ":password, 'S', 0, -1, :password_token, CURDATE())";
        $sth_utenti = $pdo->prepare($insert_utenti);
        $sth_relazione = $pdo->prepare("INSERT INTO r_enti_operatori (cod_ente, cod_utente, utente_modifica) VALUES (:codice_ente, :codice_utente, -1)");

        $insert_oe = "INSERT INTO b_operatori_economici (codice_utente, ";
        $keys_oe = array_keys($operatori[0]["operatore"]);
        foreach($keys_oe AS $key) {
          $key = str_replace(":","",$key);
          $insert_oe .= "{$key},"; 
        }
        $insert_oe .= "utente_modifica) VALUES (:codice_utente, ";
        foreach($keys_oe AS $key) $insert_oe .= "{$key},"; 
        $insert_oe .= "-1)";
        $sth_oe = $pdo->prepare($insert_oe);
        $sth_del_utente = $pdo->prepare("DELETE FROM b_utenti WHERE codice = :codice");
        $sth_psw_log = $pdo->prepare("INSERT INTO b_password_log (codice_utente, utente_modifica) VALUES (:codice_utente, -1)");
        $sth_select_cpv = $pdo->prepare('SELECT codice FROM b_cpv WHERE codice_completo = :codice_completo');
        $sth_insert_cpv = $pdo->prepare('INSERT INTO r_cpv_operatori (codice, codice_operatore, codice_utente) VALUES (:codice_cpv, :codice_operatore, :codice_utente)');
        $check_albo = $pdo->prepare('SELECT codice FROM r_partecipanti_albo WHERE codice_bando = :codice_bando AND codice_operatore = :codice_operatore AND codice_utente = :codice_utente');
        $sth_insert_albo = $pdo->prepare('INSERT INTO r_partecipanti_albo (codice_bando, codice_operatore, codice_utente, conferma, ammesso, valutato, visto, timestamp_richiesta, timestamp_abilitazione) VALUES (:codice_bando, :codice_operatore, :codice_utente, \'S\', \'S\', \'S\', \'S\', now(), now())');
        $insert_qualita = $pdo->prepare('INSERT INTO b_certificazioni_qualita (codice_operatore,ente,numero_certificato,settore,norma,data_rilascio,data_scadenza,certificato,riferimento,codice_utente,utente_modifica) VALUES (:codice_operatore,:ente,:numero_certificato,:settore,:norma,:data_rilascio,:data_scadenza,:certificato,:riferimento,:codice_utente,-1)');
        $insert_ambientali = $pdo->prepare('INSERT INTO b_certificazioni_ambientali (codice_operatore,ente,numero_certificato,settore,norma,data_rilascio,data_scadenza,certificato,riferimento,codice_utente,utente_modifica) VALUES (:codice_operatore,:ente,:numero_certificato,:settore,:norma,:data_rilascio,:data_scadenza,:certificato,:riferimento,:codice_utente,-1)');
        $insert_altro = $pdo->prepare('INSERT INTO b_altre_certificazioni (codice_operatore,tipo,denominazione,certificazione,codice_utente,utente_modifica) VALUES (:codice_operatore,:tipo,:denominazione,:certificazione,:codice_utente,-1)');
        $i = 0;
        foreach($operatori AS $source) {
          $codice_utente = 0;
          $codice_operatore = 0;
          if(! empty($source["utente"][":email"]) && (! empty($source["utente"][":pec"]) || ! empty($source["operatore"][":codice_fiscale_impresa"]))) {
            $password = genpwd(16);
            $password_token = tokenGen();
            if (filter_var($source["utente"][":email"], FILTER_VALIDATE_EMAIL)) {
              $sth_check_utente->bindValue(':email', $source["utente"][":email"]);
              $sth_check_utente->bindValue(':pec', $source["utente"][":pec"]);
              $sth_check_utente->execute();
              
              if($sth_check_utente->rowCount() < 1) {
                foreach($source["utente"] AS $key => $value) {
                  if ($key != ":dnascita") {
                    $sth_utenti->bindValue($key, ! empty($value) ? $value : '');
                  } else {
                    $data_nascita = date2mysql($value);
                    if(! empty($data_nascita) && preg_match('/^(\d{4})-((0[1-9])|(1[0-2]))-(0[1-9]|[12][0-9]|3[01])$/', $data_nascita)) {
                      $sth_utenti->bindValue(':dnascita', $data_nascita);
                    } else {
                      $sth_utenti->bindValue(':dnascita', NULL, PDO::PARAM_NULL);
                    }
                  }
                }
                $sth_utenti->bindValue(':password', password_hash(md5($password), PASSWORD_BCRYPT));
                $sth_utenti->bindValue(':password_token', $password_token);
                if($sth_utenti->execute()) {
                  $codice_utente = $pdo->lastInsertId();
                  $sth_oe->bindValue(':codice_utente', $codice_utente);
                  if (isset($source["operatore"][":ruolo_referente"])) {
                    $pmi = array("Micro" => "C","Piccola" => "P","Media" => "M","Grande" => "G");
                    if (! in_array($source["operatore"][":ruolo_referente"], array('Amministratore delegato','Amministratore unico','Consigliere delegato','Presidente del consiglio','Socio accomandatario','Legale rappresentante','Procuratore speciale','Direttore tecnico'))) $source["operatore"][":ruolo_referente"] = '';
                    if (in_array($source["operatore"][":pmi"], array_keys($pmi))) { $source["operatore"][":pmi"] = $pmi[$source["operatore"][":pmi"]]; } else { $source["operatore"][":pmi"] = ""; }
                  }
                  foreach($source["operatore"] AS $key => $value) {
                    $sth_oe->bindValue($key, ! empty($value) ? $value : '');
                  }
                  if($sth_oe->execute()) {
                    $inserite++;
                    $codice_operatore = $pdo->lastInsertId();
                    if (!is_dir($config["pub_doc_folder"]."/operatori/".$codice_operatore)) mkdir($config["pub_doc_folder"]."/operatori/".$codice_operatore,0770,true);
                    if (!empty($source["file"])) {
                      foreach($source["file"] AS $key_file => $content) {
                        if (!empty($content)) {
                          if (file_exists($content)) {
                            $name = explode("/",$content);
                            $path = array_splice($name,0,-1);
                            $name = end($name);
                            $path = implode("/",$path);
                            $copy =  copiafile_chunck($name,$config["pub_doc_folder"]."/operatori/".$codice_operatore."/",$path);
                            if (!empty($copy)) {
                              $nome = $copy["nome_file"];
                              $riferimento = $copy["nome_fisico"];
                              $pdo->go("UPDATE b_operatori_economici SET {$key_file} = \"{$nome}\", riferimento_{$key_file} = \"{$riferimento}\" WHERE codice = {$codice_operatore}");
                            }
                          } 
                        }
                      }
                    }
                    if (!empty($source["qualita"])) {
                      foreach($source["qualita"] AS $certificato) {
                        $insert_qualita->bindValue(":codice_operatore",$codice_operatore);
                        $insert_qualita->bindValue(":ente",$certificato["ente"]);
                        $insert_qualita->bindValue(":numero_certificato","");
                        $insert_qualita->bindValue(":settore",$certificato["settore"]);
                        $insert_qualita->bindValue(":norma",$certificato["norma"]);
                        $insert_qualita->bindValue(":data_rilascio","");
                        $insert_qualita->bindValue(":data_scadenza",$certificato["data_scadenza"]);
                        $insert_qualita->bindValue(":certificato","");
                        $insert_qualita->bindValue(":riferimento","");
                        $insert_qualita->bindValue(":codice_utente",$codice_utente);
                        $insert_qualita->execute();
                      }
                    }
                    if (!empty($source["ambientali"])) {
                      foreach($source["ambientali"] AS $certificato) {
                        $insert_ambientali->bindValue(":codice_operatore",$codice_operatore);
                        $insert_ambientali->bindValue(":ente",$certificato["ente"]);
                        $insert_ambientali->bindValue(":numero_certificato","");
                        $insert_ambientali->bindValue(":settore",$certificato["settore"]);
                        $insert_ambientali->bindValue(":norma",$certificato["norma"]);
                        $insert_ambientali->bindValue(":data_rilascio","");
                        $insert_ambientali->bindValue(":data_scadenza",$certificato["data_scadenza"]);
                        $insert_ambientali->bindValue(":certificato","");
                        $insert_ambientali->bindValue(":riferimento","");
                        $insert_ambientali->bindValue(":codice_utente",$codice_utente);
                        $insert_ambientali->execute();
                      }
                    }
                    if (!empty($source["altro"])) {
                      foreach($source["altro"] AS $certificato) {
                        $insert_altro->bindValue(":codice_operatore",$codice_operatore);
                        $insert_altro->bindValue(":tipo",$certificato["ente"]);
                        $insert_altro->bindValue(":denominazione",$certificato["settore"]);
                        $insert_altro->bindValue(":certificazione",$certificato["norma"]);
                        $insert_altro->bindValue(":codice_utente",$codice_utente);
                        $insert_altro->execute();
                      }
                    }
                    $sth_relazione->bindValue(':codice_ente', $_SESSION["ente"]["codice"]);
                    $sth_relazione->bindValue(':codice_utente', $codice_utente);
                    if($sth_relazione->execute()) {
                      $sth_psw_log->bindValue(':codice_utente', $codice_utente);
                      $sth_psw_log->execute();
                      $codici = null;
                      if (!empty($source["cpv"])) {
                        $source["cpv"] = trim($source["cpv"]);
                        $codici = explode(',', $source["cpv"]);
                        if(! empty($codici)) {
                          $codici = array_unique($codici);
                          if ($rel_type == "cpv") {
                            $sth_insert_cpv->bindValue(':codice_operatore', $codice_operatore);
                            $sth_insert_cpv->bindValue(':codice_utente', $codice_utente);
                            foreach ($codici as $cpv_value) {
                              $sth_select_cpv->bindValue(':codice_completo', $cpv_value);
                              if($sth_select_cpv->execute()) {
                                if($sth_select_cpv->rowCount() > 0) {
                                  $codice_cpv = $sth_select_cpv->fetch(PDO::FETCH_COLUMN, 0);
                                  $sth_insert_cpv->bindValue(':codice_cpv', $codice_cpv);
                                  $sth_insert_cpv->execute();
                                }
                              }
                            }
                          }
                        }
                      }
                      if (empty($cliCall)) $dominioLink = $_SERVER["SERVER_NAME"];
                      
                      $link_password = "https://{$dominioLink}/user/change_pwd.php?email=" . base64_encode($source["utente"][":email"]) . "&token=" . base64_encode($password_token);
                      $link_password = "<a href='{$link_password}' target='_blank' title='Cambia password'>{$link_password}</a>";

                      $corpo = $_POST["messaggio"];
                      $corpo = str_replace('[LINK-PIATTAFORMA]', "<a href='https://{$dominioLink}'>https://{$dominioLink}</a>", $corpo);
                      $corpo = str_replace('[LINK-PASSWORD]', $link_password, $corpo);
                      $corpo = str_replace('[EMAIL-SOGGETTO]', $source["utente"][":email"], $corpo);

                      $oggetto = $_POST["oggetto"];
                      
                      $mailer = new Communicator();
                      $mailer->oggetto = $oggetto;
                      $mailer->corpo = $corpo;
                      $mailer->codice_pec = 0;
                      $mailer->coda = true;
                      $mailer->destinatari = ! empty($source["utente"][":pec"]) ? $source["utente"][":pec"] : $source["utente"][":email"];
                      if(! $mailer->send()) {
                        $errori[] = array(
                          'indice' => $i,
                          'errore' => 'Errore nell&#39; invio della comunicazione.',
                          'linea' => $line
                        );
                      }
                    } else {
                      $errori[] = array(
                        'indice' => $i,
                        'errore' => 'Errore nella creazione della relazione con l&#39;ente.',
                        'linea' => $line
                      );
                    }
                  } else {
                    $sth_del_utente->bindValue(':codice', $codice_utente);
                    $sth_del_utente->execute();
                    $errori[] = array(
                      'indice' => $i,
                      'errore' => 'Errore nel salvataggio dell&#39;operatore economico.',
                      'linea' => $line
                    );
                  }
                } else {
                  $errori[] = array(
                    'indice' => $i,
                    'errore' => 'Errore nel salvataggio dell&#39;utente.',
                    'linea' => $line
                  );
                }
              } else {
                $utente = $sth_check_utente->fetch(PDO::FETCH_ASSOC);
                $sth_check_oe->bindValue(":utente",$utente["codice"]);
                $sth_check_oe->execute();
                $operatore = $sth_check_oe->fetch(PDO::FETCH_ASSOC);
                if(! empty($utente) && ! empty($operatore)) {
                  $codice_utente = $utente["codice"];
                  $codice_operatore = $operatore["codice"];
                  $sth_check_relazione->bindValue(':codice_ente', $_SESSION["ente"]["codice"]);
                  $sth_check_relazione->bindValue(':codice_utente', $utente["codice"]);
                  $sth_check_relazione->execute();
                  if($sth_check_relazione->rowCount() < 1) {
                    $sth_relazione->bindValue(':codice_ente', $_SESSION["ente"]["codice"]);
                    $sth_relazione->bindValue(':codice_utente', $utente["codice"]);
                    $sth_relazione->execute();
                    $agganci[] = array(
                      'indice' => $i,
                      'errore' => 'Operatore già presente. Registrazione estesa!',
                      'linea' => $source
                    );
                  } else {
                    $agganci[] = array(
                      'indice' => $i,
                      'errore' => 'Operatore già presente. Registrazione già estesa!',
                      'linea' => $source
                    );
                  }
                } else {
                  $errori[] = array(
                    'indice' => $i,
                    'errore' => 'Operatore già presente. Non è stato possibile verificare l&#39;associazione all&#39;ente!',
                    'linea' => $source
                  );
                }
              }
              if (!empty($_POST["elenco"]) && !empty($codice_utente) && !empty($codice_operatore)) {
                $check_albo->bindValue(':codice_operatore', $codice_operatore);
                $check_albo->bindValue(':codice_utente', $codice_utente);
                $check_albo->bindValue(':codice_bando', $_POST["elenco"]);
                $check_albo->execute();
                if ($check_albo->rowCount() == 0) {
                  $sth_insert_albo->bindValue(':codice_operatore', $codice_operatore);
                  $sth_insert_albo->bindValue(':codice_utente', $codice_utente);
                  $sth_insert_albo->bindValue(':codice_bando', $_POST["elenco"]);
                  $sth_insert_albo->execute();
                }
              }
            } else {
              $errori[] = array(
                'indice' => $i,
                'errore' => 'Indirizzo email non valido',
                'linea' => $source
              );
            }
          } else {
            $errori[] = array(
              'indice' => $i,
              'errore' => 'Dati minimi mancanti',
              'linea' => $source
            );
          }
          $i++;
          if (!empty($cliCall)) {
            $countOE++;
            show_status($countOE,$anagrafiche);
          }
        }
        ob_start();
        echo "<h1>Anagrafiche: {$anagrafiche}</h1>";
        echo "<h1>Inserite: {$inserite}</h1>";
        if (count($agganci) > 0) {
          echo "<h1>Record agganciati: " . count($agganci) . "</h1>";
        }
        if (count($errori) > 0) {
          echo "<h1>Record con errori</h1>";
          echo "<pre style=\"color:#F00\">";
          print_r($errori);
          echo "</pre>";
          echo "<a href=\"/operatori_economici/\" title=\"Operatori Economici\">Ritorna</a>";
        }
        if (isset($unlink_path)) {
          unlink($unlink_path);
          delete_directory($unlunk_tmpPath);
        } else if (isset($upload_path)) {
          if (empty($cliCall)) {
            unlink($upload_path . $filename);
          }
        }
        $report = ob_get_clean();
        if (empty($cliCall)) {
          echo $report;
        } else {
          echo "Elaborazione Conclusa" . PHP_EOL;
        }
      }
    }
  }
?>
