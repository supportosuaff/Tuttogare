<?
  if (isset($edit) && $edit && !$lock) {
    if (isset($_POST["submit"])) {
      //echo "sono dentro";
      $upload_path = "/tmp/";
      $allowed_filetypes = array('.csv');
      $filename = $_FILES["lotti"]["name"];
      $ext = substr($filename, strpos($filename, '.'), strlen($filename) - 1);
      $msg='';
      if (!in_array($ext, $allowed_filetypes)) {
        $msg = "Errore formato file importazione";
      } else {
        if (move_uploaded_file($_FILES["lotti"]["tmp_name"], $upload_path . $filename)) {
          ini_set('memory_limit', '1536M');
    	    ini_set('max_execution_time', 600);
          $bind = array();
          $bind[":codice"] = $gara["codice"];
          $sql_cpv = "SELECT r_cpv_gare.codice, b_cpv.codice_completo FROM r_cpv_gare JOIN b_cpv ON r_cpv_gare.codice = b_cpv.codice WHERE r_cpv_gare.codice_gara= :codice";
          $ris_cpv = $pdo->bindAndExec($sql_cpv,$bind);
          $cpv_gara = $ris_cpv->fetch(PDO::FETCH_ASSOC);
          ini_set('auto_detect_line_endings',TRUE);
          $handle = fopen($upload_path . $filename, "r");
          $handle_source = fopen("lotti.csv", "r");
          $array = $fields = array(); $i = 0;
          if ($handle && $handle_source) {
            while (($row = fgetcsv($handle, 0, ";")) !== false) {
              if (empty($fields)) {
                  $fields = $row;
                  continue;
              }
              foreach ($row as $k=>$value) {
                  $array[$i][$fields[$k]] = $value;
              }
              $i++;
            }
            if (!feof($handle)) {
              $msg = "Errore nella lettura del file - 1";;
            }
            $cmp = fgetcsv($handle_source, 0, ";");
            $diff = array_diff($fields,$cmp);
            fclose($handle);
            if (count($diff) > 0) $msg = "Impossibile trovare riga di intestazione";
            if (empty($msg)) {
              if(!empty($gara["procedura"])) {
                $ris_procedura = $pdo->bindAndExec("SELECT b_procedure.avcp FROM b_procedure WHERE b_procedure.codice = :codice_procedura", array(':codice_procedura' => $gara["procedura"]));
                if($ris_procedura->rowCount() > 0) {
                  $rec_procedura = $ris_procedura->fetch(PDO::FETCH_ASSOC);
                  $id_procedura = explode(";", $rec_procedura["avcp"]);
                  if(count($id_procedura) == 1) {
                    $id_procedura = $id_procedura[0];
                    $id_procedura = (int) substr($id_procedura, 0, strpos($id_procedura, '-'));
                  }
                }
              }
              if(!empty($gara["tipologia"])) {
                $ris_tipologia = $pdo->bindAndExec("SELECT esender FROM b_tipologie WHERE b_tipologie.codice = :codice_tipologia", array(':codice_tipologia' => $gara["tipologia"]));
                if($ris_tipologia->rowCount() > 0) {
                  $rec_tipologia = $ris_tipologia->fetch(PDO::FETCH_ASSOC);
                  switch($rec_tipologia["esender"]) {
                    case "SERVICES": $id_tipologia = "S"; $prevalente = "FS"; break;
                    case "WORKS": $id_tipologia = "L"; $prevalente = ""; break;
                    case "SUPPLIES": $id_tipologia = "F"; $prevalente = "FB"; break;
                  }
                }
              }
              $mandatory = ["OGGETTO","DESCRIZIONE","IMPORTO_BASE","IMPORTO_ONERI_NO_RIBASSO","IMPORTO_PERSONALE","DURATA","UNITA_DURATA","ANAC-TIPOAPPALTOTYPE","ANAC-FLAG_ESCLUSO","ANAC-FLAG_PREVEDE_RIP","ANAC-FLAG_RIPETIZIONE"];
              foreach($array AS $i => $data) {
                $keys = array_keys($data);
                $diff = array_diff($keys,$cmp);
                if (count($diff) > 0) {
                  $msg .= "Conteggio campi errato in riga #{$i}<br>";
                } else {
                  foreach($mandatory AS $man) {
										if (empty($data[$man]) && $data[$man] != "0") {
                      $msg .= "Campo {$man} obbligatorio alla riga #{$i}<br>";
                    }
									}
                }
              }
              if (empty($msg)) {
                $ris_simog = $pdo->bindAndExec("SELECT b_lotti_simog.codice_simog FROM b_lotti_simog WHERE b_lotti_simog.codice_gara = :codice_gara LIMIT 0,1", array(':codice_gara' => $gara["codice"]));
								if($ris_simog->rowCount() > 0) $codice_simog = $ris_simog->fetch(PDO::FETCH_ASSOC)["codice_simog"];
                foreach($array AS $i => $data) {
                  $key_transform_importi = ["IMPORTO_BASE","IMPORTO_ONERI_RIBASSO","IMPORTO_ONERI_NO_RIBASSO","IMPORTO_PERSONALE","ANAC-IMPORTO_LOTTO","ANAC-IMPORTO_ATTUAZIONE_SICUREZZA"];
                  foreach($key_transform_importi AS $key) {
                    $import = $data[$key];
                    $point = substr_count($import,".");
                    $comma = substr_count($import,",");
                    if ($point > 0) {
                      if ($comma > 0) {
                        $import = str_replace(".","",$import);
                        $import = str_replace(",",".",$import);
                      } else if ($point > 1) {
                        $import = str_replace(".","",$import);
                      }
                    } else if ($comma > 0) {
                      $import = str_replace(",",".",$import);
                    }
                    $data[$key] = $import;
                  }
                  $lotto = array();
                  $lotto["codice_gara"] = $gara["codice"];
                  $lotto["cig"] = $data["CIG"];
                  $lotto["oggetto"] = $data["OGGETTO"];
                  $lotto["descrizione"] = $data["DESCRIZIONE"];
                  $lotto["ulteriori_informazioni"] = $data["ULTERIORI_INFORMAZIONI"];
                  $lotto["cpv"] = $cpv_gara["codice"];
                  $lotto["importo_base"] = $data["IMPORTO_BASE"];
                  $lotto["importo_oneri_ribasso"] = $data["IMPORTO_ONERI_RIBASSO"];
                  $lotto["importo_oneri_no_ribasso"] = $data["IMPORTO_ONERI_NO_RIBASSO"];
                  $lotto["importo_personale"] = $data["IMPORTO_PERSONALE"];
                  $lotto["durata"] = $data["DURATA"];
                  $lotto["unita_durata"] = strtolower($data["UNITA_DURATA"]);
                  $salva = new salva();
                  $salva->debug = false;
                  $salva->codop = $_SESSION["codice_utente"];
                  $salva->nome_tabella = "b_lotti";
                  $salva->operazione = "INSERT";
                  $salva->oggetto = $lotto;
                  $codice_lotto = $salva->save();
                  if ($codice_lotto != false) {
                    $ris_lotti_simog = $pdo->bindAndExec("SELECT * FROM b_lotti_simog WHERE codice_gara = :codice_gara AND codice_lotto = 0",array(":codice_gara"=>$gara["codice"]));
										if ($ris_lotti_simog->rowCount() === 1 ) {
											$lotto_simog = $ris_lotti_simog->fetch(PDO::FETCH_ASSOC);
											$operazione_ls = "UPDATE";
										} else {
											$lotto_simog = array();
											$operazione_ls = "INSERT";
                    }
                    $lotto_simog["codice_gestore"] = $_SESSION["ente"]["codice"];
                    $lotto_simog["codice_simog"] = $codice_simog;
                    $lotto_simog["codice_gara"] = $gara["codice"];
                    $lotto_simog["codice_lotto"] = $codice_lotto;
                    $lotto_simog["cig"] = $lotto["cig"];
                    $lotto_simog["oggetto"] = $lotto["oggetto"];
                    $lotto_simog["luogo_nuts"] = $gara["nuts"];
                    $lotto_simog["richiesto_simog"] = "N";
                    $lotto_simog["cup"] = $gara["cup"];
                    $lotto_simog["somma_urgenza"] = $data["ANAC-SOMMA_URGENZA"];
                    $lotto_simog["importo_lotto"] = $data["ANAC-IMPORTO_LOTTO"];
                    $lotto_simog["cpv"] = $cpv_gara["codice_completo"];
                    if (!empty($id_procedura)) $lotto_simog["id_scelta_contraente"] = $id_procedura;
                    if (!empty($prevalente)) $lotto_simog["id_categoria_prevalente"] = $prevalente;
                    if (!empty($id_tipologia)) $lotto_simog["tipo_contratto"] = $id_tipologia;
                    $lotto_simog["tipoappaltotype"] = $data["ANAC-TIPOAPPALTOTYPE"];
                    $lotto_simog["flag_escluso"] = $data["ANAC-FLAG_ESCLUSO"];
                    $lotto_simog["artesclusionetype"] = $data["ANAC-ARTESCLUSIONETYPE"];
                    $lotto_simog["importo_attuazione_sicurezza"] = $data["ANAC-IMPORTO_ATTUAZIONE_SICUREZZA"];
                    $lotto_simog["triennio_anno_inizio"] = $data["ANAC-TRIENNIO_ANNO_INIZIO"];
                    $lotto_simog["triennio_anno_fine"] = $data["ANAC-TRIENNIO_ANNO_FINE"];
                    $lotto_simog["triennio_progressivo"] = $data["ANAC-TRIENNIO_PROGRESSIVO"];
                    $lotto_simog["annuale_cui_mininf"] = $data["ANAC-ANNUALE_CUI_MININF"];
                    $lotto_simog["flag_prevede_rip"] = $data["ANAC-FLAG_PREVEDE_RIP"];
                    $lotto_simog["flag_ripetizione"] = $data["ANAC-FLAG_RIPETIZIONE"];
                    $lotto_simog["cig_origine_rip"] = $data["ANAC-CIG_ORIGINE_RIP"];
                    $lotto_simog["flag_cup"] = (!empty($gara["cup"])) ? "S" : "N";
                    $salva = new salva();
                    $salva->debug = false;
                    $salva->codop = $_SESSION["codice_utente"];
                    $salva->nome_tabella = "b_lotti_simog";
                    $salva->operazione = $operazione_ls;
                    $salva->oggetto = $lotto_simog;
                    if ($salva->save() === false) {
                      $msg .= "Errore salvataggio lotto in riga #{$i}<br>";
                      $pdo->go("DELETE FROM b_lotti WHERE codice = :codice_lotto",[":codice_lotto"=>$codice_lotto]);
                    }
                    unset($salva);
                  } else {
                    $msg .= "Errore salvataggio lotto in riga #{$i}<br>";
                  }
                  log_gare($_SESSION["ente"]["codice"],$gara["codice"],"UPDATE","Lotti");
                }
              }
            } 
          } else {
            $msg = "Errore nella lettura del file - 2";
          }
          unlink($upload_path . $filename);
        } else {
          $msg = "Errore nella lettura del file - 3";
        }
      } 
      if (!empty($msg)) {
        ?>
        <h3 class="ui-alert"><?= $msg ?></h3>
        <?
      }
    }
  }
?>