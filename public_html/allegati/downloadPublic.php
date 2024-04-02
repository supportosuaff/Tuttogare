<?
  if (strpos($_GET["ref"],"enti/")===0) $ignoreLog = true;
  include_once("../../config.php");
  include_once($root."/inc/funzioni.php");
  $ref = $_GET["ref"];
  $filename = "";
  $file = explode("/",$ref);
  $path = "";
  $i = 0;

  foreach($file AS $nome_file) {
    $i++;
    purify($nome_file);
    if ($i < count($file)) $path .= "/" . $nome_file;
  }
  if (count($file) > 1) {

    $sql_search_allegati = "SELECT * FROM b_allegati WHERE nome_file = :nome_file AND cartella = :cartella AND sezione = :sezione AND codice_gara = :codice_gara AND online = 'S'";
    $ris_search_allegati = $pdo->prepare($sql_search_allegati);

    $sql_search_catalogo = "SELECT * FROM b_catalogo WHERE foto = :nome_file ";
    $ris_search_catalogo = $pdo->prepare($sql_search_catalogo);

    $sql_search_enti = "SELECT * FROM b_enti WHERE logo = :nome_file ";
    $ris_search_enti = $pdo->prepare($sql_search_enti);

    $sql_search_operatori = "SELECT * FROM b_operatori_economici WHERE (iscrizione_ordine = :nome_file)
                    OR (curriculum = :nome_file)
                    OR (certificato_camerale = :nome_file)";
    $ris_search_operatori = $pdo->prepare($sql_search_operatori);

    $sql_search_simog = "SELECT * FROM b_simog WHERE codice = :codice AND ((file_bando = :nome_file)
                    OR (file_lettera = :nome_file)
                    OR (file_disciplinare = :nome_file))";
    $ris_search_simog = $pdo->prepare($sql_search_simog);


    $array_operatori_field = array("iscrizione_ordine","curriculum","certificato_camerale");
    $array_table_certificati = array("b_certificazioni_ambientali","b_certificazioni_qualita","b_certificazioni_soa");
    $array_simog_field = array("bando","lettera","disciplinare");


    if ($file[0] == "allegati") {
      if (is_numeric($file[1]) && is_dir($config["pub_doc_folder"]."/".$file[0]."/".$file[1])) {
        $ris_search_allegati->bindValue(":nome_file",$nome_file);
        $ris_search_allegati->bindValue(":cartella","");
        $ris_search_allegati->bindValue(":sezione","gara");
        $ris_search_allegati->bindValue(":codice_gara",$file[1]);
        $ris_search_allegati->execute();
        if ($ris_search_allegati->rowCount() == 1) {
          $allegato = $ris_search_allegati->fetch(PDO::FETCH_ASSOC);
          $filename = $allegato["riferimento"];
        } else {
          $ris_search_allegati->bindValue(":sezione","faq-gara");
          $ris_search_allegati->execute();
          if ($ris_search_allegati->rowCount() == 1) {
            $allegato = $ris_search_allegati->fetch(PDO::FETCH_ASSOC);
            $filename = $allegato["riferimento"];
          }
        }
      } else if (!is_numeric($file[1]) && is_dir($config["pub_doc_folder"]."/".$file[0]."/".$file[1])) {
        $sezione = $file[1];
        $cartella = "";
        for($i=3;$i < count($file);$i++) {
          if ($file[$i] == $nome_file) {
            break;
          } else {
            $cartella .= $file[$i] . "/";
          }
        }
        $cartella = substr($cartella, 0, -1);
        $codice_gara = $file[2];
        if ($sezione == "documentale") $codice_gara = 0;
        if ($sezione == "mercato_elettronico") $sezione = "mercato";
        if ($sezione == "contratti" || $sezione == "cpn") {
          $cartella = str_replace($file[3], "", $cartella);
          $codice_gara = $file[3];
        }
        $ris_search_allegati->bindValue(":nome_file",$nome_file);
        $ris_search_allegati->bindValue(":cartella",$cartella);
        $ris_search_allegati->bindValue(":sezione",$sezione);
        $ris_search_allegati->bindValue(":codice_gara",$codice_gara);
        $ris_search_allegati->execute();

        if ($ris_search_allegati->rowCount() == 1) {
          $allegato = $ris_search_allegati->fetch(PDO::FETCH_ASSOC);
          $filename = $allegato["riferimento"];
          if ($sezione == "documentale") {
            if (!isset($_SESSION["ente"]) || (isset($_SESSION["ente"]) && $allegato["codice_ente"] != $_SESSION["ente"]["codice"])) {
              $filename = "";
            }
          }
        } else {
          $campo = "codice_bando";
          if ($sezione=="contratti") {
            $sezione = "contratto";
            $campo = "codice_contratto";
          }
          $table_modulistica = array("albo","contratto","dialogo","mercato","sda");
          $found = false;
          if (in_array($sezione, $table_modulistica) !== false) {
            $sql_search = "SELECT * FROM b_modulistica_{$sezione} WHERE {$campo} = :codice_bando AND nome_file = :nome_file";
            $ris_search = $pdo->bindAndExec($sql_search,array(":nome_file"=>$nome_file,":codice_bando"=>$file[2]));
            if ($ris_search->rowCount() > 0) {
              $modulistica = $ris_search->fetch(PDO::FETCH_ASSOC);
              $filename = $modulistica["riferimento"];
            }
          }
        }
      } else {
        $ris_search_allegati->bindValue(":nome_file",$nome_file);
        $ris_search_allegati->bindValue(":cartella","");
        $ris_search_allegati->bindValue(":sezione","gara");
        $ris_search_allegati->bindValue(":codice_gara","0");
        $ris_search_allegati->execute();
        if ($ris_search_allegati->rowCount() == 1) {
          $allegato = $ris_search_allegati->fetch(PDO::FETCH_ASSOC);
          $filename = $allegato["riferimento"];
          if (empty($allegato["codice_ente"])) {
            Header( "HTTP/1.1 404 Not found" );
            die();
          }
        }
      }
    } else if ($file[0] == "catalogo") {
      $ris_search_catalogo->bindValue(":nome_file",$nome_file);
      $ris_search_catalogo->execute();
      if ($ris_search_catalogo->rowCount() > 0) {
        $allegato = $ris_search_catalogo->fetch(PDO::FETCH_ASSOC);
        $filename = $allegato["riferimento"];
        $imageHeader = true;
      }
    } else if ($file[0] == "enti") {
      $imageHeader = true;
      if (!is_dir($config["pub_doc_folder"]."/".$file[0]."/".$file[1])) {
        $ris_search_enti->bindValue(":nome_file",$nome_file);
        $ris_search_enti->execute();
        if ($ris_search_enti->rowCount() > 0) {
          $allegato = $ris_search_enti->fetch(PDO::FETCH_ASSOC);
          $filename = $allegato["riferimento"];
        }
      } else {
        $filename = $nome_file;
      }
    } else if ($file[0] == "operatori") {
      $tmp_filename = "";
      if (isset($_SESSION["codice_utente"]) || isset($_SESSION["tmp_codice_utente"])) {
        $found = false;
        $ris_search_operatori->bindValue(":nome_file",$nome_file);
        $ris_search_operatori->execute();
        if ($ris_search_operatori->rowCount() == 1) {
          $found = true;
          $operatore = $ris_search_operatori->fetch(PDO::FETCH_ASSOC);
          foreach($array_operatori_field AS $field) {
            if ($operatore[$field]==$nome_file) {
              $codice_operatore = $operatore["codice"];
              $tmp_filename = $operatore["riferimento_".$field];
              break;
            }
          }
        }
        if (!$found) {
          foreach($array_table_certificati AS $tabella) {
            $sql_search = "SELECT * FROM {$tabella} WHERE certificato = :nome_file";
            $ris_search = $pdo->bindAndExec($sql_search,array(":nome_file"=>$nome_file));
            if ($ris_search->rowCount() > 0) {
              $certificato = $ris_search->fetch(PDO::FETCH_ASSOC);
              $codice_operatore = $certificato["codice_operatore"];
              $tmp_filename = $certificato["riferimento"];
              break;
            }
          }
        }
        if (!empty($tmp_filename)) {
          if ((isset($_SESSION["gerarchia"]) && $_SESSION["gerarchia"] <= 2)) {
            $filename = $tmp_filename;
          } else if (!empty($codice_operatore)) {
            $sql = "SELECT codice FROM b_operatori_economici WHERE codice = :codice_operatore AND codice_utente = :codice_utente ";
            $ris_check = $pdo->bindAndExec($sql,array(":codice_operatore"=>$codice_operatore,":codice_utente"=>(isset($_SESSION["codice_utente"])) ? $_SESSION["codice_utente"] : $_SESSION["tmp_codice_utente"]));
            if ($ris_check->rowCount() === 1) {
              $filename = $tmp_filename;
            }
          }
        }
      }
    } else if ($file[0] == "simog") {
      if (is_numeric($file[1]) && is_dir($config["pub_doc_folder"]."/".$file[0]."/".$file[1])) {
        // SCRIPT CARTELLE PUBBLICHE GARE
        $ris_search_simog->bindValue(":nome_file",$nome_file);
        $ris_search_simog->bindValue(":codice",$file[1]);
        $ris_search_simog->execute();
        if ($ris_search_simog->rowCount() == 1) {
          $allegato = $ris_search_simog->fetch(PDO::FETCH_ASSOC);
          foreach($array_simog_field AS $field) {
            if ($allegato["file_".$field]==$nome_file) {
              $filename = $allegato["riferimento_".$field];
              break;
            }
          }
        }
      }
    }
    if (!empty($filename)) {
      ini_set('memory_limit', '6144M');
    	ini_set('max_execution_time', 600);
      $path .= "/" . $filename;
      if (file_exists($config["pub_doc_folder"].$path)) {
        // $type = getTypeAndExtension($config["pub_doc_folder"]."/".$path);

        $filepath = $config["pub_doc_folder"]."/".$path;

        if(file_exists($filepath)) {

          $finfo = new finfo(FILEINFO_MIME_TYPE);
          $type["mime"] = $finfo->file($filepath);

          if ($type != false) {
            $limit = "2018-12-15 10:00";
            if ($_SESSION["developEnviroment"]) $limit = "2018-06-01 00:00";
            if (isset($allegato["timestamp"]) && strtotime($allegato["timestamp"]) > strtotime($limit)) {
              $nome_file = explode(".", $nome_file);

              // $nome_file[0] = ! empty($allegato["titolo"]) ? sanitize_string($allegato["titolo"]) : $nome_file[0];

              if (count($nome_file) > 2) {
                for ($i = count($nome_file) - 1; $i >= 0; $i--) {
                  if (! preg_match('/(mp4|mp3|gif|jpe?g|png|docx?|xlsx?|pptx?|odt|ods|pdf|zip|rar|csv|p7m|xml|txt|rtf)$/i', $nome_file[$i])) {
                    unset($nome_file[$i]);
                    break;
                  }
                }
              }

              // if (count($nome_file) > 2) {
              //   $salt_index = count($nome_file) - 2;
              //   unset($nome_file[$salt_index]);
              // }

              $nome_file = implode(".", $nome_file);
            }
            if (!isset($imageHeader)) {
              header('Content-Description: File Transfer');
              header('Content-Disposition: attachment; filename='.$nome_file);
            } else {
              header('Content-Disposition: inline');
            }
            header("Content-Type: {$type["mime"]}");

            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');

            // readfile($config["pub_doc_folder"].$path);

            $chunk = 1024 * 1024;
            $handle = fopen($filepath, 'rb');
            while (!feof($handle)) {
              $buffer = fread($handle, $chunk);
              echo $buffer;
              ob_flush();
              flush();
            }

            fclose($handle);
            die();
          }
        }
      }
    }
  }
  header("HTTP/1.0 404 Not Found");
  ?><h1>Non si dispone dei permessi necessari o il file non esiste</h1><?
?>