<?
  function importoCSV2Albo($filename,$codice_albo,$tipo="R") {
    global $config;
    global $pdo;
    if (class_exists("salva")) {
      $upload_path = $config["chunk_folder"]."/".$_SESSION["codice_utente"]."/";
      $allowed_filetypes = array('.csv');
      $ext = substr($filename, strpos($filename, '.'), strlen($filename) - 1);
      $msg='';
      if (!in_array($ext, $allowed_filetypes)) $msg = 'Tipologia di file non permesso';
      if (empty($msg)) {
        if (file_exists($upload_path . $filename)){
          ini_set('auto_detect_line_endings',TRUE);
          $file = fopen($upload_path . $filename, "r");
          while(! feof($file))
            $iscritti[]=fgetcsv($file,0,';');
          fclose($file);
          if (count($iscritti) > 0) {
            if ($tipo == "R") {
              $sql = "UPDATE b_commissari_albo SET attivo = 'N', utente_modifica = :utente_modifica WHERE attivo = 'S' AND codice_albo = :codice_albo ";
              $ris_update = $pdo->bindAndExec($sql,array(":utente_modifica"=>$_SESSION["codice_utente"],":codice_albo"=>$codice_albo));
              scrivilog("b_commissari_albo","UPDATE",$pdo->getSQL(),$_SESSION["codice_utente"]);
            }
            foreach($iscritti as $value){
              if($value!=null){
                if(!empty($value[0]) && $value[0]!="CODICE FISCALE"){
                  if(!preg_match("/^[a-z]{6}[0-9]{2}[a-z][0-9]{2}[a-z][0-9]{3}[a-z]$/i", $value[0])){
                    $msg .= "Attenzione, errore nell'inserimento di <strong>".$value[1]." " . $value[2] ."</strong>, <br/>verificare il codice fiscale\n";
                    break;
                  } else {
                    $sql = "DELETE FROM b_commissari_albo WHERE codice_fiscale = :codice_fiscale AND attivo = 'N' AND codice_albo = :codice_albo";
                    $ris_replace = $pdo->bindAndExec($sql,array(":codice_fiscale"=>$value[0],":codice_albo"=>$codice_albo));
                    if ($ris_replace->rowCount() > 0) {
                      scrivilog("b_commissari_albo","DELETE",$pdo->getSQL(),$_SESSION["codice_utente"]);
                    }
                    $iscritto = array();
                    $iscritto["codice_albo"] = $codice_albo;
                    if (!empty($value[0])) $iscritto["codice_fiscale"] = $value[0];
                    if (!empty($value[1])) $iscritto["cognome"] = $value[1];
                    if (!empty($value[2])) $iscritto["nome"] = $value[2];
                    $iscritto["interno"] = "";
                    if (!empty($value[3])) if ($value[3]=="S"||$value[3]=="N") $iscritto["interno"] = $value[3];
                    if (!empty($value[4])) $iscritto["telefono"] = $value[4];
                    if (!empty($value[5])) $iscritto["email"] = $value[5];
                    if (!empty($value[6])) $iscritto["fax"] = $value[6];
                    if (!empty($value[7])) $iscritto["indirizzo"] = $value[7];
                    if (!empty($value[8])) $iscritto["comune"] = $value[8];
                    if (!empty($value[9])) $iscritto["cap"] = $value[9];

                    $salva = new salva();
                    $salva->debug = false;
                    $salva->codop = $_SESSION["codice_utente"];
                    $salva->nome_tabella = "b_commissari_albo";
                    $salva->operazione = "INSERT";
                    $salva->oggetto = $iscritto;
                    $risultato_insert = $salva->save();

                    if ($risultato_insert==false) {
                      $msg .= "Errore nell'inserimento di <strong>" . $value[0] . " - " . $value[1] . " " . $value[2] . "</strong><br>";
                    }
                  }
                }
              }
            }
          } else {
            $msg = "Non sono stati rilevati record";
          }
        } else {
          $msg = "Errore nell'upload";
        }
      }
    } else {
      $msg = "Errore tecnico nell'importazione. Contattare l'Help Desk";
    }
    return $msg;
  }
?>
